<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\ConfigSetting;
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
           
            //chhange this later
            $get_emails_to_notify_failed_transactions = Setting::where('field_name','emails_to_notify_failed_transactions')->first();
            if(! $get_emails_to_notify_failed_transactions){
                logger('no email to notify yet for failed/pending transaction');
                exit;
            }

            //expected key:  email_sending_count_for_pending_transactions
            $config_setting_key = config('config_settings')[0]['key'];      
            $db_config = ConfigSetting::where('key',$config_setting_key)->first();
            
           if($db_config){
              $config_setting_value = $db_config->value;
              $config_setting_current_value = $db_config->current_value;

              if($config_setting_current_value >= $config_setting_value){
                logger('email notification max threshold reached');exit;
              }
           }else{
               //config file
               $config_setting_value = config('config_settings')[0]['value'];
               $config_setting_current_value = config('config_settings')[0]['current_value'];
               $config_setting_description = config('config_settings')[0]['description'];
               
              $db_config = ConfigSetting::create([
                'key' => $config_setting_key,
                'value' => $config_setting_value,
                'current_value' => $config_setting_current_value,
                'description' => $config_setting_description,
              ]);
           }

           

            $get_emails_to_notify_failed_transactions = Setting::where('field_name','emails_to_notify_failed_transactions')->first();  
            $date_param = '2025-04-04';
            $transactioncount = Transaction::where('set_for_manual',1)->count();

            $emails = $get_emails_to_notify_failed_transactions->field_value;
            $recipient_emails = explode(',',$emails);
          

            if( $transactioncount >= 1 ){  
                    ConfigSetting::where('key',$config_setting_key)->update([
                        'current_value' => $config_setting_current_value + 1
                    ]);

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
