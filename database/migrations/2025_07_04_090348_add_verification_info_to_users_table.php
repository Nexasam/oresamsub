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
        Schema::table('users', function (Blueprint $table) {
           $table->string('verification_attempts')->default(0)->after('bvn'); #should not be more than 3 times
           $table->string('verification_status')->default(0)->after('bvn'); #should not be more than 3 times
        });
    }

    /**
     * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verification_attempts');
            $table->dropColumn('verification_status');
            
        });
    }
};
