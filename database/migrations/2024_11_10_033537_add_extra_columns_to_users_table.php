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
            $table->string('user_monnify_reference')->nullable()->after('id');
            $table->string('is_bvn_verified')->default(0)->after('id');
            $table->string('is_nin_verified')->default(0)->after('id');
            $table->string('bvn')->nullable()->after('id');
            $table->string('bvn_json')->nullable()->after('id');
            $table->string('nin')->nullable()->after('id');
            $table->string('nin_json')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_monnify_reference');
            $table->dropColumn('is_bvn_verified');
            $table->dropColumn('is_nin_verified');
            $table->dropColumn('bvn');
            $table->dropColumn('bvn_json');
            $table->dropColumn('nin');
            $table->dropColumn('nin_json');
        });
    }
};
