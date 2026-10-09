<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('network_issue_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('network_id')->unique()->constrained('networks')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->string('restored_title')->nullable();
            $table->text('restored_message')->nullable();
            $table->timestamp('last_restored_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->unsignedInteger('priority')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_issue_alerts');
    }
};
