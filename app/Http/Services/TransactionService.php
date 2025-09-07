<?php

namespace App\Http\Services;

use Exception;
use App\Models\User;
use App\Models\SiteTemplate;
use Illuminate\Support\Facades\DB;
use App\Models\LandingPagesSetting;
use App\Traits\WalletTransactionLogs;

class TransactionService{
    use WalletTransactionLogs;
    
    public function transaction_refund($transaction_details,$amount_deducted,$refund_reason=NULL,$processed_by=NULL){
        DB::beginTransaction();
        try{
            $user_id =  $transaction_details->user->id;
            $former_wallet_balance =  $transaction_details->user->main_wallet;
            $new_wallet_balance = $transaction_details->user->main_wallet + $amount_deducted;
    
            //update user wallet
             $transaction_details->user->update([
                'main_wallet' => $new_wallet_balance
             ]); 
    
            //  $userinfooo = auth()->user()->username.' '.auth()->user()->email;
            $who_processed = $processed_by == 'cron' ? NULL : $processed_by;
             $transaction_details->update([
                'status' => 2, //i.e refunded
                'set_for_manual' => 0,
                'manually_processed_by' => $who_processed,
                'refund_reason' => $refund_reason,
                'balance_after' => $transaction_details->balance_before,
             ]); 
    
             $walletLog['user_id'] = $user_id;
             $walletLog['transaction_category'] = 'REFUND_TRANSACTION';
             $walletLog['balance_before'] = $former_wallet_balance;
             $walletLog['balance_after'] = $new_wallet_balance;
             $walletLog['transaction_id'] = $transaction_details->id;
             $walletLog['action_by'] = $processed_by;
             $walletLog['description'] = 'Transaction was refunded for the ID: '. $transaction_details->id;
             $this->log_wallet_transactions($walletLog);
            //log: refund
            logger('refund success: successful');
            DB::commit();
    
            return [
                'status' => 1,
                'message' => 'Refund was successful.',
            ];


        }catch(Exception $ex){
            DB::rollBack();
            logger('refund error: '.$ex->getMessage());
            return [
                'status' => -1,
                'message' => $ex->getMessage().' on line: '.$ex->getLine()
            ];
        }
    }


    

}