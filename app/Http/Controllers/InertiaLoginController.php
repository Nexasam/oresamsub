<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Inertia\Inertia;
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

class InertiaLoginController extends Controller
{
    // Show login page (Inertia React)
    public function create()
    {
   
        return Inertia::render('Auth/Login');
    }
    

    // Handle login form submission
    public function store(Request $request)
    {

       
        // validate input
        $request->validate([
            'email' => ['required'], // can be email, username or phone
            'password' => ['required'],
        ]);

        $loginInput = $request->input('email');
        $password   = $request->input('password');

        // check what type of login this is
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 
                (is_numeric($loginInput) ? 'phone_number' : 'username');

        $user = User::query()->where($field, $loginInput)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ])->onlyInput('email');
        }

        if ((bool) $user->is_deactivated) {
            return back()->withErrors([
                'email' => 'This account has been deactivated. Please contact support.',
            ])->onlyInput('email');
        }

        if (! $user->hasVerifiedEmail()) {
            return back()->withErrors([
                'email' => 'Please verify your email address before signing in.',
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
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
}
