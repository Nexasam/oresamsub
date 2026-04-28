<?php

use App\Models\User;
use App\Models\SiteImage;
use App\Models\ProductPlan;
use App\Models\SiteTemplate;
use App\Models\AdminColorSetting;
use App\Models\PlanProfitSetting;
use App\Models\UniqueProductPlan;
use App\Http\Middleware\RoleAssess;
use App\Models\LandingPagesSetting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\DataController;
use App\Http\Controllers\RoleController;
// use App\Http\Middleware\RoleAdminAccess;
// use App\Models\ProductPlanCustomPricing;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AirtimeController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletsController;
// use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MarketersController;
// use App\Http\Controllers\MigrationController;
use App\Http\Controllers\QuickToolController;
use App\Http\Controllers\Template2Controller;
// use App\Http\Controllers\WalletLogController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\CrystalPayController;
use App\Http\Controllers\WalletLogsController;
use App\Http\Controllers\CommissionsController;
use App\Http\Controllers\CouponCodesController;
// use App\Http\Controllers\NewTemplateController;
use App\Http\Controllers\PriceChangeController;
use App\Http\Controllers\ProductPlanController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BulkDataPlanController;
use App\Http\Controllers\InertiaLoginController;
use App\Http\Controllers\ResellerPlanController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Services\UniqueProductPlansService;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\MultilanguageController;
use App\Http\Controllers\UserDashboardController;
// use App\Http\Controllers\UserTwoFactorController;
use App\Http\Services\PlansProfitSettingsService;
use App\Http\Services\BizProfitCalculationService;
use App\Http\Controllers\DynamicAccountsController;
use App\Http\Controllers\ProductCategoryController;
// use App\Http\Controllers\UserProductPlanController;
use App\Http\Controllers\VirtualAccountsController;
use App\Http\Controllers\InertiaDashboardController;
use App\Http\Controllers\UserVerificationController;
use App\Http\Controllers\CableSubscriptionController;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;
use App\Http\Controllers\PlanProfitSettingsController;
use App\Http\Controllers\UniqueProductPlansController;
use App\Http\Controllers\WalletFundingPromoController;
use App\Http\Controllers\ProductPlanCategoryController;
use App\Http\Controllers\ReprocessTransactionController;
use App\Http\Controllers\DailyCustomerFollowupController;
use App\Http\Controllers\ElectricitySubscriptionController;
use App\Http\Controllers\ProductPlanCustomPricingController;

   
//test ui
Route::get('/testui', function () {
    return view('welcome2');
})->name('testui');


/////////////////LATER: FILAMENT IS THE NEW ADMIN UI 
/////NEW ADMIN UI 


// Inertia routes
Route::get('/login', [InertiaLoginController::class, 'create'])->name('login');
Route::post('/login', [InertiaLoginController::class, 'store'])->name('inertia.login.store');
  



Route::get('oresamsub/newlanding', fn () => view('oresamsub.landing.new'))->name('ore.landing');

Route::get('oresamsub/register', fn () => view('oresamsub.auth.register'))->name('ore.register');
            
