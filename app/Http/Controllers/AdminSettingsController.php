<?php

namespace App\Http\Controllers;

use App\Models\Admin2faSetting;
use App\Models\Automation;
use Illuminate\Http\Request;
use App\Models\ReferralSetting;
use App\Models\AdminGeneralSetting;
use App\Models\LandingPagesSetting;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminSettingsController extends Controller
{
    public function index(){
    //landingpages
        $landing_page_settings = LandingPagesSetting::get();
        foreach($landing_page_settings as $landing_page_setting){
            $data[$landing_page_setting->field_name] = $landing_page_setting->field_details;
        }

        $admin_2fa_setting = Admin2faSetting::first();
        if(!$admin_2fa_setting){
          $admin_2fa_setting = Admin2faSetting::create();
        }


        $referral_setting = ReferralSetting::first();
        if(! $referral_setting){
            $referral_setting = ReferralSetting::create();
        } 

        $ogdams = Automation::where('slug','ogdams')->first();
        $smeplug = Automation::where('slug','smeplug')->first();
        $megasubplug = Automation::where('slug','megasubplug')->first();
        $data['referral_setting'] = $referral_setting;
        $data['admin_2fa_setting'] = $admin_2fa_setting;
        $data['ogdams'] = $ogdams;
        $data['smeplug'] = $smeplug;
        $data['megasubplug'] = $megasubplug;
        // dd($data);

       
        // dd($data);
        return view('admin.settings.index')->with($data);
    }

    public function manage_referral_settings(Request $request){
        //TODO: validation later
        $validator = Validator::make($request->all(), [
            'product_commission_feature' => 'required',
            'set_product_commission_flat_rate' => 'required',
            'set_product_commission_percentage_rate' => 'required',
            'first_downline_crediting_feature' => 'required',
            'set_first_downline_crediting_flat_rate' => 'required',
            'set_first_downline_crediting_percentage_rate' => 'required',
            'set_first_downline_crediting_cap' => 'required',
          ]);
          
    
          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }

          $data = $validator->validated();
          
          $check_table = ReferralSetting::whereNotNull('id')->first();

          $result = $check_table ? ReferralSetting::where('id',$check_table->id)->update($data) : ReferralSetting::create($data);

          Session::flash('success','Referral settings successfully updated');

          return redirect()->back();
    }

    public function manage_site_logo(Request $request){
      $validator = Validator::make($request->all(), [
        'site_logo' => 'required|image|mimes:png|max:2048',
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }
      
      $logo = 'logo.'.$request->site_logo->extension();
      $checkupload = $request->site_logo->move(public_path('assets/img/logos'), $logo);
      if($checkupload){
        $general_setting = AdminGeneralSetting::first();
        if($general_setting == NULL){
          //insert
          AdminGeneralSetting::create([
            'site_logo_path' => $logo
          ]);
        }else{
          //update
          $general_setting->update([
            'site_logo_path' => $logo
          ]);
        }
        Session::flash('success','Site logo successfully updated');
        return redirect()->back();
      }
    }

    public function manage_global_user_2fa(Request $request){
      $validator = Validator::make($request->all(), [
        'global_user_2fa_setting' => 'required|max:255',
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }
      
     
        $admin_2fa_setting = Admin2faSetting::first();
        if($admin_2fa_setting == NULL){
          //insert
          Admin2faSetting::create([
            'global_user_2fa_setting' => $request->global_user_2fa_setting
          ]);
        }else{
          //update
          $admin_2fa_setting->update([
            'global_user_2fa_setting' => $request->global_user_2fa_setting
          ]);
        }
        Session::flash('success','2fa successfully updated for all users');
        return redirect()->back();
      
    }

    public function manage_automations_keys(Request $request){
      // dd($request->all());
      $validator = Validator::make($request->all(), [
        'smeplug_api_secret_key' => 'required',
        'ogdams_api_secret_key' => 'required',
        'megasub_api_password' => 'required',
        'megasub_api_public_key' => 'required'
        
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }
      
      $automations = Automation::all();
      foreach($automations as $automation){
        if($automation->slug == 'ogdams' || $automation->slug == 'ogdams_v2') {
            Automation::where('slug','ogdams')->update([
              'api_secret_key' => $request->ogdams_api_secret_key
            ]);
        }

        if($automation->slug == 'smeplug') {
          Automation::where('slug','smeplug')->update([
            'api_secret_key' => $request->smeplug_api_secret_key
          ]);
        }

        if($automation->slug == 'megasubplug') {
          Automation::where('slug','megasubplug')->update([
            'api_public_key' => $request->megasub_api_public_key,
            'api_password' => $request->megasub_api_password
          ]);
        }
      }

      Session::flash('success','Automation keys were successfully updated');
      return redirect()->back();
   
    }
    

    public function manage_landing_page_settings(Request $request){
        $landing_pages_arr = config('landing_pages');
        foreach($landing_pages_arr as $key=>$value){
            $data[$key] = "required";
        }
        $validator = Validator::make($request->all(), $data);
         
        if ($validator->stopOnFirstFailure()->fails()) {
          return redirect()->back()->withErrors($validator)->withInput();
        }

        $dataa = $validator->validated();

        $count = 0;
        foreach($dataa as $key=>$new_value){
          $column_details = LandingPagesSetting::select('field_details')->where('field_name',$key)->first();
          $old_value = $column_details->field_details;
          if($old_value != $new_value){
            $new_update['field_details'] = $new_value; 
            LandingPagesSetting::where('field_name',$key)->where('field_details',$old_value)->update($new_update);
            $count++;
          }   
        }
          
        Session::flash('success','Landing page settings successfully updated');
        return redirect()->back();
    }
}
