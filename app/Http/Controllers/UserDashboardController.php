<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\UserPlan;
use App\Models\WalletLog;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\Announcement;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use App\Models\UserProductPlan;
use App\Models\AdminColorSetting;
use App\Models\UserBulkDataWallet;
use App\Models\UserVirtualAccount;
use App\Models\LandingPagesSetting;
use App\Models\ProductPlanCategory;
use App\Http\Controllers\Controller;
use App\Models\BulkDataProductPlans;
use App\Models\FundingWebhookPayload;
use Illuminate\Support\Facades\Route;
use App\Models\FundingOptionBankCodes;

class UserDashboardController extends Controller
{
 
  public function index(){

      
    $template = SiteTemplate::first();
    if((! $template || $template->template_name == 'template_1') && env('APP_NAME') == 'OresamSub' && auth()->user()->role->role_name == 'User'){
        // $data['transactions'] = Transaction::with('product_plan')->where('user_id',auth()->id())->limit(10)->latest()->get();
        // $data['wallet_logs'] = WalletLog::with('user')
        // ->where('user_id',auth()->id())
        // ->limit(25)
        // ->latest()
        // ->get();


        $data['transactions'] = Transaction::with(relations: 'product_plan')->where('user_id',auth()->id())->limit(10)->latest()->get();
        $data['announcements'] = Announcement::where('status',1)->latest()->get();

     
        return Inertia::render('Dashboard')->with($data);
        // return view('oresamsub.pages.dashboard')->with($data);
    }

  
    $hot_sales = ProductPlanCategory::with('product')->where('is_hot_sales',1)->get();
    $user_virtual_accounts = UserVirtualAccount::where('user_id',auth()->id())->latest()->get();

    $active_bankcodes = FundingOptionBankCodes::where('visibility_status',1)->pluck('bank_code')->toArray();
    $total_expected_bankcodes = FundingOptionBankCodes::count();

    // dd($user_virtual_accounts);
    $new_hot_sales_array = [];

    $data['user_virtual_accounts'] = $user_virtual_accounts;
    $data['active_bankcodes'] = $active_bankcodes;
    $data['total_expected_bankcodes'] = $total_expected_bankcodes;

    $last_funding = FundingWebhookPayload::where('user_id',auth()->id())
                    ->whereNotNull('wallet_funding_promo_id')
                    ->latest()
                    ->first();

    $funding_res = 'nil';
    if($last_funding){
          $promo_id = $last_funding->wallet_funding_promo_id;
          $custom_wallet_funding_promo_id = $last_funding->custom_wallet_funding_promo_id;
          $promo_bonus = $last_funding->amount_settled - $last_funding->amount_paid;
          if ($promo_id != NULL && $promo_bonus > 0) {
            $formatted_bonus = number_format($promo_bonus, 2);
            $funding_res = "<br><div style='
                background: #d1fae5;
                border: 1px solid #10b981;
                color: #065f46;
                padding: 8px 14px;
                margin-top: 10px;
                border-radius: 8px;
                font-size: 10px;
                font-weight: 500;
                display: inline-block;
            '>
                🎉 <span style='color: #047857;'>₦{$formatted_bonus} bonus</span>
            </div>";
        }

        if ($custom_wallet_funding_promo_id != NULL) {
          if($promo_bonus > 0){
            $formatted_bonus = number_format($promo_bonus, 2);
            $funding_res = "<br><div style='
                background: #d1fae5;
                border: 1px solid #10b981;
                color: #065f46;
                padding: 8px 14px;
                margin-top: 10px;
                border-radius: 8px;
                font-size: 10px;
                font-weight: 500;
                display: inline-block;
            '>
                🎉 <span style='color: #047857;'>₦{$formatted_bonus} bonus</span>
            </div>";
          }else if($promo_bonus == 0){
              $formatted_bonus = number_format($promo_bonus, 2);
              $funding_res = "<br><div style='
                  background: #d1fae5;
                  border: 1px solid #10b981;
                  color: #065f46;
                  padding: 8px 14px;
                  margin-top: 10px;
                  border-radius: 8px;
                  font-size: 10px;
                  font-weight: 500;
                  display: inline-block;
              '>
                  🎉 <span style='color: #047857;'>100% funding</span>
              </div>";
          }else{
               
          }      
      }

      
    }
    $data['funding_res'] = $funding_res;

    foreach($hot_sales as $key=>$hot_sale){
      $new_hot_sales_array[$key]['product_slug'] = $hot_sale->product->slug;
      switch($hot_sale->product->slug){
        case 'utility_bills':
          $route_name = 'user.electricity.buy_electricity_subscription_by_plan_category';
          break;

        case 'data':
          $route_name = 'user.data.buy_data_by_plan_category';
          break;

        case 'airtime':
          $route_name = 'user.airtime.buy_airtime_by_plan_category';
          break;

        case 'cable_subscription':
          $route_name = 'user.cable_subscription.buy_cable_subscription_by_plan_category';
          break;

        case 'e_pins':
          $route_name = 'user.data.buy_data_by_plan_category';
          break;

        case 'result_checker':
          $route_name = 'user.data.buy_data_by_plan_category';
          break;

        default:
          $route_name = 'user.data.buy_data_by_plan_category';
          break;

      }
      $new_hot_sales_array[$key]['id'] = $hot_sale->id;
      $new_hot_sales_array[$key]['plan_category_name'] = $hot_sale->product_plan_category_name;
      $new_hot_sales_array[$key]['route_name'] = $route_name;
      $new_hot_sales_array[$key]['slug'] = $hot_sale->product->slug;

    }

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
    $data['hot_sales'] = $new_hot_sales_array;
    // dd($data);
    $data['user'] = $user_details;
    $data['users'] = User::select('id')->get();
    $data['product_plans'] = ProductPlan::select('id')->get();
    $data['product_plan_categories'] = ProductPlanCategory::select('id','product_plan_category_name')->get();
    $data['bulk_data_plans'] = BulkDataProductPlans::all();
 
    $data['user_selling_variable'] = 'user_level_'.$user_plan_level.'_selling_price';
    // dd($data);
    if($user_details->role->role_name == 'User'){
      $data['bulk_data_wallet_sum'] = UserBulkDataWallet::select('bulk_wallet_balance_mb')->where('user_id',$user_id)->sum('bulk_wallet_balance_mb');
      $data['bulk_data_wallet_count'] = UserBulkDataWallet::select('bulk_wallet_balance_mb')->where('user_id',$user_id)->count();
      $data['alltime_bulk_wallet_balance_mb'] = UserBulkDataWallet::select('alltime_bulk_wallet_balance_mb')->where('user_id',$user_id)->sum('alltime_bulk_wallet_balance_mb');
      $data['transactions'] = Transaction::with(['user','product_plan'])->where('user_id',$user_id)->latest()->get();
      $data['transactions_sum'] = Transaction::with(['user','product_plan'])->where('user_id',$user_id)->sum('amount');

      $site_colors = AdminColorSetting::get();
      if(count($site_colors) > 0){
          foreach($site_colors as $site_color){
              $data[$site_color->color_name] = $site_color->color_value;
          }
      }
     
      $template = SiteTemplate::first();
      if(!$template || $template->template_name == 'template_1'){
          return view('dashboard')->with($data);
      }else{
          //this is template 2 
          $templaten = 'template'.explode('_',$template->template_name)[1];
          return view($templaten.'.user.dashboard')->with($data);
      }

    }else{
      $data['main_wallet_balances'] = User::select('main_wallet')->sum('main_wallet');
      $data['bulk_data_wallet_sum'] = UserBulkDataWallet::select('bulk_wallet_balance_mb')->sum('bulk_wallet_balance_mb');
      $data['alltime_bulk_wallet_balance_mb'] = UserBulkDataWallet::select('alltime_bulk_wallet_balance_mb')->sum('alltime_bulk_wallet_balance_mb');
      $data['transactions'] = Transaction::with(['user','product_plan'])->latest()->get();
      //no need here
      return view('admin_dashboard')->with($data);
    }
  }
}
