<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\SiteImage;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Features;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use App\Models\LandingPagesSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $data = [];
        $landing_data = LandingPagesSetting::where('field_name','support_whatsapp_number')->first();
        $data[$landing_data->field_name] = $landing_data->field_details;

        $site_images_data = SiteImage::get();
        if(count($site_images_data) > 0){
            foreach($site_images_data as $site_image){
                $data[$site_image->image_category] = $site_image->image_name;
            }
        }
        return view('auth.login')->with($data);
    }

    /**
     * Attempt to authenticate a new session.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return mixed
     */
    public function store(LoginRequest $request)
    {
        // dd($request->all());
        $user_check = User::select('id','api_token')->where('email',$request->email)->first();
        if($user_check){
            
            if( $user_check->api_token == NULL){
                // $api_token = bcrypt('password').'_'.rand(1111,9999).'_'.time();
                $api_token = str()->random(200).time();
                $user_check->update([
                    'api_token' => $api_token
                ]);
            }

            $check_login = DB::table('sessions')->where('user_id',$user_check->id)->first();
            if($check_login){
                //a login exists somewhere
                DB::table('sessions')->where('user_id',$user_check->id)->update([
                    'user_id' => NULL,
                    'last_activity' => 172520111
                ]);
            }      
        }


        return $this->loginPipeline($request)->then(function ($request) {
            return app(LoginResponse::class);
        });
    }

    /**
     * Get the authentication pipeline instance.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Pipeline\Pipeline
     */
    protected function loginPipeline(LoginRequest $request)
    {

        if (Fortify::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                call_user_func(Fortify::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(config('fortify.pipelines.login'))) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                config('fortify.pipelines.login')
            ));
        }

        return (new Pipeline(app()))->send($request)->through(array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // dd($request->all());
        // DB::table('sessions')->where('user_id',auth()->user()->id)->delete();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}