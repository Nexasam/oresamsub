<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SiteTemplate;
use App\Traits\Dashboard\UserDashboardDataTrait;
use Illuminate\Http\Request;
use App\Models\ReferralSetting;
use Illuminate\Validation\Rule;
use App\Models\LandingPagesSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserSettingsController extends Controller
{
    use UserDashboardDataTrait;

    public function set_pin(Request $request){
      return view('user.settings.create_pin');
    }

    public function store_set_pin(Request $request){
      // dd($request->all());

        $validator = Validator::make($request->all(), [
          'pin' => ['required','string','regex:/^\d{4,5}$/'],
          'confirm_pin' => ['required','string','regex:/^\d{4,5}$/'],
        ]);
        

        if ($validator->stopOnFirstFailure()->fails()) {
          return redirect()->back()->withErrors($validator)->withInput();
        }
        
        if($request->pin != $request->confirm_pin){
          Session::flash('failure','PIN mismatch found.');
          return redirect()->back();
        }


        if($request->pin == '1234'){
          Session::flash('failure','Please use another PIN. It is not a strong PIN.');
          return redirect()->back();
        }

        User::where('id',auth()->id())->update([
          'pin' => $request->pin
        ]);


        Session::flash('success','PIN was successfully set');
        return redirect()->route('dashboard');

    }
    
    public function index(){
        
        $dataa = $this->get_user_dashboard_data();
        $data = [...$dataa];
        //landingpages
        $user_details = User::where('id',auth()->id())->first();

        if(! $user_details){
          //user is not loggedin
          redirect()->route('login');
        }
        $data['user'] = $user_details;
        $data['hideNav'] = true;

        $template = SiteTemplate::first();
        if(!$template || $template->template_name == 'template_1'){
            return view('user.settings.index')->with($data);
        }else{
            //this is template 2 
            $templaten = 'template'.explode('_',$template->template_name)[1];
            return view($templaten.'.user.settings.index')->with($data);
        }

    }


    //update_2fa
    //update_default_wallet
    //update_profile
  
    public function update_default_wallet(Request $request){

      $validator = Validator::make($request->all(), [
        'default_wallet_setting'=>['required',Rule::in(['main_wallet','bulk_data_wallet'])],
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }
     
      $dataa = $validator->validated();

      $user_details = User::where('id',auth()->id())->first();

      if(! $user_details){
        //user is not loggedin
        redirect()->route('login');
      }

      $user_details->refresh();
      $user_details->update($dataa);
          
      Session::flash('success','Default wallet was successfully updated');
      return redirect()->back();
    }


    public function update_password(Request $request){

      $validator = Validator::make($request->all(), [
        'new_password' => 'required',
        'confirm_new_password' => 'required',
        // 'current_password' => 'nullable',
        'pin5' => ['required','string','regex:/^\d{4,5}$/']
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
        // Session::flash('failure',$validator);
        // return redirect()->back();
      }

      $user_details = User::where('id',auth()->id())->first();

      if(! $user_details){
        //user is not loggedin
        redirect()->route('login');
      }

     
      //relax the strictness for now:
      // $db_user_password = $user_details->password;
      // if(! Hash::check($request->current_password,$db_user_password)){
      //   // dd('wrong current pass');
      //   Session::flash('failure','Your current password is not correct');
      //   return redirect()->back();
      // }

      if($user_details->pin != $request->pin5){
        Session::flash('failure','Wrong PIN entered');
        return redirect()->back();
      }


      if($request->new_password != $request->confirm_new_password){
        Session::flash('failure','Password confirmation is wrong');
        return redirect()->back();
      }
     
      $dataa['password'] = Hash::make($request->new_password);
     
      $user_details->update($dataa);
          
      Session::flash('success','Password was successfully updated');
      return redirect()->back();
    }

    public function update_2fa(Request $request){

      $validator = Validator::make($request->all(), [
        'user_2fa_setting'=>['required',Rule::in(['ON','OFF'])],
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $user_details = User::where('id',auth()->id())->first();

      if(! $user_details){
        //user is not loggedin
        redirect()->route('login');
      }
     
      $dataa = $validator->validated();

      $user_details->update($dataa);
          
      Session::flash('success','2fa setting successfully updated');
      return redirect()->back();
    }

    public function update_profile(Request $request){
    
      $validator = Validator::make($request->all(), [
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        'other_names' => 'nullable|max:255',
        'pin' => ['required','string','regex:/^\d{4,5}$/'],
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      

      $user_details = User::where('id',auth()->id())->first();

      if(! $user_details){
        //user is not loggedin
        redirect()->route('login');
      }

      if($user_details->pin != $request->pin){
        Session::flash('failure','Wrong PIN entered');
        return redirect()->back();
      }
     
      $dataa = $validator->validated();

      $user_details->update($dataa);

      Session::flash('success','Profile was successfully updated');
      return redirect()->back();

    }

    public function update_pin(Request $request){
    
      $validator = Validator::make($request->all(), [
        'current_pin' => ['required','string','regex:/^\d{4,5}$/'],
        'new_pin' => ['required','string','regex:/^\d{4,5}$/'],
        'confirm_new_pin' => ['required','string','regex:/^\d{4,5}$/']
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $user_details = User::where('id',auth()->id())->first();

      if(! $user_details){
        //user is not loggedin
        redirect()->route('login');
      }

      if($user_details->pin != $request->current_pin){
        Session::flash('failure','Wrong PIN entered');
        return redirect()->back();
      }

      if($request->new_pin != $request->confirm_new_pin){
        Session::flash('failure','Please ensure New PIN and Confirm New PIN are the same');
        return redirect()->back();
      }
     
      $dataa['pin'] = $request->new_pin;
      

      $user_details->update($dataa);

      Session::flash('success','A new PIN has been successfully set');
      return redirect()->back();

    }
}
