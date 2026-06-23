<?php

use App\Http\Controllers\SecurewaveWebhookController;
use App\Models\ProductPlan;
use Illuminate\Http\Request;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ProductWebhookController;
use App\Http\Controllers\ExternalIntegration\ApiIntegrationController;
use App\Http\Controllers\Api\v1\VendorUsersApi\ProductsVendorController;
use App\Http\Controllers\ExternalIntegration\Products\ProductsController;
use App\Http\Controllers\ExternalIntegration\Wallets\FundingOptionsController;
use App\Http\Controllers\ExternalIntegration\ApiIntegrationPasswordResetController;
// use App\Http\Controllers\ExternalIntegration\ApiIntegrationController;
// use App\Http\Controllers\ExternalIntegration\Products\ProductsController;
// use App\Http\ExternalIntegration\Controllers\ApiIntegrationPasswordResetController;


//whatsapp:::
// Route::post('/webhook/whatsapp', [ProductsVendorController::class, 'whatsappHook'])->name('rawapi.whatsapp.hook');
// middleware('whatsapp.token')->
Route::get('/webhook/whatsapp', function (Request $request) {

    $verifyToken = '7fK9xQmP2vL8NwR4YtH3cZd6JbS1eUaG5nX9kMfT2qVp8CrW';

    if (
        $request->hub_mode === 'subscribe' &&
        $request->hub_verify_token === $verifyToken
    ) {
        logger('whatsapp:::your head dey there');
        return response($request->hub_challenge, 200);
    }

    logger('whatsapp:::forbidden');
    return response('Forbidden', 403);
});



///////STRICTLY MSORG STYLE
Route::middleware('api_token')->post('data', [ProductsVendorController::class, 'buy_datav2'])->name('rawapi.user.buy_datav2');
///////STRICTLY MSORG STYLE

// ONE FITALL API
Route::middleware('api_token')->post('buy-service', [ProductsVendorController::class, 'buyService'])->name('rawapi.user.buy_service');

Route::post('recova_create_consent', function (Request $request) {
        
    // $recova_url = "https://recova.ng/recova_ofi_handshake/api/ConsentRequest/CreateConsentRequest";

    // // Initialize cURL
    // $ch = curl_init($recova_url);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 seconds
    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // // Enable verbose output to a file
    // $verbose = fopen('php://temp', 'w+');
    // curl_setopt($ch, CURLOPT_VERBOSE, true);
    // curl_setopt($ch, CURLOPT_STDERR, $verbose);

    // // Execute cURL
    // $response = curl_exec($ch);
    // $errno = curl_errno($ch);
    // $error = curl_error($ch);

    // // Check HTTP response code
    // $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // curl_close($ch);

    // // Output results
    // echo "cURL error number: $errno\n";
    // echo "cURL error message: $error\n";
    // echo "HTTP response code: $http_code\n";
    // echo "Response: $response\n";

    // // Show verbose info
    // rewind($verbose);
    // $verboseLog = stream_get_contents($verbose);
    // // echo "\nVerbose info:\n$verboseLog\n";exit;
    // return json_encode([
    //     'status' => -1,
    //     'message' => "\nVerbose info:\n$verboseLog\n"
    // ]);

    // return $request->all();


        $recova_url = "https://recova.ng/recova_ofi_handshake/api/ConsentRequest/CreateConsentRequest";

        $recova_token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJBUElLZXkiOiI0ZTcyZjAzMi00NGU3LTRmN2QtOTZiOS00NWY2YjZjZDA0NjQiLCJCZWxscyI6IkhSTVlSTlNGTlpQWCIsIkluc3RpdHV0aW9uSWQiOiI0MDI0NSIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6Ik9GSSIsImp0aSI6IjZjYWMzNjMxLTkzZTUtNDQ2OS04NmI1LWI4MGZmNzI5NGNhNyIsImV4cCI6MjA0NjAxMjc4OCwiaXNzIjoicmVjb3ZhLm5nIiwiYXVkIjoicmVjb3ZhLm5nIn0.envUxk5E9dL2rnPMyCyfIeMMEDcrHwmnI7yIicRw5sM';

        // $request_array = [
        //     "bvn"=>"22221006885",
        //     "businessRegistrationNumber"=>"nil",
        //     "taxIdentificationNumber"=>"nil",
        //     "loanReference"=>"LOAN_695a5d57c8b2b",
        //     "customerID"=>"f127921c6984ffc94b78dab9f7faf200",
        //     "customerName"=>"Oluwakayode Onayemi",
        //     "customerEmail"=>"principal@siconcept.org",
        //     "phoneNumber"=>"09018008000",
        //     "loanAmount"=>7000000,
        //     "totalRepaymentExpected"=>8365000,
        //     "loanTenure"=>3,
        //     "linkedAccountNumber"=>"0005280295",
        //     "repaymentType"=>"Collection",
        //     "preferredRepaymentBankCBNCode"=>"100",
        //     "preferredRepaymentAccount"=>"0005280295",
        //     "collectionPaymentSchedules"=>[
        //     [
        //         "repaymentDate"=>"2026-02-06T07:38:04.324Z",
        //         "repaymentAmountInNaira"=>455000
        //     ],
        //     [
        //         "repaymentDate"=>"2026-03-06T07:38:04.324Z",
        //         "repaymentAmountInNaira"=>455000   
        //     ],
        //     [
        //         "repaymentDate"=>"2026-04-06T07:38:04.324Z",
        //         "repaymentAmountInNaira"=>7455000
        //     ]
        //     ]
        // ];

        $request_array = $request->all();

        $request_json = json_encode($request_array);

        /* ----------------------------------------
        | 6. Call Recova API
        ---------------------------------------- */
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => rtrim($recova_url),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $request_json,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . trim($recova_token),
                'Content-Length: ' . strlen($request_json),
            ],
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

            $response   = curl_exec($curl);
            $curl_error = curl_error($curl);
            $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $response_dec = json_decode($response, true);

            if(isset($response_dec['bvn'])){
                return json_encode([
                    'status' => 1,
                    'message' => 'Consent created successfully',
                    'bvn' => $response_dec['bvn'] ?? 'nil',
                    'consentApprovalUrl' => $response_dec['consentApprovalUrl'] ?? 'nil',
                    'response' => $response_dec,
                ]);
            }
            

            /* ----------------------------------------
            | 7. Handle Errors
            ---------------------------------------- */
           
                return json_encode([
                    'status' => -1,
                    'message' => 'Curl execution failed',
                    'curl_error' => $curl_error,
                    'response_arr' => $response_dec,

                ]);
        


});


