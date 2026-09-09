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
    public function handle(StandaloneWebsite $site, StandaloneFeature $feature, string $reference): array
    {
        return DB::transaction(function () use ($site, $feature, $reference): array {
            $lockedSite = StandaloneWebsite::whereKey($site->id)->lockForUpdate()->firstOrFail();
            $lockedFeature = StandaloneFeature::whereKey($feature->id)->lockForUpdate()->firstOrFail();
            $price = BigDecimal::of($lockedFeature->purchasePriceFor($lockedSite->price_level))->toScale(2);
            $existing = StandaloneFeaturePurchase::where('standalone_website_id', $lockedSite->id)
                ->where('client_reference', $reference)->first();

            if ($existing) {
                if ($existing->standalone_feature_id !== $lockedFeature->id || $existing->amount !== (string) $price) {
                    throw new RuntimeException('reference_conflict');
                }

                return ['purchase' => $existing, 'subscription' => $this->subscription($lockedSite, $lockedFeature), 'replay' => true];
            }
            if (! $lockedFeature->is_active) {
                throw new RuntimeException('feature_inactive');
            }
            if ($lockedSite->walletEntries()->where('client_reference', $reference)->exists()) {
                throw new RuntimeException('reference_conflict');
            }

            $subscription = $this->subscription($lockedSite, $lockedFeature);
            if ($subscription && $subscription->status === 'active') {
                throw new RuntimeException('already_owned');
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
                    'client_reference' => $reference, 'purpose' => "Feature purchase: {$lockedFeature->name}",
                ]);
            }

            $subscription = StandaloneFeatureSubscription::updateOrCreate([
                'standalone_website_id' => $lockedSite->id, 'standalone_feature_id' => $lockedFeature->id,
            ], [
                'status' => 'active', 'current_period_starts_at' => $periodStarts, 'current_period_ends_at' => $periodEnds,
                'grace_ends_at' => null, 'cancelled_at' => null,
            ]);
            $purchase = StandaloneFeaturePurchase::create([
                'standalone_website_id' => $lockedSite->id, 'standalone_feature_id' => $lockedFeature->id,
                'standalone_wallet_entry_id' => $walletEntry?->id, 'transaction_id' => 'sfp_'.Str::lower(Str::random(24)),
                'client_reference' => $reference, 'billing_event' => 'purchase', 'amount' => (string) $price,
                'applied_price_level' => $lockedSite->price_level ? "level_{$lockedSite->price_level}" : 'default',
                'period_starts_at' => $periodStarts, 'period_ends_at' => $periodEnds,
            ]);

            return ['purchase' => $purchase, 'subscription' => $subscription, 'replay' => false];
        }, 3);
    }

    private function subscription(StandaloneWebsite $site, StandaloneFeature $feature): ?StandaloneFeatureSubscription
    {
        return StandaloneFeatureSubscription::where('standalone_website_id', $site->id)
            ->where('standalone_feature_id', $feature->id)->first();
    }
}
