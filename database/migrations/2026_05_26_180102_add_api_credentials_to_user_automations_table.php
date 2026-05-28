<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_automations', function (Blueprint $table) {
            $table->text('api_key')->nullable()->after('pricing_amount');
            $table->text('api_secret')->nullable()->after('api_key');
        });
    }

    public function down(): void
    {
        Schema::table('user_automations', function (Blueprint $table) {
            $table->dropColumn(['api_key', 'api_secret']);
        });
    }
};
