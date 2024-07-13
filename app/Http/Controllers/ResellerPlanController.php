<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ResellerPlanController extends Controller
{
    public function index(){
        // Gate::authorize('viewAny', UserPlan::class);
        $user_product_plans = UserPlan::get();
        $data['user_plans'] = $user_product_plans;
        return view('admin.reseller_plans.index')->with($data);
    }

    public function update_name(Request $request){
        // Gate::authorize('update', UserPlan::class);

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required'
        ]);
        
        
        if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        UserPlan::where('id',$request->id)->update([
            'updated_user_plan_name' => $request->name
        ]);

        return response()->json(['status'=>'1', 'message'=>'Reseller plan updated','data' => $request->id ]);

    }
}
