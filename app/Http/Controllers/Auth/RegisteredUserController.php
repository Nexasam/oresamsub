<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\UserPlan;
use App\Models\SiteImage;
use Illuminate\View\View;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\AdminColorSetting;
use App\Models\LandingPagesSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use App\Mail\UserRegistrationNotification;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $upline = $request->ref ?? '';
        // dd($upline);


        $data = [];
        $data['upline'] = $upline;
        // dd($data);
        $landing_data = LandingPagesSetting::where('field_name','support_whatsapp_number')->first();
        $data[$landing_data->field_name] = $landing_data->field_details;
        

        $site_images_data = SiteImage::get();
        if(count($site_images_data) > 0){
            foreach($site_images_data as $site_image){
                $data[$site_image->image_category] = $site_image->image_name;
            }
        }

        $siteTemplate = SiteTemplate::first();
        if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
            return view('auth.register')->with($data);
        }

        $site_colors = AdminColorSetting::get();
        if(count($site_colors) > 0){
            foreach($site_colors as $site_color){
                $data[$site_color->color_name] = $site_color->color_value;
            }
        }
        // dd($data);
        return view('template2.auth.register')->with($data);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // dd($request->all());
        
        $request->validate([
            'username' => ['required', 'string', 'unique:users,username'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'pin' => ['required', 'numeric', 'string','regex:/^\d{4,5}$/'],
            // 'other_names' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['required','unique:users,phone_number', 'string', 'max:255'],
            // 'upline_referral_phone_number' => ['nullable', 'string','exists:users,phone_number' ,'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()::defaults()],
        ]);

        // 	echo REGEX_CountMatches('as.sfad.asdferw.asdfsdf.@gmail.com','.');
        // 	echo $count = preg_match_all('/\b.\b/','as.sfad.asdferw.asdfsdf.l.@gmail.com');
        // 	echo $count = preg_match_all('/\b.\b/','ade.a@gmail.com');
        // 	echo substr_count('as.sfad.asdferw.asdfsdf.l.@gmail.com','.');
        // 	echo substr_count('sam.ade@gmail.com','.');
        $validate_email =  count(explode('.',$request->email));
        if($validate_email > 2){
            Session::flash('failure','This email is not allowed.. You can reach out to our support via whatsapp');
            return redirect()->back();
        }

        //second security check
        $new_email_array = explode('.',$request->email);
        $last_item = array_pop($new_email_array);
        // echo $last_item;
        $checked_email = implode('',$new_email_array).'.'.$last_item;

        if($request->email != $checked_email){
            Session::flash('failure','This email is not allowed.. You can reach out to our support via whatsapp...');
            return redirect()->back();
        }

        $upline_details = User::where('phone_number',$request->upline_referral_phone_number)->first();
        $upline_id = $upline_details != NULL && $request->upline_referral_phone_number != $request->phone_number ? $upline_details->id : NULL;
        // $upline_id = $upline_details->id;
       

        $role_details = Role::where('role_name','User')->first();
        $default_reseller_plan = UserPlan::where('is_default',1)->first();
        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        // $data['other_names'] = $request->other_names;
        $data['pin'] = $request->pin;
        $data['phone_number'] = $request->phone_number;
        $data['username'] = $request->username;
        $data['upline_id'] = $upline_id;
        $data['email'] = $request->email;
        $data['role_id'] = $role_details->id;
        $data['user_plan_id'] = $default_reseller_plan->id;
        $data['password'] = Hash::make($request->password);
        // $data['confirm_password'] = Hash::make($request->confirm_password);
        if(env('APP_NAME') == 'OresamSub'){
            $data['email_verified_at'] = date('Y-m-d H:i:s');
        }

        $user = User::create($data);

        // $user_record = User::where($user->id)->first()->toa;

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

     /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store2(Request $request): RedirectResponse
    {
   
       
        
        $request->validate([
            'username' => ['required', 'string', 'unique:users,username'],
            'fullname' => ['required', 'string', 'max:255'],
            'phone_number' => ['required','unique:users,phone_number', 'string', 'max:255'],
            // 'upline_referral_phone_number' => ['nullable', 'string','exists:users,phone_number' ,'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()::defaults()],
        ]);

        // 	echo REGEX_CountMatches('as.sfad.asdferw.asdfsdf.@gmail.com','.');
        // 	echo $count = preg_match_all('/\b.\b/','as.sfad.asdferw.asdfsdf.l.@gmail.com');
        // 	echo $count = preg_match_all('/\b.\b/','ade.a@gmail.com');
        // 	echo substr_count('as.sfad.asdferw.asdfsdf.l.@gmail.com','.');
        // 	echo substr_count('sam.ade@gmail.com','.');

        $validate_email =  count(explode('.',$request->email));
        if($validate_email > 2){
            Session::flash('failure','This email is not allowed.. You can reach out to our support via whatsapp');
            return redirect()->back();
        }

        $name_array = explode(' ',$request->fullname);
        if(count($name_array) == 1){
            $first_name = $name_array[0];
            $last_name = $name_array[0];
        }else if(count($name_array) > 1){
            $first_name = $name_array[0];
            $last_name = $name_array[1];
        }

        //second security check
        $new_email_array = explode('.',$request->email);
        $last_item = array_pop($new_email_array);
        // echo $last_item;
        $checked_email = implode('',$new_email_array).'.'.$last_item;

        if($request->email != $checked_email){
            Session::flash('failure','This email is not allowed.. You can reach out to our support via whatsapp...');
            return redirect()->back();
        }

        $upline_details = User::where('phone_number',$request->upline_referral_phone_number)->first();
        $upline_id = $upline_details != NULL && $request->upline_referral_phone_number != $request->phone_number ? $upline_details->id : NULL;
        // $upline_id = $upline_details->id;
       

        $role_details = Role::where('role_name','User')->first();
        $default_reseller_plan = UserPlan::where('is_default',1)->first();
        $data['first_name'] = $first_name;
        $data['last_name'] = $last_name;
        $data['phone_number'] = $request->phone_number;
        $data['username'] = $request->username;
        $data['upline_id'] = $upline_id;
        $data['email'] = $request->email;
        $data['pin'] = NULL;
        $data['role_id'] = $role_details->id;
        $data['user_plan_id'] = $default_reseller_plan->id;
        $data['password'] = Hash::make($request->password);

        // dd($data);
        // $data['confirm_password'] = Hash::make($request->confirm_password);
        if(env('APP_NAME') == 'OresamSub'){
            $data['email_verified_at'] = date('Y-m-d H:i:s');
        }

        $user = User::create($data);

        // $user_record = User::where($user->id)->first()->toa;

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
