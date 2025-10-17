<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\VendorUsersApi\ProductsVendorController;
use App\Http\Controllers\ExternalIntegration\Products\ProductsController;
use App\Http\Controllers\Api\v1\VendorUsersApi\AffiliateProductsVendorController;


// middleware('auth:sanctum')
//can be better later, make it simple for now

//TODO: revamp to make better into a middleware
// $user_details = $this->fetch_user_records_with_token($bearer_token);
// if(! $user_details){
//     return $this->error('Authentication failed...', data: [], code: 403 );    
// }

Route::middleware('api_token')->group(function () {

    Route::get('user/syncplans', [AffiliateProductsVendorController::class, 'syncplans'])->name('api.syncplans');
    // Route::get('user/aff/buy_airtime', [AffiliateProductsVendorController::class, 'buy_airtime'])->name('aff.api.buy_airtime_aff');


    Route::get('user/fetch_networks', [ProductsVendorController::class, 'fetch_networks'])->name('api.user.fetch_networks');
    Route::get('user/fetch_data_plans', [ProductsVendorController::class, 'fetch_data_plans'])->name('api.user.fetch_data_plans');
    Route::get('user/fetch_data_transactions', [ProductsVendorController::class, 'fetch_data_transactions'])->name('api.user.fetch_data_transactions');
    Route::get('user/fetch_transaction', [ProductsVendorController::class, 'fetch_transaction'])->name('api.user.fetch_transaction');
    // Route::get('user/fetch_airtime_plans', [ProductsVendorController::class, 'fetch_airtime_plans'])->name('api.user.fetch_airtime_plans');
    Route::get('user/fetch_airtime_transactions', [ProductsVendorController::class, 'fetch_airtime_transactions'])->name('api.user.fetch_airtime_transactions');
    
    
    

    Route::get('user/fetch_products', [ProductsVendorController::class, 'fetch_products'])->name('api.user.fetch_products');
    Route::get('user/fetch_transactions', [ProductsVendorController::class, 'fetch_transactions'])->name('api.user.fetch_transactions');
    Route::get('user/fetch_single_transaction', [ProductsVendorController::class, 'fetch_single_transaction'])->name('api.user.fetch_single_transaction');
    Route::get('user/fetch_product_plan_categories', [ProductsVendorController::class, 'fetch_product_plan_categories'])->name('api.user.fetch_product_plan_categories');
    Route::get('user/fetch_product_plans', [ProductsVendorController::class, 'fetch_product_plans'])->name('api.user.fetch_product_plans');
    Route::post('user/buy_data', [ProductsVendorController::class, 'buy_data'])->name('api.user.buy_data');
    Route::post('user/buy_airtime', [ProductsVendorController::class, 'buy_airtime'])->name('api.user.buy_airtime');
    Route::post('user/validate_metre_number', [ProductsVendorController::class, 'validate_metre_number'])->name('api.user.validate_metre_number');
    Route::post('user/validate_cable_tv', [ProductsVendorController::class, 'validate_cable_tv'])->name('api.user.validate_cable_tv');
    Route::post('user/buy_electricity', [ProductsVendorController::class, 'buy_electricity'])->name('api.user.buy_electricity');
    Route::post('user/buy_cable_tv', [ProductsVendorController::class, 'buy_cable_tv'])->name('api.user.buy_cable_tv');
    Route::get('user/parent_child_website_syncing/{email}', [ProductsController::class, 'parent_child_website_syncing'])->name('api.parent_child_website_syncing');
});
