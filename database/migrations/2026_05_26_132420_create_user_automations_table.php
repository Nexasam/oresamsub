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
        Schema::create('user_automations', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUuid('automation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('automation_pricing_type')
                ->default('PAYG');

            $table->decimal('pricing_amount', 12, 2)
                ->default(0);

            $table->decimal('first_payment', 12, 2)
                ->default(0);

            $table->enum('product', [
                'data',
                'airtime',
                'cable',
                'utility_bills',
            ])->default('data');

            $table->timestamps();

            $table->unique([
                'user_id',
                'automation_id',
                'product',
            ], 'user_automation_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_automations');
    }
};