<?php

use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AirtimeController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\ProductPlanController;
use App\Http\Controllers\ResellerPlanController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\UserProductPlanController;
use App\Http\Controllers\ProductPlanCategoryController;

Route::get('/', function () {
    // dd('e dey');
    return view('landing.index');
});

// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');





// ADMIN STARTS HERE
// ADMIN STARTS HERE
// ADMIN STARTS HERE
// ADMIN STARTS HERE
// ADMIN STARTS HERE
// ADMIN STARTS HERE
// ADMIN STARTS HERE
// ADMIN STARTS HERE
// ADMIN STARTS HERE
// Route::get('admin/users/create_user', function () {
//     return view('admin.create_user');
// })->name('admin.users.create');
// // Route::get('/admin/users', function () {
// //     return view('users');
// // })->middleware(['auth', 'verified'])->name('admin.users');


//this will be adjusted later
Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

Route::get('admin/wallets/webhook', [WalletsController::class, 'webhook'])->name('admin.wallet.crystalpay.webhook');

Route::middleware(['auth','verified'])->get('admin/users', [UsersController::class, 'index'])->name('admin.users.index');
Route::middleware(['auth','verified'])->get('admin/users/create', [UsersController::class, 'create'])->name('admin.users.create');
Route::middleware(['auth','verified'])->post('admin/users/store', [UsersController::class, 'store'])->name('admin.users.store');
Route::middleware(['auth','verified'])->get('admin/users/fetch_users', [UsersController::class, 'fetch_users'])->name('admin.users.fetch_users');

Route::middleware(['auth','verified'])->get('admin/networks', [NetworkController::class, 'index'])->name('admin.networks.index');

Route::middleware(['auth','verified'])->get('admin/reseller_plans', [ResellerPlanController::class, 'index'])->name('admin.reseller_plans.index');
Route::middleware(['auth','verified'])->post('admin/reseller_plans/update_name', [ResellerPlanController::class, 'update_name'])->name('admin.reseller_plans.update_name');


Route::middleware(['auth','verified'])->get('admin/automations/{slug}/view', [AutomationController::class, 'dashboard'])->name('admin.automation.dashboard_view');


Route::middleware(['auth','verified'])->get('admin/products', [ProductController::class, 'index'])->name('admin.products.index');
Route::middleware(['auth','verified'])->post('admin/products/store', [ProductController::class, 'store'])->name('admin.products.store');

Route::middleware(['auth','verified'])->get('admin/product_plan_categories', [ProductPlanCategoryController::class, 'index'])->name('admin.product_plan_categories.index');
Route::middleware(['auth','verified'])->get('admin/product_plan_categories/update_automation', [ProductPlanCategoryController::class, 'updateAutomation'])->name('admin.product_plan_categories.update_automation');


Route::middleware(['auth','verified'])->get('admin/product_plans', [ProductPlanController::class, 'index'])->name('admin.product_plans.index');
Route::middleware(['auth','verified'])->post('admin/product_plans/store', [ProductPlanController::class, 'store'])->name('admin.product_plans.store');



Route::middleware(['auth','verified'])->get('admin/product_categories', [ProductCategoryController::class, 'index'])->name('admin.product_categories.index');

Route::middleware(['auth','verified'])->get('admin/settings', [SettingsController::class, 'index'])->name('admin.settings.index');

Route::get('user/data/buy_data', [DataController::class, 'buy_data'])->name('user.data.buy_data');
Route::get('user/data/store', [DataController::class, 'buy_data_action'])->name('user.data.buy_data_action');
Route::get('user/data/fetch_product_plan_categories', [DataController::class, 'fetch_product_plan_categories'])->name('user.fetch_product_plan_categories'); //TODO: you can add this to a helper controller later
Route::get('user/data/fetch_product_plans', [DataController::class, 'fetch_product_plans'])->name('user.fetch_product_plans'); //TODO: you can add this to a helper controller later

Route::get('user/airtime/buy_airtime', [AirtimeController::class, 'buy_airtime'])->name('user.airtime.buy_airtime');

//ADMIN ENDS HERE
//ADMIN ENDS HERE
//ADMIN ENDS HERE
//ADMIN ENDS HERE
//ADMIN ENDS HERE
//ADMIN ENDS HERE
//ADMIN ENDS HERE
//ADMIN ENDS HERE
//ADMIN ENDS HERE
//ADMIN ENDS HERE



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
