<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_product_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignUuId('product_plan_id')
                ->constrained('product_plans')
                ->cascadeOnDelete();

            $table->foreignUuId('automation_id')
                ->constrained('automations')
                ->cascadeOnDelete();

            $table->unsignedInteger('priority')->default(1); 
            // 1 = highest priority

            $table->decimal('cost_price', 12, 2)->nullable();
            $table->decimal('selling_price', 12, 2)->nullable();

            $table->boolean('is_active')->default(true);

            $table->string('provider_plan_id');


            // optional tracking metric (can be updated later)
            $table->decimal('success_rate', 5, 2)->nullable(); 
            // e.g. 99.50 (%)

            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();

            // 🔥 performance indexes (VERY important for routing logic)
            $table->index(['product_plan_id', 'priority']);
            $table->index(['automation_id']);
            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_product_plans');
    }
};