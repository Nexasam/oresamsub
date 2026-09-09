<?php

namespace App\Http\Controllers\Api\v1\Standalone;

use App\Http\Controllers\Controller;
use App\Models\StandaloneFeature;
use App\Models\StandaloneFeatureSubscription;
use App\Models\StandaloneWebsite;
use App\Services\Standalone\PurchaseStandaloneFeature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class FeatureController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $site = $this->site($request);
        $subscriptions = $site->featureSubscriptions()->get()->groupBy('standalone_feature_id');
        $features = StandaloneFeature::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get()
            ->map(fn (StandaloneFeature $feature): array => $this->featureData($feature, $site, $subscriptions->get($feature->id, collect())));

        return response()->json(['success' => true, 'data' => $features]);
    }

    public function show(Request $request, string $feature): JsonResponse
    {
        $site = $this->site($request);
        $model = StandaloneFeature::where('slug', $feature)->where('is_active', true)->firstOrFail();
        $subscriptions = $site->featureSubscriptions()->where('standalone_feature_id', $model->id)->get();

        return response()->json(['success' => true, 'data' => $this->featureData($model, $site, $subscriptions)]);
    }

    public function purchase(Request $request, string $feature, PurchaseStandaloneFeature $purchase): JsonResponse
    {
        $model = StandaloneFeature::where('slug', $feature)->firstOrFail();
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9._:-]+$/'],
            'slot_name' => [Rule::requiredIf($model->purchase_mode === 'named_slots'), 'nullable', 'string', 'max:150', 'regex:/[A-Za-z0-9]/'],
        ]);
        try {
            $result = $purchase->handle($this->site($request), $model, trim($data['reference']), $data['slot_name'] ?? null);
        } catch (RuntimeException $exception) {
            return match ($exception->getMessage()) {
                'insufficient_balance' => response()->json(['success' => false, 'message' => 'Insufficient master wallet balance.'], 422),
                'reference_conflict' => response()->json(['success' => false, 'message' => 'This reference was already used for a different feature purchase.'], 409),
                'already_owned' => response()->json(['success' => false, 'message' => 'This one-time feature is already active.'], 409),
                'named_slot_owned' => response()->json(['success' => false, 'message' => 'This named feature slot is already active.'], 409),
                'feature_inactive' => response()->json(['success' => false, 'message' => 'This feature is not available.'], 409),
                default => throw $exception,
            };
        }

        return response()->json(['success' => true, 'data' => $this->purchaseData($result['purchase'], $result['subscription']),
            'meta' => ['idempotent_replay' => $result['replay']]], $result['replay'] ? 200 : 201);
    }

    public function purchases(Request $request): JsonResponse
    {
        $purchases = $this->site($request)->featurePurchases()->with('feature')->latest()->paginate(min(max($request->integer('per_page', 30), 1), 100));

        return response()->json(['success' => true, 'data' => collect($purchases->items())->map(fn ($purchase) => [
            'transaction_id' => $purchase->transaction_id, 'reference' => $purchase->client_reference,
            'feature' => $purchase->feature->slug, 'slot_name' => $purchase->slot_name, 'billing_event' => $purchase->billing_event,
            'amount' => $purchase->amount, 'applied_price_level' => $purchase->applied_price_level,
            'period_starts_at' => $purchase->period_starts_at?->toIso8601String(),
            'period_ends_at' => $purchase->period_ends_at?->toIso8601String(), 'created_at' => $purchase->created_at?->toIso8601String(),
        ]), 'meta' => ['current_page' => $purchases->currentPage(), 'last_page' => $purchases->lastPage(), 'total' => $purchases->total()]]);
    }

    public function cancel(Request $request, string $feature): JsonResponse
    {
        $model = StandaloneFeature::where('slug', $feature)->firstOrFail();
        $slotKey = $model->purchase_mode === 'named_slots'
            ? app(PurchaseStandaloneFeature::class)->slotKey((string) $request->validate(['slot_name' => ['required', 'string', 'max:150']])['slot_name'])
            : '__single__';
        $subscription = $this->site($request)->featureSubscriptions()->where('standalone_feature_id', $model->id)->where('slot_key', $slotKey)->firstOrFail();
        if (! $model->isRecurring()) {
            return response()->json(['success' => false, 'message' => 'Only monthly features can be cancelled.'], 409);
        }
        $subscription->update(['cancel_at_period_end' => true]);

        return response()->json(['success' => true, 'message' => 'The feature will be cancelled at the end of its paid period.',
            'data' => ['status' => $subscription->status, 'cancel_at_period_end' => true,
                'period_ends_at' => $subscription->current_period_ends_at?->toIso8601String()]]);
    }

    private function site(Request $request): StandaloneWebsite
    {
        return $request->attributes->get('standaloneWebsite');
    }

    private function featureData(StandaloneFeature $feature, StandaloneWebsite $site, $subscriptions): array
    {
        $subscription = $feature->purchase_mode === 'single' ? $subscriptions->first() : null;

        return ['slug' => $feature->slug, 'name' => $feature->name, 'description' => $feature->description,
            'billing_type' => $feature->billing_type, 'purchase_mode' => $feature->purchase_mode, 'price' => $feature->purchasePriceFor($site->price_level),
            'setup_price' => $feature->billing_type === 'monthly' ? null : $feature->priceFor($site->price_level),
            'monthly_price' => $feature->isRecurring() ? $feature->monthlyPriceFor($site->price_level) : null,
            'applied_price_level' => $site->price_level ? "level_{$site->price_level}" : 'default',
            'status' => $subscription?->status ?? 'available', 'current_period_ends_at' => $subscription?->current_period_ends_at?->toIso8601String(),
            'grace_ends_at' => $subscription?->grace_ends_at?->toIso8601String(), 'cancel_at_period_end' => $subscription?->cancel_at_period_end ?? false,
            'purchased_slots' => $feature->purchase_mode === 'named_slots' ? $subscriptions->map(fn ($slot) => [
                'slot_name' => $slot->slot_name, 'status' => $slot->status,
                'current_period_ends_at' => $slot->current_period_ends_at?->toIso8601String(),
                'grace_ends_at' => $slot->grace_ends_at?->toIso8601String(), 'cancel_at_period_end' => $slot->cancel_at_period_end,
            ])->values() : []];
    }

    private function purchaseData($purchase, StandaloneFeatureSubscription $subscription): array
    {
        return ['transaction_id' => $purchase->transaction_id, 'reference' => $purchase->client_reference,
            'feature' => $purchase->feature->slug, 'slot_name' => $purchase->slot_name,
            'billing_event' => $purchase->billing_event, 'amount' => $purchase->amount,
            'applied_price_level' => $purchase->applied_price_level, 'status' => $subscription->status,
            'period_ends_at' => $subscription->current_period_ends_at?->toIso8601String()];
    }
}
