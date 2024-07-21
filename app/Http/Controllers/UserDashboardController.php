<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPlan;
use App\Models\ProductPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\UserProductPlan;
use App\Models\UserBulkDataWallet;
use App\Models\LandingPagesSetting;
use App\Models\ProductPlanCategory;
use App\Http\Controllers\Controller;
use App\Models\BulkDataProductPlans;

class UserDashboardController extends Controller
{

 
  public function index(){
    if(! session()->has('whatsapp_support_number')){
      $whatsapp_support = LandingPagesSetting::where('field_name','support_whatsapp_number')->first();
      if($whatsapp_support){
          $whatsapp_support_number = $whatsapp_support->field_details;
      }else{
          $whatsapp_support_number = '2348168509044'; //change later
      }
      session()->put('whatsapp_support_number',$whatsapp_support_number);
    }

    
    $user_details = User::with(['user_plan','role'])->where('id',auth()->id())->first();

    // return $user_details->role->role_name;
    $user_id = $user_details->id;
    $user_plan_level = $user_details->user_plan->plan_level;
    $data['user'] = $user_details;
    $data['users'] = User::select('id')->get();
    $data['product_plans'] = ProductPlan::select('id')->get();
    $data['product_plan_categories'] = ProductPlanCategory::select('id')->get();
    $data['bulk_data_plans'] = BulkDataProductPlans::all();
 
    $data['user_selling_variable'] = 'user_level_'.$user_plan_level.'_selling_price';
    // dd($data);
    if($user_details->role->role_name == 'User'){
      $data['bulk_data_wallet_sum'] = UserBulkDataWallet::select('bulk_wallet_balance_mb')->where('user_id',$user_id)->sum('bulk_wallet_balance_mb');
      $data['bulk_data_wallet_count'] = UserBulkDataWallet::select('bulk_wallet_balance_mb')->where('user_id',$user_id)->count();
      $data['alltime_bulk_wallet_balance_mb'] = UserBulkDataWallet::select('alltime_bulk_wallet_balance_mb')->where('user_id',$user_id)->sum('alltime_bulk_wallet_balance_mb');
      $data['transactions'] = Transaction::with(['user','product_plan'])->where('user_id',$user_id)->get();
     
      return view('dashboard')->with($data);
    }else{
      $data['main_wallet_balances'] = User::select('main_wallet')->sum('main_wallet');
      $data['bulk_data_wallet_sum'] = UserBulkDataWallet::select('bulk_wallet_balance_mb')->sum('bulk_wallet_balance_mb');
      $data['alltime_bulk_wallet_balance_mb'] = UserBulkDataWallet::select('alltime_bulk_wallet_balance_mb')->sum('alltime_bulk_wallet_balance_mb');
      $data['transactions'] = Transaction::with(['user','product_plan'])->get();
      return view('admin_dashboard')->with($data);
    }
  }
}
