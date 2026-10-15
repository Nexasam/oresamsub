<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_salesperson')->default(false)->after('is_marketer')->index();
        });

        Schema::table('product_plans', function (Blueprint $table): void {
            $table->decimal('salesperson_price', 12, 2)->nullable()->after('default_selling_price');
        });
    }

    public function down(): void
    {
        Schema::table('product_plans', function (Blueprint $table): void {
            $table->dropColumn('salesperson_price');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['is_salesperson']);
            $table->dropColumn('is_salesperson');
        });
    }
};