Route::middleware(['set_locale'])->group(function () {


            Route::get('oresamsub/set_pin', fn () => view('oresamsub.pages.set_pin'))->name('ore.set_pin');
           
            Route::get('/profit', function (): array {
                $updateplan = (new BizProfitCalculationService())->calculate_profit();
                return $updateplan;
            });


            // ORESAMSUB WEBPWA V1: ROUTES (wrapped in auth middleware)
            Route::middleware(['auth','set_transaction_pin'])->group(function () {

                Route::get('/user/top-favorite-data', [ProductPlanController::class, 'topFavoriteData'])->name('user.favorite_data');


                //   INERTIAJS
                Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');   
                Route::get('/dashboard/transactions', [UserDashboardController::class, 'fetchTransactions'])->name('dashboard.transactions.fetch');   
                Route::get('/data', [InertiaDashboardController::class, 'data'])->name('inertia.data.index');   
                Route::get('/airtime', [InertiaDashboardController::class, 'airtime'])->name('inertia.airtime.index');   
                Route::get('/cable', [InertiaDashboardController::class, 'cable'])->name('inertia.cable.index');   
                Route::get('/electricity', [InertiaDashboardController::class, 'electricity'])->name('inertia.electricity.index');   
                Route::get('/virtual-accounts', [InertiaDashboardController::class, 'virtual_accounts'])->name('inertia.virtual_accounts.index');   
                Route::get('/transactions', [InertiaDashboardController::class, 'transactions'])->name('inertia.transactions.index');   
        
                Route::get('oresamsub/airtime', function () {
                    $data['networks'] = App\Models\Network::get();
                    return view('oresamsub.pages.airtime')->with($data);
                })->name('ore.airtime');

                Route::get('oresamsub/transactions', function () {
                    $data['transactions'] = App\Models\Transaction::with(relations: 'product_plan')->where('user_id',auth()->id())->limit(200)->latest()->get();
                    return view('oresamsub.pages.transactions')->with($data);
                })->name('ore.transactions');

                Route::post('oresamsub/airtime/submit', fn () => view('oresamsub.pages.airtime.submit'))->name('ore.airtime.submit');

                Route::get('oresamsub/data', function () {
                    $data['networks'] = App\Models\Network::get();
                    return view('oresamsub.pages.data')->with($data);
                })->name('ore.data');


                Route::get('oresamsub/datav2', function () {
                    $data['networks'] = App\Models\Network::get();
                    return view('oresamsub.pages.datav2')->with($data);
                })->name('ore.datav2');

                Route::post('oresamsub/data/submit', fn () => view('oresamsub.pages.data.submit'))->name('ore.data.submit');

                Route::get('oresamsub/cable', function () {
                    $product = App\Models\Product::select('id')->where('slug', 'cable_subscription')->first();
                    $product_plan_categories = App\Models\ProductPlanCategory::select('id','product_plan_category_name')->where('product_id', $product->id)->get();
                    $data['product'] = $product;
                    $data['product_plan_categories'] = $product_plan_categories;
                    return view('oresamsub.pages.cable')->with($data);
                })->name('ore.cable');

                Route::get('oresamsub/electricity', function () {
                    $product = App\Models\Product::select('id')->where('slug', 'utility_bills')->first();
                    $product_plan_categories = App\Models\ProductPlanCategory::select('id','product_plan_category_name')->where('product_id', $product->id)->get();
                    $data['product'] = $product;
                    $data['product_plan_categories'] = $product_plan_categories;
                    return view('oresamsub.pages.electricity')->with($data);
                })->name('ore.electricity');

                Route::get('oresamsub/virtual-accts', function () {
                    $virtualccts = App\Models\UserVirtualAccount::select('id','bank_name','account_name','account_number')->where('user_id',auth()->id())->get();
                    $data['virtualccts'] = $virtualccts;
                    // dd($data);
                    return view('oresamsub.pages.virtual_accounts')->with($data);
                })->name('ore.virtual_accounts');


                ///STRICTLY MARKETERS 
                 Route::middleware(['marketer'])->group(function () {
                    Route::get('/marketer/dashboard', [MarketersController::class, 'index'])->name('marketer.dashboard');
                    Route::get('/marketer/stats', [MarketersController::class, 'stats'])->name('marketer.stats'); // AJAX
                });


             
            });

            //privacy controller
            Route::get('privacy/index', [PrivacyController::class, 'index']);


            // migration tools
            // Route::get('migrate_users', [MigrationController::class, 'migrate_users']);
            // Route::get('migrate_accounts', [MigrationController::class, 'migrate_accounts']);


            Route::get('logs', [LogViewerController::class, 'index']);

            //language transslation: yoruba, igbo, hausa

            Route::get('/lang/{locale}', function ($locale) {
                // Sanitize and validate locale
                $supportedLocales = ['en', 'yo', 'ig', 'ha'];

                if (in_array($locale, $supportedLocales)) {
                    Session::put('locale', $locale);
                    App::setLocale($locale); // Optional: Immediate effect in this request
                } else {
                    abort(400, 'Unsupported language');
                }

                return redirect()->back();
            })->name('lang.switch');

 
            // NEW TEMPLATE START: temporal routes
            Route::get('template2', [Template2Controller::class, 'index'])->name('template2.index');
            Route::get('template2/login', [Template2Controller::class, 'login'])->name('template2.login');
            Route::get('template2/signup', [Template2Controller::class, 'signup'])->name('template2.signup');
            Route::get('template2/forgot-password', [Template2Controller::class, 'forgot_password'])->name('template2.forgot_password');
            Route::get('template2/dashboard', [Template2Controller::class, 'dashboard'])->name('template2.dashboard');
            Route::get('template2/buy_data', [Template2Controller::class, 'buy_data'])->name('template2.buy_data');
            Route::get('template2/buy_airtime', [Template2Controller::class, 'buy_airtime'])->name('template2.buy_airtime');
            Route::get('template2/buy_cable', [Template2Controller::class, 'buy_cable'])->name('template2.buy_cable');
            Route::get('template2/buy_electricity', [Template2Controller::class, 'buy_electricity'])->name('template2.buy_electricity');
            Route::get('template2/api_docs', [Template2Controller::class, 'api_docs'])->name('template2.api_docs');
            // NEW TEMPLATE END



            //clear browser cache
            Route::get('/clear-cache', function() {
                Artisan::call('cache:clear');
                return redirect()->route('login');
            })->name('artisan.clear_cache');



            Route::get('/unique-plans', function (): array {
                //fetch unique plans: network, size, validity
                $productplans = ProductPlan::all();


                $checkunique = UniqueProductPlan::latest()->first();
                $lastkey = $checkunique->api_id ?? 0;
                $nextcount = $lastkey + 5;

                foreach($productplans as $key=>$productplan){
                    $size = $productplan->data_size_in_mb;
                    $validity = $productplan->validity_in_days;
                    $network_id = $productplan->product_plan_category->network->id ?? 'nil';
                    $network_name = $productplan->product_plan_category->network->network_name ?? 'nil';
                    $product_slug = $productplan->product_plan_category->product->slug;
                    $product_id = $productplan->product_plan_category->product->id;
                    $cost_price = $productplan->cost_price;
                    $is_social = $productplan->is_social;
                    if($product_slug == 'data'){
                   
                        $checkunique_plan = UniqueProductPlan::where('network_id',$network_id)
                        ->where('product_id',$product_id)
                        ->where('validity_in_days',$validity)
                        ->where('data_size_in_mb',$size)
                        ->where('is_social',$is_social)
                        ->first(); 

                        if(! $checkunique_plan){
                            $dataup['api_id'] = $nextcount;
                            $dataup['product_plan_name'] = $productplan->product_plan_name;
                            $dataup['data_size_in_mb'] = $size;
                            $dataup['validity_in_days'] = $validity;
                            $dataup['network_id'] = $network_id;
                            $dataup['product_id'] = $product_id;
                            $dataup['cost_price'] = $productplan->cost_price;
                            $dataup['price_1'] = $productplan->cost_price + 100;
                            $dataup['price_2'] = $productplan->cost_price + 95;
                            $dataup['price_3'] = $productplan->cost_price + 90;
                            $dataup['price_4'] = $productplan->cost_price + 85;
                            $dataup['price_5'] = $productplan->cost_price + 80;
                            $dataup['price_6'] = $productplan->cost_price + 75;
                            $dataup['price_7'] = $productplan->cost_price + 70;
                            $dataup['price_8'] = $productplan->cost_price + 65;
                            $dataup['price_9'] = $productplan->cost_price + 60;
                            $dataup['price_10'] = $productplan->cost_price + 55;
                            $dataup['price_11'] = $productplan->cost_price + 50;
                            $dataup['price_12'] = $productplan->cost_price + 45;
                            UniqueProductPlan::create($dataup);
                    
                        }else{

                            //exists..UPDATE
                            if($cost_price > $checkunique_plan->cost_price){
                               
                            }else{
                                $cost_price = $checkunique_plan->cost_price;
                            }

                            $dataupp['cost_price'] = $cost_price;
                            $dataupp['price_1'] = $cost_price + 100;
                            $dataupp['price_2'] = $cost_price + 95;
                            $dataupp['price_3'] = $cost_price + 90;
                            $dataupp['price_4'] = $cost_price + 85;
                            $dataupp['price_5'] = $cost_price + 80;
                            $dataupp['price_6'] = $cost_price + 75;
                            $dataupp['price_7'] = $cost_price + 70;
                            $dataupp['price_8'] = $cost_price + 65;
                            $dataupp['price_9'] = $cost_price + 60;
                            $dataupp['price_10'] = $cost_price + 55;
                            $dataupp['price_11'] = $cost_price + 50;
                            $dataupp['price_12'] = $cost_price + 45;
                            UniqueProductPlan::where('id',$checkunique_plan->id)->update($dataupp);

                        
                        }
                    }

                    $nextcount++;
                }


                return [
                    'message' => 'success'
                ];
                // return $dataup;
                // dd('test');
            });

            Route::get('/plans-profit-settings', function (): array {
                //fetch unique plans: network, size, validity
                $productplans = ProductPlan::all();

                foreach($productplans as $key=>$productplan){
                    $size = $productplan->data_size_in_mb;
                    $validity = $productplan->validity_in_days;
                    $network_id = $productplan->product_plan_category->network->id ?? 'nil';
                    $network_name = $productplan->product_plan_category->network->network_name ?? 'nil';
                    $product_slug = $productplan->product_plan_category->product->slug;
                    $product_id = $productplan->product_plan_category->product->id;
                    $cost_price = $productplan->cost_price;
                    $is_social = $productplan->is_social;
                    if($product_slug == 'data'){
                   
                        $profit_setting = PlanProfitSetting::where('network_id',$network_id)
                        ->where('product_id',$product_id)
                        ->where('validity_in_days',$validity)
                        ->where('data_size_in_mb',$size)
                        // ->where('is_social',$is_social)
                        ->first(); 

                        if(! $profit_setting){
                            // $dataup['api_id'] = $nextcount;
                            $dataup['data_size_in_mb'] = $size;
                            $dataup['validity_in_days'] = $validity;
                            $dataup['network_id'] = $network_id;
                            $dataup['product_id'] = $product_id;
                            $dataup['lowest_cost_price'] = $productplan->cost_price;
                            $dataup['profit_1'] = 100;
                            $dataup['profit_2'] = 75;
                            $dataup['profit_3'] = 50;
                            $dataup['profit_4'] = 40;
                            $dataup['profit_5'] = 30;
                            $dataup['profit_6'] = 25;
                            $dataup['profit_7'] = 20;
                            $dataup['profit_8'] = 15;
                            $dataup['profit_9'] = 14;
                            $dataup['profit_10'] = 10;
                            $dataup['profit_11'] = 7;
                            $dataup['profit_12'] = 5;
                            PlanProfitSetting::create($dataup);
                    
                        }
                    }

                    // $nextcount++;
                }


                return [
                    'message' => 'success'
                ];
                // return $dataup;
                // dd('test');
            });

            Route::get('/populate-user-token', function (): array {
                $users = User::all();
                foreach($users as $user){
                    $token = hash('sha1', $user->id . Illuminate\Support\Str::random(40) . time());
                    if($user->api_token == NULL){
                        $user->update([
                            'api_token' => $token
                        ]);
                    }
                    
                }
                return [
                    'completed' => 1
                ];
            });

            Route::get('/populate-plans-api-id', function (): array {
                $productplan = ProductPlan::all();
                $lastplan = ProductPlan::select('api_id')->latest()->first();
                $lastapiid = $lastplan && $lastplan->api_id != NULL ?  $lastplan->api_id : 0;
                $nextapiid = $lastapiid + 1;

                foreach($productplan as $plan){
                 
                    if($plan->api_id == NULL){
                        $plan->update([
                            'api_id' => $nextapiid
                        ]);
                        $nextapiid++;
                    }
                    
                }
                return [
                    'completed' => 1
                ];
            });

            

            Route::get('/getsp', function (): array {
                $getsp = (new PlansProfitSettingsService())->getSellingPriceForCustomer();
                return $getsp;
            });

            Route::get('/update-product-plans', function (): array {
                $updat = (new UniqueProductPlansService())->updateUniqueIdsInProductPlan();
                return $updat;
            });

            Route::get('/test', function (): array {
    
              dd('test');
                
            });



            Route::get('/delete_user_account', function () {
                return view('auth.delete_account');
            })->name('account.deactivate');

            Route::get('/', function () {
          
                //get template name:
                $data = [];
                    $site_images_data = SiteImage::get();
                    if(count($site_images_data) > 0){
                        foreach($site_images_data as $site_image){
                            $data[$site_image->image_category] = $site_image->image_name;
                        }
                    }
                             
                
                    $landing_data = LandingPagesSetting::get();
                    // dd($landing_data);
                    foreach($landing_data as $landing_component){
                        $data[$landing_component->field_name] = $landing_component->field_details;
                    }
                
                    // dd($data);
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
                    


                    $template = SiteTemplate::first();
                    if(!$template || $template->template_name == 'template_1'){
                        return view('landing.index')->with($data);
                    }else{
                        //this is template 2 
                        return view('template2.index')->with($data);
                    }
            
                // dd($data);
            });

            Route::get('/access_denied', function () {
                return 'You are not authorized... <a href="'.route('login').'">Return back</a>';
            })->name('access_denied');


            Route::get('users/delete_user_account', [UsersController::class, 'delete_user_account'])->name('user.delete_user_account.index');
            Route::post('users/delete_user_account_action', [UsersController::class, 'delete_user_account_action'])->name('user.delete_user_account.action');
           
            Route::get('users_listing/{category}', [QuickToolController::class, 'users_listing'])->name('quicktool.users_listing');
            Route::get('users_listing_date/{date}', [QuickToolController::class, 'users_listing_date'])->name('quicktool.users_listing_date');


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
            // Route::middleware(['auth','verified'])->get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
            Route::get('product_plans/fetch_public_product_plans', [ProductPlanController::class, 'fetch_public_product_plans'])->name('fetch_public_product_plans');

            // COMMISSIONS
            Route::middleware(['auth','verified'])->get('commissions/index', [CommissionsController::class, 'index'])->name('commissions.index');
            Route::middleware(['auth','verified'])->get('commissions/fetch_commissions', [CommissionsController::class, 'fetch_commissions'])->name('user.commissions.fetch_commissions');

            Route::middleware(['auth','verified'])->get('admin/wallet_creditings/index', [WalletsController::class, 'wallet_creditings'])->name('wallet_creditings.index');

            // routes/web.php or routes/api.php
            Route::post('/commissions/transfer-to-wallet', [CommissionsController::class, 'transferToWallet'])
            ->name('commissions.transfer')
            ->middleware('auth');


              // COUPON CODES
              Route::middleware(['auth','verified','admin'])->get('coupon_codes/index', [CouponCodesController::class, 'index'])->name('admin.coupon_codes.index');
              Route::middleware(['auth','verified','admin'])->post('coupon_codes/store', [CouponCodesController::class, 'store'])->name('admin.coupon_codes.store');

             // TRANSLATION SETTINGS
             Route::middleware(['auth','verified','admin'])->get('translations/index', [MultilanguageController::class, 'index'])->name('admin.translations.index');
             Route::middleware(['auth','verified','admin'])->post('translations/store', [MultilanguageController::class, 'store'])->name('admin.translations.store');
             Route::middleware(['auth','verified','admin'])->post('translations/store_ajax', [MultilanguageController::class, 'store_ajax'])->name('admin.translations.store_ajax');
             Route::middleware(['auth','verified','admin'])->get('multilanguage/translation', [MultilanguageController::class, 'translation'])->name('multilanguage.translation');


             //customer follow up feature
             Route::middleware(['auth','verified','admin'])->get('admin/daily_customer_followup/index', [DailyCustomerFollowupController::class, 'index'])->name('admin.daily_customer_followup.index');
             Route::middleware(['auth','verified','admin'])->post('admin/daily_customer_followup/search', [DailyCustomerFollowupController::class, 'filter'])->name('admin.daily_customer_followup.filter');


             // funding promo
             Route::middleware(['auth','verified','admin'])->get('wallet_funding_promo/index', [WalletFundingPromoController::class, 'index'])->name('admin.wallet_funding_promo.index');
             Route::middleware(['auth','verified','admin'])->post('wallet_funding_promo/store', [WalletFundingPromoController::class, 'store'])->name('admin.wallet_funding_promo.store');
             Route::middleware(['auth','verified','admin'])->get('user_wallet_funding_promo/index', [WalletFundingPromoController::class, 'index_user'])->name('admin.user_wallet_funding_promo.index');
             Route::middleware(['auth','verified','admin'])->post('user_wallet_funding_promo/store', [WalletFundingPromoController::class, 'store_user'])->name('admin.user_wallet_funding_promo.index_store');
             Route::middleware(['auth','verified','admin'])->post('user_wallet_funding_promo/update/{id}', [WalletFundingPromoController::class, 'update_user'])->name('admin.user_wallet_funding_promo.update_user');
              
             //product plan custom pricing
             Route::middleware(['auth','verified','admin'])->get('product_plan_custom_pricing/index', [ProductPlanCustomPricingController::class, 'index'])->name('admin.product_plan_custom_pricing.index');
             Route::middleware(['auth','verified','admin'])->post('product_plan_custom_pricing/store', [ProductPlanCustomPricingController::class, 'store'])->name('admin.product_plan_custom_pricing.store');

            //ANNOUNCEMENT
            Route::middleware(['auth','verified','admin'])->get('announcements/index', [AnnouncementsController::class, 'index'])->name('admin.announcements.index');
            Route::middleware(['auth','verified','admin'])->post('announcements/store', [AnnouncementsController::class, 'store'])->name('admin.announcements.store');
            Route::middleware(['auth','verified','admin'])->post('announcements/update/{id}', [AnnouncementsController::class, 'update'])->name('admin.announcements.update');
            


            //IMPERSONATION
            Route::middleware(['auth','verified','admin'])->get('admin/impersonate/{id}', [UsersController::class, 'impersonate'])->name('admin.impersonate');
            Route::middleware(['auth','verified'])->get('exit_impersonate', [UsersController::class, 'exit_impersonate'])->name('admin.exit_impersonate');

            

            Route::middleware(['auth','verified','admin'])->get('product_plans/changemegasubprice', [PriceChangeController::class, 'changeMegasubPrice'])->name('changeMegasubPrice');

            Route::middleware(['auth','verified','admin'])->get('admin/users', [UsersController::class, 'index'])->name('admin.users.index');
            Route::middleware(['auth','verified','admin'])->get('admin/users/create', [UsersController::class, 'create'])->name('admin.users.create');
            Route::middleware(['auth','verified','admin'])->get('admin/users/{id}/manage_user', [UsersController::class, 'manage_user'])->name('admin.users.manage_user');
            Route::middleware(['auth','verified','admin'])->post('admin/users/fund_user_wallet', [UsersController::class, 'fund_user_wallet'])->name('admin.users.fund_user_wallet');
            Route::middleware(['auth','verified','admin'])->post('admin/users/reset_2fa', [UsersController::class, 'reset_2fa'])->name('admin.users.reset_2fa');
            Route::middleware(['auth','verified','admin'])->post('admin/users/update_user_plan', [UsersController::class, 'update_user_plan'])->name('admin.users.update_user_plan');
            Route::middleware(['auth','verified','admin'])->post('admin/users/update_user_info', [UsersController::class, 'update_user_info'])->name('admin.users.update_user_info');
            
            Route::middleware(['auth','verified','admin'])->post('admin/users/store', [UsersController::class, 'store'])->name('admin.users.store');
            Route::middleware(['auth','verified','admin'])->get('admin/users/fetch_users', [UsersController::class, 'fetch_users'])->name('admin.users.fetch_users');
            Route::middleware(['auth','verified','admin'])->get('admin/users/fetch_users_paginated', [UsersController::class, 'fetch_users_paginated'])->name('admin.users.fetch_users_paginated');
            Route::middleware(['auth','verified','admin'])->get('admin/users/toggle_verification_status', [UsersController::class, 'toggle_verification_status'])->name('admin.users.toggle_verification_status');

            Route::middleware(['auth','verified','admin'])->get('admin/networks', [NetworkController::class, 'index'])->name('admin.networks.index');

            Route::middleware(['auth','verified','admin'])->get('admin/reseller_plans', [ResellerPlanController::class, 'index'])->name('admin.reseller_plans.index');
            Route::middleware(['auth','verified','admin'])->post('admin/reseller_plans/update_name', [ResellerPlanController::class, 'update_name'])->name('admin.reseller_plans.update_name');

            Route::middleware(['auth','verified','admin'])->get('admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
            Route::middleware(['auth','verified','admin'])->get('admin/roles/{role_id}/permission', [RoleController::class, 'permissions'])->name('admin.roles.permissions');
            Route::middleware(['auth','verified','admin'])->post('admin/roles/{role_id}/permission/update', [RoleController::class, 'update_permissions'])->name('admin.roles.permissions.update');


            Route::middleware(['auth','verified','admin'])->get('admin/addons', [AddonController::class, 'index'])->name('admin.addons.index');

            Route::middleware(['auth','verified','admin'])->get('admin/automations/{slug}/view', [AutomationController::class, 'dashboard'])->name('admin.automation.dashboard_view');
            Route::middleware(['auth','verified','admin'])->get('admin/automations/create', [AutomationController::class, 'create'])->name('admin.automation.create');
            Route::middleware(['auth','verified','admin'])->get('admin/automations/index', [AutomationController::class, 'index'])->name('admin.automation.index');
            Route::middleware(['auth','verified','admin'])->post('admin/automations/store', [AutomationController::class, 'store'])->name('admin.automation.store');
            Route::middleware(['auth','verified','admin'])->post('admin/automations/storev2', [AutomationController::class, 'storev2'])->name('admin.automation.storev2');
            Route::middleware(['auth','verified','admin'])->post('admin/automations/update', [AutomationController::class, 'update'])->name('admin.automation.update');
            // Route::middleware(['auth','verified','admin'])->get('admin/automations/ogdams/view', [AutomationController::class, 'dashboard'])->name('admin.automation.ogdams.dashboard_view');


            Route::middleware(['auth','verified','admin'])->get('admin/products', [ProductController::class, 'index'])->name('admin.products.index');
            Route::middleware(['auth','verified','admin'])->post('admin/products/store', [ProductController::class, 'store'])->name('admin.products.store');
            Route::middleware(['auth','verified','admin'])->post('admin/products/update', [ProductController::class, 'update'])->name('admin.products.update');



            Route::middleware(['auth','verified','admin'])->get('admin/toggle_plan_category_visibility', [ProductPlanCategoryController::class, 'toggle_plan_category_visibility'])->name('admin.product_plan_categories.toggle_plan_category_visibility');
            Route::middleware(['auth','verified','admin'])->get('admin/toggle_hot_sales', [ProductPlanCategoryController::class, 'toggle_hot_sales'])->name('admin.product_plan_categories.toggle_hot_sales');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories', [ProductPlanCategoryController::class, 'index'])->name('admin.product_plan_categories.index');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/view/{id}', [ProductPlanCategoryController::class, 'view_details'])->name('admin.product_plan_categories.view_details');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/view_by_automation/{id}/{automation_id}', [ProductPlanCategoryController::class, 'view_details_by_automation'])->name('admin.product_plan_categories.view_details_by_automation');
            Route::middleware(['auth','verified','admin'])->post('admin/product_plan_categories/update', [ProductPlanCategoryController::class, 'update_details'])->name('admin.product_plan_categories.update_details');
            Route::middleware(['auth','verified','admin'])->post('admin/product_plan_categories/update_plan_prices', [ProductPlanCategoryController::class, 'update_plan_prices'])->name('admin.product_plan_categories.update_plan_prices');

            Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/admin_fetch_product_plan_categories', [ProductPlanCategoryController::class, 'admin_fetch_product_plan_categories'])->name('admin.product_plan_categories.admin_fetch_product_plan_categories');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/admin_fetch_product_plan_categories_paginated', [ProductPlanCategoryController::class, 'admin_fetch_product_plan_categories_paginated'])->name('admin.product_plan_categories.admin_fetch_product_plan_categories_paginated');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/details/{id}/json', [ProductPlanCategoryController::class, 'category_details_json'])->name('admin.product_plan_categories.category_details_json');
            Route::middleware(['auth','verified','admin'])->post('admin/product_plan_categories/store', [ProductPlanCategoryController::class, 'store'])->name('admin.product_plan_categories.store');
            Route::middleware(['auth','verified','admin'])->post('admin/product_plan_categories/store_plan', [ProductPlanCategoryController::class, 'store_plan'])->name('admin.product_plan_categories.store_plan');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plan_categories/update_automation', [ProductPlanCategoryController::class, 'updateAutomation'])->name('admin.product_plan_categories.update_automation');


            Route::middleware(['auth','verified','admin'])->get('admin/bulk_data_plans/{product_plan_category_id}', [BulkDataPlanController::class, 'index'])->name('admin.bulk_data_plans.index');
            Route::middleware(['auth','verified','admin'])->post('admin/bulk_data_plans/store', [BulkDataPlanController::class, 'store'])->name('admin.bulk_data_plans.store');


            //reprocess txn manually
            Route::middleware(['auth','verified','admin'])->post('transactions/reprocess_transaction', [ReprocessTransactionController::class, 'reprocess_transaction'])->name('transactions.reprocess_transaction');



            //wallet logs
            Route::middleware(['auth','verified','admin'])->get('admin/walletlogs/index', [WalletLogsController::class, 'index'])->name('admin.wallet_logs.index');
            Route::middleware(['auth','verified','admin'])->get('admin/walletlogs/admin_fetch_wallet_logs', [WalletLogsController::class, 'admin_fetch_wallet_logs'])->name('admin.wallet_logs.admin_fetch_wallet_logs');
            // Route::middleware(['auth','verified','admin'])->get('admin/transactions/index', [TransactionController::class, 'admin_all_transactions'])->name('admin.transactions.index');


            Route::middleware(['auth','verified','admin'])->post('transactions/lock_for_manual_processing', [TransactionController::class, 'lock_for_manual_processing'])->name('transactions.lock_for_manual_processing');
            Route::middleware(['auth','verified','admin'])->post('transactions/transaction_refund', [TransactionController::class, 'transaction_refund'])->name('transactions.transaction_refund');
            Route::middleware(['auth','verified','admin'])->post('transactions/manually_mark_transaction_as_successful', [TransactionController::class, 'manually_mark_transaction_as_successful'])->name('transactions.manually_mark_transaction_as_successful');
            Route::middleware(['auth','verified','admin'])->get('admin/transactions/admin_fetch_transactions', [TransactionController::class, 'admin_fetch_transactions'])->name('admin.transactions.admin_fetch_transactions');
            Route::middleware(['auth','verified','admin'])->get('admin/transactions/admin_fetch_transactions_paginated', [TransactionController::class, 'admin_fetch_transactions_paginated'])->name('admin.transactions.admin_fetch_transactions_paginated');
            Route::middleware(['auth','verified','admin'])->get('admin/transactions/index', [TransactionController::class, 'admin_all_transactions'])->name('admin.transactions.index');
            Route::middleware(['auth','verified','user'])->get('user/transactions/user_fetch_transactions', [TransactionController::class, 'user_fetch_transactions'])->name('user.transactions.user_fetch_transactions');
            Route::middleware(['auth','verified','user'])->get('user/transactions/index', [TransactionController::class, 'user_all_transactions'])->name('user.transactions.index');



            Route::middleware(['auth','verified','admin'])->post('/unique_plans/{id}/quick_update', [UniqueProductPlansController::class, 'unique_plans_quick_update'])->name('unique_plans.quick_update');
            Route::middleware(['auth','verified','admin'])->post('/unique_plans_automation/{id}/quick_update', [UniqueProductPlansController::class, 'unique_plan_automation_quick_update'])->name('unique_plans_automation.quick_update');

            Route::middleware(['auth','verified','admin'])->post('admin/save_unique_plan_pricing', [UniqueProductPlansController::class, 'save_unique_plan_pricing'])->name('admin.save_unique_plan_pricing');
            Route::middleware(['auth','verified','admin'])->get('admin/unique_product_plans/index', [UniqueProductPlansController::class, 'index'])->name('admin.unique_product_plans.index');
            Route::middleware(['auth','verified','admin'])->get('admin/unique_product_plans/fetch', [UniqueProductPlansController::class, 'fetch'])->name('admin.unique_product_plans.admin_fetch_unique_product_plans');

            Route::middleware(['auth','verified','admin'])->post('admin/save_plan_profit_settings', [PlanProfitSettingsController::class, 'save_plan_profit_setting'])->name('admin.save_plan_profit_setting');
            Route::middleware(['auth','verified','admin'])->get('admin/plan_profit_settings/index', [PlanProfitSettingsController::class, 'index'])->name('admin.plan_profit_settings.index');
            Route::middleware(['auth','verified','admin'])->get('admin/plan_profit_settings/fetch', [PlanProfitSettingsController::class, 'fetch'])->name('admin.plan_profit_settings.fetch');


            Route::middleware(['auth','verified','admin'])->get('admin/product_plans', [ProductPlanController::class, 'index'])->name('admin.product_plans.index');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plans/product_plan_details/{id}', [ProductPlanController::class, 'product_plan_details'])->name('admin.product_plans.product_plan_details');
            Route::middleware(['auth','verified','admin'])->post('admin/product_plans/store', [ProductPlanController::class, 'store'])->name('admin.product_plans.store');
            Route::middleware(['auth','verified','admin'])->post('admin/product_plans/update', [ProductPlanController::class, 'update'])->name('admin.product_plans.update');
            Route::middleware(['auth','verified','admin'])->post('admin/product_plans/update_plan2', [ProductPlanController::class, 'update_plan2'])->name('admin.product_plans.update_plan2');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plans/fetch_product_plans', [ProductPlanController::class, 'admin_fetch_product_plans'])->name('admin.product_plans.admin_fetch_product_plans');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plans/fetch_product_plans_paginated', [ProductPlanController::class, 'admin_fetch_product_plans_paginated'])->name('admin.product_plans.admin_fetch_product_plans_paginated');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plans/details/{id}/json', [ProductPlanController::class, 'product_plan_details_json'])->name('admin.product_plans.product_plan_details_json');
            Route::middleware(['auth','verified','admin'])->get('admin/toggle_product_visibility', [ProductPlanController::class, 'toggle_product_visibility'])->name('admin.product_plans.toggle_product_visibility');
            Route::middleware(['auth','verified','admin'])->get('admin/toggle_product_public_visibility', [ProductPlanController::class, 'toggle_product_public_visibility'])->name('admin.product_plans.toggle_product_public_visibility');

            Route::middleware(['auth','verified','admin'])->get('admin/product_categories', [ProductCategoryController::class, 'index'])->name('admin.product_categories.index');

            Route::middleware(['auth','verified','admin'])->post('admin/settings/emails_to_notify_failed_transactions', [AdminSettingsController::class, 'emails_to_notify_failed_transactions'])->name('admin.settings.emails_to_notify_failed_transactions');
            Route::middleware(['auth','verified','admin'])->get('admin/settings/remove_logo', [AdminSettingsController::class, 'remove_logo'])->name('admin.settings.remove_logo');
            Route::middleware(['auth','verified','admin'])->get('admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
            Route::middleware(['auth','verified','admin'])->post('admin/update_webhook_suffix_string', [AdminSettingsController::class, 'update_webhook_suffix_string'])->name('admin.settings.update_webhook_suffix_string');
            Route::middleware(['auth','verified','admin'])->post('admin/manage_automations_keys', [AdminSettingsController::class, 'manage_automations_keys'])->name('admin.settings.manage_automations_keys');
            Route::middleware(['auth','verified','admin'])->post('admin/update_funding_options', [AdminSettingsController::class, 'update_funding_options'])->name('admin.settings.update_funding_options'); //
            Route::middleware(['auth','verified','admin'])->post('admin/settings/update', [AdminSettingsController::class, 'update_settings'])->name('admin.settings.update'); //
            Route::middleware(['auth','verified','admin'])->post('admin/settings/update_api_key', [AdminSettingsController::class, 'update_api_key'])->name('admin.settings.update_api_key'); //
            Route::middleware(['auth','verified','admin'])->post('admin/settings/update_purchase_limit_settings', [AdminSettingsController::class, 'update_purchase_limit_settings'])->name('admin.settings.update_purchase_limit_settings'); //
            Route::middleware(['auth','verified','admin'])->post('admin/add_funding_option_bank_code', [AdminSettingsController::class, 'add_funding_option_bank_code'])->name('admin.settings.add_funding_option_bank_code'); //
            Route::middleware(['auth','verified','admin'])->post('admin/update_site_logo', [AdminSettingsController::class, 'manage_site_logo'])->name('admin.settings.manage_site_logo');
            Route::middleware(['auth','verified','admin'])->post('admin/update_site_images', [AdminSettingsController::class, 'manage_site_images'])->name('admin.settings.manage_site_images');
            Route::middleware(['auth','verified','admin'])->post('admin/update_site_color', [AdminSettingsController::class, 'manage_site_colors'])->name('admin.settings.manage_site_colors');
            Route::middleware(['auth','verified','admin'])->post('admin/manage_global_user_2fa', [AdminSettingsController::class, 'manage_global_user_2fa'])->name('admin.settings.manage_global_user_2fa');
            Route::middleware(['auth','verified','admin'])->post('admin/referral_settings', [AdminSettingsController::class, 'manage_referral_settings'])->name('admin.settings.referral_settings');
            Route::middleware(['auth','verified','admin'])->post('admin/landing_page_settings', [AdminSettingsController::class, 'manage_landing_page_settings'])->name('admin.settings.manage_landing_page_settings');
            Route::middleware(['auth','verified','admin'])->post('admin/settings/update_user_authentication_dashboard', [AdminSettingsController::class, 'update_user_authentication_dashboard'])->name('admin.settings.update_user_authentication_dashboard');

            Route::middleware(['auth','verified','admin'])->get('admin/profile/index', [UsersController::class, 'admin_manage_profile'])->name('admin.manage_profile.index');

            Route::middleware(['auth','verified','admin'])->get('admin/wallet/crediting_details/{id}', [WalletsController::class, 'wallet_crediting_details'])->name('admin.wallet.crediting_details');
            Route::middleware(['auth','verified','admin'])->post('admin/wallet/complete_pending_wallet_crediting/', [WalletsController::class, 'complete_pending_wallet_crediting'])->name('admin.wallet.complete_pending_wallet_crediting');


            Route::middleware(['auth','verified'])->get('user/settings/api_docs', [UsersController::class, 'api_docs'])->name('user.api.docs');
            Route::middleware(['auth','verified','user'])->get('user/profile/index', [UsersController::class, 'manage_profile'])->name('user.manage_profile.index');
            Route::middleware(['auth','verified','user'])->get('user/generate_user_bulk_data_wallets', [UsersController::class, 'generate_user_bulk_data_wallets'])->name('user.generate_user_bulk_data_wallets');
            Route::middleware(['auth','verified','user'])->get('user/settings', [UserSettingsController::class, 'index'])->name('user.settings.index');
            Route::middleware(['auth','verified','user'])->post('user/settings/update_default_wallet', [UserSettingsController::class, 'update_default_wallet'])->name('user.settings.update_default_wallet');
            Route::middleware(['auth','verified','user'])->post('user/settings/update_profile', [UserSettingsController::class, 'update_profile'])->name('user.settings.update_profile');
            Route::middleware(['auth','verified','user'])->post('user/settings/update_password', [UserSettingsController::class, 'update_password'])->name('user.settings.update_password');
            Route::middleware(['auth','verified','user'])->post('user/settings/update_pin', [UserSettingsController::class, 'update_pin'])->name('user.settings.update_pin');
            Route::middleware(['auth','verified','user'])->post('user/settings/update_2fa', [UserSettingsController::class, 'update_2fa'])->name('user.settings.update_2fa');
            Route::middleware(['auth','verified'])->get('user/settings/set_pin', [UserSettingsController::class, 'set_pin'])->name('user.settings.set_pin');
            Route::middleware(['auth','verified','user'])->post('user/settings/store_set_pin', [UserSettingsController::class, 'store_set_pin'])->name('user.settings.store_set_pin');


            //VERIFICATIONS
            Route::middleware(['auth','verified'])->get('user/verifications/index', [UserVerificationController::class, 'index'])->name('user.verification.index');
            Route::middleware(['auth','verified'])->post('user/verifications/store', [UserVerificationController::class, 'store'])->name('user.verification.store');
            
            Route::middleware(['auth','verified'])->post('user/virtual_accounts/generate', [VirtualAccountsController::class, 'generate'])->name('user.virtual_accounts.generate');
            Route::middleware(['auth','verified'])->get('user/dynamic_accounts/index', [DynamicAccountsController::class, 'index'])->name('user.dynamic_accounts.index');
            Route::middleware(['auth','verified'])->post('user/dynamic_accounts/generate', [DynamicAccountsController::class, 'generate_dynamic_account'])->name('user.dynamic_accounts.generate');
               

            Route::middleware(['auth','verified','user'])->get('user/data/buy_bulk_data/bulk_data_wallet/{data_wallet_id}', [DataController::class, 'buy_bulk_data'])->name('user.data.buy_bulk_data.bulk_data_wallet');
            Route::middleware(['auth','verified','user'])->get('user/data/buy_bulk_data', [DataController::class, 'buy_bulk_data'])->name('user.data.buy_bulk_data');
           
            //handle former implementation but remove later
            Route::middleware(['auth','verified','user'])->get('user/data/buy_data_action', [DataController::class, 'buy_data_action'])->name('user.data.buy_data_action');
            
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
            Route::middleware(['auth','verified'])->get('transactions/details/{id}/json', [TransactionController::class, 'transaction_details_json'])->name('transactions.transaction_details_json');

            //CABLE TV: user.cabletv.buy_cable_subscription

            //ELECTRICITY: electricity

            //ELECTRICITY

            //EXEMPTED SO THEY ARE ACCESSIBLE BY BOTH USER AND ADMIN
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/data/buy_data_single', [DataController::class, 'buy_data_v2'])->name('user.data.buy_data2');
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/data/buy_data', [DataController::class, 'buy_data'])->name('user.data.buy_data'); //single/bulk
            Route::middleware(['auth','verified','set_transaction_pin'])->post('user/data/store2', [DataController::class, 'buy_data_action'])->name('user.data.buy_data_action2');
            Route::middleware(['auth','verified','set_transaction_pin'])->post('user/data/buy_again_store2', [DataController::class, 'buy_again_data_action'])->name(' user.data.buy_again_data_action');
           
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/data/fetch_product_plan_categories', [DataController::class, 'fetch_product_plan_categories'])->name('user.fetch_product_plan_categories'); //TODO: you can add this to a helper controller later
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/data/fetch_product_plans', [DataController::class, 'fetch_product_plans'])->name('user.fetch_product_plans'); //TODO: you can add this to a helper controller later
            Route::middleware(['auth','verified','set_transaction_pin'])->post('user/data/fetch_data_plans_by_phone_number', [DataController::class, 'fetch_data_plans_by_phone_number'])->name('user.data.fetch_data_plans_by_phone_number'); //TODO: you can add this to a helper controller later


            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/electricity/buy_electricity', [ElectricitySubscriptionController::class, 'buy_electricity_subscription'])->name('user.electricity.buy_electricity_subscription');
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/electricity/store', [ElectricitySubscriptionController::class, 'buy_electricity_subscription_action'])->name('user.electricity.buy_electricity_subscription_action');
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/electricity/buy_electricity_subscription_by_plan_category/{id}', [ElectricitySubscriptionController::class, 'buy_electricity_subscription_by_plan_category'])->name('user.electricity.buy_electricity_subscription_by_plan_category');
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/electricity/validate_metre_number', [ElectricitySubscriptionController::class, 'validate_metre_number'])->name('user.electricity.validate_metre_number');

            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/cable_subscription/buy_cable_subscription', [CableSubscriptionController::class, 'buy_cable_subscription'])->name('user.cable_subscription.buy_cable_subscription');
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/cable_subscription/store', [CableSubscriptionController::class, 'buy_cable_subscription_action'])->name('user.cable_subscription.buy_cable_subscription_action');
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/cable_subscription/buy_cable_subscription_by_plan_category/{id}', [CableSubscriptionController::class, 'buy_cable_subscription_by_plan_category'])->name('user.cable_subscription.buy_cable_subscription_by_plan_category');
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/cable_subscription/validate_smart_card_number', [CableSubscriptionController::class, 'validate_smart_card_number'])->name('user.cable_subscription.validate_smart_card_number');


            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/airtime/buy_airtime_v2', [AirtimeController::class, 'buy_airtime_v2'])->name('user.airtime.buy_artime2');
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/airtime/buy_airtime', [AirtimeController::class, 'buy_airtime'])->name('user.airtime.buy_airtime');

            //new
            Route::middleware(['auth','verified','set_transaction_pin'])->post('user/airtime/store2', [AirtimeController::class, 'buy_airtime_action'])->name('user.airtime.buy_airtime_action2');
           
            //former... pls discard as soon as possible
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/airtime/store', [AirtimeController::class, 'buy_airtime_action'])->name('user.airtime.buy_airtime_action');
           
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/airtime/buy_airtime_by_plan_category/{id}', [AirtimeController::class, 'buy_airtime_by_plan_category'])->name('user.airtime.buy_airtime_by_plan_category');
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/airtime/fetch_single_airtime_plan', [AirtimeController::class, 'fetch_single_airtime_plan'])->name('user.airtime.fetch_single_airtime_plan');

            //EXEMPTED SO THEY ARE ACCESSIBLE BY BOTH USER AND ADMIN


            Route::middleware(['auth','verified','user','set_transaction_pin'])->get('user/data/buy_data_by_plan_category/{id}', [DataController::class, 'buy_data_by_plan_category'])->name('user.data.buy_data_by_plan_category');
            Route::middleware(['auth','verified','user','set_transaction_pin'])->get('user/data/get_single_bulk_data_wallet/{plan_id}', [DataController::class, 'get_single_bulk_data_wallet'])->name('user.data.get_single_bulk_data_wallet');

            Route::middleware(['auth','verified','user','set_transaction_pin'])->get('user/generate_dynamic_account', [CrystalPayController::class, 'generate_dynamic_account'])->name('user.crystalpay.generate_dynamic_account');
            Route::middleware(['auth','verified','user','set_transaction_pin'])->post('user/generate_virtual_account', [CrystalPayController::class, 'generate_virtual_account'])->name('user.crystalpay.generate_virtual_account');


            
            Route::middleware(['auth','verified','set_transaction_pin'])->get('admin/wallet/total_balances', [WalletsController::class, 'admin_total_balances'])->name('admin.wallet.total_balances');
            Route::middleware(['auth','verified','user','set_transaction_pin'])->get('user/monnify_verifications', [WalletsController::class, 'monnify_verifications'])->name('user.wallet.monnify.verifications');
            Route::middleware(['auth','verified','user','set_transaction_pin'])->post('user/verify_monnify_account_via_nin', [WalletsController::class, 'verify_monnify_account_via_nin'])->name('user.wallets.verify_monnify_account_via_nin');
            Route::middleware(['auth','verified','user','set_transaction_pin'])->post('user/verify_monnify_account_via_bvn', [WalletsController::class, 'verify_monnify_account_via_bvn'])->name('user.wallets.verify_monnify_account_via_bvn');
            Route::middleware(['auth','verified','user','set_transaction_pin'])->post('user/generate_monnify_virtual_accounts', [WalletsController::class, 'generate_monnify_virtual_accounts'])->name('user.wallets.generate_monnify_virtual_accounts');

            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/wallet/index', [WalletsController::class, 'index'])->name('user.wallet.index');
            Route::middleware(['auth','verified'])->post('user/wallet/generate_virtual_account', [WalletsController::class, 'generate_virtual_account'])->name('user.wallet.generate_virtual_account');


            Route::middleware(['auth','verified','user'])->get('user/wallet/fund_wallet', [WalletsController::class, 'fund_wallet'])->name('user.wallet.fund_wallet');
            Route::middleware(['auth','verified'])->get('transactions/fetch_crystal_pay_funding_transactions', [WalletsController::class, 'fetch_crystal_pay_funding_transactions'])->name('transactions.fetch_crystal_pay_funding_transactions');
            Route::middleware(['auth','verified'])->get('transactions/fetch_crystal_pay_pending_transactions', [WalletsController::class, 'fetch_crystal_pay_pending_transactions'])->name('transactions.fetch_crystal_pay_pending_transactions');
            Route::middleware(['auth','verified','admin'])->get('transactions/fetch_pending_creditings_paginated', [WalletsController::class, 'fetch_pending_creditings_paginated'])->name('transactions.fetch_pending_creditings_paginated');
            Route::middleware(['auth','verified'])->get('transactions/pending_funding_transactions', [WalletsController::class, 'pending_funding_transactions'])->name('admin.transactions.pending_funding_transactions');

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




//Stay Connected with
//             <!DOCTYPE html>
// <html lang="en">

// <head>
//     <meta charset="UTF-8">
//     <meta http-equiv="X-UA-Compatible" content="IE=edge">
//     <meta name="viewport" content="width=device-width, initial-scale=1.0">
//     <style>
//         :root {
//             --border-width: 1px;
//             --border-radius: 0.5rem;
//             --color-error: #c94b4b;
//             --color-info: #157efb;
//             --color-info-hover: #0f6ddb;
//             --color-info-text: #fff
//         }

//         .__next-auth-theme-auto,
//         .__next-auth-theme-light {
//             --color-background: #ececec;
//             --color-background-hover: hsla(0, 0%, 93%, .8);
//             --color-background-card: #fff;
//             --color-text: #000;
//             --color-primary: #444;
//             --color-control-border: #bbb;
//             --color-button-active-background: #f9f9f9;
//             --color-button-active-border: #aaa;
//             --color-separator: #ccc
//         }

//         .__next-auth-theme-dark {
//             --color-background: #161b22;
//             --color-background-hover: rgba(22, 27, 34, .8);
//             --color-background-card: #0d1117;
//             --color-text: #fff;
//             --color-primary: #ccc;
//             --color-control-border: #555;
//             --color-button-active-background: #060606;
//             --color-button-active-border: #666;
//             --color-separator: #444
//         }

//         @media (prefers-color-scheme:dark) {
//             .__next-auth-theme-auto {
//                 --color-background: #161b22;
//                 --color-background-hover: rgba(22, 27, 34, .8);
//                 --color-background-card: #0d1117;
//                 --color-text: #fff;
//                 --color-primary: #ccc;
//                 --color-control-border: #555;
//                 --color-button-active-background: #060606;
//                 --color-button-active-border: #666;
//                 --color-separator: #444
//             }

//             a.button,
//             button {
//                 background-color: var(--provider-dark-bg, var(--color-background));
//                 color: var(--provider-dark-color, var(--color-primary))
//             }

//             a.button:hover,
//             button:hover {
//                 background-color: var(--provider-dark-bg-hover, var(--color-background-hover)) !important
//             }

//             #provider-logo {
//                 display: none !important
//             }

//             #provider-logo-dark {
//                 display: block !important;
//                 width: 25px
//             }
//         }

//         html {
//             box-sizing: border-box
//         }

//         *,
//         :after,
//         :before {
//             box-sizing: inherit;
//             margin: 0;
//             padding: 0
//         }

//         body {
//             background-color: var(--color-background);
//             font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
//             margin: 0;
//             padding: 0
//         }

//         h1 {
//             font-weight: 400
//         }

//         h1,
//         p {
//             color: var(--color-text);
//             margin-bottom: 1.5rem;
//             padding: 0 1rem
//         }

//         form {
//             margin: 0;
//             padding: 0
//         }

//         label {
//             font-weight: 500;
//             margin-bottom: .25rem;
//             text-align: left
//         }

//         input[type],
//         label {
//             color: var(--color-text);
//             display: block
//         }

//         input[type] {
//             background: var(--color-background-card);
//             border: var(--border-width) solid var(--color-control-border);
//             border-radius: var(--border-radius);
//             box-sizing: border-box;
//             font-size: 1rem;
//             padding: .5rem 1rem;
//             width: 100%
//         }

//         input[type]:focus {
//             box-shadow: none
//         }

//         p {
//             font-size: 1.1rem;
//             line-height: 2rem
//         }

//         a.button {
//             line-height: 1rem;
//             text-decoration: none
//         }

//         a.button:link,
//         a.button:visited {
//             background-color: var(--color-background);
//             color: var(--color-primary)
//         }

//         button span {
//             flex-grow: 1
//         }

//         a.button,
//         button {
//             align-items: center;
//             background-color: var(--provider-bg);
//             border-color: rgba(0, 0, 0, .1);
//             border-radius: var(--border-radius);
//             color: var(--provider-color, var(--color-primary));
//             display: flex;
//             font-size: 1.1rem;
//             font-weight: 500;
//             justify-content: center;
//             min-height: 62px;
//             padding: .75rem 1rem;
//             position: relative;
//             transition: all .1s ease-in-out
//         }

//         a.button:hover,
//         button:hover {
//             background-color: var(--provider-bg-hover, var(--color-background-hover));
//             cursor: pointer
//         }

//         a.button:active,
//         button:active {
//             cursor: pointer
//         }

//         a.button #provider-logo,
//         button #provider-logo {
//             display: block;
//             width: 25px
//         }

//         a.button #provider-logo-dark,
//         button #provider-logo-dark {
//             display: none
//         }

//         #submitButton {
//             background-color: var(--brand-color, var(--color-info));
//             color: var(--button-text-color, var(--color-info-text));
//             width: 100%
//         }

//         #submitButton:hover {
//             background-color: var(--button-hover-bg, var(--color-info-hover)) !important
//         }

//         a.site {
//             color: var(--color-primary);
//             font-size: 1rem;
//             line-height: 2rem;
//             text-decoration: none
//         }

//         a.site:hover {
//             text-decoration: underline
//         }

//         .page {
//             box-sizing: border-box;
//             display: grid;
//             height: 100%;
//             margin: 0;
//             padding: 0;
//             place-items: center;
//             position: absolute;
//             width: 100%
//         }

//         .page>div {
//             text-align: center
//         }

//         .error a.button {
//             margin-top: .5rem;
//             padding-left: 2rem;
//             padding-right: 2rem
//         }

//         .error .message {
//             margin-bottom: 1.5rem
//         }

//         .signin input[type=text] {
//             display: block;
//             margin-left: auto;
//             margin-right: auto
//         }

//         .signin hr {
//             border: 0;
//             border-top: 1px solid var(--color-separator);
//             display: block;
//             margin: 2rem auto 1rem;
//             overflow: visible
//         }

//         .signin hr:before {
//             background: var(--color-background-card);
//             color: #888;
//             content: "or";
//             padding: 0 .4rem;
//             position: relative;
//             top: -.7rem
//         }

//         .signin .error {
//             background: #f5f5f5;
//             background: var(--color-error);
//             border-radius: .3rem;
//             font-weight: 500
//         }

//         .signin .error p {
//             color: var(--color-info-text);
//             font-size: .9rem;
//             line-height: 1.2rem;
//             padding: .5rem 1rem;
//             text-align: left
//         }

//         .signin form,
//         .signin>div {
//             display: block
//         }

//         .signin form input[type],
//         .signin>div input[type] {
//             margin-bottom: .5rem
//         }

//         .signin form button,
//         .signin>div button {
//             width: 100%
//         }

//         .signin .provider+.provider {
//             margin-top: 1rem
//         }

//         .logo {
//             display: inline-block;
//             margin: 1.25rem 0;
//             max-height: 70px;
//             max-width: 150px
//         }

//         .card {
//             background-color: var(--color-background-card);
//             border-radius: 2rem;
//             padding: 1.25rem 2rem
//         }

//         .card .header {
//             color: var(--color-primary)
//         }

//         .section-header {
//             color: var(--color-text)
//         }

//         @media screen and (min-width:450px) {
//             .card {
//                 margin: 2rem 0;
//                 width: 368px
//             }
//         }

//         @media screen and (max-width:450px) {
//             .card {
//                 margin: 1rem 0;
//                 width: 343px
//             }
//         }
//     </style>
//     <title>Sign In</title>
// </head>

// <body class="__next-auth-theme-light">
//     <div class="page">
//         <div class="signin">
//             <div class="card">
//                 <div class="provider">
//                     <form action="https://selfservice.mtn.ng/api/auth/callback/credentials" method="POST"><input type="hidden" name="csrfToken" value="a5753f0d5a43ae09e919504419ce10e3fa4bb34f94434b4e9f902ee82f715573" />
//                         <div>
//                             <label class="section-header" for="input-msisdn-for-credentials-provider">MSISDN</label><input name="msisdn" id="input-msisdn-for-credentials-provider" type="text" placeholder label="MSISDN" />
//                         </div>
//                         <div><label class="section-header" for="input-otp-for-credentials-provider">OTP</label><input name="otp" id="input-otp-for-credentials-provider" type="text" placeholder label="OTP" />
//                         </div><button type="submit">Sign in with Credentials</button>
//                     </form>
//                 </div>
//             </div>
//         </div>
//     </div>
// </body>

// </html>



});