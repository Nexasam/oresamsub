<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('networks', function (Blueprint $table): void {
            $table->boolean('airtime_to_cash_enabled')->default(true)->after('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('networks', function (Blueprint $table): void {
            $table->dropColumn('airtime_to_cash_enabled');
        });
    }
};
