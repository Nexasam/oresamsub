<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Network;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Product;
use App\Models\UserPlan;
use App\Models\Automation;
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
        $megasub = Automation::create([
            "id" => "9c2887ea-55b5-4f19-904e-e490a10682ea",
            "automation_name" => 'MEGASUBPLUB',
            "slug" =>'megasubplug',
        ]);

        //ogdams
        $ogdams =Automation::create([
            "id" => "9c2887ea-59c7-471a-9407-1ff44b61a349",
            "automation_name" => 'OGDAMS',
            "slug" =>'ogdams',
        ]);

        //autopilot
        $autopilot = Automation::create([
            "id" => "9c2887ea-5a69-410a-8d67-a6f62f90d19b",
            "automation_name" => 'AUTOPILOT',
            "slug" =>'autopilot',
        ]);

        //cloudsimhost
        $cloudsimhost = Automation::create([
            "id" => "9c2887ea-5b03-4085-99bb-03565b043bc6",
            "automation_name" => 'CLOUDSIMHOST',
            "slug" =>'cloudsimhost',
        ]);


        //NETWORKS
        $mtn_network = Network::create([
            'id' => '9c29efbb-0062-4f47-9e64-92ff101274d5',
            'network_name' => 'MTN'
        ]);

        $glo_network = Network::create([
            'id' => '9c29efbb-0609-4468-bfb3-880b06035f11',
            'network_name' => 'GLO'
        ]);

        $airtel_network = Network::create([
            'id' => '9c29efbb-06a8-4441-bb6c-2de40276150b',
            'network_name' => 'AIRTEL'
        ]);

        $_9mobile_network = Network::create([
            'id' => '9c29efbb-0740-4e48-8b55-d1c57fe3b916',
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
        ]);
        $user_plan_gold = UserPlan::create([
            'user_plan_name' => 'Gold Reseller Plan',
            'plan_level' => 2,
            'updated_user_plan_name' => NULL,
            'is_default' => 0,
        ]);
        $user_plan_diamond = UserPlan::create([
            'user_plan_name' => 'Diamond Reseller Plan',
            'plan_level' => 3,
            'updated_user_plan_name' => NULL,
            'is_default' => 0,
        ]);
        $user_plan_platinum = UserPlan::create([
            'user_plan_name' => 'Platinum Reseller Plan',
            'plan_level' => 4,
            'updated_user_plan_name' => NULL,
            'is_default' => 0,
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
            'id' => '9c2887f1-0fea-484a-ba7e-2fdce05241bf',
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
            'id' => '9c2887f1-1196-491c-8648-ba226a592790',
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
            'id' => '9c2887f1-1309-4277-b6cf-0ba63316acfc',
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
            'id' => '9c2887f1-1422-4c78-b676-b1c8640ad9f9',
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
            'product_categories_id' => $product_category_epins->id,
            'visibility' => 1,
            'active_status' => 1
        ]);

        


        // PRODUCT PLAN CATEGORIES - optional - for deeper classification
        $product_plan_categories_sme = ProductPlanCategory::create([
            'product_plan_category_name' => 'SME',
            'automation_id' => $megasub->id
        ]);
        $product_plan_categories_gifting = ProductPlanCategory::create([
            'product_plan_category_name' => 'GIFTING',
            'automation_id' => $ogdams->id
        ]);
        $product_plan_categories_direct = ProductPlanCategory::create([
            'product_plan_category_name' => 'DIRECT DATA',
            'automation_id' => $megasub->id
        ]);
        $product_plan_categories_cg_data = ProductPlanCategory::create([
            'product_plan_category_name' => 'CORPORATE GIFTING',
            'automation_id' => $megasub->id
            // 'product_id' => $mtn_data_product->id,
        ]);
        $product_plan_categories_sme2 = ProductPlanCategory::create([
            'product_plan_category_name' => 'SME2',
            'automation_id' => $ogdams->id

            // 'product_id' => $mtn_data_product->id,
        ]);

        $product_plan_categories_share_data = ProductPlanCategory::create([
            'product_plan_category_name' => 'DATA SHARE',
            'automation_id' => $megasub->id
            // 'product_id' => $mtn_data_product->id,
        ]);

        $product_plan_categories_share_data = ProductPlanCategory::create([
            'product_plan_category_name' => 'AWOOF',
            'automation_id' => $autopilot->id
            // 'product_id' => $mtn_data_product->id,
        ]);


        // $product_plan_categories_mtn_sme = ProductPlanCategory::create([
        //     'product_plan_category_name' => 'DATA SME',
        //     'product_id' => $mtn_data_product->id,
        // ]);
        // $product_plan_categories_mtn_gifting = ProductPlanCategory::create([
        //     'product_plan_category_name' => 'DATA GIFTING',
        //     'product_id' => $mtn_data_product->id,
        // ]);
        // $product_plan_categories_mtn_direct_data = ProductPlanCategory::create([
        //     'product_plan_category_name' => 'DATA DIRECT DATA',
        //     'product_id' => $mtn_data_product->id,
        // ]);
        // $product_plan_categories_mtn_cg_data = ProductPlanCategory::create([
        //     'product_plan_category_name' => 'MTN DATA CORPORATE GIFTING',
        //     'product_id' => $mtn_data_product->id,
        // ]);
        // $product_plan_categories_mtn_sme2_data = ProductPlanCategory::create([
        //     'product_plan_category_name' => 'MTN DATA SME2',
        //     'product_id' => $mtn_data_product->id,
        // ]);

        // $product_plan_categories_mtn_share_data = ProductPlanCategory::create([
        //     'product_plan_category_name' => 'MTN DATA SHARE DATA',
        //     'product_id' => $mtn_data_product->id,
        // ]);


        // THIS LOGIC NO LONGER HOLDS FOR NOW
        //PRODUCT PLAN AND PRICES - ADMIN LEVEL.... This needs to change and be created based on the automation
        //MTN Special Gifiting::::::::::::   
        
    }
}
