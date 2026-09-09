<?php

namespace App\Services\Standalone;

use App\Models\StandaloneFeaturePurchase;
use App\Models\StandaloneFeatureSubscription;
use App\Models\StandaloneWebsite;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RenewStandaloneFeatures
{
    public function handle(): array
    {
        $results = ['renewed' => 0, 'past_due' => 0, 'suspended' => 0, 'cancelled' => 0];
        StandaloneFeatureSubscription::with(['feature', 'standaloneWebsite'])
            ->whereIn('status', ['active', 'past_due', 'suspended'])->chunkById(100, function ($subscriptions) use (&$results): void {
                foreach ($subscriptions as $subscription) {
                    $outcome = $this->process($subscription);
                    if ($outcome) {
                        $results[$outcome]++;
                    }
                }
            });

        return $results;
    }

    private function process(StandaloneFeatureSubscription $subscription): ?string
    {
        if (! $subscription->feature->isRecurring()) {
            return null;
        }
        if ($subscription->status === 'active' && $subscription->current_period_ends_at?->isFuture()) {
            return null;
        }

        return DB::transaction(function () use ($subscription): ?string {
            $locked = StandaloneFeatureSubscription::whereKey($subscription->id)->lockForUpdate()->firstOrFail();
            if ($locked->cancel_at_period_end && $locked->current_period_ends_at?->isPast()) {
                $locked->update(['status' => 'cancelled', 'cancelled_at' => now(), 'grace_ends_at' => null]);

                return 'cancelled';
            }
            $site = StandaloneWebsite::whereKey($locked->standalone_website_id)->lockForUpdate()->firstOrFail();
            $feature = $locked->feature()->lockForUpdate()->firstOrFail();
            $price = BigDecimal::of($feature->monthlyPriceFor($site->price_level))->toScale(2);
            $before = BigDecimal::of($site->master_wallet)->toScale(2);
            if ($before->isLessThan($price)) {
                if (! $locked->grace_ends_at) {
                    $locked->update(['status' => 'past_due', 'grace_ends_at' => now()->addDays(7)]);

                    return 'past_due';
                }
                if ($locked->grace_ends_at->isPast()) {
                    $locked->update(['status' => 'suspended']);

                    return 'suspended';
                }

                return null;
            }

            $reference = 'feature-renewal-'.$locked->id.'-'.$locked->current_period_ends_at?->timestamp;
            if (StandaloneFeaturePurchase::where('standalone_website_id', $site->id)->where('client_reference', $reference)->exists()) {
                return null;
            }
            $after = $before->minus($price)->toScale(2);
            $site->update(['master_wallet' => (string) $after]);
            $entry = $site->walletEntries()->create([
                'transaction_id' => 'swl_'.Str::lower(Str::random(24)), 'type' => 'debit', 'category' => 'feature',
                'amount' => (string) $price, 'balance_before' => (string) $before, 'balance_after' => (string) $after,
                'client_reference' => $reference, 'purpose' => "Feature renewal: {$feature->name}".($locked->slot_name ? " ({$locked->slot_name})" : ''),
            ]);
            $periodStarts = now();
            $periodEnds = $periodStarts->copy()->addMonthNoOverflow();
            StandaloneFeaturePurchase::create([
                'standalone_website_id' => $site->id, 'standalone_feature_id' => $feature->id,
                'slot_name' => $locked->slot_name, 'slot_key' => $locked->slot_key,
                'standalone_wallet_entry_id' => $entry->id, 'transaction_id' => 'sfp_'.Str::lower(Str::random(24)),
                'client_reference' => $reference, 'billing_event' => 'renewal', 'amount' => (string) $price,
                'applied_price_level' => $site->price_level ? "level_{$site->price_level}" : 'default',
                'period_starts_at' => $periodStarts, 'period_ends_at' => $periodEnds,
            ]);
            $locked->update(['status' => 'active', 'current_period_starts_at' => $periodStarts,
                'current_period_ends_at' => $periodEnds, 'grace_ends_at' => null]);

            return 'renewed';
        }, 3);
    }
}
