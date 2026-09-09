<?php

namespace App\Services\Standalone;

use App\Models\StandaloneWebsite;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class DeductStandaloneWallet
{
    public function handle(StandaloneWebsite $site, string $amount, string $reference, string $purpose): array
    {
        return DB::transaction(function () use ($site, $amount, $reference, $purpose): array {
            $locked = StandaloneWebsite::whereKey($site->id)->lockForUpdate()->firstOrFail();
            $deduction = BigDecimal::of($amount)->toScale(2);
            $existing = $locked->walletEntries()->where('client_reference', $reference)->first();

            if ($existing) {
                if ($existing->amount !== (string) $deduction || $existing->purpose !== $purpose) {
                    throw new RuntimeException('reference_conflict');
                }

                return ['entry' => $existing, 'replay' => true];
            }

            $before = BigDecimal::of($locked->master_wallet)->toScale(2);
            if ($before->isLessThan($deduction)) {
                throw new RuntimeException('insufficient_balance');
            }
            $after = $before->minus($deduction)->toScale(2);
            $locked->update(['master_wallet' => (string) $after]);
            $entry = $locked->walletEntries()->create([
                'transaction_id' => 'swl_'.Str::lower(Str::random(24)),
                'type' => 'debit', 'category' => 'deduction', 'amount' => (string) $deduction,
                'balance_before' => (string) $before, 'balance_after' => (string) $after,
                'client_reference' => $reference, 'purpose' => $purpose,
            ]);

            return ['entry' => $entry, 'replay' => false];
        }, 3);
    }
}
