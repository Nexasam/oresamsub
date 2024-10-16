<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\SiteImage;
use App\Models\Automation;
use Illuminate\Http\Request;
use App\Models\FundingOption;
use App\Models\Admin2faSetting;
use App\Models\ReferralSetting;
use App\Models\AdminColorSetting;
use App\Models\AdminWebhookString;
use App\Models\AdminGeneralSetting;
use App\Models\LandingPagesSetting;
use App\Models\FundingOptionBankCodes;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminSettingsController extends Controller
{
    public function index(){
       
      
        $settings = Setting::get();
        if(count($settings) > 0){
          foreach($settings as $key=>$setting){
             $data[$setting->field_name] = $setting->field_value;
          }
        }

        // dd($data);
      
        //landingpages
        $landing_page_settings = LandingPagesSetting::get();
        // $landing_page_settings = config('landing_pages');
        // dd($landing_page_settings);
        foreach($landing_page_settings as $landing_page_setting){
            $data[$landing_page_setting->field_name] = $landing_page_setting->field_details;
        }


        $color_settings = AdminColorSetting::get();
        // $color_settings = config('landing_pages');
        // dd($color_settings);
        foreach($color_settings as $site_color){
          if($site_color->color_name == 'site_landing_analytics_color'){
              $data['site_landing_analytics_color_r'] = explode(', ',$site_color->color_value)[0];
              $data['site_landing_analytics_color_g'] = explode(', ',$site_color->color_value)[1];
              $data['site_landing_analytics_color_b'] = explode(', ',$site_color->color_value)[2];
          }else if($site_color->color_name == 'admin_site_color'){
              $data['admin_site_color_r'] = explode(', ',$site_color->color_value)[0];
              $data['admin_site_color_g'] = explode(', ',$site_color->color_value)[1];
              $data['admin_site_color_b'] = explode(', ',$site_color->color_value)[2];
          }else if($site_color->color_name == 'site_landing_review_color'){
              $data['site_landing_review_color_r'] = explode(', ',$site_color->color_value)[0];
              $data['site_landing_review_color_g'] = explode(', ',$site_color->color_value)[1];
              $data['site_landing_review_color_b'] = explode(', ',$site_color->color_value)[2];
          }     
          else{
              $data[$site_color->color_name] = $site_color->color_value;

          }
        }

        // dd($data);

        

        $admin_2fa_setting = Admin2faSetting::first();
        if(!$admin_2fa_setting){
          $admin_2fa_setting = Admin2faSetting::create();
        }


        $referral_setting = ReferralSetting::first();
        if(! $referral_setting){
            $referral_setting = ReferralSetting::create();
        } 

        $user_details = User::where('id',auth()->id())->first();

        if(! $user_details){
          //user is not loggedin
          redirect()->route('login');
        }

        $data['user'] = $user_details;
        $ogdams = Automation::where('slug','ogdams')->first();
        $smeplug = Automation::where('slug','smeplug')->first();
        $megasubplug = Automation::where('slug','megasubplug')->first();

        $funding_options = FundingOption::with('bank_codes','webhook_string')->get();
        // return $funding_options;
        
        $data['referral_setting'] = $referral_setting;
        $data['admin_2fa_setting'] = $admin_2fa_setting;
        $data['ogdams'] = $ogdams;
        $data['smeplug'] = $smeplug;
        $data['megasubplug'] = $megasubplug;
        $data['funding_options'] = $funding_options;
        // dd($data);

       
        // dd($data);
        return view('admin.settings.index')->with($data);
    }

    public function update_settings(Request $request){
      $validator = Validator::make($request->all(), [
        'max_automatic_crediting_allowed' => 'required|numeric',
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

    
      $data['max_automatic_crediting_allowed'] = $request->max_automatic_crediting_allowed;
      $max_automatic_crediting_allowed = Setting::where('field_name','max_automatic_crediting_allowed')->first();
        
        $max_automatic_crediting_allowed ? $max_automatic_crediting_allowed->update([
          'field_value' => $request->max_automatic_crediting_allowed
        ])
        : Setting::create([
          'field_name' => 'max_automatic_crediting_allowed',
          'field_value' => $request->max_automatic_crediting_allowed,
        ]);
      


     
      Session::flash('success','Settings successfully updated');

      return redirect()->back();
    }

    public function update_purchase_limit_settings(Request $request){
      $validator = Validator::make($request->all(), [
        'product_purchase_limit_daily' => 'required|numeric',
        'product_purchase_limit_last_7_days' => 'required|numeric',
        'product_purchase_limit_last_30_days' => 'required|numeric',
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      // TODO: make this better later
      if ( env('APP_NAME') == 'FoxDataHub' && auth()->id() != '9cd12bae-541a-4459-af9d-94fc43008435'  ) {
        Session::flash('failure','Sorryvalue:  you do not have access to make this change');
        return redirect()->back();
      }
     
      //daily
      $product_purchase_limit_daily = Setting::where('field_name','product_purchase_limit_daily')->first();
      $product_purchase_limit_daily ? $product_purchase_limit_daily->update([
        'field_value' => $request->product_purchase_limit_daily
      ])
      : Setting::create([
        'field_name' => 'product_purchase_limit_daily',
        'field_value' => $request->product_purchase_limit_daily,
      ]);

       //last 7 days
       $product_purchase_limit_last_7_days = Setting::where('field_name','product_purchase_limit_last_7_days')->first();
       $product_purchase_limit_last_7_days ? $product_purchase_limit_last_7_days->update([
         'field_value' => $request->product_purchase_limit_last_7_days
       ])
       : Setting::create([
         'field_name' => 'product_purchase_limit_last_7_days',
         'field_value' => $request->product_purchase_limit_last_7_days,
       ]);

       //last 30 days
       $product_purchase_limit_last_30_days = Setting::where('field_name','product_purchase_limit_last_30_days')->first();
       $product_purchase_limit_last_30_days ? $product_purchase_limit_last_30_days->update([
         'field_value' => $request->product_purchase_limit_last_30_days
       ])
       : Setting::create([
         'field_name' => 'product_purchase_limit_last_30_days',
         'field_value' => $request->product_purchase_limit_last_30_days,
       ]);
      
  
      Session::flash('success','Purchase Limit Settings successfully updated');

      return redirect()->back();
    }

    public function manage_referral_settings(Request $request){
        //TODO: validation later
        $validator = Validator::make($request->all(), [
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

    public function update_webhook_suffix_string(Request $request){
      $validator = Validator::make($request->all(), [
        'funding_option_id' => 'required',
        'webhook_suffix_string' => 'required',
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

        $admin_webhook_string = AdminWebhookString::where('funding_option_id',$request->funding_option_id)->first();
        if($admin_webhook_string == NULL){
          //insert
          AdminWebhookString::create([
            'funding_option_id' => $request->funding_option_id,
            'webhook_suffix_string' => $request->webhook_suffix_string
          ]);
        }else{
          //update
          $admin_webhook_string->update([
            'webhook_suffix_string' => $request->webhook_suffix_string
          ]);
        }
        Session::flash('success','Webhook suffix string successfully updated');
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

    

    public function manage_site_images(Request $request){
      // return $request->all();
      $validator = Validator::make($request->all(), [
        'hero_image1' => 'nullable|image|mimes:png,jpg,jpeg|max:8048',
        'hero_image2' => 'nullable|image|mimes:png,jpg,jpeg|max:8048',
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

        if($request->hasFile('hero_image1')){
            //first cleanup directory
            $current_image = SiteImage::where('image_category','hero_image1')->first();
            if($current_image){
              @unlink(public_path('assets/landing_page_assets/img/hero_image1/'.$current_image->image_name));              
            }

          $hero_image1 = 'hero_image1_'.time().'.'.$request->hero_image1->extension();
          $checkupload = $request->hero_image1->move(public_path('assets/landing_page_assets/img/hero_image1'), $hero_image1);
          if($checkupload){
            SiteImage::updateOrCreate([
              'image_category' => 'hero_image1'
              ],[
              'image_name' => $hero_image1
            ]);
          }else{
            Session::flash('failure','Site images upload could not be completed... Check hero image 1');
            return redirect()->back();
          }
        
        }

        if($request->hasFile('hero_image2')){
          //first cleanup directory
          $current_image = SiteImage::where('image_category','hero_image2')->first();
          if($current_image){
            @unlink(public_path('assets/landing_page_assets/img/hero_image2/'.$current_image->image_name));              
          }


          $hero_image2 = 'hero_image2_'.time().'.'.$request->hero_image2->extension();
          $checkupload = $request->hero_image2->move(public_path('assets/landing_page_assets/img/hero_image2'), $hero_image2);
          if($checkupload){
            SiteImage::updateOrCreate([
              'image_category' => 'hero_image2'
              ],[
              'image_name' => $hero_image2
            ]);
          }else{
            Session::flash('failure','Site images upload could not be completed... Check hero image 2');
            return redirect()->back();
          }
        }
   
        Session::flash('success','Site images successfully updated');
        return redirect()->back();
      
    }

    public function manage_site_colors(Request $request){
      // return $request->all();
      $validator = Validator::make($request->all(), [
        'site_primary_color' => 'required|max:255',
        'site_landing_page_hover_color' => 'required|max:255',
        'site_admin_sidebar_color' => 'required|max:255',
        'site_landing_analytics_color_r' => 'required|max:255',
        'site_landing_analytics_color_g' => 'required|max:255',
        'site_landing_analytics_color_b' => 'required|max:255',
        // 'site_landing_review_color_r' => 'required|max:255',
        // 'site_landing_review_color_g' => 'required|max:255',
        // 'site_landing_review_color_b' => 'required|max:255',
        'admin_site_color_r' => 'required|max:255',
        'admin_site_color_g' => 'required|max:255',
        'admin_site_color_b' => 'required|max:255',
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }
      
     
        AdminColorSetting::updateOrCreate([
          'color_name' => 'site_primary_color'
         ],[
          'color_value' => $request->site_primary_color
         ]);

         AdminColorSetting::updateOrCreate([
          'color_name' => 'site_landing_page_hover_color'
         ],[
          'color_value' => $request->site_landing_page_hover_color
         ]);

         AdminColorSetting::updateOrCreate([
          'color_name' => 'site_admin_sidebar_color'
         ],[
          'color_value' => $request->site_admin_sidebar_color

         ]);

         AdminColorSetting::updateOrCreate([
          'color_name' => 'site_landing_analytics_color'
         ],[
          'color_value' => $request->site_landing_analytics_color_r.', '.$request->site_landing_analytics_color_g.', '.$request->site_landing_analytics_color_b
         ]);

         AdminColorSetting::updateOrCreate([
          'color_name' => 'admin_site_color'
         ],[
          'color_value' => $request->admin_site_color_r.', '.$request->admin_site_color_g.', '.$request->admin_site_color_b
         ]);

        //  AdminColorSetting::updateOrCreate([
        //   'color_name' => 'site_landing_review_color'
        //  ],[
        //   'color_value' => $request->site_landing_review_color_r.', '.$request->site_landing_review_color_g.', '.$request->site_landing_review_color_b
        //  ]);

         

   
        Session::flash('success','Site colors successfully updated');
        return redirect()->back();
      
    }

    

    public function add_funding_option_bank_code(Request $request){
        $validator = Validator::make($request->all(), [
          'funding_option_id' => 'required',
          'bank_code' => 'required',    
          'bank_name' => 'required',    
          'bank_charges' => 'required',    
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
          return redirect()->back()->withErrors($validator)->withInput();
        }
  
        $check = FundingOptionBankCodes::where('funding_option_id',$request->funding_option_id)->where('bank_code',$request->bank_code)->first();
        if($check){
          Session::flash('failure','Sorry, this bank code seem already added for this funding option');
          return redirect()->back();
        }

        if($request->bank_charges > 50){
          Session::flash('failure','Sorry, bank charges cannot be greater than 50%');
          return redirect()->back();
        }
        
        $create = FundingOptionBankCodes::create([
          'funding_option_id' => $request->funding_option_id,
          'bank_code' => $request->bank_code,
          'bank_name' => $request->bank_name,
          'bank_charges' => $request->bank_charges,
        ]);

        if(! $create){
          Session::flash('failure','Error occured while adding bank code for this funding option');
          return redirect()->back();
        }

        Session::flash('success','Funding option  was successfully updated');
        return redirect()->back();
   
    }
    public function update_funding_options(Request $request){
      $validator = Validator::make($request->all(), [
        'id' => 'required',
        'api_public_key' => 'required',
        'api_secret_key' => 'required',      
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      
      FundingOption::where('id',$request->id)->update([
        'api_public_key' => $request->api_public_key,
        'api_secret_key' => $request->api_secret_key,
      ]);

      Session::flash('success','Funding option  was successfully updated');
      return redirect()->back();
   
    }

    public function manage_automations_keys(Request $request){
      // dd($request->all());
      $validator = Validator::make($request->all(), [
        // 'smeplug_api_secret_key' => 'required',
        // 'ogdams_api_secret_key' => 'required',
        'megasub_api_password' => 'required',
        'megasub_api_public_key' => 'required'
        
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }
      
      $automations = Automation::all();
      foreach($automations as $automation){
        // if($automation->slug == 'ogdams' || $automation->slug == 'ogdams_v2') {
        //     Automation::where('slug','ogdams')->update([
        //       'api_secret_key' => $request->ogdams_api_secret_key
        //     ]);
        // }

        // if($automation->slug == 'smeplug') {
        //   Automation::where('slug','smeplug')->update([
        //     'api_secret_key' => $request->smeplug_api_secret_key
        //   ]);
        // }

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
