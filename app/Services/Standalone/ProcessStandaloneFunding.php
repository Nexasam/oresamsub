<?php

namespace App\Services\Standalone;

use App\Models\StandaloneFundingEvent;
use App\Models\StandaloneWebsite;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcessStandaloneFunding
{
    public function __construct(private StandaloneFundingCallbackService $callback) {}

    public function handle(StandaloneWebsite $site, array $payload, string $providerReference): StandaloneFundingEvent
    {
        $event = DB::transaction(function () use ($site, $payload, $providerReference) {
            $existing = StandaloneFundingEvent::where('provider_reference', $providerReference)->first();
            if ($existing) {
                return $existing;
            }
            $eventId = 'evt_'.Str::lower(Str::random(24));
            $reference = 'ORS-FUND-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
            $money = fn ($value) => (string) BigDecimal::of((string) $value)->toScale(2);
            $callback = ['version' => '1.0', 'event' => 'master_wallet.funded', 'event_id' => $eventId, 'standalone_id' => $site->slug, 'reference' => $reference, 'provider_reference' => $providerReference, 'amount_gross' => $money(data_get($payload, 'amount', 0)), 'fees' => $money(data_get($payload, 'fees', 0)), 'amount_settled' => $money(data_get($payload, 'settlement_amount', 0)), 'currency' => (string) data_get($payload, 'currency', 'NGN'), 'paid_at' => (string) data_get($payload, 'paid_at', now()->toIso8601String())];

            return StandaloneFundingEvent::create(['standalone_website_id' => $site->id, 'event_id' => $eventId, 'provider_reference' => $providerReference, 'reference' => $reference, 'amount_gross' => $callback['amount_gross'], 'fees' => $callback['fees'], 'amount_settled' => $callback['amount_settled'], 'currency' => $callback['currency'], 'payment_status' => 'success', 'paid_at' => $callback['paid_at'], 'bank_name' => data_get($payload, 'receiver.bank'), 'account_number' => data_get($payload, 'receiver.account_number'), 'provider_payload' => ['transaction_status' => data_get($payload, 'transaction_status'), 'provider_reference' => $providerReference], 'callback_url' => $site->callback_url, 'callback_payload' => $callback, 'delivery_status' => 'pending']);
        });
        if ($event->attempt_count === 0) {
            $this->callback->deliver($event);
        }

        return $event->fresh();
    }
}
