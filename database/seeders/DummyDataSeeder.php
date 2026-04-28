<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Fetch existing core records ──────────────────────────────────────
        $adminRole  = DB::table('roles')->where('role_name', 'Admin')->first();
        $userRole   = DB::table('roles')->where('role_name', 'User')->first();
        $basicPlan  = DB::table('user_plans')->where('plan_level', 1)->first();
        $goldPlan   = DB::table('user_plans')->where('plan_level', 2)->first();
        $diamondPlan= DB::table('user_plans')->where('plan_level', 3)->first();
        $platinumPlan=DB::table('user_plans')->where('plan_level', 4)->first();
        $adminUser  = DB::table('users')->where('role_id', $adminRole->id)->first();

        // ── Extra dummy users ────────────────────────────────────────────────
        $userIds = [];
        $dummyUsers = [
            ['first_name'=>'Chidi',    'last_name'=>'Okonkwo',  'email'=>'chidi@example.com',   'phone'=>'08011111111', 'plan'=>$goldPlan],
            ['first_name'=>'Amaka',    'last_name'=>'Nwosu',    'email'=>'amaka@example.com',    'phone'=>'08022222222', 'plan'=>$basicPlan],
            ['first_name'=>'Tunde',    'last_name'=>'Balogun',  'email'=>'tunde@example.com',    'phone'=>'08033333333', 'plan'=>$diamondPlan],
            ['first_name'=>'Ngozi',    'last_name'=>'Eze',      'email'=>'ngozi@example.com',    'phone'=>'08044444444', 'plan'=>$basicPlan],
            ['first_name'=>'Emeka',    'last_name'=>'Obi',      'email'=>'emeka@example.com',    'phone'=>'08055555555', 'plan'=>$goldPlan],
            ['first_name'=>'Fatima',   'last_name'=>'Musa',     'email'=>'fatima@example.com',   'phone'=>'08066666666', 'plan'=>$platinumPlan],
            ['first_name'=>'Seun',     'last_name'=>'Adeyemi',  'email'=>'seun@example.com',     'phone'=>'08077777777', 'plan'=>$basicPlan],
            ['first_name'=>'Blessing', 'last_name'=>'Okoro',    'email'=>'blessing@example.com', 'phone'=>'08088888888', 'plan'=>$diamondPlan],
            ['first_name'=>'Kola',     'last_name'=>'Adesanya', 'email'=>'kola@example.com',     'phone'=>'08099999999', 'plan'=>$goldPlan],
            ['first_name'=>'Yetunde',  'last_name'=>'Fashola',  'email'=>'yetunde@example.com',  'phone'=>'08012345678', 'plan'=>$basicPlan],
        ];

        foreach ($dummyUsers as $u) {
            $uid = (string) Str::uuid();
            DB::table('users')->insert([
                'id'                   => $uid,
                'username'             => strtolower($u['first_name']) . rand(10, 99),
                'first_name'           => $u['first_name'],
                'last_name'            => $u['last_name'],
                'email'                => $u['email'],
                'phone_number'         => $u['phone'],
                'password'             => Hash::make('password'),
                'pin'                  => '1234',
                'role_id'              => $userRole->id,
                'user_plan_id'         => $u['plan']->id,
                'main_wallet'          => rand(500, 50000),
                'upline_id'            => $adminUser->id,
                'default_wallet_setting'=> 'main_wallet',
                'user_2fa_setting'     => 'OFF',
                'active'               => 1,
                'email_verified_at'    => now(),
                'created_at'           => now()->subDays(rand(1, 180)),
                'updated_at'           => now(),
            ]);
            $userIds[] = $uid;
        }

        // include existing non-admin users too
        $existingUsers = DB::table('users')->where('role_id', $userRole->id)->pluck('id')->toArray();
        $allUserIds = array_merge($userIds, $existingUsers);

        // ── Product Plans (MTN SME DATA) ─────────────────────────────────────
        $mtnSmeCategory = DB::table('product_plan_categories')
            ->where('product_plan_category_name', 'MTN SME DATA')->first();
        $gloSmeCategory = DB::table('product_plan_categories')
            ->where('product_plan_category_name', 'GLO SME DATA')->first();
        $airtelSmeCategory = DB::table('product_plan_categories')
            ->where('product_plan_category_name', 'AIRTEL SME DATA')->first();
        $mtnAirtimeCategory = DB::table('product_plan_categories')
            ->where('product_plan_category_name', 'MTN VTU (Virtual Top Up)')->first();
        $gloAirtimeCategory = DB::table('product_plan_categories')
            ->where('product_plan_category_name', 'GLO VTU (Virtual Top Up)')->first();
        $dstvCategory = DB::table('product_plan_categories')
            ->where('product_plan_category_name', 'DSTV')->first();
        $gotvCategory = DB::table('product_plan_categories')
            ->where('product_plan_category_name', 'GOTV')->first();
        $prepaidCategory = DB::table('product_plan_categories')
            ->where('product_plan_category_name', 'PREPAID')->first();
        $megasub = DB::table('automations')->first();

        $planIds = [];

        $plansData = [
            // MTN SME DATA
            ['name'=>'MTN 500MB',   'cat'=>$mtnSmeCategory,    'cost'=>140,  'sell'=>150,  'mb'=>500,   'days'=>30],
            ['name'=>'MTN 1GB',     'cat'=>$mtnSmeCategory,    'cost'=>270,  'sell'=>290,  'mb'=>1024,  'days'=>30],
            ['name'=>'MTN 2GB',     'cat'=>$mtnSmeCategory,    'cost'=>530,  'sell'=>560,  'mb'=>2048,  'days'=>30],
            ['name'=>'MTN 5GB',     'cat'=>$mtnSmeCategory,    'cost'=>1300, 'sell'=>1380, 'mb'=>5120,  'days'=>30],
            ['name'=>'MTN 10GB',    'cat'=>$mtnSmeCategory,    'cost'=>2500, 'sell'=>2650, 'mb'=>10240, 'days'=>30],
            // GLO SME DATA
            ['name'=>'GLO 1GB',     'cat'=>$gloSmeCategory,    'cost'=>250,  'sell'=>270,  'mb'=>1024,  'days'=>30],
            ['name'=>'GLO 2GB',     'cat'=>$gloSmeCategory,    'cost'=>490,  'sell'=>520,  'mb'=>2048,  'days'=>30],
            ['name'=>'GLO 5GB',     'cat'=>$gloSmeCategory,    'cost'=>1200, 'sell'=>1280, 'mb'=>5120,  'days'=>30],
            // AIRTEL SME DATA
            ['name'=>'AIRTEL 1GB',  'cat'=>$airtelSmeCategory, 'cost'=>260,  'sell'=>280,  'mb'=>1024,  'days'=>30],
            ['name'=>'AIRTEL 2GB',  'cat'=>$airtelSmeCategory, 'cost'=>510,  'sell'=>540,  'mb'=>2048,  'days'=>30],
            // MTN AIRTIME
            ['name'=>'MTN ₦100',    'cat'=>$mtnAirtimeCategory,'cost'=>95,   'sell'=>100,  'mb'=>null,  'days'=>null],
            ['name'=>'MTN ₦200',    'cat'=>$mtnAirtimeCategory,'cost'=>190,  'sell'=>200,  'mb'=>null,  'days'=>null],
            ['name'=>'MTN ₦500',    'cat'=>$mtnAirtimeCategory,'cost'=>475,  'sell'=>500,  'mb'=>null,  'days'=>null],
            ['name'=>'MTN ₦1000',   'cat'=>$mtnAirtimeCategory,'cost'=>950,  'sell'=>1000, 'mb'=>null,  'days'=>null],
            // GLO AIRTIME
            ['name'=>'GLO ₦100',    'cat'=>$gloAirtimeCategory,'cost'=>95,   'sell'=>100,  'mb'=>null,  'days'=>null],
            ['name'=>'GLO ₦500',    'cat'=>$gloAirtimeCategory,'cost'=>475,  'sell'=>500,  'mb'=>null,  'days'=>null],
            // DSTV
            ['name'=>'DSTV Padi',   'cat'=>$dstvCategory,      'cost'=>2450, 'sell'=>2500, 'mb'=>null,  'days'=>30],
            ['name'=>'DSTV Yanga',  'cat'=>$dstvCategory,      'cost'=>3450, 'sell'=>3500, 'mb'=>null,  'days'=>30],
            ['name'=>'DSTV Confam', 'cat'=>$dstvCategory,      'cost'=>6150, 'sell'=>6200, 'mb'=>null,  'days'=>30],
            ['name'=>'DSTV Compact','cat'=>$dstvCategory,      'cost'=>10450,'sell'=>10500,'mb'=>null,  'days'=>30],
            // GOTV
            ['name'=>'GOTV Smallie','cat'=>$gotvCategory,      'cost'=>1575, 'sell'=>1600, 'mb'=>null,  'days'=>30],
            ['name'=>'GOTV Jinja',  'cat'=>$gotvCategory,      'cost'=>2450, 'sell'=>2500, 'mb'=>null,  'days'=>30],
            ['name'=>'GOTV Jolli',  'cat'=>$gotvCategory,      'cost'=>3950, 'sell'=>4000, 'mb'=>null,  'days'=>30],
            // PREPAID ELECTRICITY
            ['name'=>'IKEDC Prepaid','cat'=>$prepaidCategory,  'cost'=>null, 'sell'=>0,    'mb'=>null,  'days'=>null],
            ['name'=>'EKEDC Prepaid','cat'=>$prepaidCategory,  'cost'=>null, 'sell'=>0,    'mb'=>null,  'days'=>null],
        ];

        foreach ($plansData as $p) {
            if (!$p['cat']) continue;
            $pid = (string) Str::uuid();
            DB::table('product_plans')->insert([
                'id'                        => $pid,
                'product_plan_name'         => $p['name'],
                'product_plan_category_id'  => $p['cat']->id,
                'automation_product_plan_id'=> rand(1, 99),
                'automation_id'             => $megasub->id,
                'cost_price'                => $p['cost'],
                'data_size_in_mb'           => $p['mb'],
                'validity_in_days'          => $p['days'],
                'default_selling_price'     => $p['sell'],
                'user_level_1_selling_price'=> $p['sell'],
                'user_level_2_selling_price'=> $p['sell'] ? $p['sell'] - 5  : null,
                'user_level_3_selling_price'=> $p['sell'] ? $p['sell'] - 10 : null,
                'user_level_4_selling_price'=> $p['sell'] ? $p['sell'] - 15 : null,
                'visibility'                => 1,
                'public_visibility'         => 1,
                'active_status'             => 1,
                'created_at'                => now(),
                'updated_at'                => now(),
            ]);
            $planIds[] = $pid;
        }

        // ── Transactions ─────────────────────────────────────────────────────
        $statuses    = ['1', '1', '1', '0', '2']; // mostly success
        $categories  = ['data', 'airtime', 'cable_subscription', 'utility_bills'];
        $phones      = ['08011111111','08022222222','08033333333','08044444444','08055555555'];

        for ($i = 0; $i < 80; $i++) {
            $userId  = $allUserIds[array_rand($allUserIds)];
            $planId  = $planIds[array_rand($planIds)];
            $plan    = DB::table('product_plans')->find($planId);
            $amount  = $plan ? $plan->default_selling_price : rand(100, 5000);
            $before  = rand(5000, 50000);
            $status  = $statuses[array_rand($statuses)];
            $cat     = $categories[array_rand($categories)];
            $date    = Carbon::now()->subDays(rand(0, 90))->subHours(rand(0, 23));

            DB::table('transactions')->insert([
                'id'                       => (string) Str::uuid(),
                'user_id'                  => $userId,
                'product_plan_id'          => $planId,
                'transaction_category'     => $cat,
                'status'                   => $status,
                'wallet_category'          => 'main_wallet',
                'phone_number'             => $phones[array_rand($phones)],
                'amount'                   => $amount,
                'balance_before'           => $before,
                'balance_after'            => $status === '1' ? $before - $amount : $before,
                'description'              => $plan ? $plan->product_plan_name . ' purchase' : 'Service purchase',
                'user_screen_message'      => $status === '1' ? 'Transaction successful' : ($status === '0' ? 'Transaction pending' : 'Transaction failed'),
                'admin_screen_message'     => 'Processed via MEGASUBPLUG',
                'referral_commission_status'=> '0',
                'txn_reference'            => 'TXN' . strtoupper(Str::random(10)),
                'transaction_route'        => 'web',
                'upline_commission'        => 0,
                'created_at'               => $date,
                'updated_at'               => $date,
            ]);
        }

        // ── Wallet Logs ───────────────────────────────────────────────────────
        $logTypes = ['credit', 'debit'];
        foreach ($allUserIds as $uid) {
            for ($j = 0; $j < rand(3, 8); $j++) {
                $type   = $logTypes[array_rand($logTypes)];
                $before = rand(1000, 50000);
                $amount = rand(100, 10000);
                $date   = Carbon::now()->subDays(rand(0, 90));
                DB::table('wallet_logs')->insert([
                    'id'             => (string) Str::uuid(),
                    'user_id'        => $uid,
                    'action_by'      => $uid,
                    'transaction_category' => $type === 'credit' ? 'wallet_funding' : 'data',
                    'balance_before' => $before,
                    'balance_after'  => $type === 'credit' ? $before + $amount : max(0, $before - $amount),
                    'description'    => $type === 'credit' ? 'Wallet funded via transfer' : 'Data purchase debit',
                    'created_at'     => $date,
                    'updated_at'     => $date,
                ]);
            }
        }

        // ── Announcements ─────────────────────────────────────────────────────
        $announcements = [
            ['title'=>'Welcome to OresamSub!',         'desc'=>'We are excited to have you on board. Enjoy seamless data and airtime purchases at the best prices.'],
            ['title'=>'New MTN SME Data Plans Added',   'desc'=>'We have added new affordable MTN SME data plans. Check them out now!'],
            ['title'=>'Scheduled Maintenance Notice',   'desc'=>'Our platform will undergo maintenance on Sunday 2am–4am. Services may be briefly unavailable.'],
            ['title'=>'Referral Bonus Promo',           'desc'=>'Refer a friend and earn ₦200 bonus when they make their first transaction. Share your referral link today!'],
            ['title'=>'DSTV & GOTV Subscription Live',  'desc'=>'You can now pay for your DSTV and GOTV subscriptions directly on our platform.'],
        ];

        foreach ($announcements as $a) {
            DB::table('announcements')->insert([
                'id'          => (string) Str::uuid(),
                'title'       => $a['title'],
                'description' => $a['desc'],
                'position'    => 'top',
                'status'      => 1,
                'created_at'  => now()->subDays(rand(1, 30)),
                'updated_at'  => now(),
            ]);
        }

        // ── Commissions ───────────────────────────────────────────────────────
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

        // ── Pending Funding Approvals (above max auto-credit threshold) ──────
        $pendingRefs = [];
        for ($p = 0; $p < 20; $p++) {
            $uid    = $allUserIds[array_rand($allUserIds)];
            $amount = rand(51000, 500000); // above typical max threshold
            $ref    = 'REF' . strtoupper(Str::random(12));
            $date   = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));
            DB::table('max_crystal_payments_pending_approvals')->insert([
                'id'                => (string) Str::uuid(),
                'user_id'           => $uid,
                'amount'            => $amount,
                'payment_reference' => $ref,
                'status'            => $p < 14 ? 0 : 1, // 14 pending, 6 approved
                'created_at'        => $date,
                'updated_at'        => $date,
            ]);
        }

        // ── Wallet Creditings ─────────────────────────────────────────────────
        $banks = ['Access Bank', 'GTBank', 'Zenith Bank', 'First Bank', 'UBA', 'Wema Bank'];
        $txnStatuses = ['PAID', 'PAID', 'PAID', 'PENDING'];
        $fundingStatuses = ['completed', 'completed', 'completed', 'pending'];
        
        foreach ($allUserIds as $uid) {
            $user = DB::table('users')->find($uid);
            if (!$user) continue;
            
            // Create 1-3 wallet creditings per user
            for ($k = 0; $k < rand(1, 3); $k++) {
                $amount = rand(1000, 50000);
                $bank = $banks[array_rand($banks)];
                $txnStatus = $txnStatuses[array_rand($txnStatuses)];
                $fundingStatus = $fundingStatuses[array_rand($fundingStatuses)];
                $date = Carbon::now()->subDays(rand(0, 60))->subHours(rand(0, 23));
                
                DB::table('wallet_creditings')->insert([
                    'id'                    => (string) Str::uuid(),
                    'user_id'               => $uid,
                    'transaction_reference' => 'TXN' . strtoupper(Str::random(10)),
                    'transaction_status'    => $txnStatus,
                    'funding_status'        => $fundingStatus,
                    'transaction_message'   => $txnStatus === 'PAID' ? 'Payment successful' : 'Payment pending',
                    'bank_name'             => $bank,
                    'account_name'          => $user->first_name . ' ' . $user->last_name,
                    'account_number'        => '0' . rand(100000000, 999999999),
                    'account_reference'     => 'REF' . strtoupper(Str::random(10)),
                    'amount_paid'           => $amount,
                    'amount_charged'        => $amount,
                    'amount_settled'        => $fundingStatus === 'completed' ? $amount : 0,
                    'created_at'            => $date,
                    'updated_at'            => $date,
                ]);
            }
        }

        $this->command->info('✅ Dummy data seeded successfully!');
        $this->command->info('   Users: ' . count($dummyUsers) . ' new | Plans: ' . count($planIds) . ' | Transactions: 80 | Wallet logs: ~' . count($allUserIds) * 5 . ' | Wallet Creditings: ~' . count($allUserIds) * 2);
    }
}
