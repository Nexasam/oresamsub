<?php

namespace App\Http\Controllers\Api\v1\VendorUsersApi;

use App\Models\User;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ProductPlanCategory;
use App\Traits\JsonResponseWrapper;
use App\Http\Controllers\Controller;
use App\Models\BulkDataProductPlans;
use App\Http\Services\DataPlansService;
use App\Http\Services\ProductPlanService;
use Illuminate\Support\Facades\Validator;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubCableTV;
use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
use App\Services\Automation\MegaSubPlugAutomation\MegaSubElectricity;

// use App\Http\Services\Api\v1\VendorUsersApi\Products\ProductsService;
// use App\Services\Api\Automation\MegaSubPlugAutomation\MegaSubCableTV;

class AffiliateProductsVendorController extends Controller
{
 
    use JsonResponseWrapper;


    public function syncplans(Request $request){

        $fetchpplans =   ProductPlan::with([
            'product_plan_category.product',
            'product_plan_category.network'
        ])->get();

        $user = $request->api_user ?? null;
    
        $plans = (new ProductPlanService())->fetch_all_data_plans($fetchpplans,$user);

        logger('AffiliateProductsVendorController syncplans: ', ['plans' => $plans]);
       
        return $this->success('All plans successfully fetched',data: $plans);  
    }


      

}
