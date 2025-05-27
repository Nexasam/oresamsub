<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\SiteImage;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\AdminColorSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        $site_images_data = SiteImage::get();
       $data = [];
        if(count($site_images_data) > 0){
            foreach($site_images_data as $site_image){
                $data[$site_image->image_category] = $site_image->image_name;
            }
        }

        $site_colors = AdminColorSetting::get();
        if(count($site_colors) > 0){
            foreach($site_colors as $site_color){
                $data[$site_color->color_name] = $site_color->color_value;
            }
        }
        
        $siteTemplate = SiteTemplate::first();
        if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
            return view('auth.reset-password', ['request' => $request])->with($data);
        }


        // return view('auth.confirm-password')->with($data);
        return view('template2.auth.reset-password', ['request' => $request])->with($data);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'new_pin' => ['required','string','regex:/^\d{4,5}$/'],
            'new_pin_confirmation' => ['required','string','regex:/^\d{4,5}$/'],
        ]);

        if($request->new_pin != $request->new_pin_confirmation){
            return back()->with('status', 'PIN mismatch found');
        }
       
   

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );



        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
       
        if($status == Password::PASSWORD_RESET){
            User::where('email',$request->email)->update([
                'pin' => $request->new_pin
            ]);
        }
        
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
