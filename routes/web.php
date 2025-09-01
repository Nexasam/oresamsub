<?php

use App\Models\SiteImage;
use App\Models\ProductPlan;
use App\Models\SiteTemplate;
use App\Models\AdminColorSetting;
use App\Http\Middleware\RoleAssess;
use App\Models\LandingPagesSetting;
use App\Models\UniqueProductPlan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\DataController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\RoleAdminAccess;
use App\Models\ProductPlanCustomPricing;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AirtimeController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MarketersController;
use App\Http\Controllers\MigrationController;
use App\Http\Controllers\QuickToolController;
use App\Http\Controllers\Template2Controller;
use App\Http\Controllers\WalletLogController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\CrystalPayController;
use App\Http\Controllers\WalletLogsController;
use App\Http\Controllers\CommissionsController;
use App\Http\Controllers\CouponCodesController;
use App\Http\Controllers\NewTemplateController;
use App\Http\Controllers\PriceChangeController;
use App\Http\Controllers\ProductPlanController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BulkDataPlanController;
use App\Http\Controllers\ResellerPlanController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\MultilanguageController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserTwoFactorController;
use App\Http\Services\BizProfitCalculationService;
use App\Http\Controllers\DynamicAccountsController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\UserProductPlanController;
use App\Http\Controllers\VirtualAccountsController;
use App\Http\Controllers\UserVerificationController;
use App\Http\Controllers\CableSubscriptionController;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;
use App\Http\Controllers\WalletFundingPromoController;
use App\Http\Controllers\ProductPlanCategoryController;
use App\Http\Controllers\ReprocessTransactionController;
use App\Http\Controllers\DailyCustomerFollowupController;
use App\Http\Controllers\ElectricitySubscriptionController;
use App\Http\Controllers\ProductPlanCustomPricingController;


   
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

                // Route::get('oresamsub/dashboard', fn () => view('oresamsub.pages.dashboard'))->name('ore.dashboard');

                Route::get('oresamsub/dashboard', function () {
                    $data['transactions'] = App\Models\Transaction::with('product_plan')->where('user_id',auth()->id())->limit(10)->latest()->get();
                    // dd($data);
                    return view('oresamsub.pages.dashboard')->with($data);
                })->name('ore.dashboard');

                Route::get('oresamsub/airtime', function () {
                    $data['networks'] = App\Models\Network::get();
                    return view('oresamsub.pages.airtime')->with($data);
                })->name('ore.airtime');

                Route::post('oresamsub/airtime/submit', fn () => view('oresamsub.pages.airtime.submit'))->name('ore.airtime.submit');

                Route::get('oresamsub/data', function () {
                    $data['networks'] = App\Models\Network::get();
                    return view('oresamsub.pages.data')->with($data);
                })->name('ore.data');

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



            Route::get('/test', function (): array {
                //fetch unique plans: network, size, validity
                $productplans = ProductPlan::all();


                $checkunique = UniqueProductPlan::latest()->first();
                $lastkey = $checkunique->api_id ?? 0;
                $nextcount = $lastkey + 1;

                foreach($productplans as $key=>$productplan){
                    $size = $productplan->data_size_in_mb;
                    $validity = $productplan->validity_in_days;
                    $network_id = $productplan->product_plan_category->network->id ?? 'nil';
                    $network_name = $productplan->product_plan_category->network->network_name ?? 'nil';
                    $product_slug = $productplan->product_plan_category->product->slug;
                    $product_id = $productplan->product_plan_category->product->id;
                    $cost_price = $productplan->cost_price;
                    if($product_slug == 'data'){
                   
                        $checkunique_plan = UniqueProductPlan::where('network_id',$network_id)
                        ->where('product_id',$product_id)
                        ->where('validity_in_days',$validity)
                        ->where('data_size_in_mb',$size)
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

            Route::get('/test2', function (): array {
                //fetch unique plans: network, size, validity
                $generalproductplans = UniqueProductPlan::all();


                $checkunique = UniqueProductPlan::latest()->first();
                $lastkey = $checkunique->api_id ?? 0;
                $nextcount = $lastkey + 1;

                $mess = '';

                foreach($generalproductplans as $keyy=>$productplan){
                    $size = $productplan->data_size_in_mb;
                    $validity = $productplan->validity_in_days;
                    $network_id = $productplan->network_id;
                    $product_id = $productplan->product_id;
                    $cost_price = $productplan->cost_price;
                 
                   
                    $associated_automationplans = ProductPlan::with(['product_plan_category.network','product_plan_category.product','automation'])
                    ->where('validity_in_days',$validity)
                    ->where('data_size_in_mb',$size)
                    ->get(); 

                    $data[$keyy]['unique'] = $productplan->product_plan_name;
                    //@tlest there should be one...
                    if(count($associated_automationplans) > 0){
                        foreach($associated_automationplans as $key=>$associated_automationplan){
                            $getnetworkid = $associated_automationplan->product_plan_category->network->id ?? 'nil';
                            $productid = $associated_automationplan->product_plan_category->product->id ?? 'nil';
                            $sizee = $associated_automationplan->data_size_in_mb;
                            $vall = $associated_automationplan->validity_in_days;
                            if($getnetworkid == $network_id && $productid == $product_id && $size == $sizee && $validity == $vall){
                                $dataa[$key]['pl'] = $associated_automationplan->product_plan_name;
                                $dataa[$key]['size'] = $associated_automationplan->data_size_in_mb;
                                $dataa[$key]['val'] = $associated_automationplan->validity_in_days;
                                $dataa[$key]['automa'] = $associated_automationplan->automation->automation_name;
                            }     
                        }
                        $data[$keyy]['automations'] = $dataa;
                    }  
                    
                }




                return [
                    'message' => $data
                ];
              
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
                
                    // dd($data);
                
                
                
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
            Route::middleware(['auth','verified'])->get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
            Route::get('product_plans/fetch_public_product_plans', [ProductPlanController::class, 'fetch_public_product_plans'])->name('fetch_public_product_plans');

            // COMMISSIONS
            Route::middleware(['auth','verified'])->get('commissions/index', [CommissionsController::class, 'index'])->name('commissions.index');
            Route::middleware(['auth','verified'])->get('commissions/fetch_commissions', [CommissionsController::class, 'fetch_commissions'])->name('user.commissions.fetch_commissions');

            Route::middleware(['auth','verified'])->get('admin/wallet_creditings/index', [WalletsController::class, 'wallet_creditings'])->name('wallet_creditings.index');


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
            Route::middleware(['auth','verified','admin'])->get('admin/users/toggle_verification_status', [UsersController::class, 'toggle_verification_status'])->name('admin.users.toggle_verification_status');

            Route::middleware(['auth','verified','admin'])->get('admin/networks', [NetworkController::class, 'index'])->name('admin.networks.index');

            Route::middleware(['auth','verified','admin'])->get('admin/reseller_plans', [ResellerPlanController::class, 'index'])->name('admin.reseller_plans.index');
            Route::middleware(['auth','verified','admin'])->post('admin/reseller_plans/update_name', [ResellerPlanController::class, 'update_name'])->name('admin.reseller_plans.update_name');

            Route::middleware(['auth','verified','admin'])->get('admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
            Route::middleware(['auth','verified','admin'])->get('admin/roles/{role_id}/permission', [RoleController::class, 'permissions'])->name('admin.roles.permissions');
            Route::middleware(['auth','verified','admin'])->post('admin/roles/{role_id}/permission/update', [RoleController::class, 'update_permissions'])->name('admin.roles.permissions.update');


            Route::middleware(['auth','verified','admin'])->get('admin/addons', [AddonController::class, 'index'])->name('admin.addons.index');

            Route::middleware(['auth','verified','admin'])->get('admin/automations/{slug}/view', [AutomationController::class, 'dashboard'])->name('admin.automation.dashboard_view');
            Route::middleware(['auth','verified','admin'])->get('admin/automations/index', [AutomationController::class, 'index'])->name('admin.automation.index');
            Route::middleware(['auth','verified','admin'])->post('admin/automations/store', [AutomationController::class, 'store'])->name('admin.automation.store');
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
            Route::middleware(['auth','verified','admin'])->get('admin/transactions/index', [TransactionController::class, 'admin_all_transactions'])->name('admin.transactions.index');
            Route::middleware(['auth','verified','user'])->get('user/transactions/user_fetch_transactions', [TransactionController::class, 'user_fetch_transactions'])->name('user.transactions.user_fetch_transactions');
            Route::middleware(['auth','verified','user'])->get('user/transactions/index', [TransactionController::class, 'user_all_transactions'])->name('user.transactions.index');




            Route::middleware(['auth','verified','admin'])->get('admin/product_plans', [ProductPlanController::class, 'index'])->name('admin.product_plans.index');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plans/product_plan_details/{id}', [ProductPlanController::class, 'product_plan_details'])->name('admin.product_plans.product_plan_details');
            Route::middleware(['auth','verified','admin'])->post('admin/product_plans/store', [ProductPlanController::class, 'store'])->name('admin.product_plans.store');
            Route::middleware(['auth','verified','admin'])->post('admin/product_plans/update', [ProductPlanController::class, 'update'])->name('admin.product_plans.update');
            Route::middleware(['auth','verified','admin'])->post('admin/product_plans/update_plan2', [ProductPlanController::class, 'update_plan2'])->name('admin.product_plans.update_plan2');
            Route::middleware(['auth','verified','admin'])->get('admin/product_plans/fetch_product_plans', [ProductPlanController::class, 'admin_fetch_product_plans'])->name('admin.product_plans.admin_fetch_product_plans');
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

            //CABLE TV: user.cabletv.buy_cable_subscription

            //ELECTRICITY: electricity

            //ELECTRICITY

            //EXEMPTED SO THEY ARE ACCESSIBLE BY BOTH USER AND ADMIN
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/data/buy_data_single', [DataController::class, 'buy_data_v2'])->name('user.data.buy_data2');
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/data/buy_data', [DataController::class, 'buy_data'])->name('user.data.buy_data'); //single/bulk
            Route::middleware(['auth','verified','set_transaction_pin'])->get('user/data/store', [DataController::class, 'buy_data_action'])->name('user.data.buy_data_action');
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
            // Route::middleware(['auth','verified'])->get('transactions/fetch_crystal_pay_pending_transactions', [WalletsController::class, 'fetch_crystal_pay_pending_transactions'])->name('transactions.fetch_crystal_pay_pending_transactions');
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
});