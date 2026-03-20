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
        Schema::create('automation_wallet_fundings', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('automation_id')
                ->constrained('automations')
                ->onDelete('cascade');

            $table->decimal('threshold', 12, 2)->default(1000);
            $table->decimal('amount_to_fund', 12, 2)->default(3000);
            $table->decimal('last_balance', 12, 2)->default(20000);

            $table->boolean('send_failed_notification')->default(false);
            $table->boolean('automatic_funding')->default(true);
            $table->string('linked_customer_email')->unique();
            $table->string('bank_code')->nullable();
            $table->string('active')->default('yes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_wallet_fundings');
    }
};
