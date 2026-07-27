<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MobileEmailVerificationController extends Controller
{
    public function __invoke(Request $request, string $id, string $hash): View
    {
        $user = User::query()->findOrFail($id);

        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return view('auth.mobile-email-verified', [
            'email' => $user->email,
        ]);
    }
}