Route::get('/luminox', function (Request $request) {
    $rec = DB::table('luminoxhealthcareca_posts1')->get();
    return response()->json([
        'status' => 1,
        'data' => $rec
    ]);
});

Route::get('/luminox2', function (Request $request) {
    $rec = DB::table('luminoxhealthcareca_posts2')->get();
    return response()->json([
        'status' => 1,
        'data' => $rec
    ]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//FIXED WEBHOOK

Route::post('webhook/megasub', [WalletsController::class, 'webhook22'])->name('admin.wallet.crystalpay.webhook22');
Route::post('admin/wallets/crystal_pay_webhook/{id}', [WalletsController::class, 'webhook'])->name('admin.wallet.crystalpay.webhook');
Route::post('admin/products/oresamsub', [ProductWebhookController::class, 'product_webhook'])->name('admin.product.webhook');
Route::get('admin/fetch_addons', [AddonController::class, 'fetch_addons'])->name('admin.addons.fetch_addons');
Route::post('admin/wallets/xixapayhook/{id}', [WalletsController::class, 'xixapayhook'])->name('admin.wallet.xixapay.webhook');
Route::post('admin/wallets/securewaveng_hook/{id}', [WalletsController::class, 'securewavehook'])->name('admin.wallet.securwavenghook.webhook');


// Route::post('admin/wallets/securewavehook', [SecurewaveWebhookController::class, 'securewavehook'])->name('admin.wallet.securewavehook.webhook'); //testing securewave
// Route::post('admin/wallets/securewavehook/{id}', [SecurewaveWebhookController::class, 'securewavehook'])->name('admin.wallet.securewavehook.webhook2'); //testing securewave


//WEBHOOK


// MOBILE APP API STARTS
Route::post('v1/external/register', [ApiIntegrationController::class, 'signup'])->name('api.signup');
Route::post('v1/external/login', [ApiIntegrationController::class, 'login'])->name('api.login');
Route::post('v1/external/forgot_password', [ApiIntegrationPasswordResetController::class, 'forgot_password'])->name('api.forgot_password');
Route::get('v1/external/products', [ApiIntegrationController::class, 'products'])->name('products');
Route::get('v1/external/support_information', [ApiIntegrationController::class, 'support_information'])->name('support_information');

// Route::post('v1/external/auth_check', [ApiIntegrationController::class, 'auth_check'])->name('mobile_auth_check');


// validate_user tokeng
Route::group(['prefix'=>'v1/external','as'=>'api.','middleware' =>['auth:sanctum','validate_user']], function(){
    
    Route::put('/update_fingerprint_option', [ApiIntegrationController::class, 'update_fingerprint_option'])->name('update_fingerprint_option');
    Route::put('/update_user_profile', [ApiIntegrationController::class, 'update_user_profile'])->name('update_user_profile'); //discuss this first
    Route::put('/update_user_password', [ApiIntegrationController::class, 'update_user_password'])->name('update_user_password'); //discuss this first
    Route::put('/update_user_pin', [ApiIntegrationController::class, 'update_user_pin'])->name('update_user_pin'); //discuss this first
    

    Route::post('/phone_verification', [ApiIntegrationController::class, 'phone_verification'])->name('phone_verification');
    Route::post('/confirm_phone_verification', [ApiIntegrationController::class, 'confirm_phone_verification'])->name('confirm_phone_verification');
    Route::post('/set_transaction_pin', [ApiIntegrationController::class, 'set_transaction_pin'])->name('set_transaction_pin');
    Route::post('/dashboard', [ApiIntegrationController::class, 'dashboard'])->name('dashboard');
    Route::get('/networks', [ApiIntegrationController::class, 'networks'])->name('networks');
    Route::get('/product_plan_categories', [ApiIntegrationController::class, 'product_plan_category'])->name('product_plan_categories');
    Route::get('/bulk_data_plans', [ApiIntegrationController::class, 'bulk_data_plans'])->name('bulk_data_plans');
    Route::get('/transactions', [ApiIntegrationController::class, 'transactions'])->name('transactions');


    
    Route::middleware('auth:sanctum')->post('get_active_coupons', [ProductsController::class, 'get_active_coupons'])->name('get_active_coupons');
    Route::middleware('auth:sanctum')->post('validate_coupon_code', [ProductsController::class, 'validate_coupon_code'])->name('validate_coupon_code');
    Route::middleware('auth:sanctum')->get('fetch_transactions', [ProductsController::class, 'fetch_transactions'])->name('fetch_transactions');
    Route::middleware('auth:sanctum')->get('fetch_networks', [ProductsController::class, 'fetch_networks'])->name('fetch_networks');
    Route::middleware('auth:sanctum')->get('fetch_single_transaction', [ProductsController::class, 'fetch_single_transaction'])->name('fetch_single_transaction');
    Route::middleware('auth:sanctum')->get('fetch_products', [ProductsController::class, 'fetch_products'])->name('fetch_products');
    Route::middleware('auth:sanctum')->get('fetch_product_plan_categories', [ProductsController::class, 'fetch_product_plan_categories'])->name('fetch_product_plan_categories');
    Route::middleware('auth:sanctum')->get('fetch_product_plans', [ProductsController::class, 'fetch_product_plans'])->name('fetch_product_plans');
    
    Route::middleware('auth:sanctum')->post('buy_data', [ProductsController::class, 'buy_data'])->name('buy_data');

    


    Route::middleware('auth:sanctum')->post('buy_airtime', [ProductsController::class, 'buy_airtime'])->name('buy_airtime');
    Route::middleware('auth:sanctum')->post('validate_metre_number', [ProductsController::class, 'validate_metre_number'])->name('validate_metre_number');
    Route::middleware('auth:sanctum')->post('validate_cable_tv', [ProductsController::class, 'validate_cable_tv'])->name('validate_cable_tv');
    Route::middleware('auth:sanctum')->post('buy_electricity', [ProductsController::class, 'buy_electricity'])->name('buy_electricity');
    Route::middleware('auth:sanctum')->post('buy_cable_tv', [ProductsController::class, 'buy_cable_tv'])->name('buy_cable_tv');

    ////CRYSTAL
    Route::middleware('auth:sanctum')->get('fetch_user_naira_funding_transactions', [FundingOptionsController::class, 'fetch_user_naira_funding_transactions'])->name('fetch_user_naira_funding_transactions');
    Route::middleware('auth:sanctum')->get('fetch_naira_virtual_accounts', [FundingOptionsController::class, 'fetch_naira_virtual_accounts'])->name('fetch_naira_virtual_accounts');
    Route::middleware('auth:sanctum')->post('generate_naira_virtual_accounts', [FundingOptionsController::class, 'generate_naira_virtual_accounts'])->name('generate_naira_virtual_accounts');
    Route::middleware('auth:sanctum')->get('fetch_naira_funding_options', [FundingOptionsController::class, 'fetch_naira_funding_options'])->name('fetch_naira_funding_options');


});
// MOBILE APP API ENDS



Route::prefix('v1')->group(base_path('routes/api_vendor_users.php'));
// Route::post('/api_authenticate', function (Request $request) {
//   return $request->all();
// })->middleware('auth:sanctum');
