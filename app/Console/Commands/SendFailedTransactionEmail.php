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

        if( env('APP_NAME') == 'OresamSub' ){
            // $user = User::where('new_user_alert',0)->where('username','emmanuel80')->first();
            $date_param = '2025-04-04';
            $transaction = Transaction::with('user')->where('failure_notification',0)
            ->where('status',-1)
            ->whereDate('created_at','>=',$date_param)
            ->first();
            // logger($transaction);


         
            $get_emails_to_notify_failed_transactions = Setting::where('field_name','emails_to_notify_failed_transactions')->first();
            if(! $get_emails_to_notify_failed_transactions){
                logger('no email to notify yet for failed transaction');
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
                    $dataaa['transaction_category'] = strtoupper($transaction->transaction_category);
                    $dataaa['url'] = config('app.url').'transactions/details/'.$transaction->id;
                    
                    // to(env('MAIL_FROM_ADDRESS'))
                    //TODO:: this should be dynamic later for all vendors
                    Mail::to(env('MAIL_FROM_ADDRESS'))->cc($recipient_emails)->send(new FailedTransactionNotification($dataaa));
        
                    Transaction::where('id',$transaction->id)->update([
                        'failure_notification' => 1
                    ]);
                    logger('Email sent to notify of failed transactions');

                // }
            }else{
                logger('No pending failed transaction notification...');
            }
        }   else{
            logger('na this place dey run');
        }
    }
}
