<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TodayTransactionsSeeder extends Seeder
{
    public function run(): void
    {
        $userIds   = DB::table('users')->pluck('id')->toArray();
        $planIds   = DB::table('product_plans')->pluck('id')->toArray();
        $statuses  = [1, 1, 1, 1, -1, 0]; // mostly success
        $categories= ['data', 'airtime', 'cable_subscription', 'utility_bills'];
        $phones    = ['08011111111','08022222222','08033333333','08044444444','08055555555'];

        // 20 transactions today at various hours
        for ($i = 0; $i < 20; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $planId = $planIds[array_rand($planIds)];
            $plan   = DB::table('product_plans')->find($planId);
            $amount = $plan ? $plan->default_selling_price : rand(100, 5000);
            $before = rand(5000, 50000);
            $status = $statuses[array_rand($statuses)];
            $date   = Carbon::today()->addHours(rand(0, 23))->addMinutes(rand(0, 59));

            DB::table('transactions')->insert([
                'id'                        => (string) Str::uuid(),
                'user_id'                   => $userId,
                'product_plan_id'           => $planId,
                'transaction_category'      => $categories[array_rand($categories)],
                'status'                    => $status,
                'wallet_category'           => 'main_wallet',
                'phone_number'              => $phones[array_rand($phones)],
                'amount'                    => $amount,
                'balance_before'            => $before,
                'balance_after'             => $status == 1 ? $before - $amount : $before,
                'description'               => ($plan->product_plan_name ?? 'Service') . ' purchase',
                'user_screen_message'       => $status == 1 ? 'Transaction successful' : ($status == 0 ? 'Transaction pending' : 'Transaction failed'),
                'admin_screen_message'      => 'Processed via MEGASUBPLUG',
                'referral_commission_status'=> '0',
                'txn_reference'             => 'TXN' . strtoupper(Str::random(10)),
                'transaction_route'         => 'web',
                'upline_commission'         => 0,
                'created_at'               => $date,
                'updated_at'               => $date,
            ]);
        }

        // Also add today's wallet fundings
        $users      = DB::table('users')->limit(3)->get();
        $fundingSlug= DB::table('funding_options')->value('slug') ?? 'crystal_pay';

        foreach ($users as $u) {
            $date = Carbon::today()->addHours(rand(1, 10));
            DB::table('funding_webhook_payloads')->insert([
                'id'                    => (string) Str::uuid(),
                'funding_slug'          => $fundingSlug,
                'user_id'               => $u->id,
                'user_email'            => $u->email,
                'status'                => 'PAID',
                'funding_status'        => 'completed',
                'message'               => 'Payment successful',
                'package_id'            => 'PKG' . strtoupper(Str::random(6)),
                'bank_name'             => ['GTBank', 'Access Bank', 'Zenith Bank'][rand(0,2)],
                'account_name'          => $u->first_name . ' ' . $u->last_name,
                'account_number'        => '0' . rand(100000000, 999999999),
                'account_reference'     => 'REF' . strtoupper(Str::random(8)),
                'amount_paid'           => rand(2000, 15000),
                'amount_charged'        => rand(2000, 15000),
                'amount_settled'        => rand(1900, 14000),
                'currency'              => 'NGN',
                'transaction_reference' => 'TXN' . strtoupper(Str::random(10)),
                'collection_reference'  => 'COL' . strtoupper(Str::random(10)),
                'payload_content'       => json_encode(['status' => 'PAID']),
                'created_at'            => $date,
                'updated_at'            => $date,
            ]);
        }

        $todayTxns = DB::table('transactions')->whereDate('created_at', today())->count();
        $this->command->info("✅ Today's data seeded: {$todayTxns} transactions today, 3 wallet fundings today");
    }
}
