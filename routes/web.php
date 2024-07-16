<?php

use App\Http\Middleware\RoleAssess;
use App\Models\LandingPagesSetting;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\RoleAdminAccess;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AirtimeController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\CrystalPayController;
use App\Http\Controllers\ProductPlanController;
use App\Http\Controllers\BulkDataPlanController;
use App\Http\Controllers\ResellerPlanController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\UserProductPlanController;
use App\Http\Controllers\ProductPlanCategoryController;

Route::get('/', function () {
    // dd('e dey');
    $landing_data = LandingPagesSetting::get();
    foreach($landing_data as $landing_component){
        $data[$landing_component->field_name] = $landing_component->field_details;
    }
    // dd($data);
    return view('landing.index')->with($data);
});

Route::get('/access_denied', function () {
    return 'You are not authorized';
})->name('access_denied');





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
Route::middleware(['auth','verified'])->get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
Route::post('admin/wallets/crystal_pay_webhook', [WalletsController::class, 'webhook'])->name('admin.wallet.crystalpay.webhook');



Route::middleware(['auth','verified','admin'])->get('admin/users', [UsersController::class, 'index'])->name('admin.users.index');
Route::middleware(['auth','verified','admin'])->get('admin/users/create', [UsersController::class, 'create'])->name('admin.users.create');
Route::middleware(['auth','verified','admin'])->get('admin/users/{id}/manage_user', [UsersController::class, 'manage_user'])->name('admin.users.manage_user');
Route::middleware(['auth','verified','admin'])->post('admin/users/fund_user_wallet', [UsersController::class, 'fund_user_wallet'])->name('admin.users.fund_user_wallet');
Route::middleware(['auth','verified','admin'])->post('admin/users/store', [UsersController::class, 'store'])->name('admin.users.store');
Route::middleware(['auth','verified','admin'])->get('admin/users/fetch_users', [UsersController::class, 'fetch_users'])->name('admin.users.fetch_users');

Route::middleware(['auth','verified','admin'])->get('admin/networks', [NetworkController::class, 'index'])->name('admin.networks.index');

Route::middleware(['auth','verified','admin'])->get('admin/reseller_plans', [ResellerPlanController::class, 'index'])->name('admin.reseller_plans.index');
Route::middleware(['auth','verified','admin'])->post('admin/reseller_plans/update_name', [ResellerPlanController::class, 'update_name'])->name('admin.reseller_plans.update_name');

