<?php

namespace App\Services\Standalone;

use App\Models\StandaloneFundingEvent;
use App\Models\StandaloneWalletEntry;
use App\Models\StandaloneWebsite;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcessStandaloneFunding
{
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
            $gross = $money(data_get($payload, 'amount', 0));
            $fees = $money(data_get($payload, 'fees', 0));
            $settled = $money(data_get($payload, 'settlement_amount', 0));

            $event = StandaloneFundingEvent::create(['standalone_website_id' => $site->id, 'event_id' => $eventId, 'provider_reference' => $providerReference, 'reference' => $reference, 'amount_gross' => $gross, 'fees' => $fees, 'amount_settled' => $settled, 'currency' => (string) data_get($payload, 'currency', 'NGN'), 'payment_status' => 'success', 'paid_at' => data_get($payload, 'paid_at', now()->toIso8601String()), 'bank_name' => data_get($payload, 'receiver.bank'), 'account_number' => data_get($payload, 'receiver.account_number'), 'provider_payload' => ['transaction_status' => data_get($payload, 'transaction_status'), 'provider_reference' => $providerReference], 'callback_payload' => [], 'delivery_status' => 'not_required']);

            $locked = StandaloneWebsite::whereKey($site->id)->lockForUpdate()->firstOrFail();
            $before = BigDecimal::of($locked->master_wallet)->toScale(2);
            $after = $before->plus(BigDecimal::of($settled))->toScale(2);
            $locked->update(['master_wallet' => (string) $after]);
            StandaloneWalletEntry::create([
                'standalone_website_id' => $locked->id,
                'transaction_id' => 'swl_'.Str::lower(Str::random(24)),
                'type' => 'credit', 'category' => 'funding', 'amount' => $settled,
                'balance_before' => (string) $before, 'balance_after' => (string) $after,
                'purpose' => 'Kolomoni virtual account funding', 'provider_reference' => $providerReference,
                'metadata' => ['funding_event_id' => $eventId],
            ]);

            return $event;
        });

        return $event->fresh();
    }
}
