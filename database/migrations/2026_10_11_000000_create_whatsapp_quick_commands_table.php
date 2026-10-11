<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_quick_commands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('whatsapp_phone')->index();
            $table->string('command_hash', 64);
            $table->string('command', 500);
            $table->string('command_type', 30)->nullable()->index();
            $table->string('label', 120);
            $table->unsignedInteger('usage_count')->default(1);
            $table->timestamp('last_used_at')->index();
            $table->timestamps();

            $table->unique(['whatsapp_phone', 'command_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_quick_commands');
    }
};
