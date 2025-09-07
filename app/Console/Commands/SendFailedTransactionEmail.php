<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\FailedTransactionNotification;

class SendFailedTransactionEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-failed-transaction-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Failed Transaction Email';

    /**
     * Execute the console command.
     */
    public function handle()
    {

  
            // $user = User::where('new_user_alert',0)->where('username','emmanuel80')->first();
            $date_param = '2025-09-07';
            $transaction = Transaction::with(['user','product_plan'])->where('failure_notification',0)
            ->where(function($query){
                $query->where('status',-1)
                        ->orWhere('status',0)
                        ->orWhere('status',2); //failed,pending,refunded should notify users
            })
            // ->where('transaction_category','airtime')
            ->whereDate('created_at','>=',$date_param)
            ->first();
            // logger($transaction);


         
            $get_emails_to_notify_failed_transactions = Setting::where('field_name','emails_to_notify_failed_transactions')->first();
            if(! $get_emails_to_notify_failed_transactions){
                // logger('no email to notify yet for failed/pending transaction');
                exit;
            }
          


            $emails = $get_emails_to_notify_failed_transactions->field_value;
            $recipient_emails = explode(',',$emails);
            // logger($recipient_emails);

            if( $transaction ){  
                    // foreach($transactions as $transaction){
                    // $dataaa['status'] = 'Success';
                    // $dataaa['first_name'] = $transaction->user->first_name;
                    // $dataaa['last_name'] = $transaction->user->last_name;
                    $dataaa['email'] = $transaction->user->email;
                    $dataaa['phone_number'] = $transaction->user->phone_number;
                    $dataaa['id'] = $transaction->id;
                    $dataaa['created_at'] = $transaction->created_at;
                    $dataaa['admin_message'] = $transaction->admin_screen_message;
                    $dataaa['refund_reason'] = $transaction->refund_reason ?? NULL;
                    $dataaa['product_plan_name'] = $transaction->product_plan->product_plan_name ?? 'NA';
                    $dataaa['transaction_category'] = strtoupper($transaction->transaction_category);
                    $dataaa['url'] = config('app.url').'transactions/details/'.$transaction->id;

                    //TODO: work on this later
                    // $turn_off_plan_conditions = ['Insufficient Balance_','Currently Not Available'];
                    // $affected_plan = Transaction::where(function ($query) use ($turn_off_plan_conditions) {
                    //     foreach ($turn_off_plan_conditions as $condition) {
                    //         $query->orWhere('admin_screen_message', 'like', "%$condition%");
                    //     }
                    // })->first();
                    // if($affected_plan){
                    //     logger('found for '. $transaction->id);
                    //     // $affected_plan->update([
                    //     //     'visibility' => 0
                    //     // ]);
                    // }
                    
                  
                    // TODO:: this should be dynamic later for all standalones
                    Mail::to(env('MAIL_FROM_ADDRESS'))->cc($recipient_emails)->send(new FailedTransactionNotification($dataaa));
        
                    Transaction::where('id',$transaction->id)->update([
                        'failure_notification' => 1
                    ]);
                    // logger('Email sent to notify of failed transactions');

                // }
            }else{
                // logger('No pending failed transaction notification...');
            }
        
    }
}
