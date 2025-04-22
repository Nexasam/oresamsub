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
            $table->string('bank_name')->after('bank_code')->nullable();
            $table->string('bank_charges')->after('bank_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funding_option_bank_codes', function (Blueprint $table) {
            $table->dropColumn('bank_name');
            $table->dropColumn('bank_charges');
        });
    }
};
