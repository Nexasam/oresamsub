<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\PendingTransactionNotification;

class SendPendingTransactionEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-pending-transaction-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Pending Transaction Email';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        if( env('APP_NAME') == 'OresamSub' ){
            // $user = User::where('new_user_alert',0)->where('username','emmanuel80')->first();
            $date_param = '2025-04-04';
            $transactioncount = Transaction::where('set_for_manual',1)->count();


         
            //chhange this later
            $get_emails_to_notify_failed_transactions = Setting::where('field_name','emails_to_notify_failed_transactions')->first();
            if(! $get_emails_to_notify_failed_transactions){
                logger('no email to notify yet for failed/pending transaction');
                exit;
            }
          


            $emails = $get_emails_to_notify_failed_transactions->field_value;
            $recipient_emails = explode(',',$emails);
            // logger($recipient_emails);

            if( $transactioncount >= 1 ){  
                    $dataaa['url'] = config('app.url').'dashboard';
                    $dataaa['transactions_count'] = $transactioncount;
                  
                    // TODO:: this should be dynamic later for all standalones
                    Mail::to(env('MAIL_FROM_ADDRESS'))->cc($recipient_emails)->send(new PendingTransactionNotification($dataaa));
                    // logger('Email sent to notify of pending transactions');

                // }
            }else{
                // logger('No pending pending transaction notification...');
            }
        }   else{
            // logger('na this place dey run');
        }
    }
}
