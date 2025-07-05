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
           $table->text('short_description')->nullable()->after('visibility_status');
        });
    }

    /**
     * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::table('funding_option_bank_codes', function (Blueprint $table) {
            $table->dropColumn('short_description');
            
        });
    }
};
