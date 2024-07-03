<?php

namespace App\Http\Controllers;

use App\Models\AdminGeneralSetting;
use App\Models\LandingPagesSetting;
use Illuminate\Http\Request;
use App\Models\ReferralSetting;
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

        $referral_setting = ReferralSetting::first();
        if(! $referral_setting){
            $referral_setting = ReferralSetting::create();
        } 
        $data['referral_setting'] = $referral_setting;

       
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
