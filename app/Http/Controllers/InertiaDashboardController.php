<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Services\VirtualAccountService;
use App\Models\Announcement;
use App\Models\Network;
use App\Models\NetworkIssueAlert;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Transaction;
use App\Models\UserContact;
use App\Models\UserVirtualAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Pricing\CustomerProductPricingService;
use Inertia\Inertia;

class InertiaDashboardController extends Controller
{
    // Show login page (Inertia React)
    public function dashboard()
    {
        
        $data['transactions'] = Transaction::with(relations: 'product_plan')->where('user_id',auth()->id())->limit(50)->latest()->get();
        $data['announcements'] = Announcement::where('status',1)->latest()->get();
        $contacts =  UserContact::where('user_id', auth()->id())
            ->orderByDesc('last_used_at')
            ->limit(50) // keep payload light
            ->get([
                'id',
                'phone_number',
                'name',
                'product_plan_id',
                'network_id',
        ]);
        $data['contacts'] = $contacts;
        $networkIssueAlerts = NetworkIssueAlert::with('network')
            ->orderBy('priority')
            ->orderBy('created_at')
            ->get();
        $data['networkNotices'] = [
            'active' => $networkIssueAlerts
                ->where('is_active', true)
                ->values()
                ->map(fn (NetworkIssueAlert $alert): array => $alert->activePayload())
                ->all(),
            'restored' => $networkIssueAlerts
                ->where('is_active', false)
                ->filter(fn (NetworkIssueAlert $alert): bool => $alert->last_restored_at !== null)
                ->values()
                ->map(fn (NetworkIssueAlert $alert): array => $alert->restoredPayload())
                ->all(),
        ];

       return $data;
        return Inertia::render('Dashboard')->with($data);
    }

   
    
    public function data()
    {
        return Inertia::render('BuyData', [
            'networks' => Network::all(),
    
            'contacts' => UserContact::where('user_id', auth()->id())
                ->orderByDesc('last_used_at')
                ->limit(50) // keep payload light
                ->get([
                    'id',
                    'phone_number',
                    'name',
                    'product_plan_id',
                    'network_id',
                ]),
        ]);
    }
    

    public function airtime()
    {
        $data['networks'] = Network::get();
        // dd('test');
        return Inertia::render('BuyAirtime')->with($data);
    }

    public function pricing(CustomerProductPricingService $pricingService)
    {
        $user = Auth::user()->loadMissing('user_plan');

        $plans = ProductPlan::query()
            ->with([
                'product_plan_category.network',
                'product_plan_category.product',
                'customPricings' => fn ($query) => $query
                    ->where('user_id', $user->id)
                    ->where('status', 1),
            ])
            ->where('visibility', 1)
            ->whereHas('product_plan_category.product', fn ($query) => $query->where('slug', 'data'))
            ->orderByRaw('CAST(data_size_in_mb AS DECIMAL(12, 2))')
            ->orderByRaw('CAST(validity_in_days AS UNSIGNED) DESC')
            ->get()
            ->map(function (ProductPlan $plan) use ($pricingService, $user): array {
                $resolved = $pricingService->resolve($user, $plan);
                $hasPersonalOverride = $plan->customPricings->isNotEmpty();
                $usesSalespersonPrice = ! $hasPersonalOverride
                    && $user->is_salesperson
                    && $plan->salesperson_price !== null;

                return [
                    'id' => $plan->id,
                    'name' => $plan->product_plan_name,
                    'network_id' => $plan->product_plan_category?->network?->id,
                    'network' => $plan->product_plan_category?->network?->network_name,
                    'size_mb' => (float) $plan->data_size_in_mb,
                    'validity_days' => (int) $plan->validity_in_days,
                    'price' => $resolved['price'],
                    'price_source' => $hasPersonalOverride
                        ? 'personal_override'
                        : ($usesSalespersonPrice ? 'salesperson' : 'customer_level'),
                ];
            })
            ->values();

        return Inertia::render('Pricing', [
            'plans' => $plans,
            'pricingProfile' => [
                'plan_name' => $user->user_plan?->updated_user_plan_name
                    ?? $user->user_plan?->user_plan_name
                    ?? 'Standard',
                'plan_level' => (int) ($user->user_plan?->plan_level ?? 1),
                'is_salesperson' => (bool) $user->is_salesperson,
            ],
        ]);
    }

    public function cable()
    {
        $product = Product::select('id')->where('slug', 'cable_subscription')->first();
        $product_plan_categories = ProductPlanCategory::select('id','product_plan_category_name')->where('product_id', $product->id)->get();
        $data['product'] = $product;
        $data['product_plan_categories'] = $product_plan_categories;
        return Inertia::render('BuyCable')->with($data);
    }

    public function electricity()
    {
        $product = Product::select('id')->where('slug', 'utility_bills')->first();
        $product_plan_categories = ProductPlanCategory::select('id','product_plan_category_name')->where('product_id', $product->id)->get();
        $data['product'] = $product;
        $data['product_plan_categories'] = $product_plan_categories;
        return Inertia::render('BuyElectricity')->with($data);
    }

    // public function virtual_accounts(){
    //     $virtualccts = UserVirtualAccount::select('id','bank_name','account_name','account_number')
    //     ->where('funding_slug','!=','crystal_pay')
    //     ->where('user_id',auth()->id())
    //     ->get();
        
    //     $data['virtualccts'] = $virtualccts;
    //     return Inertia::render('VirtualAccounts')->with($data);
    // }

    public function virtual_accounts()
    {
        $data['user'] = auth()->user();
        (new VirtualAccountService())->generate_accounts($data);

        $virtualccts = UserVirtualAccount::select(
                'user_virtual_accounts.id',
                'user_virtual_accounts.bank_name',
                'user_virtual_accounts.account_name',
                'user_virtual_accounts.account_number',
                'user_virtual_accounts.bank_code',
                'funding_option_bank_codes.short_description as bank_description'
            )
            ->leftJoin('funding_option_bank_codes', function($join) {
                $join->on('user_virtual_accounts.funding_option_id', '=', 'funding_option_bank_codes.funding_option_id')
                    ->on('user_virtual_accounts.bank_code', '=', 'funding_option_bank_codes.bank_code');
            })
            ->where('funding_slug','!=','crystal_pay')
            ->where('user_virtual_accounts.user_id', auth()->id())
            ->get();

        return Inertia::render('VirtualAccounts')->with(['virtualccts' => $virtualccts]);
    }

    public function transactions(){
        $data['transactions'] = Transaction::with(relations: 'product_plan')->where('user_id',auth()->id())->limit(200)->latest()->get();
        return Inertia::render('Transactions')->with($data);
    }

}
