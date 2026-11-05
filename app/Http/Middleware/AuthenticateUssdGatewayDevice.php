<?php

namespace App\Http\Middleware;

use App\Models\UssdGatewayDevice;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateUssdGatewayDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = trim((string) $request->bearerToken());
        $device = $token === '' ? null : UssdGatewayDevice::query()
            ->where('token_digest', hash('sha256', $token))
            ->where('is_active', true)
            ->first();

        if (! $device) {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive gateway token.'], 401);
        }

        $isBindingRequest = $request->is('api/ussd-gateway/v1/bind');
        $installationId = trim((string) $request->header('X-Gateway-Installation'));

        if (! $device->installation_id && ! $isBindingRequest) {
            return response()->json(['success' => false, 'message' => 'This gateway must be paired to a phone and SIM slot before it can claim work.'], 409);
        }

        if ($device->installation_id && ! hash_equals($device->installation_id, $installationId)) {
            return response()->json(['success' => false, 'message' => 'This token is paired to a different phone installation.'], 403);
        }

        $isJobRequest = $request->is('api/ussd-gateway/v1/jobs/*');
        if ($isJobRequest) {
            $reportedSlot = $request->header('X-Gateway-Sim-Slot');
            $reportedSubscription = $request->header('X-Gateway-Subscription');

            if ($reportedSlot === null || $reportedSubscription === null) {
                return response()->json(['success' => false, 'message' => 'Current SIM identity headers are required before processing a job.'], 409);
            }

            if ((int) $reportedSlot !== (int) $device->sim_slot || (int) $reportedSubscription !== (int) $device->subscription_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'The active SIM does not match this gateway pairing. No recipient was released.',
                ], 409);
            }
        }

        $device->forceFill(['last_seen_at' => now()])->saveQuietly();
        $request->attributes->set('ussd_gateway_device', $device);

        return $next($request);
    }
}
