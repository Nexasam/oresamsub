<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\LandingPagesSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // dd('sss');
        $landing_data = LandingPagesSetting::get();
        foreach($landing_data as $landing_component){
            $data[$landing_component->field_name] = $landing_component->field_details;
        }
        return view('auth.register')->with($data);
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
            'pin' => ['required', 'numeric', 'digits:4'],
            'other_names' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255'],
            'upline_referral_phone_number' => ['nullable', 'string','exists:users,phone_number' ,'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::min(8)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised()::defaults()],
        ]);

        $upline_details = User::where('phone_number',$request->upline_referral_phone_number)->first();
        $upline_id = $upline_details != NULL ? $upline_details->id : NULL;
        // $upline_id = $upline_details->id;
       

        $role_details = Role::where('role_name','User')->first();
        $default_reseller_plan = UserPlan::where('is_default',1)->first();
        $data['first_name'] = $request->first_name;
        $data['last_name'] = $request->last_name;
        $data['other_names'] = $request->other_names;
        $data['pin'] = $request->pin;
        $data['phone_number'] = $request->phone_number;
        $data['username'] = $request->username;
        $data['upline_id'] = $upline_id;
        $data['email'] = $request->email;
        $data['role_id'] = $role_details->id;
        $data['user_plan_id'] = $default_reseller_plan->id;
        $data['password'] = Hash::make($request->password);
        // $data['confirm_password'] = Hash::make($request->confirm_password);

        $user = User::create($data);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
