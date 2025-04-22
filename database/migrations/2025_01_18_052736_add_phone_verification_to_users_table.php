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
            $table->boolean('phone_verification')->after('phone_number')->default(false);
            $table->string('termii_pin_id')->after('phone_number')->nullable();
            $table->longText('termii_json')->after('phone_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_verification');
            $table->dropColumn('termii_pin_id');
            $table->dropColumn('termii_json');
        });
    }
};
