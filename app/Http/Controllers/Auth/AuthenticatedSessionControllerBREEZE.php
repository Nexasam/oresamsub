<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionControllerBREEZE extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $siteTemplate = SiteTemplate::first();
        if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
            return view('auth.login');
        }
        // dd($data);
        return view('template2.auth.login');
        // return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        
        $request->authenticate();
        // dd('na here');

        $request->session()->regenerate();

        // dd($request->all());

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
