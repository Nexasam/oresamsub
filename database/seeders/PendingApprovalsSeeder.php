<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PendingApprovalsSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->warn('No users found. Run DatabaseSeeder first.');
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            $uid  = $userIds[array_rand($userIds)];
            $date = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));

            DB::table('max_crystal_payments_pending_approvals')->insert([
                'id'                => (string) Str::uuid(),
                'user_id'           => $uid,
                'amount'            => rand(51000, 500000),
                'payment_reference' => 'REF' . strtoupper(Str::random(12)),
                'status'            => $i < 14 ? 0 : 1, // 14 pending, 6 approved
                'created_at'        => $date,
                'updated_at'        => $date,
            ]);
        }

        $this->command->info('✅ Seeded 20 pending funding approval records (14 pending, 6 approved).');
    }
}
