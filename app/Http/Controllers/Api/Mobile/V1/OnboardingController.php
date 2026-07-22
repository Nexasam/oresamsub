<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\V1\SendPhoneOtpRequest;
use App\Http\Requests\Api\Mobile\V1\SetTransactionPinRequest;
use App\Http\Requests\Api\Mobile\V1\VerifyPhoneOtpRequest;
use App\Services\Mobile\MobileOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class OnboardingController extends Controller
{
    use RespondsToMobileApi;

    public function __construct(private readonly MobileOtpService $otp) {}

    public function sendPhoneOtp(SendPhoneOtpRequest $request): JsonResponse
    {
        try {
            $this->otp->send($request->user(), $request->string('phone_number')->toString());
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception->getMessage(), null, 422);
        }

        return $this->successResponse('A verification code has been sent to your phone number.');
    }

    public function verifyPhoneOtp(VerifyPhoneOtpRequest $request): JsonResponse
    {
        try {
            $this->otp->verify($request->user(), $request->string('otp')->toString());
        } catch (RuntimeException $exception) {
            return $this->errorResponse($exception->getMessage(), null, 422);
        }

        return $this->successResponse('Phone number verified successfully.');
    }

    public function setTransactionPin(SetTransactionPinRequest $request): JsonResponse
    {
        $request->user()->update(['pin' => $request->string('pin')->toString()]);

        return $this->successResponse('Transaction PIN created successfully.');
    }

    public function verifyTransactionPin(Request $request): JsonResponse
    {
        $request->validate(['pin' => ['required', 'digits:4']]);
        $storedPin = (string) $request->user()->pin;

        if ($storedPin === '' || ! hash_equals($storedPin, $request->string('pin')->toString())) {
            return $this->errorResponse('The transaction PIN is incorrect.', null, 422);
        }

        return $this->successResponse('Transaction PIN verified successfully.');
    }
}
