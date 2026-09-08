<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standalone_websites', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('business_name');
            $table->string('contact_first_name');
            $table->string('contact_last_name');
            $table->string('email')->unique();
            $table->string('phone', 24);
            $table->string('website_url');
            $table->string('callback_url')->nullable();
            $table->text('bvn');
            $table->char('api_token_digest', 64)->unique();
            $table->string('api_token_prefix', 16);
            $table->text('webhook_signing_secret');
            $table->string('webhook_secret_hint', 32);
            $table->string('status', 16)->default('active')->index();
            $table->timestamp('api_token_rotated_at')->nullable();
            $table->timestamp('webhook_secret_rotated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standalone_websites');
    }
};
