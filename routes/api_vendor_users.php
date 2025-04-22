<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\VendorUsersApi\ProductsVendorController;


// middleware('auth:sanctum')
//can be better later, make it simple for now
Route::get('user/fetch_networks', [ProductsVendorController::class, 'fetch_networks'])->name('api.user.fetch_networks');
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

