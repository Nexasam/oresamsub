<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UssdGatewayDevice;
use App\Models\UssdRechargeAttempt;
use App\Models\UssdRechargeBatch;
use App\Services\UssdGateway\UssdGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UssdGatewayController extends Controller
{
    public function index(Request $request): View
    {
        $attempts = UssdRechargeAttempt::query()
            ->with(['device', 'batch'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest('updated_at')
            ->paginate(40)
            ->withQueryString();

        return view('admin.ussd_gateway.index', [
            'devices' => UssdGatewayDevice::query()->withCount([
                'attempts as queued_count' => fn ($query) => $query->where('status', 'queued'),
                'attempts as pending_count' => fn ($query) => $query->whereIn('status', ['picked', 'pending_review']),
            ])->latest()->get(),
            'batches' => UssdRechargeBatch::query()->with('device')->withCount('attempts')->latest()->limit(20)->get(),
            'attempts' => $attempts,
            'statuses' => ['queued', 'picked', 'pending_review', 'confirmed_success', 'confirmed_failed', 'cancelled'],
            'stats' => [
                'queued' => UssdRechargeAttempt::where('status', 'queued')->count(),
                'picked' => UssdRechargeAttempt::where('status', 'picked')->count(),
                'review' => UssdRechargeAttempt::where('status', 'pending_review')->count(),
                'success' => UssdRechargeAttempt::where('status', 'confirmed_success')->count(),
            ],
        ]);
    }

    public function storeDevice(Request $request, UssdGatewayService $service): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'main_sim_number' => ['required', 'string', 'max:20'],
            'sim_slot' => ['required', 'integer', 'between:0,1'],
        ]);
        $mainSim = $service->normalizeNigerianNumber($data['main_sim_number']);

        if (UssdGatewayDevice::where('main_sim_number', $mainSim)->exists()) {
            throw ValidationException::withMessages(['main_sim_number' => 'This main SIM is already assigned to a gateway.']);
        }

        $device = UssdGatewayDevice::create([
            'name' => $data['name'],
            'main_sim_number' => $mainSim,
            'sim_slot' => $data['sim_slot'],
            'token_digest' => hash('sha256', Str::uuid()->toString()),
            'token_hint' => 'pending',
            'created_by' => $request->user()?->id,
        ]);
        $token = $service->issueDeviceToken($device);

        return back()
            ->with('success', 'Gateway created. Copy its token now; it will not be shown again.')
            ->with('gateway_plain_token', $token);
    }

    public function updateDevice(Request $request, UssdGatewayDevice $device, UssdGatewayService $service): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'main_sim_number' => ['required', 'string', 'max:20'],
            'sim_slot' => ['required', 'integer', 'between:0,1'],
        ]);
        $mainSim = $service->normalizeNigerianNumber($data['main_sim_number']);

        if (UssdGatewayDevice::query()
            ->where('main_sim_number', $mainSim)
            ->whereKeyNot($device->id)
            ->exists()) {
            throw ValidationException::withMessages(['main_sim_number' => 'This main SIM is already assigned to a gateway.']);
        }

        if ((int) $device->sim_slot !== (int) $data['sim_slot'] && $device->installation_id) {
            throw ValidationException::withMessages(['sim_slot' => 'Reset the phone pairing before changing the SIM slot.']);
        }

        $device->update(['name' => $data['name'], 'main_sim_number' => $mainSim, 'sim_slot' => $data['sim_slot']]);

        return back()->with('success', 'Gateway updated.');
    }

    public function rotateToken(UssdGatewayDevice $device, UssdGatewayService $service): RedirectResponse
    {
        $token = $service->issueDeviceToken($device);

        return back()->with('success', 'Gateway token rotated. Update the phone before processing more recipients.')
            ->with('gateway_plain_token', $token);
    }

    public function toggleDevice(UssdGatewayDevice $device): RedirectResponse
    {
        $device->update(['is_active' => ! $device->is_active]);

        return back()->with('success', $device->is_active ? 'Gateway enabled.' : 'Gateway disabled.');
    }

    public function resetPairing(UssdGatewayDevice $device): RedirectResponse
    {
        abort_if($device->attempts()->where('status', 'picked')->exists(), 409, 'Resolve the picked attempt before resetting this device.');

        $device->update([
            'installation_id' => null,
            'subscription_id' => null,
            'device_model' => null,
            'bound_at' => null,
        ]);

        return back()->with('success', 'Phone/SIM pairing reset. The next phone must pair again before claiming work.');
    }

    public function storeBatch(Request $request, UssdGatewayService $service): RedirectResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'exists:ussd_gateway_devices,id'],
            'name' => ['required', 'string', 'max:120'],
            'plan_label' => ['nullable', 'string', 'max:160'],
            'recipient_numbers' => ['required', 'string', 'max:15000'],
        ]);
        $device = UssdGatewayDevice::findOrFail($data['device_id']);
        if (! $device->is_active) {
            throw ValidationException::withMessages(['device_id' => 'Select an active gateway device.']);
        }

        $rawNumbers = preg_split('/[\s,;|]+/', trim($data['recipient_numbers']), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $numbers = [];
        foreach ($rawNumbers as $rawNumber) {
            $numbers[] = $service->normalizeNigerianNumber($rawNumber);
        }
        $numbers = array_values(array_unique($numbers));

        if ($numbers === [] || count($numbers) > 500) {
            throw ValidationException::withMessages(['recipient_numbers' => 'Provide between 1 and 500 unique recipient numbers.']);
        }

        DB::transaction(function () use ($request, $data, $device, $numbers) {
            $batch = UssdRechargeBatch::create([
                'device_id' => $device->id,
                'name' => $data['name'],
                'plan_label' => $data['plan_label'] ?? null,
                'status' => 'queued',
                'recipient_count' => count($numbers),
                'created_by' => $request->user()?->id,
            ]);

            foreach ($numbers as $index => $number) {
                $batch->attempts()->create([
                    'device_id' => $device->id,
                    'sequence' => $index + 1,
                    'recipient_number' => $number,
                    'status' => 'queued',
                ]);
            }
        });

        return back()->with('success', count($numbers).' recipient(s) queued. Each will be released one at a time.');
    }

    public function toggleBatch(UssdRechargeBatch $batch): RedirectResponse
    {
        if (in_array($batch->status, ['completed', 'cancelled'], true)) {
            return back()->with('failure', 'Completed or cancelled batches cannot be resumed.');
        }

        $batch->update(['status' => $batch->status === 'paused' ? 'in_progress' : 'paused']);

        return back()->with('success', $batch->status === 'paused' ? 'Batch paused.' : 'Batch resumed.');
    }

    public function reviewAttempt(Request $request, UssdRechargeAttempt $attempt, UssdGatewayService $service): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', Rule::in(['confirmed_success', 'confirmed_failed', 'cancelled'])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (! in_array($attempt->status, ['picked', 'pending_review'], true)) {
            throw ValidationException::withMessages(['decision' => 'Only picked or pending-review attempts can be manually resolved.']);
        }

        $attempt->update([
            'status' => $data['decision'],
            'admin_notes' => $data['admin_notes'] ?? null,
            'reviewed_by' => $request->user()?->id,
            'reviewed_at' => now(),
        ]);
        $service->refreshBatchStatus($attempt->batch);

        return back()->with('success', 'Attempt resolved. It will never be returned to the phone again.');
    }
}
