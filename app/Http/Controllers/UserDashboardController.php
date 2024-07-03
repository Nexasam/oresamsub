<?php

namespace App\Http\Controllers;

use App\Models\BulkDataProductPlans;
use App\Models\User;
use App\Models\UserBulkDataWallet;
use App\Models\UserPlan;
use App\Models\ProductPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\UserProductPlan;
use App\Models\ProductPlanCategory;
use App\Http\Controllers\Controller;

class UserDashboardController extends Controller
{
  public function index(){
    $user_details = User::with('user_plan')->where('id',auth()->id())->first();
    $user_id = $user_details->id;
    $user_plan_level = $user_details->user_plan->plan_level;
    $data['user'] = $user_details;
    $data['transactions'] = Transaction::select('id')->where('user_id',$user_id)->get();
    $data['users'] = User::select('id')->get();
    $data['product_plans'] = ProductPlan::select('id')->get();
    $data['product_plan_categories'] = ProductPlanCategory::select('id')->get();
    $data['bulk_data_plans'] = BulkDataProductPlans::all();
    $data['bulk_data_wallet_sum'] = UserBulkDataWallet::select('bulk_wallet_balance_mb')->where('user_id',$user_id)->sum('bulk_wallet_balance_mb');
    $data['alltime_bulk_wallet_balance_mb'] = UserBulkDataWallet::select('alltime_bulk_wallet_balance_mb')->where('user_id',$user_id)->sum('alltime_bulk_wallet_balance_mb');
    $data['user_selling_variable'] = 'user_level_'.$user_plan_level.'_selling_price';
    // dd($data);
    return view('dashboard')->with($data);
  }
}
