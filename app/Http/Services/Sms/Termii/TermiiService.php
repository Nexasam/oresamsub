<?php

namespace App\Http\Services\Sms\Termii;

use App\Models\User;

class TermiiService{

    public function send_otp($data){
        $user_id = $data['user_id'];
        $phone_number = $data['phone_number'];

        if(User::select('phone_verification')->where('id',$user_id)->where('phone_verification',1)->first()){
            return [
                'status' => 1, //-1
                'message' => 'This number has already been verified',
                'data' => $data
            ];
        }
        
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

        logger("Termii Phone check:::::".$phone_num);
        
        $post_fields = [
                "api_key"=>env('TERMII_API_KEY'),
                "message_type"=>"ALPHANUMERIC",
                // "to"=>2348123592660,
                "to"=>$phone_num,
                "from"=>env('TERMII_SENDER_ID'),
                "channel"=>"generic",
                "pin_attempts"=>30,
                "pin_time_to_live"=> 10, //should be updated later
                "pin_length"=>6,
                "pin_placeholder"=>"123456",
                "message_text"=>"Your pin is 123456",
                "pin_type"=>"NUMERIC"
        ];

        $json_encoded_fields = json_encode($post_fields);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.ng.termii.com/api/sms/otp/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$json_encoded_fields,
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        $result = json_decode($response,true);

            
        if ($err) {
            logger("Termii:: cURL Error #:" . $err);
            return [
                'status' => -1,
                'message' => 'Sorry, the validation could not be completed... Please try again',
            ];
        } else {
            //   echo $response;
            // logger("Sendchamp:: result ". json_encode($result) );
            if(isset($result['status']) && $result['status'] == 200){

                User::where('id',$user_id)->update([
                    'termii_pin_id' => $result['pinId']  ?? NULL,
                    'termii_json' => $response  ?? NULL,
                    'phone_number' => $phone_number
                ]);

                return [
                    'status' => 1,
                    'message' => 'Successful. OTP has been sent to the phone number: '.$phone_num.' associated with your BVN',
                    'data' => $result
                ];

            }else{

                logger('Termii failed response: '.$response);
                return [
                    'status' => -1,
                    'message' => 'Sorry an error occurred. Try again',
                    'data' => $result
                ];

            }
        }

    }

    public function confirm_otp($data){
        $user_id = $data['user_id'];
        $otp = $data['otp'] ?? NULL;
        $pin_id = User::select('termii_pin_id','phone_number')->where('id',$user_id)->first()->termii_pin_id;
       
      
        $post_fields = [
                "api_key"=>env('TERMII_API_KEY'),
                "pin_id"=>$pin_id,
                "pin"=>$otp,
        ];

        $json_encoded_fields = json_encode($post_fields);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.ng.termii.com/api/sms/otp/verify',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$json_encoded_fields,
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        $result = json_decode($response,true);

            
        if ($err) {
                logger("Termii:: cURL Error #:" . $err);
                return [
                    'status' => -1,
                    'message' => 'Sorry, the verification could not be completed... Please try again',
                ];
        } else {

            if(isset($result['verified']) && $result['verified'] == true){

                User::where('id',$user_id)->update([
                    'phone_verification' => 1,
                ]);

                return [
                    'status' => 1,
                    'message' => 'Successful. Phone number successfully verified',
                    'data' => $result
                ];

            }else{

                logger('Termii failed response: '.$response);
                return [
                    'status' => -1,
                    'message' => 'Sorry an error occurred. Try again',
                    'data' => $result
                ];

            }
        }

    }

    

  
}