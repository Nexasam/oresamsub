<?php
namespace App\Traits;

use App\Models\WalletLog;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Void_;

trait WalletTransactionLogs{
    public function log_wallet_transactions($array): void
    {
        // DATA_FROM_MAIN_WALLET, DATA_FROM_DATA_WALLET, AIRTIME, CABLE, BILLS, BULK_DATA_PURCHASE
        // ADMIN_WALLET_CREDITING, USER_WALLET_CREDITING
        WalletLog::create([
            'user_id' => $array['user_id'],
            'transaction_category' => $array['transaction_category'], //  
            'balance_before' => $array['balance_before'],
            'balance_after' => $array['balance_after'],
            'description' => $array['description'],
            'action_by' => $array['action_by'],
            'transaction_id' => $array['transaction_id'] ?? NULL,
        ]);
    }
}