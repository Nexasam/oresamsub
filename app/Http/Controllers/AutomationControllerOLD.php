<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserPlan;
use App\Models\Automation;
use App\Models\ProductPlan;
use Illuminate\Http\Request;
use App\Models\ProductPlanCategory;

class AutomationController extends Controller
{
    public function dashboard($slug){
        $user_plans = UserPlan::orderBy('plan_level')->get();
        // dd($user_plans);

        $automation = Automation::where('slug',$slug)->first();
        $product_plan_ids = ProductPlan::where('automation_id',$automation->id)->pluck('automation_product_plan_id')->toArray();                

        $sme_network_mtn = 1;
        $sme_network_airtel = 2;
        $sme_network_9mobile = 3;
        $sme_network_glo = 4;
        // dd($automation->id);

        $selection = '';
        //move this hardcoded values later into an enum
        if($slug == 'autopilot'){
            $selection = 'selected';
            //call plans api 
        }

       

        if($slug == 'ogdams' || $slug == 'ogdams_v2'){
            //call plans api 
            $selection = 'selected';
            $ogdams_live_key = 'sk_live_8bd499ea-66f6-4650-8c7f-09a59e7c03a5';


            ///TODO: MOVE TO SERVICE
                $curl = curl_init();        
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://simhosting.ogdams.ng/api/v2/get/data/plans',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Authorization: Bearer '.$ogdams_live_key
                ),
                ));
                $response = curl_exec($curl);

                $response_array = json_decode($response,true);
                
                curl_close($curl);

                $ogdams_mtn_products = $response_array['data'][1] ?? [];

                // $ogdams_mtn_products =  collect($ogdams_mtn_products)
                // ->where('active', 1)
                // ->tap(function($collection){
                //     return $collection;
                // })
                // ->where('member', 1)
                // ->tap(function($collection){
                //     return var_dump($collection->pluck('name'));
                // });

                $ogdams_airtel_products = $response_array['data'][2] ?? [];
                $ogdams_glo_products = $response_array['data'][3] ?? [];
                $ogdams__9mobile_products = $response_array['data'][4] ?? [];   
                
                $products_count = count($ogdams_mtn_products) + count($ogdams_airtel_products) + count($ogdams_glo_products) + count($ogdams__9mobile_products) ;

                if($automation == NULL){
                    return back();
                }
        
        
                // $products = Product::select('id','product_name','slug')->get();
                $product_plan_categories = ProductPlanCategory::select('id','product_plan_category_name')->get();
            
                $data['product_plan_ids'] = $product_plan_ids ;
                $data['ogdams_mtn_products'] = $ogdams_mtn_products ;
                $data['ogdams_airtel_products'] = $ogdams_airtel_products ;
                $data['ogdams_glo_products'] = $ogdams_glo_products ;
                $data['ogdams__9mobile_products'] = $ogdams__9mobile_products ;
                // $data['products'] = $products;
                $data['product_plan_categories'] = $product_plan_categories ;
        
                $data['slug'] = $slug;
                $data['automation'] = $automation;
                $data['user_plans'] = $user_plans;
                // $data['automation_products'] = $_9mobile_products;
                // $data['automation_products'] = $automation_products ?? [];
                // dd($data);
        
                return view('admin.automations.dashboard')->with($data);
        }

        if($slug == 'smeplug'){
            //call plans api 
            $selection = 'selected';
            //call plans api 
            //username: esubcustomercare@gmail.com
            //pass: inchristalone@2612DEVELOPMENT
            //pbkey: 8a30dba11ba967631d0d99625a7aed20255e1edd5eca851dea52975c5ff9bf45
            $secret_key = '4c5edd5fe849f1d170e299ca288d5361c9255d92f7b79fa139eb0e2f4f88eb7e'; 

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://smeplug.ng/api/v1/data/plans',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$secret_key,
            'Cookie: CUBESESSID=kuohi1jd76rqo3i9r4oopum7s7'
            ),
            ));

            $response = curl_exec($curl);
            $response_array = curl_exec($response);

            curl_close($curl);

            // echo $response_array;
            dd($response_array);


            ///TODO: MOVE TO SERVICE
        
                $ogdams_mtn_products = $response_array['data'][1] ?? [];

                // $ogdams_mtn_products =  collect($ogdams_mtn_products)
                // ->where('active', 1)
                // ->tap(function($collection){
                //     return $collection;
                // })
                // ->where('member', 1)
                // ->tap(function($collection){
                //     return var_dump($collection->pluck('name'));
                // });

                $ogdams_airtel_products = $response_array['data'][2] ?? [];
                $ogdams_glo_products = $response_array['data'][3] ?? [];
                $ogdams__9mobile_products = $response_array['data'][4] ?? [];   
                
                $products_count = count($ogdams_mtn_products) + count($ogdams_airtel_products) + count($ogdams_glo_products) + count($ogdams__9mobile_products) ;

                if($automation == NULL){
                    return back();
                }
        
        
                // $products = Product::select('id','product_name','slug')->get();
                $product_plan_categories = ProductPlanCategory::select('id','product_plan_category_name')->get();
            
                $data['product_plan_ids'] = $product_plan_ids ;
                $data['ogdams_mtn_products'] = $ogdams_mtn_products ;
                $data['ogdams_airtel_products'] = $ogdams_airtel_products ;
                $data['ogdams_glo_products'] = $ogdams_glo_products ;
                $data['ogdams__9mobile_products'] = $ogdams__9mobile_products ;
                // $data['products'] = $products;
                $data['product_plan_categories'] = $product_plan_categories ;
        
                $data['slug'] = $slug;
                $data['automation'] = $automation;
                $data['user_plans'] = $user_plans;
                // $data['automation_products'] = $_9mobile_products;
                // $data['automation_products'] = $automation_products ?? [];
                // dd($data);
        
                return view('admin.automations.dashboard')->with($data);
        }

        if($slug == 'megasubplug'){
            //call plans api 
            $selection = 'selected';
            $api_key = '';
            $data = [
                    "Password" => "inchristalone@NEW2024",
                    "Authorization" => "102325246266435f47e344b"
            ];
            $encoded_data = json_encode($data);

            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://megasubplug.com/API/?action=product_prices',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Password: inchristalone@NEW2024',
                'Authorization: 102325246266435f47e344b',
                'Cookie: PHPSESSID=67up5cp8mc52aqqu08dkoetd04'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // echo $response;

            $product_plan_categories = ProductPlanCategory::select('id','product_plan_category_name')->get();
            $data['product_plan_ids'] = $product_plan_ids;
            $data['product_plan_categories'] = $product_plan_categories; 
            $data['slug'] = $slug;
            $data['automation'] = $automation;
            $data['user_plans'] = $user_plans;
            $data['data_plans'] = json_decode($response,true)['Detail'];
            // dd(json_decode($response,true)['Detail'][0]['id']);

            return view('admin.automations.megasubplug_dashboard')->with($data);

        }

        if($slug == 'cloudsimhost'){
            $selection = 'selected';
            //call plans api 
        }

        if($selection == ''){
            return back();
        }

       
    }
        
}
