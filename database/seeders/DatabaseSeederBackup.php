<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Network;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Product;
use App\Models\UserPlan;
use App\Models\ProductPlan;
use App\Models\ProductCategory;
use App\Models\UserProductPlan;
use Illuminate\Database\Seeder;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //AUTOMATIONS
        //megasub
        Automation::create([
            "automation_name" => 'Megasubplug',
            "slug" =>'megasubplug',
        ]);

        //ogdams
        Automation::create([
            "automation_name" => 'Ogdams',
            "slug" =>'ogdams',
        ]);

        //autopilot
        Automation::create([
            "automation_name" => 'Autopilot',
            "slug" =>'autopilot',
        ]);

        //cloudsimhost
        Automation::create([
            "automation_name" => 'CloudSimHost',
            "slug" =>'cloudsimhost',
        ]);


        //NETWORKS
        $mtn_network = Network::create([
            'network_name' => 'MTN'
        ]);

        $glo_network = Network::create([
            'network_name' => 'GLO'
        ]);

        $airtel_network = Network::create([
            'network_name' => 'AIRTEL'
        ]);

        $_9mobile_network = Network::create([
            'network_name' => '9MOBILE'
        ]);

        
        
        
        
        
        //USER PLANS -     
        // $user_product_plans_percentage_for_basic_user_plan = 0;
        // $user_product_plans_percentage_for_gold_user_plan = 18;
        // $user_product_plans_percentage_for_diamond_user_plan = 24;
        // $user_product_plans_percentage_for_platinum_user_plan = 28;
        $user_plan_basic = UserPlan::create([
            'user_plan_name' => 'Basic Plan',
            'plan_level' => 1,
            'updated_user_plan_name' => NULL,
            'is_default' => 1,
            'percentage_profit_share' => 0,
            // 'user_product_plan_id' => $user_basic_product_plan->id
        ]);
        $user_plan_gold = UserPlan::create([
            'user_plan_name' => 'Gold Reseller Plan',
            'plan_level' => 2,
            'updated_user_plan_name' => NULL,
            'is_default' => 0,
            'percentage_profit_share' => 18,
            // 'user_product_plan_id' => $user_gold_product_plan->id
        ]);
        $user_plan_diamond = UserPlan::create([
            'user_plan_name' => 'Diamond Reseller Plan',
            'plan_level' => 3,
            'updated_user_plan_name' => NULL,
            'is_default' => 0,
            'percentage_profit_share' => 24,
            // 'user_product_plan_id' => $user_diamond_product_plan->id
        ]);
        $user_plan_platinum = UserPlan::create([
            'user_plan_name' => 'Platinum Reseller Plan',
            'plan_level' => 4,
            'updated_user_plan_name' => NULL,
            'is_default' => 0,
            'percentage_profit_share' => 28,
            // 'user_product_plan_id' => $user_diamond_product_plan->id
        ]);
              

        //Roles
        $admin_role = Role::create([
            'role_name' => 'Admin',
        ]);
        $user_role = Role::create([
            'role_name' => 'User',
        ]);


        //PERMISSION LATER...

        
        //USERS
        User::factory()->create([
            'first_name' => 'Samuel',
            'last_name' => 'Adebunmi',
            'role' => $admin_role,
            'user_plan_id' => NULL,
            'email' => 'adebsholey4real@gmail.com',
            'phone_number' => '08168509044',
            'password' => Hash::make('password'),
        ]); 
        User::factory()->create([
            'first_name' => 'Oreofe',
            'last_name' => 'Adebunmi',
            'role' => $user_role,
            'user_plan_id' => $user_plan_basic->id,
            'email' => 'oreofe@gmail.com',
            'phone_number' => '08198092334',
            'password' => Hash::make('password'),
        ]); 
        User::factory()->create([
            'first_name' => 'Emmanuel',
            'last_name' => 'Adebunmi',
            'role' => $user_role,
            'user_plan_id' => $user_plan_diamond->id,
            'email' => 'emmanuel@gmail.com',
            'phone_number' => '08198092889',
            'password' => Hash::make('password'),
        ]); 
        User::factory()->create([
            'first_name' => 'Tolubobo',
            'last_name' => 'Adebunmi',
            'role' => $user_role,
            'user_plan_id' => $user_plan_gold->id,
            'email' => 'tolubobo@gmail.com',
            'phone_number' => '08198092889',
            'password' => Hash::make('password'),
        ]); 
        User::factory()->create([
            'first_name' => 'Paul',
            'last_name' => 'Dennis',
            'role' => $user_role,
            'user_plan_id' => $user_plan_gold->id,
            'email' => 'pauldennis@gmail.com',
            'phone_number' => '08087675566',
            'password' => Hash::make('password'),
        ]); 
        User::factory(1000)->create();


        //PRODUCT CATEGORIES
        $product_category_data = ProductCategory::create([
            'product_category_name' => 'DATA',
            'slug' => 'data',
            'visibility' => 1,
            'active_status' => 1
        ]);
        $product_category_airtime = ProductCategory::create([
            'product_category_name' => 'AIRTIME',
            'slug' => 'airtime',
            'visibility' => 1,
            'active_status' => 1
        ]);
        $product_category_bills = ProductCategory::create([
            'product_category_name' => 'UTILITY BILLS',
            'slug' => 'utility_bills',
            'visibility' => 1,
            'active_status' => 1
        ]);
        $product_category_cable = ProductCategory::create([
            'product_category_name' => 'CABLE SUBSCRIPTION',
            'slug' => 'cable_subscription',
            'visibility' => 1,
            'active_status' => 1
        ]);
        $product_category_epins = ProductCategory::create([
            'product_category_name' => 'E-PINS',
            'slug' => 'e_pins',
            'visibility' => 1,
            'active_status' => 1
        ]);
        $product_category_result_checker = ProductCategory::create([
            'product_category_name' => 'RESULT CHECKER',
            'slug' => 'result_checker',
            'visibility' => 1,
            'active_status' => 1
        ]);


        //PRODUCTS
        $mtn_data_product = Product::create([
            'product_name' => 'MTN Data',
            'network_id' => $mtn_network->id,
            'slug' => 'mtn_data_product',
            'product_categories_id' => $product_category_data->id,
            'visibility' => 1,
            'active_status' => 1
        ]);

        $mtn_airtime_product = Product::create([
            'product_name' => 'MTN Airtime',
            'network_id' => $mtn_network->id,
            'slug' => 'mtn_airtime_product',
            'product_categories_id' => $product_category_airtime->id,
            'visibility' => 1,
            'active_status' => 1
        ]);

        $glo_data_product = Product::create([
            'product_name' => 'GLO Data',
            'network_id' => $glo_network->id,
            'slug' => 'glo_data_product',
            'product_categories_id' => $product_category_data->id,
            'visibility' => 1,
            'active_status' => 1
        ]);

        $glo_airtime_product = Product::create([
            'product_name' => 'GLO Airtime',
            'network_id' => $glo_network->id,
            'slug' => 'glo_airtime_product',
            'product_categories_id' => $product_category_airtime->id,
            'visibility' => 1,
            'active_status' => 1
        ]);

        $airtel_data_product = Product::create([
            'product_name' => 'AIRTEL Data',
            'network_id' => $airtel_network->id,
            'slug' => 'airtel_data_product',
            'product_categories_id' => $product_category_data->id,
            'visibility' => 1,
            'active_status' => 1
        ]);

        $airtel_airtime_product = Product::create([
            'product_name' => 'AIRTEL Airtime',
            'network_id' => $airtel_network->id,
            'slug' => 'airtel_airtime_product',
            'product_categories_id' => $product_category_airtime->id,
            'visibility' => 1,
            'active_status' => 1
        ]);

        $_9mobile_data_product = Product::create([
            'product_name' => '9MOBILE Data',
            'network_id' => $_9mobile_network->id,
            'slug' => '9mobile_data_product',
            'product_categories_id' => $product_category_data->id,
            'visibility' => 1,
            'active_status' => 1
        ]);
        $_9mobile_airtime_product = Product::create([
            'product_name' => '9MOBLE Airtime',
            'network_id' => $_9mobile_network->id,
            'slug' => '9mobile_airtime_product',
            'product_categories_id' => $product_category_airtime->id,
            'visibility' => 1,
            'active_status' => 1
        ]);
        $cable_gotv_product = Product::create([
            'product_name' => 'CABLE - GOTV',
            'network_id' => NULL,
            'slug' => 'gotv_product',
            'product_categories_id' => $product_category_cable->id,
            'visibility' => 1,
            'active_status' => 1
        ]);
        $cable_startimes_product = Product::create([
            'product_name' => 'CABLE - STAR TIMES',
            'network_id' => NULL,
            'slug' => 'startimes_product',
            'product_categories_id' => $product_category_cable->id,
            'visibility' => 1,
            'active_status' => 1
        ]);
        $cable_dstv_product = Product::create([
            'product_name' => 'CABLE - DSTV',
            'network_id' => NULL,
            'slug' => 'dstv_product',
            'product_categories_id' => $product_category_cable->id,
            'visibility' => 1,
            'active_status' => 1
        ]);
       
        $electricity_product = Product::create([
            'product_name' => 'ELECTRICITY / BILLS',
            'network_id' => NULL,
            'slug' => 'bills_product',
            'product_categories_id' => $product_category_bills->id,
            'visibility' => 1,
            'active_status' => 1
        ]);

        $result_checker_product = Product::create([
            'product_name' => 'RESULT CHECKER',
            'network_id' => NULL,
            'slug' => 'result_checker_product',
            'product_categories_id' => $product_category_result_checker->id,
            'visibility' => 1,
            'active_status' => 1
        ]);

        $epins_product = Product::create([
            'product_name' => 'RESULT CHECKER',
            'network_id' => NULL,
            'slug' => 'e_pins_product',
            'product_categories_id' => $product_category_result_checker->id,
            'visibility' => 1,
            'active_status' => 1
        ]);

        


        // PRODUCT PLAN CATEGORIES - optional - for deeper classification
        $product_plan_categories_mtn_sme = ProductPlanCategory::create([
            'product_plan_category_name' => 'MTN SME',
            'product_id' => $mtn_data_product->id,
        ]);
        $product_plan_categories_mtn_gifting = ProductPlanCategory::create([
            'product_plan_category_name' => 'MTN GIFTING',
            'product_id' => $mtn_data_product->id,
        ]);
        $product_plan_categories_mtn_direct_data = ProductPlanCategory::create([
            'product_plan_category_name' => 'MTN SHARE DATA',
            'product_id' => $mtn_data_product->id,
        ]);
        $product_plan_categories_mtn_cg_data = ProductPlanCategory::create([
            'product_plan_category_name' => 'MTN CORPORATE GIFTING',
            'product_id' => $mtn_data_product->id,
        ]);
        $product_plan_categories_mtn_sme2_data = ProductPlanCategory::create([
            'product_plan_category_name' => 'MTN SME2',
            'product_id' => $mtn_data_product->id,
        ]);

        $product_plan_categories_mtn_share_data = ProductPlanCategory::create([
            'product_plan_category_name' => 'MTN SHARE DATA',
            'product_id' => $mtn_data_product->id,
        ]);
        


        // THIS LOGIC NO LONGER HOLDS FOR NOW
        //PRODUCT PLAN AND PRICES - ADMIN LEVEL.... This needs to change and be created based on the automation
        //MTN Special Gifiting::::::::::::   
        // $product_plans_data1 = ProductPlan::create([
        //     'product_plan_name' => '50MB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $mtn_data_product->id,
        //     'data_size_in_mb' => 50,
        //     'validity_in_days' => 30,
        //     'cost_price' => 45,
        //     'profit' => 5, //admin product: this is what he shares into user_product_plans, customer buying sees: 45+5=50
        //     'product_plan_category_id' => $product_plan_categories_mtn_gifting->id, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $product_plans_data2 = ProductPlan::create([
        //     'product_plan_name' => '250MB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $mtn_data_product->id,
        //     'data_size_in_mb' => 250,
        //     'validity_in_days' => 30,
        //     'cost_price' => 100,
        //     'profit' => 30, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' => $product_plan_categories_mtn_gifting->id, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $product_plans_data3 = ProductPlan::create([
        //     'product_plan_name' => '500MB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $mtn_data_product->id,
        //     'data_size_in_mb' => 500,
        //     'validity_in_days' => 30,
        //     'cost_price' => 150,
        //     'profit' => 50, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' => $product_plan_categories_mtn_gifting->id, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $product_plans_data4 = ProductPlan::create([
        //     'product_plan_name' => '1GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $mtn_data_product->id,
        //     'data_size_in_mb' => 1000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 230,
        //     'profit' => 70, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' => $product_plan_categories_mtn_gifting->id, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $product_plans_data5 = ProductPlan::create([
        //     'product_plan_name' => '2GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $mtn_data_product->id,
        //     'data_size_in_mb' => 2000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 400,
        //     'profit' => 100, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' => $product_plan_categories_mtn_gifting->id, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $product_plans_data6 = ProductPlan::create([
        //     'product_plan_name' => '3GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $mtn_data_product->id,
        //     'data_size_in_mb' => 3000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 450,
        //     'profit' => 150, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' => $product_plan_categories_mtn_gifting->id, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);


        // //MTN SMEs
        // $product_plans_data7 = ProductPlan::create([
        //     'product_plan_name' => '1GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $mtn_data_product->id,
        //     'data_size_in_mb' => 1000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 250,
        //     'profit' => 80, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' => $product_plan_categories_mtn_sme->id, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $product_plans_data8 = ProductPlan::create([
        //     'product_plan_name' => '2GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $mtn_data_product->id,
        //     'data_size_in_mb' => 2000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 350,
        //     'profit' => 150, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' => $product_plan_categories_mtn_sme->id, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $product_plans_data9 = ProductPlan::create([
        //     'product_plan_name' => '5GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $mtn_data_product->id,
        //     'data_size_in_mb' => 5000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 1300,
        //     'profit' => 225, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' => $product_plan_categories_mtn_sme->id, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // //glo = 1gb, 2gb
        // $product_plans_data10 = ProductPlan::create([
        //     'product_plan_name' => '1GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $glo_data_product->id,
        //     'data_size_in_mb' => 1000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 200,
        //     'profit' => 70, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' => NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1
        // ]);

        // $product_plans_data11 = ProductPlan::create([
        //     'product_plan_name' => '2GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $glo_data_product->id,
        //     'data_size_in_mb' => 2000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 300,
        //     'profit' => 80, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // //airtel = 1gb, 2gb
        // $product_plans_data12 = ProductPlan::create([
        //     'product_plan_name' => '1GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $airtel_data_product->id,
        //     'data_size_in_mb' => 1000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 240,
        //     'profit' => 55, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' => NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1
        // ]);

        // $product_plans_data13 = ProductPlan::create([
        //     'product_plan_name' => '2GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $airtel_data_product->id,
        //     'data_size_in_mb' => 2000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 310,
        //     'profit' => 80, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // //9mobile = 1gb, 2gb
        // $product_plans_data14 = ProductPlan::create([
        //     'product_plan_name' => '1GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $_9mobile_data_product->id,
        //     'data_size_in_mb' => 1000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 200,
        //     'profit' => 70, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' => NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1
        // ]);

        // $product_plans_data15 = ProductPlan::create([
        //     'product_plan_name' => '2GB 30days', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $_9mobile_data_product->id,
        //     'data_size_in_mb' => 2000,
        //     'validity_in_days' => 30,
        //     'cost_price' => 300,
        //     'profit' => 80, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // //product plans - airtime
        // $product_plans_airtime = ProductPlan::create([
        //     'product_plan_name' => 'MTN Airtime', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $mtn_airtime_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => NULL,
        //     'profit' => 2, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $product_plans_airtime2 = ProductPlan::create([
        //     'product_plan_name' => 'GLO Airtime', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $glo_airtime_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => NULL,
        //     'profit' => 2, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $product_plans_airtime3 = ProductPlan::create([
        //     'product_plan_name' => 'AIRTEL Airtime', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $airtel_airtime_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => NULL,
        //     'profit' => 2, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $product_plans_airtime4 = ProductPlan::create([
        //     'product_plan_name' => '9MOBILE Airtime', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $_9mobile_airtime_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => NULL,
        //     'profit' => 2, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // //electricty electricity_ibadan_ibedc_product
        // $product_plans_ibadan_bills = ProductPlan::create([
        //     'product_plan_name' => 'Ibadan Prepaid', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $electricity_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 500,
        //     'profit' => 30, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $product_plans_lagos_bills = ProductPlan::create([
        //     'product_plan_name' => 'Lagos Prepaid', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $electricity_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 500,
        //     'profit' => 30, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // //TV subscriptions
        // $dstv_compact = ProductPlan::create([
        //     'product_plan_name' => 'DSTV Compact', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $cable_dstv_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 15000,
        //     'profit' => 200, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // $dstv_compact_plus = ProductPlan::create([
        //     'product_plan_name' => 'DSTV Compact Plus', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $cable_dstv_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 25000,
        //     'profit' => 200, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // $dstv_premium = ProductPlan::create([
        //     'product_plan_name' => 'DSTV Premium', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $cable_dstv_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 37000,
        //     'profit' => 200, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // $gotv_smallie_monthly = ProductPlan::create([
        //     'product_plan_name' => 'GOTV Smallie Monthly', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $cable_gotv_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 1500,
        //     'profit' => 85, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $gotv_smallie_yearly = ProductPlan::create([
        //     'product_plan_name' => 'GOTV Smallie Yearly', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $cable_gotv_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 10200,
        //     'profit' => 200, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $gotv_max = ProductPlan::create([
        //     'product_plan_name' => 'GOTV Max', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $cable_gotv_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 7000,
        //     'profit' => 200, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        

        // $startimes_nova_weekly = ProductPlan::create([
        //     'product_plan_name' => 'DTT (Antenna) Nova Weekly', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $cable_startimes_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 500,
        //     'profit' => 20, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // $startimes_nova_monthly = ProductPlan::create([
        //     'product_plan_name' => 'DTT (Antenna) Nova Monthly', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $cable_startimes_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 1700,
        //     'profit' => 20, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // $startimes_basic_weekly = ProductPlan::create([
        //     'product_plan_name' => 'DTT (Antenna) Basic Weekly', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $cable_startimes_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 1000,
        //     'profit' => 40, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // $startimes_basic_monthly = ProductPlan::create([
        //     'product_plan_name' => 'DTT (Antenna) Basic Monthly', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $cable_startimes_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 3000,
        //     'profit' => 40, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        // //Result Checker
        // $waec_result_checker = ProductPlan::create([
        //     'product_plan_name' => 'WAEC Token (Result Checker)', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $result_checker_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 1350,
        //     'profit' => 150, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);
        // $neco_result_checker = ProductPlan::create([
        //     'product_plan_name' => 'NECO Token (Result Checker)', //just concatenate other params here: majorly price: costprice + profit::::: if it has a product plan category e.g MTN 1GB MTN SME or just add those detail
        //     'product_id' => $result_checker_product->id,
        //     'data_size_in_mb' => NULL,
        //     'validity_in_days' => NULL,
        //     'cost_price' => 3600,
        //     'profit' => 300, //admin product: this is what he shares into user_product_plans, customer buying sees: 250+80=330
        //     'product_plan_category_id' =>NULL, // NULLABLE
        //     'visibility' => 1,
        //     'active_status' => 1,
        // ]);

        
   
        //THIS LOGIC NO LONGER HOLDS:::: USER PRODUCT PLANS inherits from product_plans .... things that will change:
        // name of the user product plan
        // profit_category: FLAT / PERCENTAGE
        // profit: flat/percentage   flat rate cannot be more than productplan profit: percentage cannot be more than 90%/100% sha
        // : profit of product_plans
        // the id of the product_plan
        // but the actual creation will be a loop through with percentage as the default: will be easier for the Admin
        // which can be edited later to flat rate for each as he wishes
        
       
        //create user product plan for basic user plan
        // $fetch_all_product_plans = ProductPlan::get();
        // // logger(json_encode($fetch_all_product_plans->toArray()));
        // // logger(json_encode($fetch_all_product_plans));
        // // logger(gettype($fetch_all_product_plans->toArray()));
        // foreach($fetch_all_product_plans as  $product_plan){
        //     // $product_plan_cost_price = $product_plan->cost_price ?? 0;
        //     // logger($product_plan);
        //     $data['user_plan_id'] = $user_plan_basic->id;
        //     $data['product_plan_id'] = $product_plan->id;
        //     $data['profit'] = ($user_plan_basic->percentage_profit_share / 100) * $product_plan->profit;
        //     UserProductPlan::create($data);

        //     $dataGold['user_plan_id'] = $user_plan_gold->id;
        //     $dataGold['product_plan_id'] = $product_plan->id;
        //     $dataGold['profit'] = ($user_plan_gold->percentage_profit_share / 100) * $product_plan->profit;
        //     UserProductPlan::create($dataGold);

        //     $dataDiamond['user_plan_id'] = $user_plan_diamond->id;
        //     $dataDiamond['product_plan_id'] = $product_plan->id;
        //     $dataDiamond['profit'] = ($user_plan_diamond->percentage_profit_share / 100) * $product_plan->profit;
        //     UserProductPlan::create($dataDiamond);

        //     $dataPlatinum['user_plan_id'] = $user_plan_platinum->id;
        //     $dataPlatinum['product_plan_id'] = $product_plan->id;
        //     $dataPlatinum['profit'] = ($user_plan_platinum->percentage_profit_share / 100) * $product_plan->profit;
        //     UserProductPlan::create($dataPlatinum);     
        // }
        
        // NOTE: if a user_product_plan is not found, its parent plan details will apply
        // $user_basic_product_plan = UserProductPlan::create([

        // ]);
        // $user_gold_product_plan = '';
        // $user_diamond_product_plan = '';

        


        
    }
}
