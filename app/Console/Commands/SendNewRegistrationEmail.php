<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WalletFundingNotification;
use App\Mail\UserRegistrationNotification;

class SendNewRegistrationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-new-registration-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send New Registration Email';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        if(env('APP_NAME') == 'OresamSub'){
            // $user = User::where('new_user_alert',0)->where('username','emmanuel80')->first();
            $user = User::where('new_user_alert',0)->first();
            if( $user){
                $dataaa['status'] = 'Success';
                $dataaa['first_name'] = $user->first_name;
                $dataaa['last_name'] = $user->last_name;
                $dataaa['email'] = $user->email;
                $dataaa['phone_number'] = $user->phone_number;
                $dataaa['created_at'] = $user->created_at;
                $dataaa['url'] = config('app.url').'login';
                
                Mail::to(env('MAIL_FROM_ADDRESS'))
                ->cc('oreoofeadebunmigrace@gmail.com') //TODO:: this should be dynamic later for all vendors
                ->send(new UserRegistrationNotification($dataaa));
    
                User::where('id',$user->id)->update([
                    'new_user_alert' => 1
                ]);
                // logger('New Registration Processed');

            }
    
        }
       
    }
}
