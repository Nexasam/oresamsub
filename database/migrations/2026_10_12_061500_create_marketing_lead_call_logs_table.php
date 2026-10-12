<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_lead_call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_lead_id')->constrained()->cascadeOnDelete();
            $table->uuid('user_id')->nullable()->index();
            $table->string('phone')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('called_at')->index();
            $table->timestamps();

            $table->index(['marketing_lead_id', 'called_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_lead_call_logs');
    }
};
