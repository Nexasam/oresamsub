<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileCatalogueController extends Controller
{
    use RespondsToMobileApi;

    public function products(): JsonResponse
    {
        $disabledSlugs = collect(['data', 'airtime', 'cable_subscription', 'utility_bills'])->reject(fn ($slug) => match ($slug) {
            'cable_subscription' => config('mobile.features.cable'), 'utility_bills' => config('mobile.features.electricity'), default => config("mobile.features.$slug"),
        });
        $products = Product::query()
            ->where('visibility', '1')
            ->where('active_status', '1')
            ->whereNotIn('slug', $disabledSlugs)
            ->whereNotIn('slug', config('mobile.hidden_product_slugs', []))
            ->orderBy('product_name')
            ->get(['id', 'slug', 'product_name'])
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->product_name,
            ]);

        return $this->successResponse('Products fetched successfully.', $products);
    }

    public function categories(Request $request): JsonResponse
    {
        $request->validate(['product' => ['nullable', 'string', 'max:100']]);

        $categories = ProductPlanCategory::query()
            ->with(['product:id,slug,product_name', 'network:id,network_name'])
            ->where('visibility', '1')
            ->when($request->filled('product'), fn ($query) => $query->whereHas(
                'product',
                fn ($product) => $product->where('slug', $request->string('product'))
                    ->where('visibility', '1')->where('active_status', '1')
            ))
            ->whereHas('product', fn ($query) => $query->where('visibility', '1')->where('active_status', '1'))
            ->orderBy('product_plan_category_name')
            ->get()
            ->map(fn (ProductPlanCategory $category) => [
                'id' => $category->id,
                'name' => $category->product_plan_category_name,
                'is_hot_sale' => $category->is_hot_sales,
                'product' => [
                    'id' => $category->product->id,
                    'slug' => $category->product->slug,
                    'name' => $category->product->product_name,
                ],
                'network' => $category->network ? [
                    'id' => $category->network->id,
                    'name' => $category->network->network_name,
                ] : null,
            ]);

        return $this->successResponse('Product categories fetched successfully.', $categories);
    }

    public function plans(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'uuid', 'exists:product_plan_categories,id'],
        ]);
        $level = min(7, max(1, (int) ($request->user()->user_plan?->plan_level ?? 1)));
        $priceField = "user_level_{$level}_selling_price";

        $plans = ProductPlan::query()
            ->where('product_plan_category_id', $validated['category_id'])
            ->where('visibility', '1')
            ->where('public_visibility', '1')
            ->where('active_status', '1')
            ->orderByRaw('CAST(default_selling_price AS DECIMAL(12,2))')
            ->get()
            ->map(fn (ProductPlan $plan) => [
                'id' => $plan->id,
                'name' => $plan->product_plan_name,
                'price' => round((float) ($plan->{$priceField} ?: $plan->default_selling_price), 2),
                'data_size_mb' => $plan->data_size_in_mb ? (int) $plan->data_size_in_mb : null,
                'validity_days' => $plan->validity_in_days ? (int) $plan->validity_in_days : null,
            ]);

        return $this->successResponse('Product plans fetched successfully.', $plans);
    }
}
