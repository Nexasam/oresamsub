<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class UserTwoFactorController extends Controller
{
    public function index(): View
    {
        return view('auth.twoFactor');
    }
    
    public function store(Request $request): ValidationException|RedirectResponse
    {
        $request->validate([
            'two_factor_code' => ['integer', 'required'],
        ]);
        $user = auth()->user();
        if ($request->input('two_factor_code') !== $user->two_factor_code) {
            throw ValidationException::withMessages([
                'two_factor_code' => __('The code you entered doesnt match our records'),
            ]);
        }
        $user->resetTwoFactorCode();
        return redirect()->to(RouteServiceProvider::HOME);
    }
    public function resend(): RedirectResponse
    {
        $user = auth()->user();
        $user->generateTwoFactorCode();
        $user->notify(new SendTwoFactorCode());
        return redirect()->back()->withStatus(__('Code has been sent again'));
    }
}
