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
        Schema::table('automations', function (Blueprint $table) {
           $table->string('automation_group')->after('automation_name')->nullable();
           $table->string('data_url')->after('automation_name')->nullable();
           $table->string('airtime_url')->after('automation_name')->nullable();
           $table->string('cable_url')->after('automation_name')->nullable();
           $table->string('electricity_url')->after('automation_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('automations', function (Blueprint $table) {
            $table->dropColumn('automation_group');
            $table->dropColumn('data_url');
            $table->dropColumn('airtime_url');
            $table->dropColumn('cable_url');
            $table->dropColumn('electricity_url');
        });
    }
};
