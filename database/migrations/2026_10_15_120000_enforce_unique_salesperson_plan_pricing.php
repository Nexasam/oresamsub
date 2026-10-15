<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('product_plan_custom_pricings')
            ->select('user_id', 'product_plan_id')
            ->groupBy('user_id', 'product_plan_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->each(function ($duplicate): void {
                $ids = DB::table('product_plan_custom_pricings')
                    ->where('user_id', $duplicate->user_id)
                    ->where('product_plan_id', $duplicate->product_plan_id)
                    ->orderByDesc('updated_at')
                    ->orderByDesc('created_at')
                    ->pluck('id');

                DB::table('product_plan_custom_pricings')
                    ->whereIn('id', $ids->slice(1)->all())
                    ->delete();
            });

        Schema::table('product_plan_custom_pricings', function (Blueprint $table): void {
            $table->unique(
                ['user_id', 'product_plan_id'],
                'custom_pricing_user_plan_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('product_plan_custom_pricings', function (Blueprint $table): void {
            $table->dropUnique('custom_pricing_user_plan_unique');
        });
    }
};
