<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WalletFundingNotification;
use App\Mail\UserRegistrationNotification;

class SyncAddons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-latest-list-of-addons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get list of addons/features';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        if(env('APP_NAME') != 'OresamSub'){
            // $user = User::where('new_user_alert',0)->where('username','emmanuel80')->first();
            //here you fetch from oresam featurelist... just the list
             $curl = curl_init();        
             curl_setopt_array($curl, array(
             CURLOPT_URL => 'https://oresamsub.com/admin/addons',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json'
                ),
            ));

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://oresamsub.com/admin/addons',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json'
                    ),
                )
            );
            $response = curl_exec($curl);

            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            $response_dec = json_decode($response,true);

            foreach($response_dec['data'] as $each_feature){
                echo $each_feature['id'].'<br>';
                // logger('Feature List Ran: '.$each_feature['id']); //test well then insert
            }

            
    
        }else{
            // logger('this is oresam...cant fetch');
        }
       
    }
}
