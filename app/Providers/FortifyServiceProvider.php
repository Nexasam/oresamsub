<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Setting;
use App\Models\SiteImage;
use Illuminate\Support\Str;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use App\Models\AdminColorSetting;
use Illuminate\Support\Facades\Hash;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                $users_redirect_after_authentication = Setting::where('field_name','users_redirect_after_authentication')->first();
                $user_dashboard = $users_redirect_after_authentication == NULL ? 'dashboard' : $users_redirect_after_authentication->field_value;
                // return redirect()->intended('/'.$user_dashboard);
                return redirect()->intended('/dashboard');
                
            }
        });

        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                $users_redirect_after_authentication = Setting::where('field_name','users_redirect_after_authentication')->first();
                $user_auth_redirect_page = $users_redirect_after_authentication == NULL ? 'dashboard' : $users_redirect_after_authentication->field_value;
                // return redirect()->intended('/'.$user_auth_redirect_page);
                return redirect()->intended('/dashboard');

            }
        });

        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                $users_redirect_after_authentication = Setting::where('field_name','users_redirect_after_authentication')->first();
                $user_auth_redirect_page = $users_redirect_after_authentication == NULL ? 'dashboard' : $users_redirect_after_authentication->field_value;
                // return redirect()->intended('/'.$user_auth_redirect_page);
                // return redirect()->intended('/user/data/buy_dataaa');
                return redirect()->intended('/dashboard');

                
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

            if(env('APP_NAME') != 'Irecharg'){
                $siteTemplate = SiteTemplate::first();
                if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
                    $templateloginview = 'auth.login';
                    $templateregisterview = 'auth.register';
                    $templateforgotview = 'auth.forgot-password';
                    $templateresetview = 'auth.reset-password';
    
                }else{
                    $templateloginview = 'template2.auth.login';
                    $templateregisterview = 'template2.auth.register';
                    $templateforgotview = 'template2.auth.forgot_password';
                    $templateresetview = 'template2.auth.reset_password';
                }
    
                Fortify::loginView(function () use ($templateloginview) {
                    return view($templateloginview);
                });
    
                Fortify::registerView(function () use ($templateregisterview) {
                    return view($templateregisterview);
                });
    
                Fortify::requestPasswordResetLinkView(function () use ($templateforgotview) {
                    return view($templateforgotview);
                });
    
                Fortify::resetPasswordView(function () use ($templateresetview) {
                    return view($templateresetview);
                });
    
                $data = [];
                $site_images_data = SiteImage::get();
                
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
                
                Fortify::twoFactorChallengeView(function () use ($data) {
                return view('auth.two-factor-challenge')->with($data);
                });
    
                // Fortify::twoFactorChallengeView(function ()  {
                //     return view('auth.two-factor-challenge');
                // });
    
            }
        
         

            Fortify::authenticateUsing(function(Request $request){
                $user = User::where('email',$request->email)->first();
                if($user && Hash::check($request->password,$user->password)){
                    return $user;
                }
            });


        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
