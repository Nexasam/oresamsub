<?php

namespace App\Services\Mobile;

use App\Models\MobileRefreshToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class MobileTokenService
{
    public function issue(User $user, string $deviceName, Request $request): array
    {
        $accessExpiresAt = now()->addMinutes(config('mobile.access_token_minutes'));
        $refreshExpiresAt = now()->addDays(config('mobile.refresh_token_days'));
        $plainRefreshSecret = Str::random(80);

        $accessToken = $user->createToken(
            name: 'mobile:'.$deviceName,
            abilities: ['mobile'],
            expiresAt: $accessExpiresAt,
        );

        $refreshToken = MobileRefreshToken::create([
            'user_id' => $user->id,
            'device_name' => $deviceName,
            'token_hash' => hash('sha256', $plainRefreshSecret),
            'expires_at' => $refreshExpiresAt,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return [
            'access_token' => $accessToken->plainTextToken,
            'access_token_expires_at' => $accessExpiresAt->toIso8601String(),
            'refresh_token' => $refreshToken->id.'|'.$plainRefreshSecret,
            'refresh_token_expires_at' => $refreshExpiresAt->toIso8601String(),
            'token_type' => 'Bearer',
        ];
    }

    public function rotate(string $plainToken, Request $request): array
    {
        [$id, $secret] = array_pad(explode('|', $plainToken, 2), 2, null);

        if (! $id || ! $secret) {
            throw new RuntimeException('Invalid refresh token.');
        }

        return DB::transaction(function () use ($id, $secret, $request) {
            $refreshToken = MobileRefreshToken::query()->lockForUpdate()->find($id);

            if (! $refreshToken || $refreshToken->revoked_at || $refreshToken->expires_at->isPast()) {
                throw new RuntimeException('Refresh token is expired or revoked.');
            }

            if (! hash_equals($refreshToken->token_hash, hash('sha256', $secret))) {
                throw new RuntimeException('Invalid refresh token.');
            }

            $refreshToken->update([
                'last_used_at' => now(),
                'revoked_at' => now(),
            ]);

            return [
                'user' => $refreshToken->user,
                'tokens' => $this->issue($refreshToken->user, $refreshToken->device_name, $request),
            ];
        });
    }
}
