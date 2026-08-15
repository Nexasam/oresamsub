<?php

namespace App\Http\Controllers;

use App\Models\CustomerFollowupCall;
use App\Models\AccountOfficerProfile;
use App\Models\CustomerOfficerAssignment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DailyCustomerFollowupController extends Controller
{
    private const DEFAULT_INACTIVE_DAYS = 30;
    private const DEFAULT_PURCHASE_COUNT = 3;
    private const DEFAULT_ACTIVITY_DAYS = 30;

    public function index(Request $request)
    {
        $validated = $request->validate([
            'customer_type' => ['nullable', Rule::in(['all', 'generic', 'pos'])],
            'segment' => ['nullable', Rule::in(['all', 'stale', 'suddenly_inactive', 'never_purchased'])],
            'inactivity_mode' => ['nullable', Rule::in(['days', 'period'])],
            'inactive_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'last_purchase_from' => ['nullable', 'date'],
            'last_purchase_to' => ['nullable', 'date', 'after_or_equal:last_purchase_from'],
            'purchase_count' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'activity_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', Rule::in([15, 30, 50, 100])],
            'officer_id' => ['nullable', 'uuid', 'exists:users,id'],
            'performance_from' => ['nullable', 'date'],
            'performance_to' => ['nullable', 'date', 'after_or_equal:performance_from'],
        ]);

        $filters = array_merge([
            'customer_type' => 'all',
            'segment' => 'all',
            'inactivity_mode' => 'days',
            'inactive_days' => self::DEFAULT_INACTIVE_DAYS,
            'last_purchase_from' => null,
            'last_purchase_to' => null,
            'purchase_count' => self::DEFAULT_PURCHASE_COUNT,
            'activity_days' => self::DEFAULT_ACTIVITY_DAYS,
            'search' => null,
            'per_page' => 30,
            'officer_id' => null,
            'performance_from' => now()->startOfMonth()->toDateString(),
            'performance_to' => now()->toDateString(),
        ], $validated);

        $customers = $this->retentionQuery($filters)
            ->paginate((int) $filters['per_page'])
            ->withQueryString();

        $officers = auth()->user()->hasPermission('followups.view_all')
            ? AccountOfficerProfile::with('user')->orderByDesc('is_active')->get()
            : collect();
        $performance = $this->performance($filters);

        return view('admin.daily_customer_followup.index', compact('customers', 'filters', 'officers', 'performance'));
    }

    public function storeCall(Request $request, User $customer)
    {
        $assignment = $customer->currentOfficerAssignment;
        abort_unless($request->user()->hasPermission('followups.view_all') || $assignment?->officer_id === $request->user()->id, 403);
        $validated = $request->validate([
            'outcome' => ['required', Rule::in(['answered', 'no_answer', 'busy', 'unreachable', 'wrong_number'])],
            'feedback' => ['required', 'string', 'max:5000'],
            'followup_status' => ['required', Rule::in(['follow_up_again', 'resolved_reactivated', 'not_interested'])],
            'next_followup_at' => [
                'nullable',
                'required_if:followup_status,follow_up_again',
                'date',
                'after_or_equal:now',
            ],
        ]);

        CustomerFollowupCall::query()->create($validated + [
            'customer_id' => $customer->id,
            'called_by' => $request->user()->id,
            'account_officer_id' => $assignment?->officer_id,
        ]);

        return back()->with('success', 'Customer call logged successfully.');
    }

    private function retentionQuery(array $filters): Builder
    {
        $successful = static fn (Builder $query) => $query->where('status', 1);

        $query = User::query()
            ->whereHas('role', fn (Builder $builder) => $builder->where('role_name', 'User'))
            ->with([
                'latestFollowupCall.caller',
                'followupCalls' => fn ($calls) => $calls->with('caller')->latest(),
            ])
            ->withMax(['transactions as last_successful_purchase_at' => $successful], 'created_at')
            ->withCount(['transactions as successful_purchase_count' => $successful])
            ->withCount(['transactions as activity_window_purchase_count' => function (Builder $builder) use ($filters) {
                $inactiveSince = now()->subDays((int) $filters['inactive_days']);
                $builder->where('status', 1)
                    ->whereBetween('created_at', [
                        $inactiveSince->copy()->subDays((int) $filters['activity_days']),
                        $inactiveSince,
                    ]);
            }])
            ->addSelect([
                'latest_followup_at' => CustomerFollowupCall::query()
                    ->select('next_followup_at')
                    ->whereColumn('customer_id', 'users.id')
                    ->latest('created_at')
                    ->limit(1),
                'latest_followup_status' => CustomerFollowupCall::query()
                    ->select('followup_status')
                    ->whereColumn('customer_id', 'users.id')
                    ->latest('created_at')
                    ->limit(1),
            ]);

        if (! auth()->user()->hasPermission('followups.view_all')) {
            $query->whereHas('officerAssignments', fn (Builder $builder) => $builder->where('officer_id', auth()->id())->whereNull('ended_at'));
        } elseif ($filters['officer_id']) {
            $query->whereHas('officerAssignments', fn (Builder $builder) => $builder->where('officer_id', $filters['officer_id'])->whereNull('ended_at'));
        }

        if ($filters['customer_type'] !== 'all') {
            $query->where('customer_category', $filters['customer_type']);
        }

        if ($filters['search']) {
            $search = '%'.trim($filters['search']).'%';
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('first_name', 'like', $search)
                    ->orWhere('last_name', 'like', $search)
                    ->orWhere('username', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('phone_number', 'like', $search);
            });
        }

        if ($filters['segment'] === 'never_purchased') {
            $query->whereDoesntHave('transactions', $successful);
        } elseif ($filters['segment'] === 'suddenly_inactive') {
            $this->applySuddenlyInactive($query, $filters);
        } elseif ($filters['inactivity_mode'] === 'period') {
            $this->applyLastPurchasePeriod($query, $filters);
        } elseif ($filters['segment'] === 'stale') {
            $cutoff = now()->subDays((int) $filters['inactive_days']);
            $query->whereDoesntHave('transactions', function (Builder $builder) use ($cutoff) {
                $builder->where('status', 1)->where('created_at', '>', $cutoff);
            });
        }

        return $query
            ->orderByRaw("CASE WHEN latest_followup_status = 'follow_up_again' AND latest_followup_at <= ? THEN 0 ELSE 1 END", [now()])
            ->orderByRaw('last_successful_purchase_at IS NOT NULL')
            ->orderBy('last_successful_purchase_at')
            ->orderBy('users.created_at');
    }

    private function performance(array $filters): array
    {
        $officerId = auth()->user()->hasPermission('followups.view_all')
            ? ($filters['officer_id'] ?: null)
            : auth()->id();
        $assignments = CustomerOfficerAssignment::query()->whereNull('ended_at');
        if ($officerId) $assignments->where('officer_id', $officerId);
        $customerIds = $assignments->pluck('customer_id');
        $from = Carbon::parse($filters['performance_from'])->startOfDay();
        $to = Carbon::parse($filters['performance_to'])->endOfDay();
        $calls = CustomerFollowupCall::query()->whereIn('customer_id', $customerIds)->whereBetween('created_at', [$from, $to]);
        $latestCalls = CustomerFollowupCall::query()->whereIn('customer_id', $customerIds)->latest()->get()->unique('customer_id');
        $overdue = $latestCalls->filter(fn ($call) => $call->followup_status === 'follow_up_again' && $call->next_followup_at?->isPast())->count();
        $staleCutoff = now()->subDays((int) $filters['inactive_days']);
        $stale = User::whereIn('id', $customerIds)->whereDoesntHave('transactions', fn ($q) => $q->where('status', 1)->where('created_at', '>', $staleCutoff))->count();
        $reactivated = User::whereIn('id', $customerIds)->withMax(['transactions as last_success' => fn ($q) => $q->where('status', 1)], 'created_at')->withMax('followupCalls as last_call', 'created_at')->get()->filter(fn ($user) => $user->last_call && $user->last_success && Carbon::parse($user->last_success)->greaterThan(Carbon::parse($user->last_call)))->count();
        $portfolio = $customerIds->unique()->count();
        $contacted = (clone $calls)->distinct()->count('customer_id');
        return compact('portfolio', 'contacted', 'overdue', 'stale', 'reactivated') + ['contact_rate' => $portfolio ? round($contacted / $portfolio * 100, 1) : 0];
    }

    private function applySuddenlyInactive(Builder $query, array $filters): void
    {
        $inactiveSince = now()->subDays((int) $filters['inactive_days']);
        $activityStarted = $inactiveSince->copy()->subDays((int) $filters['activity_days']);

        $query
            ->whereDoesntHave('transactions', function (Builder $builder) use ($inactiveSince) {
                $builder->where('status', 1)->where('created_at', '>', $inactiveSince);
            })
            ->whereHas('transactions', function (Builder $builder) use ($activityStarted, $inactiveSince) {
                $builder->where('status', 1)
                    ->where('created_at', '>=', $activityStarted)
                    ->where('created_at', '<=', $inactiveSince);
            }, '>=', (int) $filters['purchase_count']);
    }

    private function applyLastPurchasePeriod(Builder $query, array $filters): void
    {
        if (! $filters['last_purchase_from'] || ! $filters['last_purchase_to']) {
            return;
        }

        $from = Carbon::parse($filters['last_purchase_from'])->startOfDay();
        $to = Carbon::parse($filters['last_purchase_to'])->endOfDay();

        $query
            ->whereHas('transactions', function (Builder $builder) use ($from, $to) {
                $builder->where('status', 1)->whereBetween('created_at', [$from, $to]);
            })
            ->whereDoesntHave('transactions', function (Builder $builder) use ($to) {
                $builder->where('status', 1)->where('created_at', '>', $to);
            });
    }
}
