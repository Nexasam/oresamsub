<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\SiteTemplate;
use App\Models\FundingOption;
use App\Models\UserVirtualAccount;
use App\Models\LandingPagesSetting;
use App\Models\FundingOptionBankCodes;

class BvnVerificationService{

    public function bvn_verification($data){
        $user = $data['user'];
        $user_id = $user->id;
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $middle_name = $data['middle_name'];
        $email = $data['email'];
        $phone_number = $data['phone_number'];
        $bvn = $data['bvn'];
        $gender = $data['gender'];
        $dob = $data['dob'];
        $bankcode = "20867"; //hardcoded
        $verification_point = 0;
        $charge = 50;

        //for now: verification amount is 50;
        if($user->main_wallet < $charge){
            return [
                'status' => -1,
                'message' => "You need up to N$charge in your account for BVN verification",
            ];
        }

        if($user->verification_status == 1){
            return [
                'status' => -1,
                'message' => "Your account has already been verified",
            ];
        }

        if($user->verification_attempts >= 3){
            return [
                'status' => -1,
                'message' => "You have exceeded attempts for verification",
            ];
        }

        //use xixapay for oresamsub.
        $getxixa = FundingOption::where('slug','xixapay')->first();
        if(config('app.name') == 'OresamSub' && $getxixa){

            $contract_code = $getxixa->contract_code;
            $api_biz_id = $getxixa->contract_code;
            $api_secret_key = $getxixa->api_secret_key;
            $api_public_key = $getxixa->api_public_key;
            $error = '';
            $error_count = 0;
    
            if($user->bvn_json == NULL){
                $arr = [
                    'businessId' => $contract_code,
                    'id_number' => $bvn,
                    'id_type' => 'bvn',
                ];
                $arrjson = json_encode($arr);
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.xixapay.com/api/identity/verify',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$arrjson,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$api_secret_key,
                    'api-key: '.$api_public_key,
                    'Content-Type: application/json'
                ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
         
                $response_dec = json_decode($response,true);
        
                if(  isset($response_dec['status']) && $response_dec['status'] == 'success' && isset($response_dec['data'])   ){
                    $vendor_first_name = $response_dec['data']['personal_details']['first_name'] ?? NULL;
                    $vendor_last_name = $response_dec['data']['personal_details']['last_name'] ?? NULL;
                    $vendor_middle_name = $response_dec['data']['personal_details']['middle_name'] ?? NULL;
                    $vendor_gender = $response_dec['data']['personal_details']['gender'] ?? NULL;
                    $vendor_date_of_birth = $response_dec['data']['personal_details']['date_of_birth'] ?? NULL;
        
                    $vendor_phone_number = $response_dec['data']['contact_details']['phone_number'] ?? NULL;
                    $vendor_email = $response_dec['data']['contact_details']['email'] ?? NULL;
        
                    $charge = $response_dec['data']['verification_metadata']['cost_charged'] ?? NULL;
        
                    //gender point: 20
                    if(  strtolower($gender) != strtolower($vendor_gender) ){
                        $error .= " Gender did not match ";
                        $error_count++;
                    }else{
                        $verification_point += 20; //for gender
                    }
                 
        
                    //lastname point: 50
                    if( in_array( $last_name, [$vendor_first_name,$vendor_last_name,$vendor_middle_name] ) ){
                        $error .= " Last name did not match ";
                        $error_count++;
                    }else{
                        $verification_point += 50; //for lastname
                    }
        
                    //dob point: 20
                    if(  $dob != $vendor_date_of_birth ){
                        $error .= " Date of Birth did not match ";
                        $error_count++;
                    }else{
                        $verification_point += 20; //for dob
                    }
        
                    //email point: 20
                    if(  strtolower($email) != strtolower($vendor_email) ){
                        $error .= " Email did not match ";
                        $error_count++;
                    }else{
                        $verification_point += 20; //for email
                    }
        
                    //phone_number point: 20
                    if(  strtolower($phone_number) != strtolower($vendor_phone_number) ){
                        $error .= " Phone number did not match ";
                        $error_count++;
                    }else{
                        $verification_point += 20; //for phone
                    }
        
        
                    //qualification
                    if($verification_point >= 70){
                        //update user information
                        User::where('id',$user_id)->update([
                            'bvn' => $bvn,
                            'bvn_json' => $response,
                            'verification_status' => 1,
                            'main_wallet' => $user->main_wallet - $charge,
                        ]);
                        return [
                            'status' => 1,
                            'message' => "BVN Verification was successful",
                        ];
                    }
        
                    logger('BVN Verification: errors: '.$error_count.' errormessage: '.$error);
                    $new_verification_attempts = $user->verification_attempts + 1;
                    User::where('id',$user_id)->update([
                        'bvn' => $bvn,
                        'bvn_json' => $response,
                        'verification_status' => 0,
                        'main_wallet' => $user->main_wallet - $charge,
                        'verification_attempts' => $user->verification_attempts + 1,
                    ]);
                    return [
                        'status' => -1,
                        'message' => 'BVN Verification was not successful. No of errors found: '.$error_count.'. You have made '.$new_verification_attempts.' attempts out of 3.',
                    ];
                    
                }else{
                    return [
                        'status' => -1,
                        'message' => $response_dec['message'],
                    ];
                }

            }else{
                //user verification already available
                $bvn_json = $user->bvn_json;
                $response = $user->bvn_json;
                $response_dec = json_decode($bvn_json,true);
        
                if(  isset($response_dec['status']) && $response_dec['status'] == 'success' && isset($response_dec['data'])   ){
                    $vendor_first_name = $response_dec['data']['personal_details']['first_name'] ?? NULL;
                    $vendor_last_name = $response_dec['data']['personal_details']['last_name'] ?? NULL;
                    $vendor_middle_name = $response_dec['data']['personal_details']['middle_name'] ?? NULL;
                    $vendor_gender = $response_dec['data']['personal_details']['gender'] ?? NULL;
                    $vendor_date_of_birth = $response_dec['data']['personal_details']['date_of_birth'] ?? NULL;
        
                    $vendor_phone_number = $response_dec['data']['contact_details']['phone_number'] ?? NULL;
                    $vendor_email = $response_dec['data']['contact_details']['email'] ?? NULL;
        
                    $charge = $response_dec['data']['verification_metadata']['cost_charged'] ?? NULL;
        
                    //gender point: 20
                    if(  strtolower($gender) != strtolower($vendor_gender) ){
                        $error .= " Gender did not match ";
                        $error_count++;
                    }else{
                        $verification_point += 20; //for gender
                    }
                 
        
                    //lastname point: 50
                    if( in_array( $last_name, [$vendor_first_name,$vendor_last_name,$vendor_middle_name] ) ){
                        $error .= " Last name did not match ";
                        $error_count++;
                    }else{
                        $verification_point += 50; //for lastname
                    }
        
                    //dob point: 20
                    if(  $dob != $vendor_date_of_birth ){
                        $error .= " Date of Birth did not match ";
                        $error_count++;
                    }else{
                        $verification_point += 20; //for dob
                    }
        
                    //email point: 20
                    if(  strtolower($email) != strtolower($vendor_email) ){
                        $error .= " Email did not match ";
                        $error_count++;
                    }else{
                        $verification_point += 20; //for email
                    }
        
                    //phone_number point: 20
                    if(  strtolower($phone_number) != strtolower($vendor_phone_number) ){
                        $error .= " Phone number did not match ";
                        $error_count++;
                    }else{
                        $verification_point += 20; //for phone
                    }
        
        
                    //qualification
                    if($verification_point >= 70){
                        //update user information
                        User::where('id',$user_id)->update([
                            'bvn' => $bvn,
                            'bvn_json' => $response,
                            'verification_status' => 1,
                        ]);
                        return [
                            'status' => 1,
                            'message' => "BVN Verification was successful",
                        ];
                    }
        
                    logger('BVN Verification: errors: '.$error_count.' errormessage: '.$error);
                    $new_verification_attempts = $user->verification_attempts + 1;
                    User::where('id',$user_id)->update([
                        'bvn' => $bvn,
                        'bvn_json' => $response,
                        'verification_status' => 0,
                        'verification_attempts' => $user->verification_attempts + 1,
                    ]);
                    return [
                        'status' => -1,
                        'message' => 'BVN Verification was not successful. No of errors found: '.$error_count.'. You have made '.$new_verification_attempts.' attempts out of 3.',
                    ];            
        
                }else{
                    return [
                        'status' => -1,
                        'message' => $response_dec['message'],
                    ];
                }
                
            }
 
        }else{
            //provider not integrated yet.
            return [
                'status' => -1,
                'message' => "Verification could not be completed",
            ];
        }

 
    }


    

}