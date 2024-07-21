<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ReferralSetting;
use Illuminate\Validation\Rule;
use App\Models\LandingPagesSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserSettingsController extends Controller
{
    public function index(){
        //landingpages
        $user_details = User::where('id',auth()->id())->first();

        if(! $user_details){
          //user is not loggedin
          redirect()->route('login');
        }
        $data['user'] = $user_details;
        // dd($data);
        return view('user.settings.index')->with($data);
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
        'current_password' => 'required',
        'pin5' => ['required','digits:4']
      ]);

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $user_details = User::where('id',auth()->id())->first();

      if(! $user_details){
        //user is not loggedin
        redirect()->route('login');
      }

      // $hashed_current_password = Hash::make($request->current_password);
      $db_user_password = $user_details->password;
      if(! Hash::check($request->current_password,$db_user_password)){
        // dd('wrong current pass');
        Session::flash('failure','Your current password is not correct');
        return redirect()->back();
      }

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
        'pin' => ['required','digits:4']
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
        'current_pin' => 'required|digits:4',
        'new_pin' => 'required|digits:4',
        'confirm_new_pin' => 'required|digits:4',
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
