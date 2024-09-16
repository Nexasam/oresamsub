<?php

use App\Models\AdminColorSetting;
use App\Models\ProductPlan;
use App\Http\Middleware\RoleAssess;
use App\Models\LandingPagesSetting;
use App\Models\SiteImage;
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
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BulkDataPlanController;
use App\Http\Controllers\ResellerPlanController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserTwoFactorController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\UserProductPlanController;
use App\Http\Controllers\CableSubscriptionController;
use App\Http\Controllers\ProductPlanCategoryController;
use App\Http\Controllers\ElectricitySubscriptionController;

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::get('/', function () {
    // dd('e dey');
    $data = [];
    $site_images_data = SiteImage::get();
    if(count($site_images_data) > 0){
        foreach($site_images_data as $site_image){
            $data[$site_image->image_category] = $site_image->image_name;
        }
    }
   


    $landing_data = LandingPagesSetting::get();
    foreach($landing_data as $landing_component){
        $data[$landing_component->field_name] = $landing_component->field_details;
    }

    $site_colors = AdminColorSetting::get();
    foreach($site_colors as $site_color){
        if($site_color->color_name == 'site_landing_analytics_color'){
            $data['site_landing_analytics_color_r'] = explode(', ',$site_color->color_value)[0];
            $data['site_landing_analytics_color_g'] = explode(', ',$site_color->color_value)[1];
            $data['site_landing_analytics_color_b'] = explode(', ',$site_color->color_value)[2];
        }else if($site_color->color_name == 'admin_site_color'){
            $data['admin_site_color_r'] = explode(', ',$site_color->color_value)[0];
            $data['admin_site_color_g'] = explode(', ',$site_color->color_value)[1];
            $data['admin_site_color_b'] = explode(', ',$site_color->color_value)[2];
        }else if($site_color->color_name == 'site_landing_review_color'){
            $data['site_landing_review_color_r'] = explode(', ',$site_color->color_value)[0];
            $data['site_landing_review_color_g'] = explode(', ',$site_color->color_value)[1];
            $data['site_landing_review_color_b'] = explode(', ',$site_color->color_value)[2];
        }     
        else{
            $data[$site_color->color_name] = $site_color->color_value;

        }
    }

    // dd($data);

    $product_plans = ProductPlan::get();
    $data['product_plans'] = $product_plans;

    // dd($data);
    return view('landing.index')->with($data);
});

