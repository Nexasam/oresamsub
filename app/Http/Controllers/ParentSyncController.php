<?php

namespace App\Http\Controllers;

use App\Models\ProductPlan;
use Illuminate\Http\Request;
use App\Http\Services\DataPlansService;

class ParentSyncController extends Controller
{
    // public function syncplans(Request $request){
        
    //     $fetchpplans = ProductPlan::get();
    //     foreach($fetchpplans as $plann){
    //         $dataservice['user'] = $request->api_user;
    //         $dataservice['network_id'] = $networkuuid;
    //         $dataservice['product_id'] = $product_id;
    //         $dataservice['is_api'] = 'yes';
    //         $plans = (new DataPlansService())->fetch_user_data_plans($dataservice)['plans'];
    //     }
       

    //     return $this->success('Data plans successfully fetched',data: $plans);  
    // } 
}
