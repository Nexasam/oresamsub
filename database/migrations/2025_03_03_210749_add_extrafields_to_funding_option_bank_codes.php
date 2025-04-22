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
        Schema::table('funding_option_bank_codes', function (Blueprint $table) {
            $table->string('rate_category')->after('bank_code')->default('Flat')->comment('Flat or Percentage');
            $table->string('capped_at')->after('bank_code')->default('100')->comment('If percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funding_option_bank_codes', function (Blueprint $table) {
            $table->dropColumn('rate_category');
            $table->dropColumn('capped_at');
        });
    }
};
