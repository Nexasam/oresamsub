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

    public function phoneNumberNetworkValidation($phone_number,$shouldHave234 = false){
           

        $strippedString = str_replace(' ', '', $phone_number); //remove space
        $phone_number  = str_replace('+','',$strippedString); //remove plus to generate new number

       if(strlen($phone_number) != 11 && strlen($phone_number) != 13 && strlen($phone_number != 14) ){
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

       $validated_phone_number = $shouldHave234 ? $phone_num_234 : $phone_num_no_234;

       $number_prefix = substr($validated_phone_number, 0, 4);

    
        $mtnPrefixes = ["0803","0806","0703","0706","0707","0810","0813","0814","0816","0903","0906","0913","0916"];
        $airtelPrefixes = ["0802","0808","0701","0708","0812","0902","0907","0901","0912"];
        $gloPrefixes = ["0805","0807","0705","0811","0815","0905","0915"];
        $etisalatPrefixes = ["0809","0817", "0818","0909","0908"]; //9mobile

        if( in_array($number_prefix,$mtnPrefixes)){
            $selected_network = 'MTN';
        }elseif(in_array($number_prefix,$airtelPrefixes))  {
            $selected_network = 'AIRTEL';
        }elseif(in_array($number_prefix,$gloPrefixes))  {
            $selected_network = 'GLO';
        }elseif(in_array($number_prefix,$etisalatPrefixes))  {
            $selected_network = '9MOBILE';
        }else{
            $selected_network = 'NIL'; //could not be determined
        }


        


       return [
        'status' => 1,
        'message' => 'success',
        'validated_phone_number' => $validated_phone_number,
        'selected_network' => $selected_network,
       ];


        
}
}
