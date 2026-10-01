<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class GloAuthResearchController extends Controller
{
    private const BASE_URL = 'https://glocafeapp.gloworld.com/api';

    private const CLIENT_ID = 'T4TsZKPIYzQ0ygQa4Tkhd51pNvTXHIDtCkiGXOhX';

    private const SCOPE = 'read write openid';

    public function index(): View
    {
        abort_unless(app()->environment('local'), 404);

        return view('api-research.glo-auth-refresh');
    }

    public function probe(Request $request): View
    {
        abort_unless(app()->environment('local'), 404);

        $values = $request->validate([
            'phone_number' => ['required', 'string', 'regex:/^(?:0\d{10}|234\d{10}|\+234\d{10})$/'],
            'glo_pin' => ['required', 'string', 'regex:/^\d{6}$/'],
        ]);

        try {
            $login = Http::asForm()
                ->acceptJson()
                ->withHeaders(['x-client-platform' => 'android'])
                ->connectTimeout(10)
                ->timeout(30)
                ->post(self::BASE_URL.'/auth/v1/oauth2-utils/login/', [
                    'client_id' => self::CLIENT_ID,
                    'grant_type' => 'password',
                    'scope' => self::SCOPE,
                    'username' => $this->normalizePhone($values['phone_number']),
                    'password' => $values['glo_pin'],
                ]);
        } catch (ConnectionException) {
            return view('api-research.glo-auth-refresh', [
                'result' => ['stage' => 'login', 'transport_error' => true],
            ]);
        }

        $loginJson = is_array($login->json()) ? $login->json() : [];
        $accessToken = $loginJson['access_token'] ?? null;
        $refreshToken = $loginJson['refresh_token'] ?? null;

        $result = [
            'stage' => 'login',
            'login_http_status' => $login->status(),
            'login_successful' => $login->successful(),
            'login_response_keys' => $this->keys($loginJson),
            'has_access_token' => is_string($accessToken) && $accessToken !== '',
            'has_refresh_token' => is_string($refreshToken) && $refreshToken !== '',
            'login_expires_in' => $this->safeExpiry($loginJson['expires_in'] ?? null),
        ];

        if (! $login->successful() || ! is_string($refreshToken) || $refreshToken === '') {
            return view('api-research.glo-auth-refresh', compact('result'));
        }

        try {
            $refresh = Http::asForm()
                ->acceptJson()
                ->withHeaders(['x-client-platform' => 'android'])
                ->connectTimeout(10)
                ->timeout(30)
                ->post(self::BASE_URL.'/auth/v1/oauth2/token/', [
                    'client_id' => self::CLIENT_ID,
                    'grant_type' => 'refresh_token',
                    'scope' => self::SCOPE,
                    'refresh_token' => $refreshToken,
                ]);
        } catch (ConnectionException) {
            $result['stage'] = 'refresh';
            $result['refresh_transport_error'] = true;

            return view('api-research.glo-auth-refresh', compact('result'));
        }

        $refreshJson = is_array($refresh->json()) ? $refresh->json() : [];
        $newAccessToken = $refreshJson['access_token'] ?? null;
        $newRefreshToken = $refreshJson['refresh_token'] ?? null;

        $result += [
            'stage' => 'refresh',
            'refresh_http_status' => $refresh->status(),
            'refresh_successful' => $refresh->successful(),
            'refresh_response_keys' => $this->keys($refreshJson),
            'refresh_has_access_token' => is_string($newAccessToken) && $newAccessToken !== '',
            'refresh_has_refresh_token' => is_string($newRefreshToken) && $newRefreshToken !== '',
            'refresh_access_token_rotated' => is_string($newAccessToken)
                && $newAccessToken !== ''
                && ! hash_equals((string) $accessToken, $newAccessToken),
            'refresh_token_rotated' => is_string($newRefreshToken)
                && $newRefreshToken !== ''
                && ! hash_equals($refreshToken, $newRefreshToken),
            'refresh_expires_in' => $this->safeExpiry($refreshJson['expires_in'] ?? null),
        ];

        return view('api-research.glo-auth-refresh', compact('result'));
    }

    private function keys(array $value): array
    {
        $sensitive = ['access_token', 'refresh_token', 'id_token'];

        return collect(array_keys($value))
            ->map(fn (int|string $key): string => (string) $key)
            ->reject(fn (string $key): bool => in_array(strtolower($key), $sensitive, true))
            ->sort()
            ->values()
            ->all();
    }

    private function normalizePhone(string $phone): string
    {
        $phone = ltrim(trim($phone), '+');

        if (str_starts_with($phone, '0')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '234')) {
            return substr($phone, 3);
        }

        return $phone;
    }

    private function safeExpiry(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $seconds = (int) $value;

        return $seconds >= 0 && $seconds <= 31_536_000 ? $seconds : null;
    }
}
