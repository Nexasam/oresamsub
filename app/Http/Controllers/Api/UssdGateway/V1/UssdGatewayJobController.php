<?php

namespace App\Http\Controllers\Api\UssdGateway\V1;

use App\Http\Controllers\Controller;
use App\Models\UssdRechargeAttempt;
use App\Services\UssdGateway\UssdGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UssdGatewayJobController extends Controller
{
    public function bind(Request $request): JsonResponse
    {
        $device = $request->attributes->get('ussd_gateway_device');
        $data = $request->validate([
            'installation_id' => ['required', 'uuid'],
            'sim_slot' => ['required', 'integer', 'between:0,1'],
            'subscription_id' => ['required', 'integer'],
            'device_model' => ['nullable', 'string', 'max:120'],
        ]);

        if ((int) $data['sim_slot'] !== (int) $device->sim_slot) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong SIM slot. This gateway is configured for SIM '.($device->sim_slot + 1).'.',
            ], 409);
        }

        if ($device->installation_id && $device->installation_id !== $data['installation_id']) {
            return response()->json(['success' => false, 'message' => 'Gateway is already paired to another phone. Reset pairing from admin first.'], 409);
        }

        if ($device->installation_id && (int) $device->subscription_id !== (int) $data['subscription_id']) {
            return response()->json([
                'success' => false,
                'message' => 'The SIM subscription has changed. Processing is blocked until an admin resets and pairs this gateway again.',
            ], 409);
        }

        $device->update([
            'installation_id' => $data['installation_id'],
            'subscription_id' => $data['subscription_id'],
            'device_model' => $data['device_model'] ?? null,
            'bound_at' => $device->bound_at ?: now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Phone and SIM slot paired.', 'data' => [
            'device_id' => $device->id,
            'main_sim_number' => $device->main_sim_number,
            'sim_slot' => $device->sim_slot,
        ]]);
    }

    public function claim(Request $request, UssdGatewayService $service): JsonResponse
    {
        $device = $request->attributes->get('ussd_gateway_device');
        $job = $service->claimNext($device);

        return response()->json([
            'success' => true,
            'data' => $job,
            'message' => $job ? 'One recipient was picked exactly once.' : 'No queued recipient is available.',
        ]);
    }

    public function started(Request $request, UssdRechargeAttempt $attempt, UssdGatewayService $service): JsonResponse
    {
        $data = $request->validate(['dispatch_token' => ['required', 'string', 'max:100']]);
        $this->assertOwnedAttempt($request, $attempt);
        $service->assertDispatchToken($attempt, $data['dispatch_token']);

        if ($attempt->status !== 'picked') {
            return response()->json(['success' => false, 'message' => 'Attempt is no longer dispatchable.'], 409);
        }

        if (! $attempt->dial_started_at) {
            $attempt->update(['dial_started_at' => now()]);
        }

        return response()->json(['success' => true, 'message' => 'Dial start recorded.']);
    }

    public function result(Request $request, UssdRechargeAttempt $attempt, UssdGatewayService $service): JsonResponse
    {
        $data = $request->validate([
            'dispatch_token' => ['required', 'string', 'max:100'],
            'client_result_id' => ['required', 'uuid'],
            'outcome' => ['required', 'in:response_captured,session_ended,network_unavailable,device_error,not_dispatched'],
            'response_text' => ['nullable', 'string', 'max:4000'],
        ]);
        $fingerprint = hash('sha256', json_encode([
            'outcome' => $data['outcome'],
            'response_text' => trim((string) ($data['response_text'] ?? '')),
        ], JSON_UNESCAPED_UNICODE));

        return DB::transaction(function () use ($request, $attempt, $service, $data, $fingerprint) {
            $lockedAttempt = UssdRechargeAttempt::query()->whereKey($attempt->id)->lockForUpdate()->firstOrFail();
            $this->assertOwnedAttempt($request, $lockedAttempt);
            $service->assertDispatchToken($lockedAttempt, $data['dispatch_token']);

            if ($lockedAttempt->client_result_id) {
                if ($lockedAttempt->client_result_id === $data['client_result_id'] && $lockedAttempt->response_fingerprint === $fingerprint) {
                    return response()->json(['success' => true, 'message' => 'Result was already recorded.', 'data' => ['status' => $lockedAttempt->status]]);
                }

                return response()->json(['success' => false, 'message' => 'A different result is already stored for this attempt.'], 409);
            }

            if (in_array($lockedAttempt->status, UssdRechargeAttempt::TERMINAL_STATUSES, true)) {
                return response()->json([
                    'success' => true,
                    'message' => 'This attempt was already manually resolved by an admin. The phone may clear its local copy.',
                    'data' => ['status' => $lockedAttempt->status],
                ]);
            }

            if ($lockedAttempt->status !== 'picked') {
                return response()->json(['success' => false, 'message' => 'Attempt is not awaiting a device result.'], 409);
            }

            $lockedAttempt->update([
                'status' => 'pending_review',
                'client_result_id' => $data['client_result_id'],
                'device_outcome' => $data['outcome'],
                'response_text' => trim((string) ($data['response_text'] ?? '')) ?: null,
                'response_fingerprint' => $fingerprint,
                'result_received_at' => now(),
            ]);
            $service->refreshBatchStatus($lockedAttempt->batch);

            return response()->json([
                'success' => true,
                'message' => 'Result stored for manual review. The recipient will not be automatically retried.',
                'data' => ['status' => 'pending_review'],
            ]);
        }, 3);
    }

    public function status(Request $request): JsonResponse
    {
        $device = $request->attributes->get('ussd_gateway_device');

        return response()->json(['success' => true, 'data' => [
            'device' => [
                'id' => $device->id,
                'name' => $device->name,
                'main_sim_number' => $device->main_sim_number,
                'sim_slot' => $device->sim_slot,
                'processing_mode' => $device->processing_mode,
                'subscription_id' => $device->subscription_id,
                'device_model' => $device->device_model,
            ],
            'counts' => $device->attempts()->selectRaw('status, COUNT(*) aggregate')->groupBy('status')->pluck('aggregate', 'status'),
        ]]);
    }

    private function assertOwnedAttempt(Request $request, UssdRechargeAttempt $attempt): void
    {
        abort_unless($attempt->device_id === $request->attributes->get('ussd_gateway_device')->id, 404);
    }
}
