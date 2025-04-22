<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Network;
use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserPlan;
use App\Models\Automation;
use App\Models\Permission;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\FundingOption;
use App\Models\ProductCategory;
use App\Models\ReferralSetting;
use App\Models\UserProductPlan;
use Illuminate\Database\Seeder;
use App\Models\UserBulkDataWallet;
use App\Models\LandingPagesSetting;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Hash;


class DatabaseSeederTesting extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        //create the default list here
        ReferralSetting::create();

        //create landing pages defaultss
        $landing_pages_arr = config('landing_pages');
        foreach($landing_pages_arr as $key=>$value){
            $data['field_name'] = $key;
            $data['field_details'] = $value[2];
            $data['template_type'] = 'template_1';
            $data['visibility'] = 1;
            LandingPagesSetting::create($data);
        }

        // FUNDING OPTIONS
        $crystal_pay = FundingOption::create([
            "funding_option_name" => "Crystal Pay",
            "slug" => "crystal_pay",
            "is_current_option" => "1",
            "api_public_key" => 'xxxxxxxx',
            "api_secret_key" =>'xxxxxxxx',
            "activation_status" =>1,
        ]);

        $flutterwave = FundingOption::create([
            "funding_option_name" => "Flutterwave",
            "slug" => "flutterwave",
            "is_current_option" => "0",
            "api_public_key" => 'xxxxxxxx',
            "api_secret_key" =>'xxxxxxxx',
            "activation_status" =>0,
        ]);

        //AUTOMATIONS
        //megasub
        $megasub = Automation::create([
            "id" => "9c2887ea-55b5-4f19-904e-e490a10682ea",
            "automation_name" => 'MEGASUBPLUB',
            "slug" =>'megasubplug',
            "api_public_key" =>'xxxx246266435f47e344bxxxx',
            "api_password" =>'Akwaowo0000@',
        ]);

        //ogdams
        $ogdams =Automation::create([
            "id" => "9c2887ea-59c7-471a-9407-1ff44b61a349",
            "automation_name" => 'OGDAMS',
            "slug" =>'ogdams',
            "api_public_key" => 'xxxxx-1ec0-47ea-9593-xxxxx'
        ]);

         //ogdams - 2
         $ogdamsv2 =Automation::create([
            "id" => "9c2887ea-59c7-471a-9407-1ff44b61akj1",
            "automation_name" => 'OGDAMS v2',
            "slug" =>'ogdams_v2',
            "api_public_key" => 'xxxxx-1ec0-47ea-9593-xxxxx'
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

        $smeplug = Automation::create([
            "id" => "9c2887ea-7c78-4085-99bb-03565b066we5",
            "automation_name" => 'SMEPLUG',
            "slug" =>'smeplug',
        ]);


        //NETWORKS
        $mtn_network = Network::create([
            'id' => '9c29efbb-0062-4f47-9e64-92ff101274d5',
            'network_name' => 'MTN'
        ]);

        $glo_network = Network::create([
            'id' => 'a7642d68-84b8-4532-a4b9-3dce8895f2e8',
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
     
     
        //assign all permissions to Admin:
        // $file_permissions = config('permissions');
        // // logger($file_permissions);
        // foreach($file_permissions as $key=>$permission){
        //     if($file_permissions[$key]['slug'] != 'data_purchase'){
        //         Permission::updateOrCreate(
        //         [
        //             'role_id' => $admin_role->id,
        //             'permission_slug' => $file_permissions[$key]['slug'],
        //         ],
        //         [
        //             'permission_name' => $file_permissions[$key]['name'],
        //             'permission_slug' => $file_permissions[$key]['slug'],
        //             'permission_create' => $file_permissions[$key]['create'],
        //             'permission_read' => $file_permissions[$key]['read'],
        //             'permission_update' => $file_permissions[$key]['update'],
        //             'permission_delete' => $file_permissions[$key]['delete'],
        //         ]);
        //     }
        // }

        // $user_permissions = ['data_purchase'];
        // foreach($file_permissions as $key=>$permission){

        //     if( in_array($file_permissions[$key]['slug'],$user_permissions) ){
        //         Permission::updateOrCreate(
        //             [
        //                 'role_id' => $user_role->id,
        //                 'permission_slug' => $file_permissions[$key]['slug'],
        //             ],
        //             [
        //                 'permission_name' => $file_permissions[$key]['name'],
        //                 'permission_slug' => $file_permissions[$key]['slug'],
        //                 'permission_create' => $file_permissions[$key]['create'],
        //                 'permission_read' => $file_permissions[$key]['read'],
        //                 'permission_update' => $file_permissions[$key]['update'],
        //                 'permission_delete' => $file_permissions[$key]['delete'],
        //         ]);
        //     }
        // }
        //PERMISSION LATER...



         $user_role = Role::create([
            'role_name' => 'User',
        ]);
        
        //USERS
        User::factory()->create([
            'username' => 'samuel'.rand(11,99),
            'first_name' => 'Samuel',
            'last_name' => 'Adebunmi',
            'pin' => rand(1111,9999),
            'main_wallet' => 20000,
            'role_id' => $admin_role,
            'user_plan_id' => $user_plan_diamond->id,
            'email' => 'adebsholey4real@gmail.com',
            'phone_number' => '08168509044',
            'password' => Hash::make('password'),
        ]); 
       $user_ore =  User::factory()->create([
            'username' => 'oreofe'.rand(11,99),
            'first_name' => 'Oreofe',
            'last_name' => 'Adebunmi',
            'pin' => 1234,
            'role_id' => $user_role,
            'user_plan_id' => $user_plan_basic->id,
            'email' => 'oreofe@gmail.com',
            'phone_number' => '08198092334',
            'password' => Hash::make('password'),
        ]); 
        User::factory()->create([
            'username' => 'emmanuel'.rand(11,99),
            'first_name' => 'Emmanuel',
            'last_name' => 'Adebunmi',
            'pin' => rand(1111,9999),
            'role_id' => $user_role,
            'user_plan_id' => $user_plan_diamond->id,
            'email' => 'emmanuel@gmail.com',
            'phone_number' => '08198092771',
            'password' => Hash::make('password'),
        ]); 

        //PRODUCT CATEGORIES change to===> PRODUCTS
        $product_data = Product::create([
            'id' => '9c3a0c19-1920-434b-b98d-8d3d370afa9b',
            'product_name' => 'DATA',
            'slug' => 'data',
            'visibility' => 1,
            'active_status' => 1
        ]);

        $product_airtime = Product::create([
            'id' => '9c3a0c19-1d05-4a07-8135-9dcaab9c3994',
            'product_name' => 'AIRTIME',
            'slug' => 'airtime',
            'visibility' => 1,
            'active_status' => 1
        ]);
        $product_bills = Product::create([
            'id' => '9c3a0c19-1da5-49e2-b9fd-6094c7f37610',
            'product_name' => 'UTILITY BILLS',
            'slug' => 'utility_bills',
            'visibility' => 1,
            'active_status' => 1
        ]);
        $product_cable = Product::create([
            'id' => '9c3a0c19-1e76-4e58-9ebb-a74853b4eebb',
            'product_name' => 'CABLE SUBSCRIPTION',
            'slug' => 'cable_subscription',
            'visibility' => 1,
            'active_status' => 1
        ]);
        $product_epins = Product::create([
            'id' => '9c3a0c19-2059-4423-a413-91dff2688730',
            'product_name' => 'E-PINS',
            'slug' => 'e_pins',
            'visibility' => 1,
            'active_status' => 1
        ]);
        $product_result_checker = Product::create([
            'id' => '9c3a0c19-214a-4fad-96e0-fa0438dae861',
            'product_name' => 'RESULT CHECKER',
            'slug' => 'result_checker',
            'visibility' => 1,
            'active_status' => 1
        ]); 




       // PRODUCT PLAN CATEGORIES - compulsory*** - for deeper classification
       $pr_plan_mtn_airtime_momo = ProductPlanCategory::create([
        'id' => 'e2d7b231-7c9f-44dd-9b05-7c27ed29e16e',
        'product_plan_category_name' => 'MTN AIRTIME (MOMO)',
        'product_id' => $product_airtime->id,
        'network_id' => $mtn_network->id,
        'automation_id' => $megasub->id,
        'is_hot_sales' => 0,
        // 'bulk_data_wallet_in_mb' => 1048576
      ]);

      $pr_plan_mtn_airtime_vtu = ProductPlanCategory::create([
        'id' => '0ed2d8b7-8c2e-4442-85c7-840f801552f0',
        'product_plan_category_name' => 'MTN VTU (Virtual Top Up)',
        'product_id' => $product_airtime->id,
        'network_id' => $mtn_network->id,
        'automation_id' => $megasub->id,
        'is_hot_sales' => 0,
        // 'bulk_data_wallet_in_mb' => 1048576
      ]);

      $pr_plan_mtn_airtime_share_n_sell = ProductPlanCategory::create([
        'id' => '1fb2806c-6dd5-49d3-badb-481d70e372a1',
        'product_plan_category_name' => 'MTN AIRTIME (SHARE N SELL)',
        'product_id' => $product_airtime->id,
        'network_id' => $mtn_network->id,
        'automation_id' => $megasub->id,
        'is_hot_sales' => 0,
        // 'bulk_data_wallet_in_mb' => 1048576
      ]);


      $pr_plan_glo_airtime = ProductPlanCategory::create([
        'id' => 'fb1064bd-0d51-4eb1-b78f-fa74fab4b89f',
        'product_plan_category_name' => 'GLO VTU (Virtual Top Up)',
        'product_id' => $product_airtime->id,
        'network_id' => $glo_network->id,
        'automation_id' => $megasub->id,
        'is_hot_sales' => 0,
        // 'bulk_data_wallet_in_mb' => 1048576
      ]);
      

      $pr_plan_airtel_airtime = ProductPlanCategory::create([
        'id' => '93d019fc-d9a0-4bcd-957c-8a11a9e5e133',
        'product_plan_category_name' => 'AIRTEL VTU (Virtual Top Up)',
        'product_id' => $product_airtime->id,
        'network_id' => $airtel_network->id,
        'automation_id' => $megasub->id,
        'is_hot_sales' => 0,
        // 'bulk_data_wallet_in_mb' => 1048576
      ]);

      $pr_plan_9mobile_airtime = ProductPlanCategory::create([
        'id' => '7559521a-272e-4b27-9330-c1442154626f',
        'product_plan_category_name' => '9MOBILE VTU (Virtual Top Up)',
        'product_id' => $product_airtime->id,
        'network_id' => $_9mobile_network->id,
        'automation_id' => $megasub->id,
        'is_hot_sales' => 0,
        // 'bulk_data_wallet_in_mb' => 1048576
      ]);
       

        // SME
        $pr_plan_mtn_sme_data = ProductPlanCategory::create([
            'id' => '9c39f216-00a0-42ab-b195-558133f67a15',
            'product_plan_category_name' => 'MTN SME DATA',
            'product_id' => $product_data->id,
            'network_id' => $mtn_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_plan_glo_sme_data = ProductPlanCategory::create([
            'id' => '9c39f216-020d-4d37-842b-840a7ff82d54',
            'product_plan_category_name' => 'GLO SME DATA',
            'product_id' => $product_data->id,
            'network_id' => $glo_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_plan_airtel_sme_data = ProductPlanCategory::create([
            'id' => '9c39f216-02d9-4a46-b8de-eb48f668da88',
            'product_plan_category_name' => 'AIRTEL SME DATA',
            'product_id' => $product_data->id,
            'network_id' => $airtel_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_plan_9mobile_sme_data = ProductPlanCategory::create([
            'id' => '9c39f216-03e8-4417-bcc9-c098e77f2c51',
            'product_plan_category_name' => '9MOBILE SME DATA',
            'product_id' => $product_data->id,
            'network_id' => $_9mobile_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);


         // SME2
         $pr_plan_mtn_sme2_data = ProductPlanCategory::create([
            'id' => '9c39f216-0484-4070-a83c-906999d62c97',
            'product_plan_category_name' => 'MTN SME2 DATA',
            'product_id' => $product_data->id,
            'network_id' => $mtn_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_plan_glo_sme2_data = ProductPlanCategory::create([
            'id' => '9c39f216-0513-4050-994d-59a60e99c464',
            'product_plan_category_name' => 'GLO SME2 DATA',
            'product_id' => $product_data->id,
            'network_id' => $glo_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_plan_airtel_sme2_data = ProductPlanCategory::create([
            'id' => '9c39f216-05ab-4db8-8de8-fbe47374224a',
            'product_plan_category_name' => 'AIRTEL SME2 DATA',
            'product_id' => $product_data->id,
            'network_id' => $airtel_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_plan_9mobile_sme2_data = ProductPlanCategory::create([
            'id' => '9c39f216-063f-40ce-bba1-c0edb16e05a5',
            'product_plan_category_name' => '9MOBILE SME2 DATA',
            'product_id' => $product_data->id,
            'network_id' => $_9mobile_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576 
        ]);



         // CG
         $pr_plan_mtn_cg_data = ProductPlanCategory::create([
            'id'=> '9c39f216-06d6-48fc-971e-d5778723497e',
            'product_plan_category_name' => 'MTN CG DATA',
            'product_id' => $product_data->id,
            'network_id' => $mtn_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576 
        ]);

        $pr_plan_glo_cg_data = ProductPlanCategory::create([
            'id'=> '9c39f216-076e-4697-b93e-785e05643fa5',
            'product_plan_category_name' => 'GLO CG DATA',
            'product_id' => $product_data->id,
            'network_id' => $glo_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576 
        ]);

        $pr_plan_airtel_cg_data = ProductPlanCategory::create([
            'id'=> '9c39f216-0805-4e5c-89b6-2c251f5821ab',
            'product_plan_category_name' => 'AIRTEL CG DATA',
            'product_id' => $product_data->id,
            'network_id' => $airtel_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576 
        ]);

        $pr_plan_9mobile_cg_data = ProductPlanCategory::create([
            'id'=> '9c39f216-089c-44fc-a535-cc6f6a56bf68',
            'product_plan_category_name' => '9MOBILE CG DATA',
            'product_id' => $product_data->id,
            'network_id' => $_9mobile_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576 
        ]);


         // Gifting
         $pr_plan_mtn_gifting_data = ProductPlanCategory::create([
            'id'=> '9c39f216-095b-46de-8466-88158a31e3e2',
            'product_plan_category_name' => 'MTN GIFTING DATA',
            'product_id' => $product_data->id,
            'network_id' => $mtn_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576 
        ]);

        $pr_plan_glo_gifting_data = ProductPlanCategory::create([
            'id'=> '9c39f216-09f1-433d-ab74-f86737ea7f1e',
            'product_plan_category_name' => 'GLO GIFTING DATA',
            'product_id' => $product_data->id,
            'network_id' => $glo_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576 
        ]);

        $pr_plan_airtel_gifting_data = ProductPlanCategory::create([
            'id'=> '9c39f216-0a81-4113-abf1-65e18c728ddc',
            'product_plan_category_name' => 'AIRTEL GIFTING DATA',
            'product_id' => $product_data->id,
            'network_id' => $airtel_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576 
        ]);

        $pr_plan_9mobile_gifting_data = ProductPlanCategory::create([
            'id'=> '9c39f216-0b12-4c61-b72a-f5ff38b0a689',
            'product_plan_category_name' => '9MOBILE GIFTING DATA',
            'product_id' => $product_data->id,
            'network_id' => $_9mobile_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576 
        ]);


        // share data
        $pr_plan_mtn_share_data = ProductPlanCategory::create([
            'id'=> '9c39f216-0bb9-472d-8775-6bc4379fec91',
            'product_plan_category_name' => 'MTN SHARE DATA',
            'product_id' => $product_data->id,
            'network_id' => $mtn_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576 
        ]);

        $pr_plan_glo_share_data = ProductPlanCategory::create([
            'id'=> '9c39f216-0c42-48fc-af5a-528e86d1de12',
            'product_plan_category_name' => 'GLO SHARE DATA',
            'product_id' => $product_data->id,
            'network_id' => $glo_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576 
        ]);

        $pr_plan_airtel_share_data = ProductPlanCategory::create([
            'id'=> '9c39f216-0ccf-4860-aab5-60b25eab9e3a',
            'product_plan_category_name' => 'AIRTEL SHARE DATA',
            'product_id' => $product_data->id,
            'network_id' => $airtel_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_plan_9mobile_share_data = ProductPlanCategory::create([
            'id'=> '9c39f216-0d65-4f57-a15f-db1b07c58c95',
            'product_plan_category_name' => '9MOBILE SHARE DATA',
            'product_id' => $product_data->id,
            'network_id' => $_9mobile_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);


        // AWOOF data
        $pr_plan_mtn_awoof_data = ProductPlanCategory::create([
            'id'=> '9c39f216-0df6-48d9-8530-3e320243058f',
            'product_plan_category_name' => 'MTN AWOOF DATA',
            'product_id' => $product_data->id,
            'network_id' => $mtn_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_plan_glo_awoof_data = ProductPlanCategory::create([
            'id'=> '9c39f216-0e89-4455-8f37-c764c5f26ead',
            'product_plan_category_name' => 'GLO AWOOF DATA',
            'product_id' => $product_data->id,
            'network_id' => $glo_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_plan_airtel_awoof_data = ProductPlanCategory::create([
            'id'=> '9c39f216-0f19-4ba0-96ee-decb9ed99a82',
            'product_plan_category_name' => 'AIRTEL AWOOF DATA',
            'product_id' => $product_data->id,
            'network_id' => $airtel_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_plan_9mobile_awoof_data = ProductPlanCategory::create([
            'id'=> '9c39f216-0faf-4924-bee6-52a1149341ef',
            'product_plan_category_name' => '9MOBILE AWOOF DATA',
            'product_id' => $product_data->id,
            'network_id' => $_9mobile_network->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_gotv = ProductPlanCategory::create([
            'id'=> '9ade7334-bfae-4fe1-9bc1-cd78fef6fac8',
            'product_plan_category_name' => 'GOTV',
            'product_id' => $product_cable->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);


        $pr_startimes = ProductPlanCategory::create([
            'id'=> 'b3176d9f-6f12-45e0-9640-71c509271825',
            'product_plan_category_name' => 'STARTIMES',
            'product_id' => $product_cable->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_dstv = ProductPlanCategory::create([
            'id'=> 'a798c9a4-cd1b-4bd1-b26c-8932119d00a5',
            'product_plan_category_name' => 'DSTV',
            'product_id' => $product_cable->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $pr_bills_prepaid = ProductPlanCategory::create([
            'id'=> '12c6a955-8ce2-452e-ae09-40266fd6c531',
            'product_plan_category_name' => 'PREPAID',
            'product_id' => $product_bills->id,
            'automation_id' => $megasub->id,
            'is_hot_sales' => 0,
            // 'bulk_data_wallet_in_mb' => 1048576
        ]);

        $users = User::with(['role' => function($query){
            $query->where('role_name','User');
        }])->get();
        


        //data, main wallet
        for($i = 1; $i <= 100; $i++){
            Transaction::create([
                'user_id' => $user_ore->id,
                'product_plan_id' => '9c83dc1a-8262-4f54-8c40-41029830fe5a',
                'transaction_category' => 'data',
                'status' => 1,
                'wallet_category' => 'main_wallet',
                'phone_number' => '08168509044',
                'amount' => '00',
                'balance_before' => '10000',
                'balance_after' => '9000000',
                'description' => 'Data Purchase - TEST',
                'user_screen_message' => 'successfully processed',
                'admin_screen_message' => 'successfully processed',
            ]);
        }

        // //data, data wallet
        // for($i = 1; $i <= 100; $i++){
        //     Transaction::create([
        //         'user_id' => $user_ore->id,
        //         'product_plan_id' => '9c83dc1a-8262-4f54-8c40-41029830fe5a',
        //         'transaction_category' => 'data',
        //         'status' => 1,
        //         'wallet_category' => 'data_wallet',
        //         'phone_number' => '08168509044',
        //         'amount' => '1000',
        //         'balance_before' => '10000',
        //         'balance_after' => '9000000',
        //         'description' => 'Data Purchase - TEST',
        //         'user_screen_message' => 'successfully processed',
        //         'admin_screen_message' => 'successfully processed',
        //     ]);
        // }

        //airtime
        for($i = 1; $i <= 100; $i++){
            Transaction::create([
                'user_id' => $user_ore->id,
                'product_plan_id' => '9ca4dde6-8cd7-4cb7-80da-608887b2de8d',
                'transaction_category' => 'airtime',
                'status' => 1,
                'wallet_category' => 'main_wallet',
                'phone_number' => '08168509044',
                'amount' => '1000',
                'balance_before' => '10000',
                'balance_after' => '9000000',
                'description' => 'Airtime Purchase - TEST',
                'user_screen_message' => 'successfully processed',
                'admin_screen_message' => 'successfully processed',
            ]);
        }

        //prepaid, electricity
        for($i = 1; $i <= 100; $i++){
            Transaction::create([
                'user_id' => $user_ore->id,
                'product_plan_id' => '9cc91f03-44c3-4e53-a7a1-a1eed50138b0', 
                'transaction_category' => 'utility_bills',
                'status' => 1,
                'wallet_category' => 'main_wallet',
                'phone_number' => '08168509044',
                'metre_number' => '123456789',
                'amount' => '1000',
                'balance_before' => '10000',
                'balance_after' => '9000000',
                'description' => 'Utility Purchase - TEST',
                'user_screen_message' => 'successfully processed',
                'admin_screen_message' => 'successfully processed',
            ]);
        }

        //cable
        for($i = 1; $i <= 100; $i++){
            Transaction::create([
                'user_id' => $user_ore->id,
                'product_plan_id' => '9cc91e28-6ec3-40fb-89f5-847ad2aada72',
                'transaction_category' => 'cable_subscription',
                'status' => 1,
                'wallet_category' => 'main_wallet',
                'phone_number' => '08168509044',
                'smart_card_number' => '123456789',
                'amount' => '1000',
                'balance_before' => '10000',
                'balance_after' => '9000000',
                'description' => 'Cable Purchase - TEST',
                'user_screen_message' => 'successfully processed',
                'admin_screen_message' => 'successfully processed',
            ]);
        }    
        
    }
}
