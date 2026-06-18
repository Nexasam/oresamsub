<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevel7FieldsToProductPlansTable extends Migration
{
    public function up()
    {
        Schema::table('product_plans', function (Blueprint $table) {
            $table->decimal('user_level_7_selling_price', 10, 2)->nullable()->after('user_level_6_selling_price');
            $table->decimal('user_level_7_commission', 10, 2)->nullable()->after('user_level_6_commission');
        });
    }

    public function down()
    {
        Schema::table('product_plans', function (Blueprint $table) {
            $table->dropColumn([
                'user_level_7_selling_price',
                'user_level_7_commission'
            ]);
        });
    }
}