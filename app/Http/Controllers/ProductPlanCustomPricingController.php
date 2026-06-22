<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Network;
use App\Models\CouponCode;
use App\Models\ProductPlan;
use Illuminate\Http\Request;
use App\Models\FundingOption;
use Illuminate\Validation\Rule;
use App\Models\WalletFundingPromo;
use App\Models\ProductPlanCategory;
use App\Models\UserWalletFundingPromo;
use Illuminate\Support\Facades\Session;
use App\Models\ProductPlanCustomPricing;
use Illuminate\Support\Facades\Validator;

class ProductPlanCustomPricingController extends Controller
{
    // public function index(){
       
    //     // dd('asdfsdfsssppppp');
    //     $networks = Network::get();
    //     // $userrs = User::select('id','username')->get();
  
    //     $product_plans = ProductPlan::with(['automation'])->latest()->get();
    //     $product_plan_custom_pricings = ProductPlanCustomPricing::with(['user','product_plan'])->latest()->get();
    //     $networks = Network::get();
    //     $data['product_plans'] = $product_plans;
    //     $data['product_plan_custom_pricings'] = $product_plan_custom_pricings;
    //     $data['networks'] = $networks;
      

    //     return view('admin.product_plans.product_plan_custom_pricing.index')->with($data);
    // }


    public function index(Request $request)
    {
        $search = $request->search;

        $product_plan_custom_pricings = ProductPlanCustomPricing::query()
            ->with(['user', 'product_plan'])
            ->when($search, function ($query) use ($search) {

                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%");
                })
                ->orWhereHas('product_plan', function ($q) use ($search) {
                    $q->where('product_plan_name', 'like', "%{$search}%");
                });

            })
            ->latest()
            ->paginate(20)
            ->withQueryString();
         
            $networks = Network::get();

        $product_plans = ProductPlan::with(['automation'])->latest()->get();


        // $product_plan_custom_pricings = ProductPlanCustomPricing::with(['user','product_plan'])->latest()->get();


        return view('admin.product_plans.product_plan_custom_pricing.index', compact(
            // 'customPricings',
            'networks',
            'product_plan_custom_pricings',
            'product_plans',
            'search'
        ));
    }

    public function store(Request $request){
                  
        $validator = Validator::make($request->all(), [
          'username' => 'required|exists:users,username',
          'product_plan_id' => 'required|exists:product_plans,id',
          'status' => 'required|integer',
          'price' => 'required|integer',
        ]);

        $validator->stopOnFirstFailure(); // set the behavior before checking

        if ($validator->fails()) {
          Session::flash('failure', $validator->errors()->first());
          return redirect()->back();
        }

       $user = User::where('username',$request->username)->first();

         // Prevent duplicate custom pricing
          $existingPricing = ProductPlanCustomPricing::where('user_id', $user->id)
            ->where('product_plan_id', $request->product_plan_id)
            ->first();

        if ($existingPricing) {
            Session::flash(
                'failure',
                'Custom pricing already exists for this user and product plan.'
            );

            return redirect()->back();
        }

        $data['product_plan_id'] = $request->product_plan_id;
        $data['user_id'] = $user->id;
        $data['price'] = $request->price;
        $data['status'] = 1;
        $data['added_by'] = auth()->id();
        $newpricing = ProductPlanCustomPricing::create($data);

        Session::flash('success','Custom Pricing for '.$request->username.' was successfully added');
        return redirect()->back();
    }

    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'username' => 'required|exists:users,username',
        'product_plan_id' => 'required|exists:product_plans,id',
        'status' => 'required|integer',
        'price' => 'required|integer',
    ]);

    $validator->stopOnFirstFailure();

    if ($validator->fails()) {
        Session::flash('failure', $validator->errors()->first());
        return redirect()->back();
    }

    $pricing = ProductPlanCustomPricing::find($id);

    if (!$pricing) {
        Session::flash('failure', 'Custom pricing record not found');
        return redirect()->back();
    }

    $user = User::where('username', $request->username)->first();

    $pricing->update([
        'product_plan_id' => $request->product_plan_id,
        'user_id'         => $user->id,
        'price'           => $request->price,
        'status'          => $request->status,
    ]);

    Session::flash(
        'success',
        'Custom Pricing for '.$request->username.' was successfully updated'
    );

    return redirect()->back();
}

    public function update_user($id,Request $request){
         
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_plan_id' => 'required|exists:product_plans,id',
            'status' => 'required|integer',
            'price' => 'required|integer',
          ]);
  
          $validator->stopOnFirstFailure(); // set the behavior before checking
  
          if ($validator->fails()) {
            Session::flash('failure', $validator->errors()->first());
            return redirect()->back();
          }
  
          $data['product_plan_id'] = $request->product_plan_id;
          $data['user_id'] = $request->promo_metric;
          $data['price'] = $request->price;
          $data['status'] = 1;
          $data['added_by'] = auth()->id();
          $newpricing = ProductPlanCustomPricing::where('id',$id)->update($data);
  
          Session::flash('success','User Wallet Funding Promo was successfully updated');
          return redirect()->back();  
      }

}
