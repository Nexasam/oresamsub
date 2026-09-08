<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standalone_virtual_accounts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('standalone_website_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignUuid('funding_option_id')->constrained('funding_options');
            $table->string('account_reference')->unique();
            $table->string('account_number')->unique();
            $table->string('bank_code', 20)->default('1');
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_email')->nullable();
            $table->string('provider_status')->nullable();
            $table->json('provider_metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standalone_virtual_accounts');
    }
};
