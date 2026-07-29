<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bonuses') || ! Schema::hasTable('bonus_entitlements')) {
            return;
        }

        DB::table('bonus_entitlements')
            ->join('bonuses', 'bonuses.id', '=', 'bonus_entitlements.bonus_id')
            ->whereNotNull('bonuses.reward_valid_days')
            ->select([
                'bonus_entitlements.id',
                'bonus_entitlements.awarded_at',
                'bonus_entitlements.expires_at',
                'bonuses.reward_valid_days',
            ])
            ->orderBy('bonus_entitlements.id')
            ->chunk(500, function ($entitlements) {
                foreach ($entitlements as $entitlement) {
                    $correctExpiry = Carbon::parse($entitlement->awarded_at)
                        ->addDays((int) $entitlement->reward_valid_days);

                    if ($correctExpiry->greaterThan(Carbon::parse($entitlement->expires_at))) {
                        DB::table('bonus_entitlements')
                            ->where('id', $entitlement->id)
                            ->update([
                                'expires_at' => $correctExpiry,
                                'updated_at' => now(),
                            ]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Existing customer rewards are intentionally not shortened on rollback.
    }
};
