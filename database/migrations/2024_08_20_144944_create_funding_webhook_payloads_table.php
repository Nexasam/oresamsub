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
        Schema::create('funding_webhook_payloads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('funding_slug');
            $table->string('user_id');
            $table->string('user_email');
            $table->string('status');
            $table->string('funding_status');
            $table->string('message');
            $table->string('package_id');
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('account_reference');
            $table->string('amount_paid');
            $table->string('amount_charged');
            $table->string('amount_settled');
            $table->string('currency');            
            $table->string('transaction_reference');
            $table->string('collection_reference');
            $table->longText('payload_content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funding_webhook_payloads');
    }
};
