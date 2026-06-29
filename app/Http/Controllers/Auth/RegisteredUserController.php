<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\UserPlan;
use App\Models\SiteImage;
use Illuminate\View\View;
use App\Events\Registered;
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
use Illuminate\Support\Facades\Session;
use App\Http\Services\CrystalPayService;
use Illuminate\Validation\Rules\Password;
use App\Mail\UserRegistrationNotification;
use App\Http\Services\VirtualAccountService;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $upline = $request->ref ?? '';
        $data = [];
        $data['upline'] = $upline;
        // dd($upline);

        if( env('APP_NAME') == 'OresamSub') {
            return view('oresamsub.auth.register')->with($data);
        }


       
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
            'password' => ['required', 'confirmed', Password::min(6)]
            // 'password' => ['required', 'confirmed', Password::min(8)
            // ->letters()
            // ->mixedCase()
            // ->numbers()
            // ->symbols()
            // ->uncompromised()::defaults()],
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

        $upline_details = NULL;
        if(isset($request->upline_referral_phone_number) &&  $request->upline_referral_phone_number != NULL){
            $upline_details = User::where('phone_number',$request->upline_referral_phone_number)->first();
        }
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
        
        // if(env('APP_NAME') == 'OresamSub'){
        // $data['email_verified_at'] = date('Y-m-d H:i:s');
        // }

        $user = User::create($data);

        // $dataaa['user'] = $user;
        // (new VirtualAccountService())->generate_accounts($dataaa);

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

        logger('reg payloa:'.json_encode($request->all()));
        // 1. Validate input
        $validated = $request->validate([
            'username' => ['required', 'string', 'unique:users,username'],
            'fullname' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:users,phone_number'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'usage' => ['required', 'string', 'in:Personal,Business'],
            'transaction_volume' => ['nullable', 'string', 'in:100_199,200_999,1000_4999,5000+'],
        ]);

        // Ensure transaction_volume is provided if usage is Business
        if ($validated['usage'] === 'Business' && empty($request->transaction_volume)) {
            return back()->withInput()->with('failure', 'Please select your weekly transaction volume.');
        }

        // 2. Fast email security check
        $dotCount = substr_count($validated['email'], '.');
        if ($dotCount > 2 || preg_match('/\.+@/', $validated['email'])) {
            return back()->with('failure', 'This email is not allowed. Contact support via WhatsApp.');
        }

        // 3. Split name
        $nameParts = explode(' ', $validated['fullname'], 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? $firstName;

        // 4. Upline check
        $uplineId = null;
        if ($request->filled('upline_referral_phone_number') && $request->upline_referral_phone_number !== $validated['phone_number']) {
            $upline = User::where('phone_number', $request->upline_referral_phone_number)->first();
            if ($upline) {
                $uplineId = $upline->id;
            }
        }

        // 5. Role and Plan
        $roleId = Role::where('role_name', 'User')->value('id');

        if($request->usage == 'Personal'){
            $defaultPlanId = UserPlan::where('plan_level', 4)->value('id');
        }else{
            //for business: if after a whhile no show, we downgrade the customer
            $defaultPlanId = UserPlan::where('plan_level', 4)->value('id');
        }



        // 6. Create user
        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $validated['username'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'upline_id' => $uplineId,
            'pin' => null,
            'role_id' => $roleId,
            'user_plan_id' => $defaultPlanId,
            'password' => Hash::make($validated['password']),
            'usage' => $validated['usage'],
            'transaction_volume' => $request->transaction_volume ?? null,
            'email_verified_at' => now(),
        ]);

        // 7. Dispatch event
        // event(new Registered($user));

        // $dataaa['user'] = $user;
        // $va = (new VirtualAccountService())->generate_accounts($dataaa);
        // if($va['status'] == 1){
        //     logger('account generated for '.$request->fullname);
        // }

        // 8. Login and redirect
        Auth::login($user);
        return redirect()->route('dashboard');
    }



    public function store2latestbackup(Request $request): RedirectResponse
    {
        // 1. Validate input
        $validated = $request->validate([
            'username' => ['required', 'string', 'unique:users,username'],
            'fullname' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:users,phone_number'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        // 2. Fast email security check
        $dotCount = substr_count($validated['email'], '.');
        if ($dotCount > 2 || preg_match('/\.+@/', $validated['email'])) {
            return back()->with('failure', 'This email is not allowed. Contact support via WhatsApp.');
        }

        // 3. Split name (fallback to fullname if only one)
        $nameParts = explode(' ', $validated['fullname'], 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? $firstName;

        // 4. Upline check
        $uplineId = null;
        if ($request->filled('upline_referral_phone_number') && $request->upline_referral_phone_number !== $validated['phone_number']) {
            $upline = User::where('phone_number', $request->upline_referral_phone_number)->first();
            if ($upline) {
                $uplineId = $upline->id;
            }
        }

        // 5. Role and Plan
        $roleId = Role::where('role_name', 'User')->value('id');
        $defaultPlanId = UserPlan::where('is_default', 1)->value('id');

        // 6. Create user
        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $validated['username'],
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
            'upline_id' => $uplineId,
            'pin' => null,
            'role_id' => $roleId,
            'user_plan_id' => $defaultPlanId,
            'password' => Hash::make($validated['password']),
            // 'email_verified_at' => env('APP_NAME') === 'OresamSub' ? now() : null,
        ]);

        // 7. Dispatch event to handle account setup
        event(new Registered($user));

        // 8. Login and redirect
        Auth::login($user);
        return redirect()->route('dashboard');
    }


}
