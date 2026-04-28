<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

$users = DB::table('users')
    ->where('role_id', DB::table('roles')->where('role_name', 'User')->first()->id)
    ->pluck('id')
    ->toArray();

$banks = ['Access Bank', 'GTBank', 'Zenith Bank', 'First Bank', 'UBA', 'Wema Bank'];
$txnStatuses = ['PAID', 'PAID', 'PAID', 'PENDING'];
$fundingStatuses = ['completed', 'completed', 'completed', 'pending'];

$count = 0;
foreach ($users as $uid) {
    $user = DB::table('users')->find($uid);
    if (!$user) continue;
    
    for ($k = 0; $k < rand(1, 3); $k++) {
        $amount = rand(1000, 50000);
        $bank = $banks[array_rand($banks)];
        $txnStatus = $txnStatuses[array_rand($txnStatuses)];
        $fundingStatus = $fundingStatuses[array_rand($fundingStatuses)];
        $date = Carbon::now()->subDays(rand(0, 60))->subHours(rand(0, 23));
        
        DB::table('wallet_creditings')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $uid,
            'transaction_reference' => 'TXN' . strtoupper(Str::random(10)),
            'transaction_status' => $txnStatus,
            'funding_status' => $fundingStatus,
            'transaction_message' => $txnStatus === 'PAID' ? 'Payment successful' : 'Payment pending',
            'bank_name' => $bank,
            'account_name' => $user->first_name . ' ' . $user->last_name,
            'account_number' => '0' . rand(100000000, 999999999),
            'account_reference' => 'REF' . strtoupper(Str::random(10)),
            'amount_paid' => $amount,
            'amount_charged' => $amount,
            'amount_settled' => $fundingStatus === 'completed' ? $amount : 0,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
        $count++;
    }
}

echo "✅ Added {$count} wallet creditings successfully!\n";
