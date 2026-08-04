<?php

namespace App\Services;

use App\Models\Bonus;
use App\Models\BonusLog;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WeeklyBonusReward;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;

class WeeklyTransactionBonusService
{
    public function processWeek(DateTimeInterface|string $weekStart): array
    {
        $start = CarbonImmutable::parse($weekStart, 'Africa/Lagos')->startOfWeek()->startOfDay();
        $end = $start->endOfWeek()->endOfDay();
        $rewarded = 0;
        $totalAmount = 0.0;

        $campaigns = Bonus::query()
            ->where('status', true)
            ->where('group', Bonus::GROUP_WEEKLY_TRANSACTION_VOLUME)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $end))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', $start))
            ->orderByDesc('priority')
            ->get();

        foreach ($campaigns as $campaign) {
            $conditions = $campaign->conditions ?? [];
            $minimum = round((float) ($conditions['weekly_minimum_volume'] ?? 0), 2);
            $categories = ($conditions['weekly_category_scope'] ?? 'all') === 'selected'
                ? array_values(array_filter($conditions['weekly_categories'] ?? []))
                : [];
            $transactionStart = $campaign->starts_at && $campaign->starts_at->greaterThan($start)
                ? CarbonImmutable::instance($campaign->starts_at)
                : $start;
            $transactionEnd = $campaign->ends_at && $campaign->ends_at->lessThan($end)
                ? CarbonImmutable::instance($campaign->ends_at)
                : $end;

            $volumes = Transaction::query()
                ->selectRaw('user_id, SUM(amount * 1.0) as qualifying_volume')
                ->where('status', '1')
                ->whereBetween('created_at', [$transactionStart->toDateTimeString(), $transactionEnd->toDateTimeString()])
                ->when($categories !== [], fn ($query) => $query->whereIn('transaction_category', $categories))
                ->groupBy('user_id')
                ->get()
                ->filter(fn ($row) => round((float) $row->qualifying_volume, 2) >= $minimum);

            foreach ($volumes as $volumeRow) {
                $user = User::query()->find($volumeRow->user_id);
                if (! $user || ! $user->hasVerifiedEmail() || (bool) $user->is_deactivated) {
                    continue;
                }
                if ($campaign->isTargeted() && ! $campaign->targets($user)) {
                    continue;
                }
                if (WeeklyBonusReward::query()->where('bonus_id', $campaign->id)->where('user_id', $user->id)->whereDate('week_start', $start->toDateString())->exists()) {
                    continue;
                }

                $volume = round((float) $volumeRow->qualifying_volume, 2);
                $reward = $campaign->funding_type === 'percent'
                    ? round($volume * (float) $campaign->funding_value / 100, 2)
                    : round((float) $campaign->funding_value, 2);
                if ($campaign->funding_type === 'percent' && $campaign->funding_cap !== null) {
                    $reward = min($reward, round((float) $campaign->funding_cap, 2));
                }
                if ($reward <= 0) {
                    continue;
                }

                $credited = DB::transaction(function () use ($campaign, $user, $start, $end, $volume, $reward, $categories): bool {
                    $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
                    if (WeeklyBonusReward::query()->where('bonus_id', $campaign->id)->where('user_id', $lockedUser->id)->whereDate('week_start', $start->toDateString())->exists()) {
                        return false;
                    }
                    $before = round((float) $lockedUser->bonus_wallet, 2);
                    $after = round($before + $reward, 2);
                    $weeklyReward = WeeklyBonusReward::create([
                        'bonus_id' => $campaign->id,
                        'user_id' => $lockedUser->id,
                        'week_start' => $start->toDateString(),
                        'week_end' => $end->toDateString(),
                        'qualifying_volume' => $volume,
                        'reward_amount' => $reward,
                        'transaction_categories' => $categories ?: null,
                    ]);
                    $lockedUser->update(['bonus_wallet' => $after]);
                    BonusLog::create([
                        'bonus_id' => $campaign->id,
                        'bonus_entitlement_id' => null,
                        'user_id' => $lockedUser->id,
                        'event_type' => 'weekly_volume_reward',
                        'amount' => $reward,
                        'balance_before' => $before,
                        'balance_after' => $after,
                        'event_key' => "weekly-volume:{$campaign->id}:{$lockedUser->id}:{$start->toDateString()}",
                        'metadata' => [
                            'weekly_bonus_reward_id' => $weeklyReward->id,
                            'week_start' => $start->toDateString(),
                            'week_end' => $end->toDateString(),
                            'qualifying_volume' => $volume,
                            'transaction_categories' => $categories ?: ['all'],
                        ],
                    ]);

                    return true;
                });

                if (! $credited) {
                    continue;
                }

                $rewarded++;
                $totalAmount = round($totalAmount + $reward, 2);
            }
        }

        return ['rewarded' => $rewarded, 'amount' => $totalAmount, 'week_start' => $start->toDateString(), 'week_end' => $end->toDateString()];
    }
}
