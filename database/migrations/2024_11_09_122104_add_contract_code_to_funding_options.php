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
        Schema::table('funding_options', function (Blueprint $table) {
            $table->string('contract_code')->after('is_current_option')->nullable()->comment('This is for flutterwave');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funding_options', function (Blueprint $table) {
            $table->dropColumn('contract_code');
        });
    }
};
