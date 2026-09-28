<?php

namespace App\Http\Controllers;

use App\Mail\AirtimeToCashSubmittedMail;
use App\Models\AirtimeToCashRequest;
use App\Models\ConfigSetting;
use App\Models\Network;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AirtimeToCashController extends Controller
{
    public function index(): Response
    {
        $settings = $this->settings();

        $requests = AirtimeToCashRequest::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (AirtimeToCashRequest $request): array => $this->customerResource($request));

        return Inertia::render('AirtimeToCash', [
            'networks' => Network::query()
                ->where('airtime_to_cash_enabled', true)
                ->select('id', 'network_name')
                ->orderBy('network_name')
                ->get(),
            'requests' => $requests,
            'settings' => [
                'enabled' => $settings['enabled'],
                'rate_per_100' => $settings['rate_per_100'],
                'support_whatsapp' => $settings['support_whatsapp'],
                'fraud_disclaimer_text' => $this->fraudDisclaimerText(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $settings = $this->settings();

        if (! $settings['enabled']) {
            return back()->with('failure', 'Airtime-to-cash is currently unavailable. Please contact support.');
        }

        $validated = $request->validate([
            'network_id' => ['required', 'exists:networks,id'],
            'airtime_amount' => ['required', 'numeric', 'min:100', 'max:1000000'],
            'sender_phone' => ['required', 'string', 'min:10', 'max:20'],
            'payout_bank_name' => ['required', 'string', 'max:120'],
            'payout_account_name' => ['required', 'string', 'max:120'],
            'payout_account_number' => ['required', 'string', 'min:5', 'max:30'],
            'customer_transfer_reference' => ['nullable', 'string', 'max:120'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'fraud_disclaimer_accepted' => ['accepted'],
        ]);

        $network = Network::query()
            ->where('airtime_to_cash_enabled', true)
            ->findOrFail($validated['network_id']);
        $airtimeAmount = round((float) $validated['airtime_amount'], 2);
        $rate = (float) $settings['rate_per_100'];
        $cashAmount = round(($airtimeAmount * $rate) / 100, 2);

        $requestRecord = AirtimeToCashRequest::query()->create([
            'reference' => $this->newReference(),
            'user_id' => auth()->id(),
            'network_id' => $network->id,
            'network_name' => $network->network_name,
            'airtime_amount' => $airtimeAmount,
            'cash_amount' => $cashAmount,
            'rate_per_100' => $rate,
            'sender_phone' => $validated['sender_phone'],
            'payout_bank_name' => $validated['payout_bank_name'],
            'payout_account_name' => $validated['payout_account_name'],
            'payout_account_number' => $validated['payout_account_number'],
            'customer_transfer_reference' => $validated['customer_transfer_reference'] ?? null,
            'customer_note' => $validated['customer_note'] ?? null,
            'fraud_disclaimer_accepted_at' => now(),
            'fraud_disclaimer_text' => $this->fraudDisclaimerText(),
            'fraud_disclaimer_ip' => $request->ip(),
            'fraud_disclaimer_user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            'status' => AirtimeToCashRequest::STATUS_PENDING,
            'source' => 'pwa',
        ]);

        $this->notifySupport($requestRecord, $settings['support_email']);

        return back()->with('success', 'Airtime-to-cash request submitted. Our admin will verify and process it manually.');
    }

    public function adminIndex(Request $request)
    {
        $status = (string) $request->query('status', '');

        $requests = AirtimeToCashRequest::query()
            ->with(['user', 'processor'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.airtime_to_cash.index', [
            'requests' => $requests,
            'statuses' => AirtimeToCashRequest::statuses(),
            'selectedStatus' => $status,
            'settings' => $this->settings(),
            'networks' => Network::query()->orderBy('network_name')->get(),
        ]);
    }

    public function adminUpdate(Request $request, AirtimeToCashRequest $airtimeToCashRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(AirtimeToCashRequest::statuses())],
            'admin_note' => ['nullable', 'string', 'max:2000'],
            'payout_reference' => ['nullable', 'string', 'max:120'],
        ]);

        $airtimeToCashRequest->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null,
            'payout_reference' => $validated['payout_reference'] ?? null,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        Session::flash('success', 'Airtime-to-cash request updated.');

        return back();
    }

    public function adminSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'support_email' => ['required', 'email', 'max:190'],
            'support_whatsapp' => ['required', 'string', 'max:30'],
            'rate_per_100' => ['required', 'numeric', 'min:1', 'max:100'],
            'enabled' => ['required', Rule::in(['0', '1'])],
            'enabled_network_ids' => ['nullable', 'array'],
            'enabled_network_ids.*' => ['string', 'exists:networks,id'],
        ]);

        $this->setSetting('airtime_to_cash_enabled', (string) $validated['enabled'], 'Controls whether customers can submit airtime-to-cash requests.');
        $this->setSetting('airtime_to_cash_support_email', $validated['support_email'], 'Admin/support email notified when customers submit airtime-to-cash requests.');
        $this->setSetting('airtime_to_cash_support_whatsapp', preg_replace('/\D+/', '', $validated['support_whatsapp']) ?: $validated['support_whatsapp'], 'WhatsApp support number shown to customers for airtime-to-cash help.');
        $this->setSetting('airtime_to_cash_rate_per_100', (string) $validated['rate_per_100'], 'Cash payout in NGN for every NGN 100 airtime submitted.');

        $enabledNetworkIds = collect($validated['enabled_network_ids'] ?? [])->map(fn ($id): string => (string) $id)->all();

        Network::query()->update(['airtime_to_cash_enabled' => false]);

        if ($enabledNetworkIds !== []) {
            Network::query()->whereIn('id', $enabledNetworkIds)->update(['airtime_to_cash_enabled' => true]);
        }

        Session::flash('success', 'Airtime-to-cash settings updated.');

        return back();
    }

    private function settings(): array
    {
        return [
            'enabled' => $this->setting('airtime_to_cash_enabled', '1', 'Controls whether customers can submit airtime-to-cash requests.') === '1',
            'support_email' => $this->setting('airtime_to_cash_support_email', 'admin@example.com', 'Admin/support email notified when customers submit airtime-to-cash requests.'),
            'support_whatsapp' => $this->setting('airtime_to_cash_support_whatsapp', '234xxxxxxxxxx', 'WhatsApp support number shown to customers for airtime-to-cash help.'),
            'rate_per_100' => (float) $this->setting('airtime_to_cash_rate_per_100', '90', 'Cash payout in NGN for every NGN 100 airtime submitted.'),
        ];
    }

    private function setting(string $key, string $default, string $description): string
    {
        $setting = ConfigSetting::query()->firstOrCreate(
            ['key' => $key],
            [
                'value' => $default,
                'current_value' => $default,
                'description' => $description,
            ],
        );

        if ($setting->current_value !== null && $setting->current_value !== '') {
            return (string) $setting->current_value;
        }

        if ($setting->value !== null && $setting->value !== '') {
            return (string) $setting->value;
        }

        return $default;
    }

    private function setSetting(string $key, string $value, string $description): void
    {
        ConfigSetting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'current_value' => $value,
                'description' => $description,
            ],
        );
    }

    private function notifySupport(AirtimeToCashRequest $requestRecord, string $supportEmail): void
    {
        if (! filter_var($supportEmail, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($supportEmail)->send(new AirtimeToCashSubmittedMail($requestRecord->load('user')));
        } catch (\Throwable $exception) {
            Log::warning('Airtime-to-cash support email failed.', [
                'request_id' => $requestRecord->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function customerResource(AirtimeToCashRequest $request): array
    {
        return [
            'id' => $request->id,
            'reference' => $request->reference,
            'network_name' => $request->network_name,
            'airtime_amount' => (float) $request->airtime_amount,
            'cash_amount' => (float) $request->cash_amount,
            'rate_per_100' => (float) $request->rate_per_100,
            'sender_phone' => $request->sender_phone,
            'payout_bank_name' => $request->payout_bank_name,
            'payout_account_name' => $request->payout_account_name,
            'payout_account_number_masked' => $request->maskedAccountNumber(),
            'customer_transfer_reference' => $request->customer_transfer_reference,
            'status' => $request->status,
            'admin_note' => $request->admin_note,
            'payout_reference' => $request->payout_reference,
            'fraud_disclaimer_accepted_at' => optional($request->fraud_disclaimer_accepted_at)->toIso8601String(),
            'created_at' => optional($request->created_at)->toIso8601String(),
            'updated_at' => optional($request->updated_at)->toIso8601String(),
        ];
    }

    private function newReference(): string
    {
        do {
            $reference = 'ATC-'.now()->format('ymd').'-'.Str::upper(Str::random(8));
        } while (AirtimeToCashRequest::query()->where('reference', $reference)->exists());

        return $reference;
    }

    private function fraudDisclaimerText(): string
    {
        return 'I confirm that the airtime I am selling is legitimately owned by me or I am authorized to sell it. I understand that fraudulent, stolen, borrowed, reversed, disputed, or unauthorized airtime may cause this request to be rejected, payout to be delayed or reversed, my account to be restricted, and the matter to be reported where necessary. I agree that OresamSub will manually verify the airtime before payment.';
    }
}