Route::get('/access_denied', function () {
    return 'You are not authorized. <a href="'.route('login').'">Return back</a>';
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


// Route::middleware(['auth', 'twofactor'])->group(function () {
//     Route::get('verify/resend', [UserTwoFactorController::class, 'resend'])->name('verify.resend');
//     Route::resource('verify', UserTwoFactorController::class)->only(['index', 'store']);
// });

//this will be adjusted later
Route::middleware(['auth','verified'])->get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
Route::get('product_plans/fetch_public_product_plans', [ProductPlanController::class, 'fetch_public_product_plans'])->name('fetch_public_product_plans');




Route::middleware(['auth','verified','admin'])->get('admin/users', [UsersController::class, 'index'])->name('admin.users.index');
Route::middleware(['auth','verified','admin'])->get('admin/users/create', [UsersController::class, 'create'])->name('admin.users.create');
Route::middleware(['auth','verified','admin'])->get('admin/users/{id}/manage_user', [UsersController::class, 'manage_user'])->name('admin.users.manage_user');
Route::middleware(['auth','verified','admin'])->post('admin/users/fund_user_wallet', [UsersController::class, 'fund_user_wallet'])->name('admin.users.fund_user_wallet');
Route::middleware(['auth','verified','admin'])->post('admin/users/reset_2fa', [UsersController::class, 'reset_2fa'])->name('admin.users.reset_2fa');
Route::middleware(['auth','verified','admin'])->post('admin/users/store', [UsersController::class, 'store'])->name('admin.users.store');
Route::middleware(['auth','verified','admin'])->get('admin/users/fetch_users', [UsersController::class, 'fetch_users'])->name('admin.users.fetch_users');
Route::middleware(['auth','verified','admin'])->get('admin/users/toggle_verification_status', [UsersController::class, 'toggle_verification_status'])->name('admin.users.toggle_verification_status');

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
Route::middleware(['auth','verified','admin'])->post('admin/products/update', [ProductController::class, 'update'])->name('admin.products.update');



Route::middleware(['auth','verified','admin'])->get('admin/toggle_plan_category_visibility', [ProductPlanCategoryController::class, 'toggle_plan_category_visibility'])->name('admin.product_plan_categories.toggle_plan_category_visibility');
Route::middleware(['auth','verified','admin'])->get('admin/toggle_hot_sales', [ProductPlanCategoryController::class, 'toggle_hot_sales'])->name('admin.product_plan_categories.toggle_hot_sales');
Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories', [ProductPlanCategoryController::class, 'index'])->name('admin.product_plan_categories.index');
Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/view/{id}', [ProductPlanCategoryController::class, 'view_details'])->name('admin.product_plan_categories.view_details');
Route::middleware(['auth','verified','admin'])->post('admin/product_plan_categories/update', [ProductPlanCategoryController::class, 'update_details'])->name('admin.product_plan_categories.update_details');
Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/admin_fetch_product_plan_categories', [ProductPlanCategoryController::class, 'admin_fetch_product_plan_categories'])->name('admin.product_plan_categories.admin_fetch_product_plan_categories');
Route::middleware(['auth','verified','admin'])->post('admin/product_plan_categories/store', [ProductPlanCategoryController::class, 'store'])->name('admin.product_plan_categories.store');
Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/update_automation', [ProductPlanCategoryController::class, 'updateAutomation'])->name('admin.product_plan_categories.update_automation');

Route::middleware(['auth','verified','admin'])->get('admin/bulk_data_plans/{product_plan_category_id}', [BulkDataPlanController::class, 'index'])->name('admin.bulk_data_plans.index');
Route::middleware(['auth','verified','admin'])->post('admin/bulk_data_plans/store', [BulkDataPlanController::class, 'store'])->name('admin.bulk_data_plans.store');

Route::middleware(['auth','verified','admin'])->get('admin/transactions/admin_fetch_transactions', [TransactionController::class, 'admin_fetch_transactions'])->name('admin.transactions.admin_fetch_transactions');
Route::middleware(['auth','verified','admin'])->get('admin/transactions/index', [TransactionController::class, 'admin_all_transactions'])->name('admin.transactions.index');
Route::middleware(['auth','verified','user'])->get('user/transactions/user_fetch_transactions', [TransactionController::class, 'user_fetch_transactions'])->name('user.transactions.user_fetch_transactions');
Route::middleware(['auth','verified','user'])->get('user/transactions/index', [TransactionController::class, 'user_all_transactions'])->name('user.transactions.index');


Route::middleware(['auth','verified','admin'])->get('admin/product_plans', [ProductPlanController::class, 'index'])->name('admin.product_plans.index');
Route::middleware(['auth','verified','admin'])->get('admin/product_plans/product_plan_details/{id}', [ProductPlanController::class, 'product_plan_details'])->name('admin.product_plans.product_plan_details');
Route::middleware(['auth','verified','admin'])->post('admin/product_plans/store', [ProductPlanController::class, 'store'])->name('admin.product_plans.store');
Route::middleware(['auth','verified','admin'])->post('admin/product_plans/update', [ProductPlanController::class, 'update'])->name('admin.product_plans.update');
Route::middleware(['auth','verified','admin'])->get('admin/product_plans/fetch_product_plans', [ProductPlanController::class, 'admin_fetch_product_plans'])->name('admin.product_plans.admin_fetch_product_plans');
Route::middleware(['auth','verified','admin'])->get('admin/toggle_product_visibility', [ProductPlanController::class, 'toggle_product_visibility'])->name('admin.product_plans.toggle_product_visibility');
Route::middleware(['auth','verified','admin'])->get('admin/toggle_product_public_visibility', [ProductPlanController::class, 'toggle_product_public_visibility'])->name('admin.product_plans.toggle_product_public_visibility');



Route::middleware(['auth','verified','admin'])->get('admin/product_categories', [ProductCategoryController::class, 'index'])->name('admin.product_categories.index');

Route::middleware(['auth','verified','admin'])->get('admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
Route::middleware(['auth','verified','admin'])->post('admin/update_webhook_suffix_string', [AdminSettingsController::class, 'update_webhook_suffix_string'])->name('admin.settings.update_webhook_suffix_string');
Route::middleware(['auth','verified','admin'])->post('admin/manage_automations_keys', [AdminSettingsController::class, 'manage_automations_keys'])->name('admin.settings.manage_automations_keys');
Route::middleware(['auth','verified','admin'])->post('admin/update_funding_options', [AdminSettingsController::class, 'update_funding_options'])->name('admin.settings.update_funding_options'); //
Route::middleware(['auth','verified','admin'])->post('admin/settings/update', [AdminSettingsController::class, 'update_settings'])->name('admin.settings.update'); //
Route::middleware(['auth','verified','admin'])->post('admin/add_funding_option_bank_code', [AdminSettingsController::class, 'add_funding_option_bank_code'])->name('admin.settings.add_funding_option_bank_code'); //
Route::middleware(['auth','verified','admin'])->post('admin/update_site_logo', [AdminSettingsController::class, 'manage_site_logo'])->name('admin.settings.manage_site_logo');
Route::middleware(['auth','verified','admin'])->post('admin/update_site_images', [AdminSettingsController::class, 'manage_site_images'])->name('admin.settings.manage_site_images');
Route::middleware(['auth','verified','admin'])->post('admin/update_site_color', [AdminSettingsController::class, 'manage_site_colors'])->name('admin.settings.manage_site_colors');
Route::middleware(['auth','verified','admin'])->post('admin/manage_global_user_2fa', [AdminSettingsController::class, 'manage_global_user_2fa'])->name('admin.settings.manage_global_user_2fa');
Route::middleware(['auth','verified','admin'])->post('admin/referral_settings', [AdminSettingsController::class, 'manage_referral_settings'])->name('admin.settings.referral_settings');
Route::middleware(['auth','verified','admin'])->post('admin/landing_page_settings', [AdminSettingsController::class, 'manage_landing_page_settings'])->name('admin.settings.manage_landing_page_settings');

Route::middleware(['auth','verified','admin'])->get('admin/profile/index', [UsersController::class, 'admin_manage_profile'])->name('admin.manage_profile.index');


Route::middleware(['auth','verified','admin'])->get('admin/wallet_creditings/index', [WalletsController::class, 'wallet_creditings'])->name('admin.wallet_creditings.index');


Route::middleware(['auth','verified','user'])->get('user/profile/index', [UsersController::class, 'manage_profile'])->name('user.manage_profile.index');
Route::middleware(['auth','verified','user'])->get('user/generate_user_bulk_data_wallets', [UsersController::class, 'generate_user_bulk_data_wallets'])->name('user.generate_user_bulk_data_wallets');
Route::middleware(['auth','verified','user'])->get('user/settings', [UserSettingsController::class, 'index'])->name('user.settings.index');
Route::middleware(['auth','verified','user'])->post('user/settings/update_default_wallet', [UserSettingsController::class, 'update_default_wallet'])->name('user.settings.update_default_wallet');
Route::middleware(['auth','verified','user'])->post('user/settings/update_profile', [UserSettingsController::class, 'update_profile'])->name('user.settings.update_profile');
Route::middleware(['auth','verified','user'])->post('user/settings/update_password', [UserSettingsController::class, 'update_password'])->name('user.settings.update_password');
Route::middleware(['auth','verified','user'])->post('user/settings/update_pin', [UserSettingsController::class, 'update_pin'])->name('user.settings.update_pin');
Route::middleware(['auth','verified','user'])->post('user/settings/update_2fa', [UserSettingsController::class, 'update_2fa'])->name('user.settings.update_2fa');



Route::middleware(['auth','verified','user'])->get('user/data/buy_bulk_data/bulk_data_wallet/{data_wallet_id}', [DataController::class, 'buy_bulk_data'])->name('user.data.buy_bulk_data.bulk_data_wallet');
Route::middleware(['auth','verified','user'])->get('user/data/buy_bulk_data', [DataController::class, 'buy_bulk_data'])->name('user.data.buy_bulk_data');
// Route::middleware(['auth','verified','user'])->post('user/data/buy_data_action', [DataController::class, 'buy_data_action'])->name('user.data.buy_data_action');
Route::middleware(['auth','verified','user'])->post('user/data/buy_bulk_data_action', [DataController::class, 'buy_bulk_data_action'])->name('user.data.buy_bulk_data_action');
Route::middleware(['auth','verified','user'])->post('user/data/fetch_bulk_data_plans', [DataController::class, 'fetch_bulk_data_plans'])->name('user.data.fetch_bulk_data_plans');
Route::middleware(['auth','verified','user'])->get('user/data/fetch_bulk_data_plan_details', [DataController::class, 'fetch_bulk_data_plan_details'])->name('user.data.fetch_bulk_data_plan_details');

//available to both user and admin: first 4 majorely for users
Route::middleware(['auth','verified'])->get('transactions/fetch_airtime_transactions', [AirtimeController::class, 'fetch_airtime_transactions'])->name('transactions.fetch_airtime_transactions');
Route::middleware(['auth','verified'])->get('transactions/fetch_data_transactions', [DataController::class, 'fetch_data_transactions'])->name('transactions.fetch_data_transactions');
Route::middleware(['auth','verified'])->get('transactions/fetch_data_wallet_transactions', [DataController::class, 'fetch_data_wallet_transactions'])->name('transactions.fetch_data_wallet_transactions');
Route::middleware(['auth','verified'])->get('transactions/fetch_cable_transactions', [CableSubscriptionController::class, 'fetch_cable_transactions'])->name('transactions.fetch_cable_transactions');
Route::middleware(['auth','verified'])->get('transactions/fetch_electricity_transactions', [ElectricitySubscriptionController::class, 'fetch_electricity_transactions'])->name('transactions.fetch_electricity_transactions');
Route::middleware(['auth','verified'])->get('transactions/details/{id}', [TransactionController::class, 'transaction_details'])->name('transactions.transaction_details');
Route::middleware(['auth','verified'])->post('transactions/transaction_refund', [TransactionController::class, 'transaction_refund'])->name('transactions.transaction_refund');


Route::middleware(['auth','verified','user'])->get('user/airtime/buy_airtime', [AirtimeController::class, 'buy_airtime'])->name('user.airtime.buy_airtime');
Route::middleware(['auth','verified','user'])->get('user/airtime/store', [AirtimeController::class, 'buy_airtime_action'])->name('user.airtime.buy_airtime_action');
Route::middleware(['auth','verified','user'])->get('user/airtime/buy_airtime_by_plan_category/{id}', [AirtimeController::class, 'buy_airtime_by_plan_category'])->name('user.airtime.buy_airtime_by_plan_category');
Route::middleware(['auth','verified','user'])->get('user/airtime/fetch_single_airtime_plan', [AirtimeController::class, 'fetch_single_airtime_plan'])->name('user.airtime.fetch_single_airtime_plan');


//CABLE TV: user.cabletv.buy_cable_subscription
Route::middleware(['auth','verified','user'])->get('user/cable_subscription/buy_cable_subscription', [CableSubscriptionController::class, 'buy_cable_subscription'])->name('user.cable_subscription.buy_cable_subscription');
Route::middleware(['auth','verified','user'])->get('user/cable_subscription/store', [CableSubscriptionController::class, 'buy_cable_subscription_action'])->name('user.cable_subscription.buy_cable_subscription_action');
Route::middleware(['auth','verified','user'])->get('user/cable_subscription/buy_cable_subscription_by_plan_category/{id}', [CableSubscriptionController::class, 'buy_cable_subscription_by_plan_category'])->name('user.cable_subscription.buy_cable_subscription_by_plan_category');
Route::middleware(['auth','verified','user'])->get('user/cable_subscription/validate_smart_card_number', [CableSubscriptionController::class, 'validate_smart_card_number'])->name('user.cable_subscription.validate_smart_card_number');


//ELECTRICITY: electricity
Route::middleware(['auth','verified','user'])->get('user/electricity/buy_electricity', [ElectricitySubscriptionController::class, 'buy_electricity_subscription'])->name('user.electricity.buy_electricity_subscription');
Route::middleware(['auth','verified','user'])->get('user/electricity/store', [ElectricitySubscriptionController::class, 'buy_electricity_subscription_action'])->name('user.electricity.buy_electricity_subscription_action');
Route::middleware(['auth','verified','user'])->get('user/electricity/buy_electricity_subscription_by_plan_category/{id}', [ElectricitySubscriptionController::class, 'buy_electricity_subscription_by_plan_category'])->name('user.electricity.buy_electricity_subscription_by_plan_category');
Route::middleware(['auth','verified','user'])->get('user/electricity/validate_metre_number', [ElectricitySubscriptionController::class, 'validate_metre_number'])->name('user.electricity.validate_metre_number');

//ELECTRICITY

Route::middleware(['auth','verified','user'])->get('user/data/buy_data', [DataController::class, 'buy_data'])->name('user.data.buy_data');
Route::middleware(['auth','verified','user'])->get('user/data/buy_data_by_plan_category/{id}', [DataController::class, 'buy_data_by_plan_category'])->name('user.data.buy_data_by_plan_category');
Route::middleware(['auth','verified','user'])->get('user/data/get_single_bulk_data_wallet/{plan_id}', [DataController::class, 'get_single_bulk_data_wallet'])->name('user.data.get_single_bulk_data_wallet');
Route::middleware(['auth','verified','user'])->get('user/data/store', [DataController::class, 'buy_data_action'])->name('user.data.buy_data_action');
Route::middleware(['auth','verified','user'])->get('user/data/fetch_product_plan_categories', [DataController::class, 'fetch_product_plan_categories'])->name('user.fetch_product_plan_categories'); //TODO: you can add this to a helper controller later
Route::middleware(['auth','verified','user'])->get('user/data/fetch_product_plans', [DataController::class, 'fetch_product_plans'])->name('user.fetch_product_plans'); //TODO: you can add this to a helper controller later

Route::middleware(['auth','verified','user'])->get('user/generate_dynamic_account', [CrystalPayController::class, 'generate_dynamic_account'])->name('user.crystalpay.generate_dynamic_account');
Route::middleware(['auth','verified','user'])->post('user/generate_virtual_account', [CrystalPayController::class, 'generate_virtual_account'])->name('user.crystalpay.generate_virtual_account');


Route::middleware(['auth','verified','user'])->get('user/wallet/index', [WalletsController::class, 'index'])->name('user.wallet.index');
Route::middleware(['auth','verified','user'])->get('user/wallet/fund_wallet', [WalletsController::class, 'fund_wallet'])->name('user.wallet.fund_wallet');
Route::middleware(['auth','verified','user'])->post('user/wallet/generate_virtual_account', [WalletsController::class, 'generate_virtual_account'])->name('user.wallet.generate_virtual_account');
Route::middleware(['auth','verified'])->get('transactions/fetch_crystal_pay_funding_transactions', [WalletsController::class, 'fetch_crystal_pay_funding_transactions'])->name('transactions.fetch_crystal_pay_funding_transactions');
Route::middleware(['auth','verified'])->get('transactions/fetch_crystal_pay_pending_transactions', [WalletsController::class, 'fetch_crystal_pay_pending_transactions'])->name('transactions.fetch_crystal_pay_pending_transactions');
Route::middleware(['auth','verified'])->get('transactions/pending_funding_transactions', [WalletsController::class, 'pending_funding_transactions'])->name('admin.transactions.pending_funding_transactions');


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
