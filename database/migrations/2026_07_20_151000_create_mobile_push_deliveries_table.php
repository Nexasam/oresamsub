<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_push_deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('mobile_device_installation_id')->constrained()->cascadeOnDelete();
            $table->string('event_key');
            $table->string('category', 30);
            $table->string('status', 30)->default('queued');
            $table->string('expo_ticket_id')->nullable();
            $table->string('error_code')->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();
            $table->unique(
                ['mobile_device_installation_id', 'event_key'],
                'mobile_push_device_event_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_push_deliveries');
    }
};
