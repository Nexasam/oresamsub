<?php

namespace App\Services\AccountOfficers;

use App\Models\AccountOfficerProfile;
use App\Models\CustomerOfficerAssignment;
use App\Models\OfficerAssignmentBatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WeightedCustomerAllocator
{
    public function allocate(User $initiator, string $type = 'unassigned'): OfficerAssignmentBatch
    {
        return DB::transaction(function () use ($initiator, $type) {
            $profiles = AccountOfficerProfile::query()->with('user')->where('is_active', true)->lockForUpdate()->get();
            $totalWeight = (float) $profiles->sum('allocation_weight');
            if ($profiles->isEmpty() || abs($totalWeight - 100.0) > 0.001) {
                throw new InvalidArgumentException('Active account officer weights must total 100%.');
            }

            $customers = User::query()
                ->whereKeyNot($initiator->id)
                ->whereHas('role', fn ($query) => $query->where('role_name', 'User'))
                ->whereDoesntHave('officerAssignments', fn ($query) => $query->whereNull('ended_at'))
                ->whereDoesntHave('accountOfficerProfile')
                ->orderBy('created_at')->orderBy('id')->lockForUpdate()->get();

            $counts = $this->counts($profiles, $customers->count());
            $batch = OfficerAssignmentBatch::create([
                'type' => $type,
                'configuration' => $profiles->mapWithKeys(fn ($profile) => [$profile->user_id => (float) $profile->allocation_weight])->all(),
                'total_customers' => $customers->count(),
                'initiated_by' => $initiator->id,
            ]);

            $offset = 0;
            foreach ($profiles as $profile) {
                foreach ($customers->slice($offset, $counts[$profile->id]) as $customer) {
                    CustomerOfficerAssignment::create([
                        'customer_id' => $customer->id,
                        'officer_id' => $profile->user_id,
                        'batch_id' => $batch->id,
                        'assigned_by' => $initiator->id,
                        'started_at' => now(),
                    ]);
                }
                $offset += $counts[$profile->id];
            }

            return $batch;
        }, 3);
    }

    private function counts($profiles, int $total): array
    {
        $rows = $profiles->map(function ($profile) use ($total) {
            $exact = $total * (float) $profile->allocation_weight / 100;
            return ['id' => $profile->id, 'count' => (int) floor($exact), 'remainder' => $exact - floor($exact)];
        });
        $remaining = $total - $rows->sum('count');
        foreach ($rows->sortByDesc('remainder')->take($remaining) as $row) {
            $index = $rows->search(fn ($candidate) => $candidate['id'] === $row['id']);
            $rows[$index] = [...$rows[$index], 'count' => $rows[$index]['count'] + 1];
        }
        return $rows->pluck('count', 'id')->all();
    }
}
