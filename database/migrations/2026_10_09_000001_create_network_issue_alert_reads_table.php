<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('network_issue_alert_reads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('network_issue_alert_id')->constrained('network_issue_alerts')->cascadeOnDelete();
            $table->string('notice_key');
            $table->timestamps();

            $table->unique(['user_id', 'notice_key'], 'network_issue_reads_user_notice_unique');
            $table->index(['user_id', 'network_issue_alert_id'], 'network_issue_reads_user_alert_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_issue_alert_reads');
    }
};
