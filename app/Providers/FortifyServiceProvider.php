<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Setting;
use App\Models\SiteImage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
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
                return redirect()->intended('/'.$user_dashboard);
                // return redirect()->intended('/user/data/buy_dataaa');
                
            }
        });

        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                $users_redirect_after_authentication = Setting::where('field_name','users_redirect_after_authentication')->first();
                $user_auth_redirect_page = $users_redirect_after_authentication == NULL ? 'dashboard' : $users_redirect_after_authentication->field_value;
                return redirect()->intended('/'.$user_auth_redirect_page);
                // return redirect()->intended('/user/data/buy_dataaa');
                
            }
        });

        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                $users_redirect_after_authentication = Setting::where('field_name','users_redirect_after_authentication')->first();
                $user_auth_redirect_page = $users_redirect_after_authentication == NULL ? 'dashboard' : $users_redirect_after_authentication->field_value;
                return redirect()->intended('/'.$user_auth_redirect_page);
                // return redirect()->intended('/user/data/buy_dataaa');
                
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        

            Fortify::loginView(function () {
                return view('auth.login');
            });

            Fortify::registerView(function () {
                return view('auth.register');
            });

            Fortify::requestPasswordResetLinkView(function () {
                return view('auth.forgot-password');
            });

            Fortify::resetPasswordView(function () {
                return view('auth.reset-password');
            });

            $data = [];
            $site_images_data = SiteImage::get();
            
            if(count($site_images_data) > 0){
                foreach($site_images_data as $site_image){
                    $data[$site_image->image_category] = $site_image->image_name;
                }
            }
            Fortify::twoFactorChallengeView(function () use ($data) {
            return view('auth.two-factor-challenge')->with($data);
            });

            // Fortify::twoFactorChallengeView(function ()  {
            //     return view('auth.two-factor-challenge');
            // });


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
