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
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('status')->default('1');
            $table->string('users')->after('description')->nullable(); #nullable means all
            $table->string('allowed_pages')->after('description')->nullable(); #nullable means all
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('status'); 
            $table->dropColumn('users'); 
            $table->dropColumn('allowed_pages'); 
        });
    }
};
