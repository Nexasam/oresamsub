<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\Process\Process;
use Throwable;

class AirtelPaymentOptionsResearchController extends Controller
{
    private const BASE_URL = 'https://airtelcareapp.airtel.com.ng';

    private const RSA_PUBLIC_KEY = 'MIIBojANBgkqhkiG9w0BAQEFAAOCAY8AMIIBigKCAYEAoFKeweLTtXFRnuqe9rJxiTOqm93/CZldhphxOAsPOPZKw2mGLsBk1Gf+6+rfnSKmnxDYtUDkkeLL2leS1IEuPj687RXuz4Lyn6SYHeWErr9QqYqSa1V+1HDs7zdnm3YD+HEPy8eiV8Sfs0ZmUQ846esXp/J6WaDqxM13K5JZIawwbi4MaEGKKj5gBTIHkoUtdxAnOK/4FDdr6PivZ3TVBfFTK3HO1afwtmTHrMiI6hETQp32K2NM5he21T/LvR9XvOfpBDOEkjG8ZFYU3aREim8J1NEU2QPYz6lhAaewCeWKmJmbD8F56NVMZnRewT0qrn9H4Zw1BeyHu5BiqL+0uUHVlO3XupQb9RnRonMe/Z/pR34J1Y1Z53P2yP44xL/INlf/dWyiUmtdHi23+VvcCExX2Vu0OIUlHg7cw/yC5uC1Qy3JHy8m5O2f6JshHShRszvNWjclyeoaeGDncCtXdz2lNNvK7sbUMCxjFcxMsjnhpiL3h0NvxjGjvV3HsQDHAgMBAAE=';

    public function index(): View
    {
        abort_unless(app()->environment('local'), 404);

        return view('api-research.airtel-payment-options');
    }

