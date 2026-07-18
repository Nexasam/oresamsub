<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ore_whatsapp_favorites', function (Blueprint $table) {
            $table->string('product_type', 20)->default('data')->after('shortcut');
            $table->decimal('amount', 12, 2)->nullable()->after('beneficiary_phone');
            $table->index(['user_id', 'product_type']);
        });
    }

    public function down(): void
    {
        Schema::table('ore_whatsapp_favorites', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'product_type']);
            $table->dropColumn(['product_type', 'amount']);
        });
    }
};
