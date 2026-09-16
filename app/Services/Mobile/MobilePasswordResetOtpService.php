<?php

namespace App\Services\Mobile;

use App\Models\MobileEmailVerificationCode;
use App\Models\User;
use App\Notifications\MobilePasswordResetOtpNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MobilePasswordResetOtpService
{
    public function issue(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        DB::transaction(function () use ($user, $code): void {
            MobileEmailVerificationCode::query()
                ->where('user_id', $user->id)
                ->where('purpose', 'password_reset')
                ->whereNull('consumed_at')
                ->update(['consumed_at' => now()]);

            MobileEmailVerificationCode::create([
                'user_id' => $user->id,
                'purpose' => 'password_reset',
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(10),
            ]);
        });

        $user->notify(new MobilePasswordResetOtpNotification($code));
    }

    public function consume(User $user, string $code): bool
    {
        return DB::transaction(function () use ($user, $code): bool {
            $reset = MobileEmailVerificationCode::query()
                ->where('user_id', $user->id)
                ->where('purpose', 'password_reset')
                ->whereNull('consumed_at')
                ->latest()
                ->lockForUpdate()
                ->first();

            if (! $reset || $reset->expires_at->isPast() || $reset->attempts >= MobileEmailOtpService::MAX_ATTEMPTS) {
                return false;
            }

            if (! Hash::check($code, $reset->code_hash)) {
                $reset->increment('attempts');
                return false;
            }

            $reset->update(['consumed_at' => now()]);
            return true;
        });
    }
}