    public function send(Request $request): View
    {
        abort_unless(app()->environment('local'), 404);

        $values = $request->validate([
            'session_token' => ['required', 'string'],
            'uid_key' => ['required', 'string'],
            'dynamic_token' => ['required', 'string'],
            'device_id' => ['required', 'regex:/^[0-9a-f]{16}$/i'],
            'subscriber_id' => ['required', 'string'],
            'purchase_beneficiary' => ['nullable', 'string'],
            'purchase_amount' => ['required', 'numeric', 'gt:0'],
            'purchase_product_code' => ['required', 'string'],
            'device_imei' => ['nullable', 'string'],
            'device_mac_address' => ['nullable', 'string'],
        ]);

        $amount = (float) $values['purchase_amount'];
        $beneficiary = trim($values['purchase_beneficiary'] ?: $values['subscriber_id']);
        $units = (int) ceil($amount * 2) / 2;
        $consumerTransactionId = (string) Str::uuid();

        $payload = [
            'siNumber' => $beneficiary,
            'price' => $this->decimal($amount),
            'subcat' => 'PREPAID_MOBILE',
            'suggestMode' => '0',
            'flowType' => 'PREPAID_BUY_BUNDLES',
            'subFlowType' => 'UNKNOWN',
            'currency' => 'NGN',
            'units' => (string) $units,
            'payerBankId' => '',
            'displayType' => '1',
            'productCode' => $values['purchase_product_code'],
            'lob' => 'prepaid',
            'deviceManufacturer' => 'TECNO MOBILE LIMITED',
            'deviceBrand' => 'TECNO',
            'deviceProduct' => 'KG5j-OP',
            'devicetype' => 'TECNO KG5j',
            'osystem' => 'Android',
            'osversion' => '11',
            'carrier' => 'MTN NG',
            'availableCarriers' => 'Airtel NG,Airtel NG',
            'resolution' => '720x1444',
            'deviceid' => $values['device_id'],
            'deviceip' => '',
            'appversion' => '1.4.23',
            'buildNumber' => '249',
            'imei' => $values['device_imei'] ?? '',
            'macAddress' => $values['device_mac_address'] ?? '',
            'isGSMLoanEnabled' => false,
            'overdraftLoanEnabled' => false,
            'x-consumer-txn-id' => $consumerTransactionId,
        ];

        try {
            $commonHeaders = [
                'Accept' => 'application/json',
                'requesttype' => 'singed_encrypt',
                'x-bsy-eyv' => 'x.1.1',
                'x-bsy-dt' => $values['dynamic_token'],
                'x-bsy-did' => $values['device_id'],
                'x-bsy-ct' => $values['subscriber_id'],
                'x-client' => 'map',
                'x-service-class' => 'DEFAULT',
                'x-bsy-os' => 'android',
                'x-bsy-network' => '4',
                'x-bsy-net' => '2',
                'x-bsy-manufacturer' => 'TECNO MOBILE LIMITED',
                'x-bsy-device-brand' => 'TECNO',
                'x-bsy-device-product' => 'KG5j-OP',
                'x-bsy-carrier' => 'MTN NG',
                'x-bsy-vpn' => '0',
                'x-bsy-snet' => '0',
                'x-bsy-vn' => '1.4.23',
                'x-bsy-bn' => '249',
                'x-bsy-locale' => 'en',
                'User-Agent' => 'android',
            ];

            $probePath = '/myairtelapp/africa/v4/prepaid/accountbalance?siNumber='.rawurlencode($values['subscriber_id']);
            $probeSignature = base64_encode(hash_hmac('sha256', 'GET'.$probePath, $values['session_token'], true));
            $probeResponse = Http::timeout(30)->withHeaders($commonHeaders + [
                'x-bsy-utkn' => $values['uid_key'].':'.$probeSignature,
            ])->get(self::BASE_URL.$probePath);

            [$encryptedBody, $encryptedEnvelope] = $this->encrypt($payload);
            $path = '/myairtelapp/africa/v3/payment/paymentoptions';
            $signature = base64_encode(hash_hmac('sha256', 'POST'.$path.$encryptedBody, $values['session_token'], true));

            $response = Http::timeout(30)->withHeaders($commonHeaders + [
                'Content-Type' => 'application/json; charset=utf-8',
                'x-bsy-utkn' => $values['uid_key'].':'.$signature,
                'x-bsy-rp' => $encryptedEnvelope,
            ])->withBody($encryptedBody, 'application/json; charset=utf-8')
                ->post(self::BASE_URL.$path);

            $result = [
                'auth_probe' => [
                    'endpoint' => 'GET accountbalance',
                    'http_status' => $probeResponse->status(),
                    'status' => $probeResponse->json('status'),
                    'responseCode' => $probeResponse->json('responseCode'),
                    'errorMsg' => $probeResponse->json('errorMsg'),
                ],
                'http_status' => $response->status(),
                'encrypted_body_length' => strlen($encryptedBody),
                'payload' => $payload,
                'response' => $response->json() ?? $response->body(),
            ];
        } catch (ConnectionException $exception) {
            $result = ['error' => 'Connection failed: '.$exception->getMessage(), 'payload' => $payload];
        } catch (Throwable $exception) {
            report($exception);
            $result = ['error' => $exception->getMessage(), 'payload' => $payload];
        }

        return view('api-research.airtel-payment-options', compact('result'));
    }

