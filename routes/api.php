<?php

use App\Models\ProductPlan;
use Illuminate\Http\Request;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\MobileApiController;
use App\Http\Controllers\UserDashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [MobileApiController::class, 'mobile_login'])->name('api.mobile_login');
Route::post('/register', [MobileApiController::class, 'mobile_signup'])->name('api.mobile_signup');
Route::middleware('auth:sanctum')->get('/mobile_networks', [MobileApiController::class, 'mobile_networks'])->name('api.mobile_networks');
Route::middleware('auth:sanctum')->get('/mobile_product_plan_categories', [MobileApiController::class, 'mobile_product_plan_category'])->name('api.mobile_networks');
Route::middleware('auth:sanctum')->get('/mobile_products', [MobileApiController::class, 'mobile_products'])->name('api.mobile_products');
Route::middleware('auth:sanctum')->get('/mobile_bulk_data_plans', [MobileApiController::class, 'mobile_bulk_data_plans'])->name('api.mobile_bulk_data_plans');
Route::middleware('auth:sanctum')->get('/mobile_transactions', [MobileApiController::class, 'mobile_transactions'])->name('api.mobile_transactions');


Route::middleware('auth:sanctum')->post('/mobile_auth_check', [MobileApiController::class, 'mobile_auth_check'])->name('api.mobile_auth_check');

// Route::post('/api_authenticate', function (Request $request) {
//   return $request->all();
// })->middleware('auth:sanctum');
