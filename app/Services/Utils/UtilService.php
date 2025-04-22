<?php
namespace App\Services\Utils;

class UtilService{
    public function phoneNumberValidation($phone_number,$shouldHave234 = false){
           

            $strippedString = str_replace(' ', '', $phone_number); //remove space
            $phone_number  = str_replace('+','',$strippedString); //remove plus to generate new number

           if(strlen($phone_number) != 11 && strlen($phone_number) != 13 ){
            return [
                'status' => -1,
                'message' => 'Please check your number again',
                'validated_phone_number' => $phone_number,
               ];
           }
            
            if( strlen($phone_number) == 11){
                $extracted_number = substr($phone_number,1);
                $phone_num_234 = "234$extracted_number";
                $phone_num_no_234 = $phone_number;
            
            }
            if( strlen($phone_number) == 13){
                $extracted_number = substr($phone_number,3);
                $phone_num_234 = "234$extracted_number";
                $phone_num_no_234 = "0$extracted_number";
                
            }
            if( strlen($phone_number) == 14){
                $extracted_number = substr($phone_number,4);
                $phone_num_234 = "234$extracted_number";
                $phone_num_no_234 = "0$extracted_number";
            }

           $validated_phone_number = $shouldHave234 ?
           $phone_num_234
           :
           $phone_num_no_234
           ;

           return [
            'status' => 1,
            'message' => 'success',
            'validated_phone_number' => $validated_phone_number,
           ];


            
    }
}
