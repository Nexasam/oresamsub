<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standalone_websites', function (Blueprint $table): void {
            $table->decimal('master_wallet', 18, 2)->default(0)->after('status');
            $table->string('api_token_type', 16)->default('operational')->after('api_token_prefix');
            $table->boolean('api_token_must_rotate')->default(false)->after('api_token_type');
            $table->timestamp('api_token_expires_at')->nullable()->after('api_token_must_rotate');
        });
    }

    public function down(): void
    {
        Schema::table('standalone_websites', function (Blueprint $table): void {
            $table->dropColumn(['master_wallet', 'api_token_type', 'api_token_must_rotate', 'api_token_expires_at']);
        });
    }
};
