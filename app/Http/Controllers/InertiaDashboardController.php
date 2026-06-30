<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Services\VirtualAccountService;
use App\Models\Announcement;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlanCategory;
use App\Models\Transaction;
use App\Models\UserContact;
use App\Models\UserVirtualAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
