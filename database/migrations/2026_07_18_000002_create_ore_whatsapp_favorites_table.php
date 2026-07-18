<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ore_whatsapp_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_plan_id')->constrained('product_plans')->cascadeOnDelete();
            $table->string('shortcut', 30);
            $table->string('beneficiary_phone')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'shortcut']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ore_whatsapp_favorites');
    }
};
