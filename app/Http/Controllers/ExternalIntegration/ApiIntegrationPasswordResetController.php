<?php

namespace App\Http\Controllers\ExternalIntegration;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use App\Traits\JsonResponseWrapperMobile;
use App\Http\Services\UserOnboarding\TransactionPinService;

class ApiIntegrationPasswordResetController extends Controller
{
    use JsonResponseWrapperMobile;
    
    public function forgot_password(Request $request){
            
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'] 
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return $this->success('Reset instructions have been sent to the email provided');          
        }

        return $this->error('Password reset action failed');          


    }
}
