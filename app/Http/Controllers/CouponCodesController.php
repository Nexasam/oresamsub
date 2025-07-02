<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\CouponCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CouponCodesController extends Controller
{
    public function index(){
       
        // dd('asdfsdfsss');
        $networks = Network::get();
        $cc = CouponCode::with('product_plan_category.network')->latest()->get();
        $ppc = ProductPlanCategory::get();
        $data['networks'] = $networks;
        $data['coupon_codes'] = $cc;
        $data['product_plan_categories'] = $ppc;
        // return $data;
        return view('admin.coupon_codes.index')->with($data);
    }

    public function store(Request $request){

          $validator = Validator::make($request->all(), [
            'title' => 'required',
            'code' => 'required',
            'amount' => 'required',
            'product_slug' => 'required',
            'network_id' => 'required',
            'product_plan_category_id' => 'required',
            'transaction_metrics' => ['required',Rule::in(['before','after'])],
            'transaction_metrics_date' => 'required|date',
            'slots' => 'required|integer',
          ]);
    
          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }

        

          $data['title'] = $request->title;
          $data['code'] = $request->code;
          $data['amount'] = $request->amount;
          $data['slots'] = $request->slots;
          $data['slots_remaining'] = $request->slots;
          $data['product_slug'] = $request->product_slug;
          $data['network_id'] = $request->network_id;
          $data['product_plan_category_id'] = $request->product_plan_category_id;
          $data['transaction_metrics'] = $request->transaction_metrics;
          $data['transaction_metrics_date'] = $request->transaction_metrics_date;
          $data['status'] = 1;
          $newco = CouponCode::create($data);

          if($newco){
            CouponCode::where('id','!=',$newco->id)->update([
                'status' => 0
              ]);
          }

          Session::flash('success','Coupon Code was successfully added and other coupon codes have been automatically deactivated');
          return redirect()->back();

        // $data = $request->all();
        // dd($data);
    }
}
