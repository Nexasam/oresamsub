<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    $user_details = auth()->user();
    $user_id = $user_details->id;
    $data['user'] = $user_details;
    $data['transactions'] = Transaction::select('id')->where('user_id',$user_id)->get();
    $data['users'] = User::select('id')->get();
    $data['product_plans'] = ProductPlan::select('id')->get();
    $data['product_plan_categories'] = ProductPlanCategory::select('id')->get();
    return view('dashboard')->with($data);
  }
}
