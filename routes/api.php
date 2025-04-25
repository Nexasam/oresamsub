<?php

use App\Http\Controllers\ProductWebhookController;
use App\Models\ProductPlan;
use Illuminate\Http\Request;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ExternalIntegration\ApiIntegrationController;
use App\Http\Controllers\ExternalIntegration\Products\ProductsController;
use App\Http\Controllers\ExternalIntegration\Wallets\FundingOptionsController;
use App\Http\Controllers\ExternalIntegration\ApiIntegrationPasswordResetController;
// use App\Http\Controllers\ExternalIntegration\ApiIntegrationController;
// use App\Http\Controllers\ExternalIntegration\Products\ProductsController;
// use App\Http\ExternalIntegration\Controllers\ApiIntegrationPasswordResetController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//FIXED WEBHOOK
Route::post('admin/wallets/crystal_pay_webhook/{id}', [WalletsController::class, 'webhook'])->name('admin.wallet.crystalpay.webhook');
Route::post('admin/products/oresamsub', [ProductWebhookController::class, 'product_webhook'])->name('admin.product.webhook');
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
    

    Route::post('/phone_verification', [ApiIntegrationController::class, 'phone_verification'])->name('phone_verification');
    Route::post('/confirm_phone_verification', [ApiIntegrationController::class, 'confirm_phone_verification'])->name('confirm_phone_verification');
    Route::post('/set_transaction_pin', [ApiIntegrationController::class, 'set_transaction_pin'])->name('set_transaction_pin');
    Route::post('/dashboard', [ApiIntegrationController::class, 'dashboard'])->name('dashboard');
    Route::get('/networks', [ApiIntegrationController::class, 'networks'])->name('networks');
    Route::get('/product_plan_categories', [ApiIntegrationController::class, 'product_plan_category'])->name('product_plan_categories');
    Route::get('/bulk_data_plans', [ApiIntegrationController::class, 'bulk_data_plans'])->name('bulk_data_plans');
    Route::get('/transactions', [ApiIntegrationController::class, 'transactions'])->name('transactions');


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
