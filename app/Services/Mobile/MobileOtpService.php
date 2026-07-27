<?php

namespace App\Services\Mobile;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MobileOtpService
{
    public function send(User $user, string $phoneNumber): void
    {
        $normalizedPhone = $this->normalizeNigerianPhone($phoneNumber);

        $response = Http::acceptJson()
            ->timeout(15)
            ->post('https://api.ng.termii.com/api/sms/otp/send', [
                'api_key' => config('services.termii.api_key', env('TERMII_API_KEY')),
                'message_type' => 'ALPHANUMERIC',
                'to' => $normalizedPhone,
                'from' => config('services.termii.sender_id', env('TERMII_SENDER_ID')),
                'channel' => 'generic',
                'pin_attempts' => 5,
                'pin_time_to_live' => 10,
                'pin_length' => 6,
                'pin_placeholder' => '123456',
                'message_text' => 'Your OresamSub verification code is 123456',
                'pin_type' => 'NUMERIC',
            ]);

        $pinId = $response->json('pinId');

        if (! $response->successful() || ! is_string($pinId) || $pinId === '') {
            throw new RuntimeException('Unable to send the verification code.');
        }

        $user->update([
            'phone_number' => $this->localPhone($normalizedPhone),
            'termii_pin_id' => $pinId,
            'termii_json' => json_encode($response->json(), JSON_THROW_ON_ERROR),
            'phone_verification' => false,
        ]);
    }

    public function verify(User $user, string $otp): void
    {
        if (! $user->termii_pin_id) {
            throw new RuntimeException('Request a verification code first.');
        }

        $response = Http::acceptJson()
            ->timeout(15)
            ->post('https://api.ng.termii.com/api/sms/otp/verify', [
                'api_key' => config('services.termii.api_key', env('TERMII_API_KEY')),
                'pin_id' => $user->termii_pin_id,
                'pin' => $otp,
            ]);

        if (! $response->successful() || $response->json('verified') !== true) {
            throw new RuntimeException('The verification code is invalid or expired.');
        }

        $user->update([
            'phone_verification' => true,
            'termii_pin_id' => null,
        ]);
    }

    private function normalizeNigerianPhone(string $phoneNumber): string
    {
        $digits = preg_replace('/\D+/', '', $phoneNumber);

        if (preg_match('/^0[789][01]\d{8}$/', $digits)) {
            return '234'.substr($digits, 1);
        }

        if (preg_match('/^234[789][01]\d{8}$/', $digits)) {
            return $digits;
        }

        throw new RuntimeException('Enter a valid Nigerian phone number.');
    }

    private function localPhone(string $normalizedPhone): string
    {
        return '0'.substr($normalizedPhone, 3);
    }
}
