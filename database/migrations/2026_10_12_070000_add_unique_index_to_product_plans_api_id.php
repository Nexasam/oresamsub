<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('product_plans')
            ->where('api_id', '')
            ->update(['api_id' => null]);

        $duplicates = DB::table('product_plans')
            ->select('api_id', DB::raw('COUNT(*) as duplicate_count'))
            ->whereNotNull('api_id')
            ->where('api_id', '<>', '')
            ->groupBy('api_id')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('api_id')
            ->get();

        if ($duplicates->isNotEmpty()) {
            $summary = $duplicates
                ->map(fn ($duplicate) => "{$duplicate->api_id} ({$duplicate->duplicate_count} plans)")
                ->implode(', ');

            throw new RuntimeException(
                "Cannot add unique index to product_plans.api_id because duplicates still exist: {$summary}. ".
                'Fix the duplicate API IDs, then rerun php artisan migrate.'
            );
        }

        Schema::table('product_plans', function (Blueprint $table): void {
            $table->unique('api_id', 'product_plans_api_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('product_plans', function (Blueprint $table): void {
            $table->dropUnique('product_plans_api_id_unique');
        });
    }
};
