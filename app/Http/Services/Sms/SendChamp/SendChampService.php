<?php

namespace App\Services\Api\Sms\SendChamp;

use App\Models\User;

class SendChampService{

    public function send_otp($data){
        $user_id = $data['user_id'];
        $phone_number = $data['bvn_phone_number'];
        $bvn_json = $data['bvn_json'] ?? NULL;
        $bvn = $data['bvn'] ?? NULL;
        
        if( strlen($phone_number) == 11){
            $extracted_number = substr($phone_number,1);
            $phone_num = "234$extracted_number";
          
         }
         elseif( strlen($phone_number) == 13){
             $extracted_number = substr($phone_number,3);
             $phone_num = "234$extracted_number";
            
         }
         elseif( strlen($phone_number) == 14){
             $extracted_number = substr($phone_number,4);
             $phone_num = "234$extracted_number";
         }else{
            return [
                'status' => -1,
                'message' => 'Wrong number format detected',
            ];
         }

        logger("SendChamp Phone check:::::".$phone_num);
        
        $curl = curl_init(); 
        curl_setopt_array($curl, [
          CURLOPT_URL => env('SEND_CHAMP_OTP_URL'),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode([
            'channel' => 'sms',
            'sender' => env('SEND_CHAMP_SENDER'),
            'token_type' => 'numeric',
            'token_length' => 5,
            'expiration_time' => 60,
            'customer_mobile_number' => $phone_num
          ]),
          CURLOPT_HTTPHEADER => [
            "Authorization: Bearer ".env('SEND_CHAMP_TOKEN'),
            "accept: application/json",
            "content-type: application/json"
          ],
        ]);
        
        $response = curl_exec($curl);
        $result = json_decode($response,true);

        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            logger("SendChamp:: cURL Error #:" . $err);
            return [
                'status' => -1,
                'message' => 'Sorry, the validation could not be completed... Please try again',
            ];
        } else {
            //   echo $response;
            // logger("Sendchamp:: result ". json_encode($result) );
            if(isset($result['status']) && isset($result['code']) && $result['status'] == 'success' && $result['code'] == 200){

                User::where('id',$user_id)->update([
                    'sendchamp_reference' => $result['data']['reference']  ?? NULL,
                    'sendchamp_otp' => $result['data']['token']  ?? NULL,
                    'sendchamp_channel' => $result['data']['channel']['name'] ?? 'sms',
                    'sendchamp_success_status' => 1,
                    'sendchamp_json' => $response,
                    'bvn_verification_status' => 'success',
                    'bvn_json' => $bvn_json,
                    'bvn' => $bvn,
                ]);

                return [
                    'status' => 1,
                    'message' => 'Successful. OTP has been sent to the phone number associated with your BVN',
                ];

            }else if( isset($result['error']) && $result['error'] == 'insufficient funds'){
                logger('Insufficient funds in wallet...');
                return [
                    'status' => -1,
                    'message' => 'Sorry an error occurred. Try again...',
                ];
            }else{
                logger($response);
                return [
                    'status' => -1,
                    'message' => 'Sorry an error occurred. Try again',

                ];
            }
        }
    
    }

    // public function confirm_otp($user_id,$vendor_id,$otp){
            
    //         $user_details = User::select('phone_number','full_name','sendchamp_reference')
    //         ->where('user_id',$user_id)
    //         ->where('vendor_id',$vendor_id)
    //         ->first();
    //         $phone_number = $user_details->phone_number;
    //         $sendchamp_reference = $user_details->sendchamp_reference;
        
    //         $curl = curl_init();
            
    //         // curl_setopt_array($curl, [
    //         // CURLOPT_URL => $this->sendChampConfirmOtpUrl,
    //         // CURLOPT_RETURNTRANSFER => true,
    //         // CURLOPT_ENCODING => "",
    //         // CURLOPT_MAXREDIRS => 10,
    //         // CURLOPT_TIMEOUT => 30,
    //         // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         // CURLOPT_CUSTOMREQUEST => "POST",
    //         // CURLOPT_POSTFIELDS => json_encode([
    //         //     'verification_reference' => $sendchamp_reference,
    //         //     'verification_code' => $otp
    //         // ]),
    //         // CURLOPT_HTTPHEADER => [
    //         //     "Authorization: Bearer ".$this->sendChampToken,
    //         //     "accept: application/json",
    //         //     "content-type: application/json"
    //         // ],
    //         // ]);

    //         curl_setopt_array($curl, [
    //           CURLOPT_URL => "https://api.sendchamp.com/api/v1/verification/confirm",
    //           CURLOPT_RETURNTRANSFER => true,
    //           CURLOPT_ENCODING => "",
    //           CURLOPT_MAXREDIRS => 10,
    //           CURLOPT_TIMEOUT => 30,
    //           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //           CURLOPT_CUSTOMREQUEST => "POST",
    //           CURLOPT_POSTFIELDS => json_encode([
    //             'verification_reference' =>  $sendchamp_reference,
    //             'verification_code' =>  (string) $otp
    //           ]),
    //           CURLOPT_HTTPHEADER => [
    //             "Authorization: Bearer ".$this->sendChampToken,
    //             "accept: application/json",
    //             "content-type: application/json"
    //           ],
    //         ]);
            
    //         $response = curl_exec($curl);
    //         $result = json_decode($response,true);
    //         $err = curl_error($curl);
            
    //         curl_close($curl);
            
    //         if ($err) {
    //             // logger("SendChamp:: cURL Error #:" . $err);
    //             return [
    //                 'status' => -1,
    //                 'message' => 'Sorry, the validation could not be completed',
    //             ];
    //         } else {
    //         //   echo $response;
    //           logger("SendchampConfirm:: status ". $response);
    //           if(isset($result['status']) && isset($result['code']) && $result['status'] == 'success' && $result['code'] == 200){
    //             // logger("Success ".$result['message']);

    //             User::where('user_id',$user_id)->where('vendor_id',$vendor_id)->update([
    //                 'is_phone_verified' => 'YES'
    //             ]);
    //             return [
    //                 'status' => 1,
    //                 'message' => 'Verification was successful',
    //             ];
    
    //           }else if( isset($result['error']) && $result['error'] == 'insufficient funds'){
    //             // logger($result['error']);
    //             return [
    //                 'status' => -1,
    //                 'message' => 'Sorry an error occurred...Please ensure you are entering the right otp',
    //             ];
    //           }else{
    //             // logger($response);
    //             return [
    //                 'status' => -1,
    //                 'message' => 'Sorry an error occurred. Try again. Please ensure you are entering the right otp',
    
    //             ];
    //           }
    //         }
    // }
}