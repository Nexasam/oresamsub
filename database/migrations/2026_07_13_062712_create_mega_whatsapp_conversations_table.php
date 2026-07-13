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
        Schema::create('mega_whatsapp_conversations', function (Blueprint $table) {

            $table->id();

            $table->string('phone')->index();

            $table->uuid('user_id')
                ->nullable();

            $table->string('current_state')
                ->nullable();

            $table->json('payload')
                ->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'mega_whatsapp_conversations'
        );
    }
};