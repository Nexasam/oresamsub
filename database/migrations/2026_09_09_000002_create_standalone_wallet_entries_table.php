<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standalone_wallet_entries', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('standalone_website_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('type', 12)->index();
            $table->string('category', 24)->index();
            $table->decimal('amount', 18, 2);
            $table->decimal('balance_before', 18, 2);
            $table->decimal('balance_after', 18, 2);
            $table->string('client_reference')->nullable();
            $table->string('purpose', 255);
            $table->string('provider_reference')->nullable()->unique();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['standalone_website_id', 'client_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standalone_wallet_entries');
    }
};
