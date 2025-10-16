<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Network;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\UserVirtualAccount;
use App\Models\ProductPlanCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InertiaDashboardController extends Controller
{
    // Show login page (Inertia React)
    public function dashboard()
    {
        
        $data['transactions'] = Transaction::with(relations: 'product_plan')->where('user_id',auth()->id())->limit(10)->latest()->get();
        $data['announcements'] = Announcement::where('status',1)->latest()->get();

     
        return Inertia::render('Dashboard')->with($data);
    }

    public function data()
    {
        $data['networks'] = Network::get();
        // dd('test');
        return Inertia::render('BuyData')->with($data);
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

    public function virtual_accounts(){
        $virtualccts = UserVirtualAccount::select('id','bank_name','account_name','account_number')
        ->where('slug','!=','crystal_pay')
        ->where('user_id',auth()->id())
        ->get();
        
        $data['virtualccts'] = $virtualccts;
        return Inertia::render('VirtualAccounts')->with($data);
    }

    public function transactions(){
        $data['transactions'] = Transaction::with(relations: 'product_plan')->where('user_id',auth()->id())->limit(200)->latest()->get();
        return Inertia::render('Transactions')->with($data);
    }

}
