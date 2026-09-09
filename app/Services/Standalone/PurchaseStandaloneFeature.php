<?php

namespace App\Services\Standalone;

use App\Models\StandaloneFeature;
use App\Models\StandaloneFeaturePurchase;
use App\Models\StandaloneFeatureSubscription;
use App\Models\StandaloneWebsite;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PurchaseStandaloneFeature
{
    public function handle(StandaloneWebsite $site, StandaloneFeature $feature, string $reference, ?string $slotName = null): array
    {
        return DB::transaction(function () use ($site, $feature, $reference, $slotName): array {
            $lockedSite = StandaloneWebsite::whereKey($site->id)->lockForUpdate()->firstOrFail();
            $lockedFeature = StandaloneFeature::whereKey($feature->id)->lockForUpdate()->firstOrFail();
            $slotName = $lockedFeature->purchase_mode === 'named_slots' ? trim((string) $slotName) : null;
            $slotKey = $slotName ? $this->slotKey($slotName) : '__single__';
            $price = BigDecimal::of($lockedFeature->purchasePriceFor($lockedSite->price_level))->toScale(2);
            $existing = StandaloneFeaturePurchase::where('standalone_website_id', $lockedSite->id)
                ->where('client_reference', $reference)->first();

            if ($existing) {
                if ($existing->standalone_feature_id !== $lockedFeature->id || $existing->slot_key !== $slotKey || $existing->amount !== (string) $price) {
                    throw new RuntimeException('reference_conflict');
                }

                return ['purchase' => $existing, 'subscription' => $this->subscription($lockedSite, $lockedFeature, $slotKey), 'replay' => true];
            }
            if (! $lockedFeature->is_active) {
                throw new RuntimeException('feature_inactive');
            }
            if ($lockedSite->walletEntries()->where('client_reference', $reference)->exists()) {
                throw new RuntimeException('reference_conflict');
            }

            $subscription = $this->subscription($lockedSite, $lockedFeature, $slotKey);
            if ($subscription && $subscription->status === 'active') {
                throw new RuntimeException($lockedFeature->purchase_mode === 'named_slots' ? 'named_slot_owned' : 'already_owned');
            }

            $periodStarts = $lockedFeature->isRecurring() ? now() : null;
            $periodEnds = $periodStarts?->copy()->addMonthNoOverflow();
            $walletEntry = null;
            if (! $price->isZero()) {
                $before = BigDecimal::of($lockedSite->master_wallet)->toScale(2);
                if ($before->isLessThan($price)) {
                    throw new RuntimeException('insufficient_balance');
                }
                $after = $before->minus($price)->toScale(2);
                $lockedSite->update(['master_wallet' => (string) $after]);
                $walletEntry = $lockedSite->walletEntries()->create([
                    'transaction_id' => 'swl_'.Str::lower(Str::random(24)), 'type' => 'debit', 'category' => 'feature',
                    'amount' => (string) $price, 'balance_before' => (string) $before, 'balance_after' => (string) $after,
                    'client_reference' => $reference, 'purpose' => "Feature purchase: {$lockedFeature->name}".($slotName ? " ({$slotName})" : ''),
                ]);
            }

            $subscription = StandaloneFeatureSubscription::updateOrCreate([
                'standalone_website_id' => $lockedSite->id, 'standalone_feature_id' => $lockedFeature->id,
                'slot_name' => $slotName, 'slot_key' => $slotKey,
            ], [
                'status' => 'active', 'current_period_starts_at' => $periodStarts, 'current_period_ends_at' => $periodEnds,
                'grace_ends_at' => null, 'cancelled_at' => null,
            ]);
            $purchase = StandaloneFeaturePurchase::create([
                'standalone_website_id' => $lockedSite->id, 'standalone_feature_id' => $lockedFeature->id,
                'slot_name' => $slotName, 'slot_key' => $slotKey,
                'standalone_wallet_entry_id' => $walletEntry?->id, 'transaction_id' => 'sfp_'.Str::lower(Str::random(24)),
                'client_reference' => $reference, 'billing_event' => 'purchase', 'amount' => (string) $price,
                'applied_price_level' => $lockedSite->price_level ? "level_{$lockedSite->price_level}" : 'default',
                'period_starts_at' => $periodStarts, 'period_ends_at' => $periodEnds,
            ]);

            return ['purchase' => $purchase, 'subscription' => $subscription, 'replay' => false];
        }, 3);
    }

    private function subscription(StandaloneWebsite $site, StandaloneFeature $feature, string $slotKey): ?StandaloneFeatureSubscription
    {
        return StandaloneFeatureSubscription::where('standalone_website_id', $site->id)
            ->where('standalone_feature_id', $feature->id)->where('slot_key', $slotKey)->first();
    }

    public function slotKey(string $name): string
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower(Str::ascii($name))) ?? '';
    }
}
