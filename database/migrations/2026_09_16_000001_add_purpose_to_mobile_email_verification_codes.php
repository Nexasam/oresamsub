<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobile_email_verification_codes', function (Blueprint $table) {
            $table->string('purpose', 40)->default('email_verification')->index();
        });
    }

    public function down(): void
    {
        Schema::table('mobile_email_verification_codes', function (Blueprint $table) {
            $table->dropColumn('purpose');
        });
    }
};
