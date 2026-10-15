<?php

namespace App\Http\Controllers;

use App\Http\Services\DataPlansService;
use App\Models\Network;
use App\Models\ProductPlan;
use App\Models\ProductPlanCustomPricing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductPlanCustomPricingController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->search);

        $product_plan_custom_pricings = ProductPlanCustomPricing::query()
            ->with(['user.user_plan', 'product_plan.product_plan_category.network'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search): void {
                    $searchQuery->whereHas('user', function ($userQuery) use ($search): void {
                        $userQuery->where('username', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    })->orWhereHas('product_plan', function ($planQuery) use ($search): void {
                        $planQuery->where('product_plan_name', 'like', "%{$search}%")
                            ->orWhere('api_id', 'like', "%{$search}%");
                    });
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->integer('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $networks = Network::orderBy('network_name')->get();
        $product_plans = ProductPlan::query()
            ->with(['product_plan_category.network'])
            ->where('visibility', 1)
            ->orderBy('product_plan_name')
            ->get();
        $salespeople = User::query()
            ->where('is_salesperson', true)
            ->orderBy('username')
            ->get(['id', 'username', 'first_name', 'last_name', 'phone_number']);

        return view('admin.product_plans.product_plan_custom_pricing.index', compact(
            'networks',
            'product_plan_custom_pricings',
            'product_plans',
            'salespeople',
            'search'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['nullable', 'string', 'required_without:usernames', 'exists:users,username'],
            'usernames' => ['nullable', 'array', 'required_without:username'],
            'usernames.*' => ['string', 'distinct', 'exists:users,username'],
            'product_plan_id' => ['required', 'exists:product_plans,id'],
            'status' => ['required', Rule::in([0, 1])],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $usernames = collect($validated['usernames'] ?? [])
            ->push($validated['username'] ?? null)
            ->filter()
            ->unique()
            ->values();

        foreach ($usernames as $username) {
            if ((int) $validated['status'] === 1 && ($failure = $this->validateMinimumCustomPrice($validated['product_plan_id'], $validated['price'], $username))) {
                return back()->with('failure', $failure.' Salesperson: '.$username.'.')->withInput();
            }
        }

        DB::transaction(function () use ($usernames, $validated): void {
            User::query()
                ->whereIn('username', $usernames)
                ->get()
                ->each(function (User $user) use ($validated): void {
                    ProductPlanCustomPricing::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'product_plan_id' => $validated['product_plan_id'],
                        ],
                        [
                            'price' => round((float) $validated['price'], 2),
                            'status' => (int) $validated['status'],
                            'added_by' => auth()->id(),
                        ]
                    );
                });
        });

        return back()->with('success', 'Salesperson pricing was saved for '.$usernames->count().' account(s).');
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'exists:users,username'],
            'product_plan_id' => ['required', 'exists:product_plans,id'],
            'status' => ['required', Rule::in([0, 1])],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        if ((int) $validated['status'] === 1 && ($failure = $this->validateMinimumCustomPrice($validated['product_plan_id'], $validated['price'], $validated['username']))) {
            return back()->with('failure', $failure)->withInput();
        }

        $pricing = ProductPlanCustomPricing::findOrFail($id);
        $user = User::where('username', $validated['username'])->firstOrFail();

        $duplicateExists = ProductPlanCustomPricing::query()
            ->where('user_id', $user->id)
            ->where('product_plan_id', $validated['product_plan_id'])
            ->whereKeyNot($pricing->id)
            ->exists();

        if ($duplicateExists) {
            return back()->with('failure', 'This salesperson already has pricing for the selected plan. Edit that existing row instead.');
        }

        $pricing->update([
            'product_plan_id' => $validated['product_plan_id'],
            'user_id' => $user->id,
            'price' => round((float) $validated['price'], 2),
            'status' => (int) $validated['status'],
        ]);

        return back()->with('success', "Salesperson pricing for {$user->username} was successfully updated.");
    }

    private function validateMinimumCustomPrice(string $productPlanId, mixed $price, ?string $username = null): ?string
    {
        $productPlan = ProductPlan::with('product_plan_category.product')->find($productPlanId);

        if (! $productPlan) {
            return 'Product plan not found.';
        }

        if (($productPlan->product_plan_category?->product?->slug ?? null) !== 'data') {
            return null;
        }

        $user = $username ? User::with('user_plan')->where('username', $username)->first() : null;
        $planLevel = max(1, min(12, (int) ($user?->user_plan?->plan_level ?? 1)));
        $normalPriceColumn = 'user_level_'.$planLevel.'_selling_price';
        $normalSellingPrice = (float) ($productPlan->{$normalPriceColumn} ?? $productPlan->user_level_1_selling_price ?? $productPlan->default_selling_price ?? 0);
        $costPrice = (float) $productPlan->cost_price;

        $minimumSellingPrice = DataPlansService::minimumDataSellingPriceForNormalPrice($productPlan, $normalSellingPrice);

        if ((float) $price < $minimumSellingPrice) {
            $rule = $normalSellingPrice > $costPrice
                ? 'cost plus ₦'.DataPlansService::DEFAULT_MINIMUM_DATA_PROFIT.' minimum margin'
                : 'cost price';

            return 'Salesperson price cannot be below ₦'.number_format($minimumSellingPrice, 2).' for this plan. Current cost is ₦'.number_format($costPrice, 2).' and the active minimum rule is '.$rule.'.';
        }

        return null;
    }
}