    public function purchase(Request $request): View
    {
        abort_unless(app()->environment('local'), 404);

        $values = $request->validate([
            'session_token' => ['required', 'string'],
            'uid_key' => ['required', 'string'],
            'dynamic_token' => ['required', 'string'],
            'device_id' => ['required', 'regex:/^[0-9a-f]{16}$/i'],
            'subscriber_id' => ['required', 'string'],
            'purchase_amount' => ['required', 'numeric', Rule::in([75])],
            'purchase_product_code' => ['required', Rule::in(['Daily_Plan_75'])],
            'purchase_bundle_name' => ['required', Rule::in(['Daily Plan 75'])],
            'purchase_validity' => ['required', Rule::in(['1 Day'])],
            'purchase_confirmation' => ['required', Rule::in(['PURCHASE 75 NGN'])],
            'device_imei' => ['nullable', 'string'],
            'device_mac_address' => ['nullable', 'string'],
        ]);

        $headers = $this->commonHeaders($values);
        $subscriber = trim($values['subscriber_id']);

        try {
            $probePath = '/myairtelapp/africa/v4/prepaid/accountbalance?siNumber='.rawurlencode($subscriber);
            $probeResponse = Http::timeout(30)->withHeaders($headers + [
                'x-bsy-utkn' => $this->signature('GET'.$probePath, $values),
            ])->get(self::BASE_URL.$probePath);

            if (! $probeResponse->successful() || strtolower((string) $probeResponse->json('status')) !== 'success') {
                return view('api-research.airtel-payment-options', ['result' => [
                    'stage' => 'session-validation',
                    'http_status' => $probeResponse->status(),
                    'message' => 'The Airtel session validation failed. No purchase was attempted.',
                ]]);
            }

            $paymentOptionsPayload = $this->paymentOptionsPayload($values, $subscriber);
            [$optionsBody, $optionsEnvelope] = $this->encrypt($paymentOptionsPayload);
            $optionsPath = '/myairtelapp/africa/v3/payment/paymentoptions';
            $optionsResponse = Http::timeout(30)->withHeaders($headers + [
                'Content-Type' => 'application/json; charset=utf-8',
                'x-bsy-utkn' => $this->signature('POST'.$optionsPath.$optionsBody, $values),
                'x-bsy-rp' => $optionsEnvelope,
            ])->withBody($optionsBody, 'application/json; charset=utf-8')->post(self::BASE_URL.$optionsPath);

            if (! $optionsResponse->successful() || strtolower((string) $optionsResponse->json('status')) !== 'success') {
                return view('api-research.airtel-payment-options', ['result' => [
                    'stage' => 'payment-options',
                    'http_status' => $optionsResponse->status(),
                    'message' => 'Payment-option validation failed. No purchase was attempted.',
                ]]);
            }

            $purchasePayload = $this->purchasePayload($values, $subscriber);
            [$purchaseBody, $purchaseEnvelope] = $this->encrypt($purchasePayload);
            $purchasePath = '/myairtelapp/africa/v1/money/processtransaction';
            $purchaseResponse = Http::timeout(30)->withHeaders($headers + [
                'Content-Type' => 'application/json',
                'x-bsy-utkn' => $this->signature('POST'.$purchasePath.$purchaseBody, $values),
                'x-bsy-rp' => $purchaseEnvelope,
            ])->withBody($purchaseBody, 'application/json')->post(self::BASE_URL.$purchasePath);

            $result = [
                'stage' => 'purchase',
                'http_status' => $purchaseResponse->status(),
                'summary' => [
                    'amount' => 75.0,
                    'currency' => 'NGN',
                    'productCode' => 'Daily_Plan_75',
                    'bundleName' => 'Daily Plan 75',
                    'packValidity' => '1 Day',
                    'paymentMode' => 'AIRTIME',
                    'ownLine' => true,
                ],
                'airtel' => [
                    'status' => $purchaseResponse->json('status'),
                    'responseCode' => $purchaseResponse->json('responseCode'),
                    'message' => $purchaseResponse->json('message') ?? $purchaseResponse->json('errorMsg'),
                    'txnId' => $purchaseResponse->json('data.txnId'),
                ],
            ];
        } catch (ConnectionException $exception) {
            $result = ['stage' => 'connection', 'error' => 'Connection failed: '.$exception->getMessage()];
        } catch (Throwable $exception) {
            report($exception);
            $result = ['stage' => 'local-error', 'error' => $exception->getMessage()];
        }

        return view('api-research.airtel-payment-options', compact('result'));
    }

    private function commonHeaders(array $values): array
    {
        return [
            'Accept' => 'application/json',
            'requesttype' => 'singed_encrypt',
            'x-bsy-eyv' => 'x.1.1',
            'x-bsy-dt' => $values['dynamic_token'],
            'x-bsy-did' => $values['device_id'],
            'x-bsy-ct' => $values['subscriber_id'],
            'x-client' => 'map',
            'x-service-class' => 'DEFAULT',
            'x-bsy-os' => 'android',
            'x-bsy-network' => '4',
            'x-bsy-net' => '2',
            'x-bsy-manufacturer' => 'TECNO MOBILE LIMITED',
            'x-bsy-device-brand' => 'TECNO',
            'x-bsy-device-product' => 'KG5j-OP',
            'x-bsy-carrier' => 'MTN NG',
            'x-bsy-vpn' => '0',
            'x-bsy-snet' => '0',
            'x-bsy-vn' => '1.4.23',
            'x-bsy-bn' => '249',
            'x-bsy-locale' => 'en',
            'User-Agent' => 'android',
        ];
    }

