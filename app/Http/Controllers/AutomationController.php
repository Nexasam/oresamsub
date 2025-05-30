<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Models\UserPlan;
use App\Models\Automation;
use App\Models\ProductPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AutomationController extends Controller
{

    // TODO
    function slugifyWithUnderscore($text) {
        // Replace non letter or digits by underscore
        $text = preg_replace('~[^\pL\d]+~u', '_', $text);
    
        // Transliterate to ASCII
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    
        // Remove unwanted characters
        $text = preg_replace('~[^_\w]+~', '', $text);
    
        // Trim underscores from ends
        $text = trim($text, '_');
    
        // Remove duplicate underscores
        $text = preg_replace('~_+~', '_', $text);
    
        // Lowercase
        $text = strtolower($text);
    
        return $text ?: 'n_a';
    }
    

    public function index(Request $request){
        $automations = Automation::get();
        $data['automations'] = $automations;
        return view('admin.automations.index')->with($data);
        // return $data;
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'automation_name' => 'required|unique:automations,automation_name',
            'api_public_key' => 'required',
            'api_secret_key' => 'nullable',
            'api_password' => 'nullable',
            'data_url' => 'nullable',
            'airtime_url' => 'nullable',
            'cable_url' => 'nullable',
            'electricity_url' => 'nullable',
            'automation_group' => 'required',
            'domain_url' => 'nullable',
            'whatsapp_support_link' => 'nullable',
        ]);

        $automation_slug = $this->slugifyWithUnderscore($request->automation_name);     

        if ($validator->stopOnFirstFailure()->fails()) {
            Session::flash('failure',$validator->errors()->first());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if(auth()->user()->email != 'adebsholey4real@gmail.com'){
            //no waay
            Session::flash('failure','Sorry you do not have access');
            return redirect()->back();
        }

        try{

            Automation::create([
                'automation_name' => $request->automation_name,
                'api_public_key' => $request->api_public_key,
                'api_secret_key' => $request->api_secret_key,
                'api_password' => $request->api_password,
                'automation_group' => $request->automation_group,
                'data_url' => $request->data_url,
                'airtime_url' => $request->airtime_url,
                'electricity_url' => $request->electricity_url,
                'cable_url' => $request->cable_url,
                'whatsapp_support_link' => $request->whatsapp_support_link,
                'domain_url' => $request->domain_url,
                'slug' => $automation_slug,
            ]);
            DB::commit();        
            Session::flash('success','Automation was successfully created');
            return redirect()->back();

        }catch(Exception $ex){
            logger($ex->getMessage().' on line '.$ex->getLine());
            DB::rollback();
            Session::flash('failure',$ex->getMessage());
            return redirect()->back();
        }
       

    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'automation_name' => 'required',
            'api_public_key' => 'required',
            'api_secret_key' => 'nullable',
            'api_password' => 'nullable',
            'automation_group' => 'required',
            'domain_url' => 'nullable',
            'whatsapp_support_link' => 'nullable',
        ]);

        $automation_slug = $this->slugifyWithUnderscore($request->automation_name);     

        if ($validator->stopOnFirstFailure()->fails()) {
            Session::flash('failure',$validator->errors()->first());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try{

            Automation::where('id',$request->id)->update([
                'automation_name' => $request->automation_name,
                'api_public_key' => $request->api_public_key,
                'api_secret_key' => $request->api_secret_key,
                'api_password' => $request->api_password,
                'automation_group' => $request->automation_group,
                'domain_url' => $request->domain_url,
                'whatsapp_support_link' => $request->whatsapp_support_link,
                'slug' => $automation_slug,
            ]);
            DB::commit();        
            Session::flash('success','Automation was successfully updated');
            return redirect()->back();

        }catch(Exception $ex){
            logger($ex->getMessage().' on line '.$ex->getLine());
            DB::rollback();
            Session::flash('failure',$ex->getMessage());
            return redirect()->back();
        }
       

    }

    public function dashboard($slug){
        $user_plans = UserPlan::orderBy('plan_level')->get();
        // dd($user_plans);

        $automation = Automation::where('slug',$slug)->first();
        $product_plan_ids = ProductPlan::where('automation_id',$automation->id)
        ->pluck('automation_product_plan_id')->toArray();                

        $sme_network_mtn = 1;
        $sme_network_airtel = 2;
        $sme_network_9mobile = 3;
        $sme_network_glo = 4;
        // dd($automation->id);

        $product_plan_categories = ProductPlanCategory::select('id','product_plan_category_name')->get();
        $data['product_plan_ids'] = $product_plan_ids;
        $data['product_plan_categories'] = $product_plan_categories; 
        $data['slug'] = $slug;
        $data['automation'] = $automation;
        $data['user_plans'] = $user_plans;

        $selection = '';
        //move this hardcoded values later into an enum
        if($slug == 'autopilot'){
            $selection = 'selected';
            //call plans api 
        }

       
        try{

            if($slug == 'ogdams' || $slug == 'ogdams_v2'){
                //call plans api 
                $selection = 'selected';
                // sk_live_8bd499ea-66f6-4650-8c7f-09a59e7c03a5
                // $ogdams_public_key = 'sk_live_8bd499ea-66f6-4650-8c7f-09a59e7c03a5';
                $ogdams_public_key = $automation->api_public_key;
                // dd($ogdams_public_key);
    
    
                ///TODO: MOVE TO SERVICE
                    // $curl = curl_init();        
                    // curl_setopt_array($curl, array(
                    // CURLOPT_URL => 'https://simhosting.ogdams.ng/api/v2/get/data/plans',
                    // CURLOPT_RETURNTRANSFER => true,
                    // CURLOPT_ENCODING => '',
                    // CURLOPT_MAXREDIRS => 10,
                    // CURLOPT_TIMEOUT => 0,
                    // CURLOPT_FOLLOWLOCATION => true,
                    // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    // CURLOPT_CUSTOMREQUEST => 'GET',
                    // CURLOPT_HTTPHEADER => array(
                    //     'Accept: application/json',
                    //     'Authorization: Bearer '.$ogdams_public_key
                    // ),
                    // ));
                    
                    
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
                        'Authorization: Bearer '.$ogdams_public_key
                    ),
                    ));
    
                    $response = curl_exec($curl);
    
                    curl_close($curl);
                    // echo $response;
                    // dd($response);
    
                    $response_array = json_decode($response,true);
                    
                    curl_close($curl);
    
                    $ogdams_mtn_products = $response_array['data'][1] ?? [];
                    $ogdams_airtel_products = $response_array['data'][2] ?? [];
                    $ogdams_glo_products = $response_array['data'][3] ?? [];
                    $ogdams__9mobile_products = $response_array['data'][4] ?? [];   
                    
                    $products_count = count($ogdams_mtn_products) + count($ogdams_airtel_products) + count($ogdams_glo_products) + count($ogdams__9mobile_products) ;
    
                    if($automation == NULL || $automation->api_public_key == NULL){
                        Session::flash('failure','Please ensure your automation api keys are set');
                        return redirect()->route('admin.settings.index');
                        // return back()->with('status' , 'Please check your settings and ensure keys are set');
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
    
            else if($slug == 'smeplug'){
                //call plans api 
                $selection = 'selected';
                //call plans api 
                //4c5edd5fe849f1d170e299ca288d5361c9255d92f7b79fa139eb0e2f4f88eb7e
                $secret_key = $automation->api_secret_key; 
    
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
                $response_array = json_decode($response,true);
    
                curl_close($curl);
    
                // echo $response_array;
                // dd($response);
    
    
                ///TODO: MOVE TO SERVICE
            
                    $smeplug_mtn_products = $response_array['data'][$sme_network_mtn] ?? [];
                    $smeplug_airtel_products = $response_array['data'][$sme_network_airtel] ?? [];
                    $smeplug_glo_products = $response_array['data'][$sme_network_glo] ?? [];
                    $smeplug__9mobile_products = $response_array['data'][$sme_network_9mobile] ?? [];   
                    
                    $products_count = count($smeplug_mtn_products) + count($smeplug_airtel_products) + count($smeplug_glo_products) + count($smeplug__9mobile_products) ;
    
                    if($automation == NULL || $automation->api_secret_key == NULL){
                        Session::flash('failure','Please ensure your automation api keys are set');
                        return redirect()->route('admin.settings.index');
                        // return back()->with('status' , 'Please check your settings and ensure keys are set');
                    }
            
            
            
                    // $products = Product::select('id','product_name','slug')->get();
                    $product_plan_categories = ProductPlanCategory::select('id','product_plan_category_name')->get();
                
                    $data['product_plan_ids'] = $product_plan_ids;
                    $data['smeplug_mtn_products'] = $smeplug_mtn_products;
                    $data['smeplug_airtel_products'] = $smeplug_airtel_products;
                    $data['smeplug_glo_products'] = $smeplug_glo_products;
                    $data['smeplug__9mobile_products'] = $smeplug__9mobile_products;
                    // $data['products'] = $products;
                    $data['product_plan_categories'] = $product_plan_categories;
            
                    $data['slug'] = $slug;
                    $data['automation'] = $automation;
                    $data['user_plans'] = $user_plans;
                    // $data['automation_products'] = $_9mobile_products;
                    // $data['automation_products'] = $automation_products ?? [];
                    // dd($data);
            
                    return view('admin.automations.smeplug_dashboard')->with($data);
            }
    
            else if($slug == 'megasubplug'){
    
                
                //call plans api 
                //password: inchristalone@NEW2024,  author: 102325246266435f47e344b
                $selection = 'selected';
                $api_key = '';
                $data = [
                        "Password" => $automation->api_password,
                        "Authorization" => $automation->api_public_key,
                ];
                $encoded_data = json_encode($data);
                // dd($encoded_data);
    
                if($automation == NULL || $automation->api_public_key == NULL || $automation->api_password == NULL){
                    Session::flash('failure','Please ensure your automation api keys are set');
                    return redirect()->route('admin.settings.index');
                    // return back()->with('status' , 'Please check your settings and ensure keys are set');
                }
        
    
                // dd($automation->api_password.' '.$automation->api_public_key);
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
                    'Password: '.$automation->api_password,
                    'Authorization: '.$automation->api_public_key,
                    "Accept: application/json",
                    "Content-Type: application/json",
                ),
                ));
    
                $response = curl_exec($curl);
    
                curl_close($curl);
                // echo $response;
                // dd($response);

                $response_decoded = json_decode($response,true);
                if(isset($response_decoded['Status']) && $response_decoded['Status'] == 'Error'){
                    Session::flash('failure',$response_decoded['Detail']);
                    return back();
                }
                
                // $plan_details = json_decode($response,true)['Detail'];
                // for($i = 0; $i < count($plan_details); $i++) {
                //     $already_saved_check = in_array($plan_details[$i]['id'],$product_plan_ids);
                //     $plan_details[$i]['product_plan_category_id'] = ;
                // }
                // exit;
               
    
               
                
                $data['data_plans'] = $response_decoded['Detail'];
                // $data['data_plans'] = json_decode($response,true)['Detail'];
                $data['automation'] = $automation;
                $data['product_plan_ids'] = $product_plan_ids;
                $data['product_plan_categories'] = $product_plan_categories; 
                $data['slug'] = $slug;
                $data['user_plans'] = $user_plans;
                // dd(json_decode($response,true)['Detail'][0]['id']);
    
                return view('admin.automations.megasubplug_dashboard')->with($data);
    
            }
    
            else if($slug == 'cloudsimhost'){
                $selection = 'selected';
                //call plans api 
            }

            else if($slug == 'affatech'){

                    if($automation == NULL || $automation->api_public_key == NULL){
                        Session::flash('failure','Please ensure your automation api keys are set');
                        return redirect()->route('admin.settings.index');
                        // return back()->with('status' , 'Please check your settings and ensure keys are set');
                    }

                    $curl = curl_init();        
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://www.affatech.com.ng/api/network/',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Token '.$automation->api_public_key
                    ),
                    ));

                    $response = curl_exec($curl);
                    $response_array = json_decode($response,true); 
                    // dd($response_array); 
                    $data['response_array'] = $response_array;
                    return view('admin.automations.affatech_dashboard')->with($data);   

            }

            else{
                return back();
            }
    
            
        }catch(Exception $exception){
            $errormessage = $exception->getMessage();
            $errorline = $exception->getLine();
            logger("Error occured in fetching plans: $errormessage on $errorline");
            Session::flash('failure','Please ensure your automation api keys are setss..');
            return redirect()->route('admin.settings.index');
        }
       
    }
        
}
