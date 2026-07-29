<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mobile\V1\TransactionResource;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Services\BonusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileDashboardController extends Controller
{
    use RespondsToMobileApi;

    public function __invoke(Request $request, BonusService $bonuses): JsonResponse
    {
        $user = $request->user();
        $bonusSummary = $bonuses->summary($user, $request);
        $user->refresh();
        $recentTransactions = Transaction::query()
            ->with([
                'product_plan.product_plan_category.product',
                'product_plan.product_plan_category.network',
            ])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
        $recentDataTransactions = Transaction::query()
            ->where('user_id', $user->id)
            ->where('transaction_category', 'data')
            ->where('status', '1')
            ->whereNotNull('product_plan_id')
            ->latest()
            ->limit(100)
            ->get(['product_plan_id'])
            ->unique('product_plan_id')
            ->take(15);
        $plansById = ProductPlan::query()
            ->with(['product_plan_category.product', 'product_plan_category.network'])
            ->whereIn('id', $recentDataTransactions->pluck('product_plan_id'))
            ->where('visibility', '1')
            ->where('public_visibility', '1')
            ->where('active_status', '1')
            ->whereHas('product_plan_category', fn ($category) => $category
                ->where('visibility', '1')
                ->whereHas('product', fn ($product) => $product
                    ->where('slug', 'data')
                    ->where('visibility', '1')
                    ->where('active_status', '1')))
            ->get()
            ->keyBy('id');
        $level = min(7, max(1, (int) ($user->user_plan?->plan_level ?? 1)));
        $priceField = "user_level_{$level}_selling_price";
        $buyAgainPlans = $recentDataTransactions
            ->map(function (Transaction $transaction) use ($plansById, $priceField) {
                $plan = $plansById->get($transaction->product_plan_id);
                if (! $plan) {
                    return null;
                }

                $category = $plan->product_plan_category;

                return [
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->product_plan_name,
                    'price' => round((float) ($plan->{$priceField} ?: $plan->default_selling_price), 2),
                    'provider' => $category->network?->network_name ?? $category->product_plan_category_name,
                ];
            })
            ->filter()
            ->values();

        return $this->successResponse('Dashboard fetched successfully.', [
            'wallet' => [
                'currency' => 'NGN',
                'balance' => round((float) $user->main_wallet, 2),
                'bonus_balance' => round((float) $user->bonus_wallet, 2),
            ],
            'bonus' => $bonusSummary,
            'summary' => [
                'total_transactions' => Transaction::where('user_id', $user->id)->count(),
                'successful_transactions' => Transaction::where('user_id', $user->id)->where('status', '1')->count(),
                'pending_transactions' => Transaction::where('user_id', $user->id)->whereIn('status', ['0', '3'])->count(),
            ],
            'recent_transactions' => TransactionResource::collection($recentTransactions)->resolve($request),
            'buy_again_plans' => $buyAgainPlans,
        ]);
    }
}
