<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_push_campaigns', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('request_token')->unique();
            $table->string('title', 80);
            $table->string('message', 180);
            $table->string('destination', 40);
            $table->string('audience_type', 30)->default('all');
            $table->uuid('audience_value')->nullable();
            $table->string('status', 30)->default('queued');
            $table->unsignedInteger('recipient_users')->default(0);
            $table->unsignedInteger('recipient_devices')->default(0);
            $table->foreignUuid('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->text('failure_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_push_campaigns');
    }
};
