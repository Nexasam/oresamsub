<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use App\Models\SiteImage;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Features;
use App\Models\AdminColorSetting;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use App\Models\LandingPagesSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Http\Services\CouponCodeService;
use App\Http\Services\CrystalPayService;
use App\Http\Services\VirtualAccountService;
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
    public function create(Request $request)
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

        $site_colors = AdminColorSetting::get();
        if(count($site_colors) > 0){
            foreach($site_colors as $site_color){
                $data[$site_color->color_name] = $site_color->color_value;
            }
        }

        $siteTemplate = SiteTemplate::first();
        if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
            return view('auth.login')->with($data);
        }
       

        return view('template2.auth.login')->with($data);
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
        // dd($request->)

        $password = $request->password;

        // $user_check = User::select('id','api_token','old_platform_password','password')->where('email',$request->email)->first();
        $username_param = $request->email;
        $user_check = User::where('email', $username_param)
        ->orWhere('username', $username_param)
        ->orWhere('phone_number', $username_param)
        ->first();

        if($user_check){

            $request->merge([
                'user' => $user_check
            ]);

            $djangoHash = $user_check->old_platform_password;
            $new_password_hashed = $user_check->password;

            

            //migration tool
            if(env('APP_NAME') == 'CrystaltechData'){ 
                if($djangoHash != NULL && ! Hash::check($request->password,$new_password_hashed)  ){
                    if (Hash::check($request->password,$djangoHash)) {
                            $new_password_hash = Hash::make($password);
                            $user_check->update([
                                'password' => $new_password_hash
                            ]);
                            $user_check->refresh();
                            // echo "Password is valid!";exit;
                    }   
                }
            }else{
                if($djangoHash != NULL && ! Hash::check($request->password,$new_password_hashed)  ){
                
                    if ($this->verifyDjangoPassword($password, $djangoHash)) {
                        $new_password_hash = Hash::make($password);
                        $user_check->update([
                            'password' => $new_password_hash
                        ]);
                        $user_check->refresh();
                    } 
                    
                }
            }
            

            //we not expecting the customer to have password as password - NAH
            //if old account gets here, then password is updated
            
            if( $user_check->api_token == NULL){
                $user_idd = $user_check->id;
                $cleaned_userid = str_replace('-', '', $user_idd);
                $randomLetters = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3);
                $api_token = rand(11111,99999).$cleaned_userid.time().$randomLetters;
                // $api_token = str()->random(200).time();
                $user_check->update([
                    'api_token' => $api_token
                ]);
            }

            //Generate crystalpay virtual accounts for those without va or incomplete va:
            $dataaa['user'] = $user_check;
            (new VirtualAccountService())->generate_accounts($dataaa);

            $coupon_check = (new CouponCodeService())->determine_if_user_qualify($dataaa);
            $user_check->coupons = $coupon_check['status'] == 1 ? $coupon_check['coupon_info'] : NULL;


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
     * Attempt to authenticate a new session.
     *
     * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
     * @return mixed
     */
    function verifyDjangoPassword($password, $djangoHash)
    {
        // Format: algorithm$iterations$salt$hash
        [$algo, $iterations, $salt, $hash] = explode('$', $djangoHash);
    
        if ($algo !== 'pbkdf2_sha256') {
            throw new Exception('Unsupported hash algorithm.');
        }
    
        // Decode base64 hash
        $expected = base64_decode($hash);
    
        // Create PBKDF2 hash using SHA-256
        $derivedKey = hash_pbkdf2('sha256', $password, $salt, (int)$iterations, strlen($expected), true);
    
        return hash_equals($expected, $derivedKey);
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