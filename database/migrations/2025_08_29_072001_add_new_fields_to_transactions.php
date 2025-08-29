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
        Schema::table('transactions', function (Blueprint $table) {
           $table->string('reprocess_automation_id')->nullable()->after('manually_processed_by');    
           $table->string('automation_plan_amount')->nullable()->after('manually_processed_by')->comment('some vendors do others dont');    
           $table->string('first_automation_balance_before')->nullable()->after('manually_processed_by')->comment('some vendors do others dont');    
           $table->string('first_automation_balance_after')->nullable()->after('manually_processed_by')->comment('some vendors do others dont');    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
           $table->dropColumn('reprocess_automation_id');    
           $table->dropColumn('automation_plan_amount');
           $table->dropColumn('first_automation_balance_before');
           $table->dropColumn('first_automation_balance_after');
        });
    }
};
