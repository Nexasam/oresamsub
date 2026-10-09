<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('network_issue_alerts', function (Blueprint $table) {
            if (! Schema::hasColumn('network_issue_alerts', 'restored_title')) {
                $table->string('restored_title')->nullable()->after('message');
            }

            if (! Schema::hasColumn('network_issue_alerts', 'restored_message')) {
                $table->text('restored_message')->nullable()->after('restored_title');
            }

            if (! Schema::hasColumn('network_issue_alerts', 'last_restored_at')) {
                $table->timestamp('last_restored_at')->nullable()->after('restored_message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('network_issue_alerts', function (Blueprint $table) {
            if (Schema::hasColumn('network_issue_alerts', 'last_restored_at')) {
                $table->dropColumn('last_restored_at');
            }

            if (Schema::hasColumn('network_issue_alerts', 'restored_message')) {
                $table->dropColumn('restored_message');
            }

            if (Schema::hasColumn('network_issue_alerts', 'restored_title')) {
                $table->dropColumn('restored_title');
            }
        });
    }
};
