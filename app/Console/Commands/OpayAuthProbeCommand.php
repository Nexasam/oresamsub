<?php

namespace App\Console\Commands;

use App\Services\Opay\OpayAuthClient;
use App\Services\Opay\OpayResponseDecoder;
use App\Services\Opay\OpayTransportContext;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class OpayAuthProbeCommand extends Command
{
    protected $signature = 'opay:auth-probe
        {--phone= : Authorized OPay phone number for this auth-only probe}
        {--email= : Authorized OPay email address for email-identifier auth diagnosis}
        {--phone-format=app : Phone payload format: app, national, e164, local, or plus}
        {--device-id= : Optional known OPay device id; a one-time id is generated if omitted}
        {--prompt-device-context : Prompt hidden for captured OPay app device/risk context}
        {--debug-shape : Print redacted request-shape diagnostics without secret/header/body values}
        {--skip-session-meta : Skip the signed/encrypted GraphQL session metadata probe}
        {--test-aes-key= : Testing only; forces request AES key in the testing environment}';

    protected $description = 'Safely probe OPay first-party authentication without printing secrets or performing purchases.';

    public function handle(OpayResponseDecoder $decoder): int
    {
        $authClient = $this->authClient();
        $email = $this->normalizeEmail((string) ($this->option('email') ?: ''));
        $phoneInput = $email === ''
            ? (string) ($this->option('phone') ?: $this->ask('OPay phone number'))
            : (string) ($this->option('phone') ?: '');
        $phone = $email === ''
            ? $this->normalizePhone($phoneInput, (string) $this->option('phone-format'))
            : '';
        $maskedIdentifier = $email !== ''
            ? $this->maskEmail($email)
            : $this->maskPhone($phone);
        $password = (string) $this->secret('OPay password/PIN (hidden)');

        if ($password === '') {
            $this->components->error('Password/PIN is required for the auth probe.');

            return self::FAILURE;
        }

        $deviceContext = $this->deviceContext();
        $deviceId = (string) (
            $deviceContext['device_id']
            ?? $this->option('device-id')
            ?: Str::uuid()
        );
        $testAesKey = $this->testAesKey();

        $context = new OpayTransportContext(
            app: (string) config('opay.transport.app', 'opay'),
            platform: (string) config('opay.transport.platform', 'android'),
            versionName: (string) config('opay.version_name', '8.17.2.494'),
            deviceId: $deviceId,
            token: '',
            extraHeaders: $deviceContext['headers'] ?? [],
        );

        $loginRequest = $authClient->prepareUserLogin(
            context: $context,
            payload: [
                'deviceId' => $deviceId,
                'phoneNumber' => $phone,
                'userEmail' => $email,
                'password' => $password,
                'fingerprintPassword' => '',
                'currentLoginMode' => 'password',
                'clientSupportValidationTypes' => $this->clientSupportValidationTypes(),
                'securityId' => '',
                'valChainId' => '',
                'refreshToken' => '',
                'extraMap' => $this->loginExtraMap(),
                'supportPwdPrefix' => 'Y',
            ],
            aesKey: $testAesKey,
        );

        try {
            $login = Http::timeout(30)
                ->withHeaders($loginRequest->headers)
                ->post($loginRequest->url, $loginRequest->body);
        } catch (ConnectionException $exception) {
            $this->components->error('OPay auth probe connection failed. No purchase was attempted.');

            return self::FAILURE;
        }

        try {
            $loginPayload = $decoder->decode($login->body(), $loginRequest->aesKey);
        } catch (Throwable $exception) {
            $this->components->error('OPay auth response could not be decoded safely.');

            return self::FAILURE;
        }

        $summary = [
            'identifier_type' => $email !== '' ? 'email' : 'phone',
            'masked_identifier' => $maskedIdentifier,
            'phone_format' => $email !== '' ? 'none' : (string) $this->option('phone-format'),
            'http_status' => $login->status(),
            ...$decoder->authSummary($loginPayload),
        ];

        if ($this->option('debug-shape')) {
            $summary = [
                ...$summary,
                ...$this->redactedRequestShape($loginRequest),
            ];
        }

        $accessToken = $this->accessToken($loginPayload);

        if ($accessToken !== '' && ! $this->option('skip-session-meta')) {
            $summary['session_meta_probe'] = $this->probeSessionMeta(
                authClient: $authClient,
                decoder: $decoder,
                context: new OpayTransportContext(
                    app: $context->app,
                    platform: $context->platform,
                    versionName: $context->versionName,
                    deviceId: $context->deviceId,
                    token: $accessToken,
                    extraHeaders: $context->extraHeaders,
                ),
                aesKey: $testAesKey,
            );
        } elseif ($accessToken === '') {
            $summary['session_meta_probe'] = 'skipped_no_access_token';
        } else {
            $summary['session_meta_probe'] = 'skipped_by_option';
        }

        $this->line('OPay auth probe result');

        foreach ($summary as $key => $value) {
            $this->line($key.': '.$this->summaryValue($value));
        }

        return self::SUCCESS;
    }

    private function authClient(): OpayAuthClient
    {
        return new OpayAuthClient(
            baseUrl: (string) config('opay.auth_base_url', 'https://api.opayweb.com'),
            publicKeyPem: (string) config('opay.rsa_keys.1'),
            packageName: (string) config('opay.package_name', 'team.opay.pay'),
            versionCode: (string) config('opay.version_code', '8458272'),
        );
    }

    /**
     * Values returned by team.opay.pay.account.verify.d.b() in the APK.
     *
     * @return list<string>
     */
    private function clientSupportValidationTypes(): array
    {
        return [
            'otp',
            'email',
            'password',
            'paymentPin',
            'name',
            'secretProtect',
            'face',
            'bvn',
            'nin',
            'mtn',
            'whatsApp',
            'popupNotify',
            'popupOutCall',
            'answerQuestion',
            'secretQuestion',
        ];
    }

    /**
     * Static entries from team.opay.pay.account.verify.d.a().
     * Face SDK environment values are device/runtime generated and are omitted
     * here until the upstream explicitly requires them.
     *
     * @return array<string, string>
     */
    private function loginExtraMap(): array
    {
        return [
            'supportMTN' => '0',
            'poolVersion' => 'V2',
            'supportFaceVersion' => 'V2',
        ];
    }

    private function probeSessionMeta(
        OpayAuthClient $authClient,
        OpayResponseDecoder $decoder,
        OpayTransportContext $context,
        ?string $aesKey,
    ): string {
        $request = $authClient->prepareSessionMetaQuery(
            context: $context,
            aesKey: $aesKey,
        );

        try {
            $response = Http::timeout(30)
                ->withHeaders($request->headers)
                ->post($request->url, $request->body);
        } catch (ConnectionException) {
            return 'connection_failed';
        }

        if (! $response->successful()) {
            return 'http_'.$response->status();
        }

        try {
            $payload = $decoder->decode($response->body(), $request->aesKey);
        } catch (Throwable) {
            return 'decode_failed';
        }

        return data_get($payload, 'data.currentUser') !== null
            || data_get($payload, 'currentUser') !== null
            ? 'success'
            : 'decoded_without_current_user';
    }

    private function normalizePhone(string $phone, string $format = 'app'): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            $national = substr($digits, 1);
        } elseif (str_starts_with($digits, '234')) {
            $national = substr($digits, 3);
        } elseif (str_starts_with($digits, '8') || str_starts_with($digits, '9') || str_starts_with($digits, '7')) {
            $national = $digits;
        } else {
            throw new RuntimeException('OPay phone number must be a Nigerian mobile number.');
        }

        return match ($format) {
            'app', 'plus' => '+234'.$national,
            'national' => $national,
            'e164' => '234'.$national,
            'local' => '0'.$national,
            default => throw new RuntimeException('OPay phone format must be one of: app, national, e164, local, plus.'),
        };
    }

    private function normalizeEmail(string $email): string
    {
        $email = trim($email);

        if ($email === '') {
            return '';
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('OPay email address is invalid.');
        }

        return strtolower($email);
    }

    private function maskPhone(string $phone): string
    {
        return '+234******'.substr($phone, -4);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $prefix = substr($local, 0, 1);

        return $prefix.'***@'.$domain;
    }

    /**
     * @return array{device_id?: string, headers?: array<string, string>}
     */
    private function deviceContext(): array
    {
        if (! $this->option('prompt-device-context')) {
            return [];
        }

        $deviceId = trim((string) $this->secret('Captured OPay device_id (hidden, blank to use --device-id/generated)'));
        $blackbox = trim((string) $this->secret('Captured OPay blackbox header (hidden, blank to omit)'));
        $gaid = trim((string) $this->ask('Captured gaid header, if known', ''));
        $traceId = trim((string) $this->ask('Captured trace_id header, if known', ''));
        $appsflyerId = trim((string) $this->ask('Captured appsflyerId header, if known', ''));
        $instanceId = trim((string) $this->ask('Captured instanceId header, if known', ''));
        $mcc = trim((string) $this->ask('Captured mcc header, if known', ''));
        $model = trim((string) $this->ask('Captured model header, if known', ''));
        $dma = trim((string) $this->ask('Captured dma/manufacturer header, if known', ''));
        $location = trim((string) $this->ask('Captured location header, if known', 'null|null'));

        $headers = array_filter([
            'blackbox' => $blackbox,
            'gaid' => $gaid,
            'trace_id' => $traceId,
            'appsflyerId' => $appsflyerId,
            'instanceId' => $instanceId,
            'mcc' => $mcc,
            'model' => $model,
            'dma' => $dma,
            'location' => $location,
        ], fn (string $value): bool => $value !== '');

        return array_filter([
            'device_id' => $deviceId,
            'headers' => $headers,
        ], fn (mixed $value): bool => $value !== '' && $value !== []);
    }

    private function accessToken(array $payload): string
    {
        return (string) (
            data_get($payload, 'data.authAccessToken.value')
            ?? data_get($payload, 'authAccessToken.value')
            ?? data_get($payload, 'data.token')
            ?? ''
        );
    }

    private function summaryValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'yes' : 'no';
        }

        if (is_array($value)) {
            return implode(',', array_map('strval', $value));
        }

        if ($value === null || $value === '') {
            return 'none';
        }

        return (string) $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function redactedRequestShape(mixed $request): array
    {
        $payload = json_decode($request->canonicalJson, true);
        $payloadKeys = is_array($payload) ? array_keys($payload) : [];
        $extraMapKeys = is_array($payload['extraMap'] ?? null) ? array_keys($payload['extraMap']) : [];
        $headerNames = array_keys($request->headers);
        sort($headerNames);

        return [
            'debug_request_method' => $request->method,
            'debug_request_path' => $request->path,
            'debug_payload_keys' => $payloadKeys,
            'debug_extra_map_keys' => $extraMapKeys,
            'debug_body_keys' => array_keys($request->body),
            'debug_header_names' => $headerNames,
            'debug_present_device_id' => $this->hasNonEmptyHeader($request->headers, 'device_id'),
            'debug_present_blackbox' => $this->hasNonEmptyHeader($request->headers, 'blackbox'),
            'debug_present_trace_id' => $this->hasNonEmptyHeader($request->headers, 'trace_id'),
            'debug_present_gaid' => $this->hasNonEmptyHeader($request->headers, 'gaid'),
            'debug_present_appsflyer_id' => $this->hasNonEmptyHeader($request->headers, 'appsflyerId'),
            'debug_present_instance_id' => $this->hasNonEmptyHeader($request->headers, 'instanceId'),
            'debug_present_sign_v3' => $this->hasNonEmptyHeader($request->headers, 'signV3'),
            'debug_present_token' => $this->hasNonEmptyHeader($request->headers, 'token'),
            'debug_present_authorization' => $this->hasNonEmptyHeader($request->headers, 'Authorization'),
            'debug_present_encrypted_body' => isset($request->body['encrypt_data']) && (string) $request->body['encrypt_data'] !== '',
            'debug_present_encrypted_aes_key' => isset($request->body['encrypt_aes_key']) && (string) $request->body['encrypt_aes_key'] !== '',
        ];
    }

    /**
     * @param  array<string, mixed>  $headers
     */
    private function hasNonEmptyHeader(array $headers, string $name): bool
    {
        return array_key_exists($name, $headers) && (string) $headers[$name] !== '';
    }

    private function testAesKey(): ?string
    {
        $value = (string) ($this->option('test-aes-key') ?: '');

        if ($value === '') {
            return null;
        }

        if (! app()->environment('testing')) {
            throw new RuntimeException('--test-aes-key is only allowed while running tests.');
        }

        return $value;
    }
}