Route::middleware(['auth','verified','admin'])->get('admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
Route::middleware(['auth','verified','admin'])->get('admin/roles/{role_id}/permission', [RoleController::class, 'permissions'])->name('admin.roles.permissions');
Route::middleware(['auth','verified','admin'])->post('admin/roles/{role_id}/permission/update', [RoleController::class, 'update_permissions'])->name('admin.roles.permissions.update');


Route::middleware(['auth','verified','admin'])->get('admin/automations/{slug}/view', [AutomationController::class, 'dashboard'])->name('admin.automation.dashboard_view');
// Route::middleware(['auth','verified','admin'])->get('admin/automations/ogdams/view', [AutomationController::class, 'dashboard'])->name('admin.automation.ogdams.dashboard_view');


Route::middleware(['auth','verified','admin'])->get('admin/products', [ProductController::class, 'index'])->name('admin.products.index');
Route::middleware(['auth','verified','admin'])->post('admin/products/store', [ProductController::class, 'store'])->name('admin.products.store');

Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories', [ProductPlanCategoryController::class, 'index'])->name('admin.product_plan_categories.index');
Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/view/{id}', [ProductPlanCategoryController::class, 'view_details'])->name('admin.product_plan_categories.view_details');
Route::middleware(['auth','verified','admin'])->post('admin/product_plan_categories/update', [ProductPlanCategoryController::class, 'update_details'])->name('admin.product_plan_categories.update_details');
Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/admin_fetch_product_plan_categories', [ProductPlanCategoryController::class, 'admin_fetch_product_plan_categories'])->name('admin.product_plan_categories.admin_fetch_product_plan_categories');
Route::middleware(['auth','verified','admin'])->post('admin/product_plan_categories/store', [ProductPlanCategoryController::class, 'store'])->name('admin.product_plan_categories.store');
Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/update_automation', [ProductPlanCategoryController::class, 'updateAutomation'])->name('admin.product_plan_categories.update_automation');

Route::middleware(['auth','verified','admin'])->get('admin/bulk_data_plans/{product_plan_category_id}', [BulkDataPlanController::class, 'index'])->name('admin.bulk_data_plans.index');
Route::middleware(['auth','verified','admin'])->post('admin/bulk_data_plans/store', [BulkDataPlanController::class, 'store'])->name('admin.bulk_data_plans.store');


Route::middleware(['auth','verified','admin'])->get('admin/product_plans', [ProductPlanController::class, 'index'])->name('admin.product_plans.index');
Route::middleware(['auth','verified','admin'])->post('admin/product_plans/store', [ProductPlanController::class, 'store'])->name('admin.product_plans.store');



Route::middleware(['auth','verified','admin'])->get('admin/product_categories', [ProductCategoryController::class, 'index'])->name('admin.product_categories.index');

Route::middleware(['auth','verified','admin'])->get('admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
Route::middleware(['auth','verified','admin'])->post('admin/manage_automations_keys', [AdminSettingsController::class, 'manage_automations_keys'])->name('admin.settings.manage_automations_keys');
Route::middleware(['auth','verified','admin'])->post('admin/update_site_logo', [AdminSettingsController::class, 'manage_site_logo'])->name('admin.settings.manage_site_logo');
Route::middleware(['auth','verified','admin'])->post('admin/manage_global_user_2fa', [AdminSettingsController::class, 'manage_global_user_2fa'])->name('admin.settings.manage_global_user_2fa');
Route::middleware(['auth','verified','admin'])->post('admin/referral_settings', [AdminSettingsController::class, 'manage_referral_settings'])->name('admin.settings.referral_settings');
Route::middleware(['auth','verified','admin'])->post('admin/landing_page_settings', [AdminSettingsController::class, 'manage_landing_page_settings'])->name('admin.settings.manage_landing_page_settings');




Route::middleware(['auth','verified','user'])->get('user/settings', [UserSettingsController::class, 'index'])->name('user.settings.index');
Route::middleware(['auth','verified','user'])->post('user/settings/update_default_wallet', [UserSettingsController::class, 'update_default_wallet'])->name('user.settings.update_default_wallet');
Route::middleware(['auth','verified','user'])->post('user/settings/update_profile', [UserSettingsController::class, 'update_profile'])->name('user.settings.update_profile');
Route::middleware(['auth','verified','user'])->post('user/settings/update_password', [UserSettingsController::class, 'update_password'])->name('user.settings.update_password');
Route::middleware(['auth','verified','user'])->post('user/settings/update_2fa', [UserSettingsController::class, 'update_2fa'])->name('user.settings.update_2fa');



Route::middleware(['auth','verified','user'])->get('user/data/buy_bulk_data/bulk_data_wallet/{data_wallet_id}', [DataController::class, 'buy_bulk_data'])->name('user.data.buy_bulk_data.bulk_data_wallet');
Route::middleware(['auth','verified','user'])->get('user/data/buy_bulk_data', [DataController::class, 'buy_bulk_data'])->name('user.data.buy_bulk_data');
// Route::middleware(['auth','verified','user'])->post('user/data/buy_data_action', [DataController::class, 'buy_data_action'])->name('user.data.buy_data_action');
Route::middleware(['auth','verified','user'])->post('user/data/buy_bulk_data_action', [DataController::class, 'buy_bulk_data_action'])->name('user.data.buy_bulk_data_action');
Route::middleware(['auth','verified','user'])->post('user/data/fetch_bulk_data_plans', [DataController::class, 'fetch_bulk_data_plans'])->name('user.data.fetch_bulk_data_plans');
Route::middleware(['auth','verified','user'])->get('user/data/fetch_bulk_data_plan_details', [DataController::class, 'fetch_bulk_data_plan_details'])->name('user.data.fetch_bulk_data_plan_details');


Route::middleware(['auth','verified','user'])->get('user/data/buy_data', [DataController::class, 'buy_data'])->name('user.data.buy_data');
Route::middleware(['auth','verified','user'])->get('user/data/get_single_bulk_data_wallet/{plan_id}', [DataController::class, 'get_single_bulk_data_wallet'])->name('user.data.get_single_bulk_data_wallet');
Route::middleware(['auth','verified','user'])->get('user/data/store', [DataController::class, 'buy_data_action'])->name('user.data.buy_data_action');
Route::middleware(['auth','verified','user'])->get('user/data/fetch_product_plan_categories', [DataController::class, 'fetch_product_plan_categories'])->name('user.fetch_product_plan_categories'); //TODO: you can add this to a helper controller later
Route::middleware(['auth','verified','user'])->get('user/data/fetch_product_plans', [DataController::class, 'fetch_product_plans'])->name('user.fetch_product_plans'); //TODO: you can add this to a helper controller later

Route::middleware(['auth','verified','user'])->get('user/generate_dynamic_account', [CrystalPayController::class, 'generate_dynamic_account'])->name('user.crystalpay.generate_dynamic_account');


Route::middleware(['auth','verified','user'])->get('user/airtime/buy_airtime', [AirtimeController::class, 'buy_airtime'])->name('user.airtime.buy_airtime');

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
