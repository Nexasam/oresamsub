<?php

namespace App\Services;

use App\Models\BonusLog;
use App\Models\Commissions;
use App\Models\FundingWebhookPayload;
use App\Models\Transaction;
use App\Models\UplineFundingBonusLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class BusinessProfitReportService
{
    public function generate(array $filters = []): array
    {
        $from = Carbon::parse($filters['from'] ?? now()->startOfMonth())->startOfDay();
        $to = Carbon::parse($filters['to'] ?? now()->endOfMonth())->endOfDay();
        $category = $filters['category'] ?? null;
        $automationId = $filters['automation_id'] ?? null;
        $fundingProvider = $filters['funding_provider'] ?? null;

        $successfulQuery = Transaction::query()
            ->with(['product_plan.automation'])
            ->where('status', '1')
            ->whereBetween('updated_at', [$from, $to])
            ->when($category, fn (Builder $query) => $query->where('transaction_category', $category))
            ->when($automationId, fn (Builder $query) => $query->where('automation_id', $automationId));

        $transactions = $successfulQuery->get();
        $transactionRevenue = 0.0;
        $providerCost = 0.0;
        $serviceCharges = 0.0;
        $estimatedCostCount = 0;
        $categoryRows = [];

        foreach ($transactions as $transaction) {
            $purchaseRevenue = is_numeric($transaction->discounted_amount) && (float) $transaction->discounted_amount > 0
                ? (float) $transaction->discounted_amount
                : (float) $transaction->amount;
            $serviceCharge = (float) ($transaction->service_charge ?? 0);
            $cost = $this->transactionCost($transaction);
            if (! is_numeric($transaction->automation_plan_amount) || (float) $transaction->automation_plan_amount <= 0) {
                $estimatedCostCount++;
            }

            $transactionRevenue += $purchaseRevenue + $serviceCharge;
            $serviceCharges += $serviceCharge;
            $providerCost += $cost;
            $key = $transaction->transaction_category ?: 'uncategorised';
            $categoryRows[$key] ??= ['transactions' => 0, 'revenue' => 0.0, 'cost' => 0.0, 'gross_profit' => 0.0];
            $categoryRows[$key]['transactions']++;
            $categoryRows[$key]['revenue'] += $purchaseRevenue + $serviceCharge;
            $categoryRows[$key]['cost'] += $cost;
            $categoryRows[$key]['gross_profit'] += ($purchaseRevenue + $serviceCharge) - $cost;
        }

        $transactionGrossProfit = round($transactionRevenue - $providerCost, 2);
        $transactionIds = $transactions->pluck('id');
        $commissions = $transactionIds->isEmpty()
            ? collect()
            : Commissions::query()->whereIn('transaction_id', $transactionIds)->get();
        $commissionAccrued = round((float) $commissions->sum('commission'), 2);
        $commissionAvailable = round((float) $commissions->where('status', '1')->sum('commission'), 2);
        $commissionPaid = round((float) $commissions->where('payout_status', '1')->sum('commission'), 2);

        $fundings = FundingWebhookPayload::query()
            ->where('funding_status', 'success')
            ->whereBetween('updated_at', [$from, $to])
            ->when($fundingProvider, fn (Builder $query) => $query->where('funding_slug', $fundingProvider))
            ->get();
        $fundingPaid = 0.0;
        $fundingProviderSettlement = 0.0;
        $fundingWalletCredit = 0.0;

        foreach ($fundings as $funding) {
            $fundingPaid += (float) $funding->amount_paid;
            $fundingProviderSettlement += $this->providerSettlement($funding);
            $fundingWalletCredit += (float) $funding->amount_settled;
        }
        $fundingNetMargin = round($fundingProviderSettlement - $fundingWalletCredit, 2);

        $bonusLogs = BonusLog::query()->whereBetween('created_at', [$from, $to])->get();
        $campaignWalletExpense = round((float) $bonusLogs->where('event_type', 'wallet_converted')->sum('amount'), 2);
        $campaignFundingRewards = round((float) $bonusLogs
            ->where('event_type', 'funding_reward')
            ->when($fundingProvider, fn ($logs) => $logs->where('funding_provider', $fundingProvider))
            ->sum('amount'), 2);
        $campaignAwards = round((float) $bonusLogs->where('event_type', 'entitlement_granted')->sum('amount'), 2);
        $campaignExpired = round((float) $bonusLogs->where('event_type', 'expired')->sum('amount'), 2);

        $uplineBonusExpense = round((float) UplineFundingBonusLog::query()
            ->whereBetween('created_at', [$from, $to])
            ->when($fundingProvider, fn (Builder $query) => $query->where('funding_provider', $fundingProvider))
            ->sum('bonus_amount'), 2);

        $refundedTotal = round((float) Transaction::query()
            ->where('status', '2')
            ->whereBetween('updated_at', [$from, $to])
            ->when($category, fn (Builder $query) => $query->where('transaction_category', $category))
            ->when($automationId, fn (Builder $query) => $query->where('automation_id', $automationId))
            ->get(['amount', 'discounted_amount'])
            ->sum(fn (Transaction $transaction) => is_numeric($transaction->discounted_amount)
                && (float) $transaction->discounted_amount > 0
                    ? (float) $transaction->discounted_amount
                    : (float) $transaction->amount), 2);
        $failedCount = Transaction::query()
            ->where('status', '-1')
            ->whereBetween('updated_at', [$from, $to])
            ->when($category, fn (Builder $query) => $query->where('transaction_category', $category))
            ->when($automationId, fn (Builder $query) => $query->where('automation_id', $automationId))
            ->count();

        $netProfit = round(
            $transactionGrossProfit
            + $fundingNetMargin
            - $commissionAccrued
            - $campaignWalletExpense
            - $uplineBonusExpense,
            2
        );
        $campaignLiabilityMovement = round($campaignAwards - $campaignWalletExpense - $campaignExpired, 2);
        $currentBonusWalletLiability = round((float) User::query()->sum('bonus_wallet'), 2);

        return [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'category' => $category,
                'automation_id' => $automationId,
                'funding_provider' => $fundingProvider,
            ],
            'summary' => [
                'net_profit' => $netProfit,
                'transaction_gross_profit' => $transactionGrossProfit,
                'funding_net_margin' => $fundingNetMargin,
                'transaction_revenue' => round($transactionRevenue, 2),
                'provider_cost' => round($providerCost, 2),
                'service_charges' => round($serviceCharges, 2),
                'commission_accrued' => $commissionAccrued,
                'commission_available' => $commissionAvailable,
                'commission_paid' => $commissionPaid,
                'campaign_wallet_expense' => $campaignWalletExpense,
                'campaign_funding_rewards_in_funding_margin' => $campaignFundingRewards,
                'upline_funding_bonus_expense' => $uplineBonusExpense,
                'campaign_liability_movement' => $campaignLiabilityMovement,
                'current_bonus_wallet_liability' => $currentBonusWalletLiability,
                // Backward-compatible alias for integrations consuming the original report.
                'outstanding_bonus_liability' => $campaignLiabilityMovement,
                'expired_bonus_released' => $campaignExpired,
                'refunded_volume' => $refundedTotal,
            ],
            'counts' => [
                'successful_transactions' => $transactions->count(),
                'failed_transactions' => $failedCount,
                'funding_transactions' => $fundings->count(),
                'estimated_provider_cost_transactions' => $estimatedCostCount,
            ],
            'funding' => [
                'customer_paid' => round($fundingPaid, 2),
                'provider_settlement' => round($fundingProviderSettlement, 2),
                'wallet_credit' => round($fundingWalletCredit, 2),
                'net_margin' => $fundingNetMargin,
            ],
            'categories' => collect($categoryRows)->map(fn (array $row, string $name) => [
                'category' => $name,
                'transactions' => $row['transactions'],
                'revenue' => round($row['revenue'], 2),
                'provider_cost' => round($row['cost'], 2),
                'gross_profit' => round($row['gross_profit'], 2),
            ])->values()->all(),
            'notes' => [
                'Funding net margin already includes customer funding incentives because it compares provider settlement with the final wallet credit.',
                'Provider cost is estimated from the product-plan cost where an actual automation amount was not stored.',
                'Current bonus-wallet liability is a live point-in-time value and is therefore not limited by the report date filter.',
                'Refunded volume is disclosed separately and is not deducted twice from transactions already excluded from successful revenue.',
            ],
        ];
    }

    private function transactionCost(Transaction $transaction): float
    {
        if (is_numeric($transaction->automation_plan_amount) && (float) $transaction->automation_plan_amount > 0) {
            return round((float) $transaction->automation_plan_amount, 2);
        }

        return round((float) ($transaction->product_plan?->cost_price ?? 0), 2);
    }

    private function providerSettlement(FundingWebhookPayload $funding): float
    {
        $payload = json_decode((string) $funding->payload_content, true) ?: [];

        return round((float) match ($funding->funding_slug) {
            'crystal_pay' => data_get($payload, 'event_data.data.settled', (float) $funding->amount_paid - (float) $funding->amount_charged),
            'xixapay', 'securewaveng' => data_get($payload, 'settlement_amount', (float) $funding->amount_paid - (float) $funding->amount_charged),
            default => (float) $funding->amount_paid - (float) $funding->amount_charged,
        }, 2);
    }
}