    private function paymentOptionsPayload(array $values, string $subscriber): array
    {
        return [
            'siNumber' => $subscriber, 'price' => '75', 'subcat' => 'PREPAID_MOBILE',
            'suggestMode' => '0', 'flowType' => 'PREPAID_BUY_BUNDLES', 'subFlowType' => 'UNKNOWN',
            'currency' => 'NGN', 'units' => '75', 'payerBankId' => '', 'displayType' => '1',
            'productCode' => 'Daily_Plan_75', 'lob' => 'prepaid',
            ...$this->devicePayload($values),
            'isGSMLoanEnabled' => false, 'overdraftLoanEnabled' => false,
        ];
    }

    private function purchasePayload(array $values, string $subscriber): array
    {
        return [
            ...$this->devicePayload($values),
            'clientTxnId' => (string) Str::uuid(), 'siNumber' => $subscriber, 'pgId' => 0,
            'amount' => 75, 'msisdn' => $subscriber, 'amountdisplayText' => 'Amount', 'currency' => 'NGN',
            'transactionType' => 'PREPAID_BUY_BUNDLES', 'subTransactionType' => 'UNKNOWN',
            'paymentMode' => 'AIRTIME', 'displayType' => 1, 'units' => 0, 'recipientName' => '',
            'transactionFee' => 0.0, 'totalAmount' => 0.0, 'comments' => '', 'convenienceFee' => 0.0,
            'benefitIcon' => 'null', 'isOtherBanks' => true, 'productCode' => 'Daily_Plan_75',
            'bundleName' => 'Daily Plan 75', 'packValidity' => '1 Day',
            'isSegmentedBundle' => false, 'isBPFlow' => false,
        ];
    }

    private function devicePayload(array $values): array
    {
        return [
            'deviceip' => '', 'appversion' => '1.4.23', 'x-consumer-txn-id' => (string) Str::uuid(),
            'resolution' => '720x1444', 'deviceid' => $values['device_id'], 'buildNumber' => '249',
            'devicetype' => 'TECNO KG5j', 'osystem' => 'Android', 'carrier' => 'MTN NG',
            'macAddress' => $values['device_mac_address'] ?? '', 'imei' => $values['device_imei'] ?? '',
            'availableCarriers' => 'Airtel NG,Airtel NG', 'deviceProduct' => 'KG5j-OP',
            'deviceManufacturer' => 'TECNO MOBILE LIMITED', 'osversion' => '11', 'deviceBrand' => 'TECNO',
        ];
    }

    private function signature(string $signingText, array $values): string
    {
        return $values['uid_key'].':'.base64_encode(hash_hmac('sha256', $signingText, $values['session_token'], true));
    }

    private function encrypt(array $payload): array
    {
        $plaintext = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $pot = (string) Str::uuid();
        $salt = random_bytes(16);
        $iv = random_bytes(12);
        $envelope = json_encode([
            'pen' => base64_encode($salt),
            'pot' => $pot,
            'ts' => (int) floor(microtime(true) * 1000),
        ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        $key = hash_pbkdf2('sha256', $pot, $salt, 65536, 32, true);
        $tag = '';
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, '', 16);
        throw_if($ciphertext === false, \RuntimeException::class, 'AES-GCM encryption failed.');

        $keyFile = tempnam(sys_get_temp_dir(), 'airtel-public-key-');
        throw_if($keyFile === false, \RuntimeException::class, 'Could not create temporary key file.');

        try {
            $pem = "-----BEGIN PUBLIC KEY-----\n".chunk_split(self::RSA_PUBLIC_KEY, 64, "\n")."-----END PUBLIC KEY-----\n";
            file_put_contents($keyFile, $pem, LOCK_EX);
            $process = new Process([
                'openssl', 'pkeyutl', '-encrypt', '-pubin', '-inkey', $keyFile,
                '-pkeyopt', 'rsa_padding_mode:oaep', '-pkeyopt', 'rsa_oaep_md:sha256',
                '-pkeyopt', 'rsa_mgf1_md:sha256',
            ]);
            $process->setInput($envelope);
            $process->mustRun();
            $encryptedEnvelope = $process->getOutput();
        } finally {
            @unlink($keyFile);
        }

        return [base64_encode($iv.$ciphertext.$tag), base64_encode($encryptedEnvelope)];
    }

    private function decimal(float $amount): string
    {
        return rtrim(rtrim(number_format($amount, 2, '.', ''), '0'), '.');
    }
}
