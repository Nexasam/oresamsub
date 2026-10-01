<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\ConfigSetting;
use App\Services\AdminEmailNotificationRecipients;
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
    public function handle(AdminEmailNotificationRecipients $recipients)
    {

     
            //chhange this later
            $recipient_emails = $recipients->emails('pending_transactions');
            if ($recipient_emails->isEmpty()) {
                return self::SUCCESS;
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

           

            $date_param = '2025-04-04';
            $transactioncount = Transaction::where('set_for_manual',1)
            ->count();

            if( $transactioncount >= 1 ){  
                    ConfigSetting::where('key',$config_setting_key)->update([
                        'current_value' => $config_setting_current_value + 1
                    ]);

                    $dataaa['url'] = config('app.url').'dashboard';
                    $dataaa['transactions_count'] = $transactioncount;
                  
                    // TODO:: this should be dynamic later for all standalones
                    Mail::to($recipient_emails->all())->send(new PendingTransactionNotification($dataaa));
                    // logger('Email sent to notify of pending transactions');

                // }
            }else{
                // logger('No pending pending transaction notification...');
            }
        return self::SUCCESS;
    }
}
