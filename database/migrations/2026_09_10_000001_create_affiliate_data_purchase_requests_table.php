<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_data_purchase_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 100);
            $table->char('request_fingerprint', 64);
            $table->foreignUuid('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('pending');
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->json('response_body')->nullable();
            $table->json('provider_response')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_data_purchase_requests');
    }
};
