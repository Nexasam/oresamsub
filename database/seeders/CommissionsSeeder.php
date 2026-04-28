<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommissionsSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = DB::table('users')
            ->whereIn('role_id', DB::table('roles')->where('role_name', 'Admin')->pluck('id'))
            ->first();

        $txns = DB::table('transactions')->where('status', '1')->limit(20)->get();

        foreach ($txns as $txn) {
            DB::table('commissions')->insert([
                'id'             => (string) Str::uuid(),
                'transaction_id' => $txn->id,
                'commission'     => rand(10, 200),
                'beneficiary'    => $adminUser->id,
                'transaction_by' => $txn->user_id,
                'payout_status'  => '1',
                'status'         => '1',
                'created_at'     => $txn->created_at,
                'updated_at'     => $txn->created_at,
            ]);
        }

        // Seed wallet funding payloads (wallet creditings table)
        $users = DB::table('users')->limit(3)->get();
        $fundingSlug = DB::table('funding_options')->value('slug') ?? 'crystal_pay';

        foreach ($users as $u) {
            DB::table('funding_webhook_payloads')->insert([
                'id'                   => (string) Str::uuid(),
                'funding_slug'         => $fundingSlug,
                'user_id'              => $u->id,
                'user_email'           => $u->email,
                'status'               => 'PAID',
                'funding_status'       => 'completed',
                'message'              => 'Payment successful',
                'package_id'           => 'PKG' . strtoupper(Str::random(6)),
                'bank_name'            => ['GTBank', 'Access Bank', 'Zenith Bank'][rand(0,2)],
                'account_name'         => $u->first_name . ' ' . $u->last_name,
                'account_number'       => '0' . rand(100000000, 999999999),
                'account_reference'    => 'REF' . strtoupper(Str::random(8)),
                'amount_paid'          => rand(1000, 20000),
                'amount_charged'       => rand(1000, 20000),
                'amount_settled'       => rand(900, 19000),
                'currency'             => 'NGN',
                'transaction_reference'=> 'TXN' . strtoupper(Str::random(10)),
                'collection_reference' => 'COL' . strtoupper(Str::random(10)),
                'payload_content'      => json_encode(['status' => 'PAID', 'amount' => rand(1000, 20000)]),
                'created_at'           => now()->subDays(rand(1, 30)),
                'updated_at'           => now(),
            ]);
        }

        $this->command->info('✅ Commissions & wallet fundings seeded: ' . DB::table('commissions')->count() . ' commissions, ' . DB::table('funding_webhook_payloads')->count() . ' fundings');
    }
}
