<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductCategory::create([
            'product_category_name' => 'DATA',
            'visibility' => '1',
            'active_status' => '1',
        ]);

        ProductCategory::create([
            'product_category_name' => 'AIRTIME',
            'visibility' => '1',
            'active_status' => '1',
        ]);

        ProductCategory::create([
            'product_category_name' => 'BILLS PAYMENT',
            'visibility' => '1',
            'active_status' => '1',
        ]);

        ProductCategory::create([
            'product_category_name' => 'ELECTRICITY',
            'visibility' => '1',
            'active_status' => '1',
        ]);

        ProductCategory::create([
            'product_category_name' => 'E-PINS',
            'visibility' => '1',
            'active_status' => '1',
        ]);


        ProductCategory::create([
            'product_category_name' => 'RESULT CHECKER',
            'visibility' => '1',
            'active_status' => '1',
        ]);
    }
}
