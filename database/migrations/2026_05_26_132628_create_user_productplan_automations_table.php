<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_product_plan_automations', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('user_automation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('product_plan_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('automation_product_plan_id');

            $table->integer('priority')
                ->default(1);

            $table->boolean('status')
                ->default(1);

            $table->timestamps();

            $table->index([
                'product_plan_id',
                'priority',
            ]);

            $table->unique([
                'user_automation_id',
                'product_plan_id',
                'automation_product_plan_id',
            ], 'unique_provider_plan_mapping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_product_plan_automations');
    }
};