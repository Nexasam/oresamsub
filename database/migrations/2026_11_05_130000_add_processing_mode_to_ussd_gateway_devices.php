<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ussd_gateway_devices', function (Blueprint $table) {
            $table->string('processing_mode', 16)->default('manual')->after('is_active')->index();
        });
    }

    public function down(): void
    {
        Schema::table('ussd_gateway_devices', function (Blueprint $table) {
            $table->dropIndex(['processing_mode']);
            $table->dropColumn('processing_mode');
        });
    }
};
