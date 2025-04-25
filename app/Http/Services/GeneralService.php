<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\SiteTemplate;
use App\Models\LandingPagesSetting;

class GeneralService{

    public function support_information(){
        
        $get_site_template = SiteTemplate::first();
        $site_template = $get_site_template->template_name ?? 'template_1';
        $template1_cols = ['topnav_phone','topnav_email','support_whatsapp_number'];
        $template2_cols = ['email_address_template2','phone_template2','support_whatsapp_number_template2'];
        
        if($site_template == 'template_1'){
           
            $setting = LandingPagesSetting::select('field_name','field_details','template_type')
            ->whereIn('field_name',$template1_cols)
            ->where('template_type',$site_template)->get();
        
        }else{

            $setting = LandingPagesSetting::select('field_name','field_details','template_type')
            ->whereIn('field_name',$template2_cols)
            ->where('template_type',$site_template)
            ->get();
        }
   
        // foreach($template2_cols as $template_col){
        //     echo $template_col.PHP_EOL;
        // }
        // dd($template2_cols);


        return [
            'status' => 1,
            'message' => 'Support information successfully fetched.',
            'data' => $setting->toArray()
        ];
    }

    // public function update_user_profile($data){
    //     $user_id = $data['user_id'];
    //     $fingerprint_status = $data['fingerprint_status'];

    //     User::where('id',$user_id)->update([
    //         'fingerprint_option' => $fingerprint_status
    //     ]);

    //     return [
    //         'status' => 1,
    //         'message' => 'Fingerprint status succesfully changed.',
    //         'data' => $data
    //     ];
    // }

    

}