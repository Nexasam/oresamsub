<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_email_notification_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->boolean('failed_transactions')->default(false);
            $table->boolean('pending_transactions')->default(false);
            $table->boolean('automation_low_balance')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_email_notification_preferences');
    }
};
