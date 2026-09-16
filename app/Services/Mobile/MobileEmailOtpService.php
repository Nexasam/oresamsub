<?php

namespace App\Services\Mobile;

use App\Models\MobileEmailVerificationCode;
use App\Models\User;
use App\Notifications\MobileVerifyEmailNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MobileEmailOtpService
{
    public const MAX_ATTEMPTS = 5;

    public function issue(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        DB::transaction(function () use ($user, $code): void {
            MobileEmailVerificationCode::query()
                ->where('user_id', $user->id)
                ->where('purpose', 'email_verification')
                ->whereNull('consumed_at')
                ->update(['consumed_at' => now()]);

            MobileEmailVerificationCode::create([
                'user_id' => $user->id,
                'purpose' => 'email_verification',
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(10),
            ]);
        });

        $user->notify(new MobileVerifyEmailNotification($code));
    }

    public function verify(User $user, string $code): bool
    {
        return DB::transaction(function () use ($user, $code): bool {
            $verification = MobileEmailVerificationCode::query()
                ->where('user_id', $user->id)
                ->where('purpose', 'email_verification')
                ->whereNull('consumed_at')
                ->latest()
                ->lockForUpdate()
                ->first();

            if (! $verification || $verification->expires_at->isPast() || $verification->attempts >= self::MAX_ATTEMPTS) {
                return false;
            }

            if (! Hash::check($code, $verification->code_hash)) {
                $verification->increment('attempts');
                return false;
            }

            $verification->update(['consumed_at' => now()]);
            $user->markEmailAsVerified();

            return true;
        });
    }
}
