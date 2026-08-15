<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_followup_calls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('called_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('outcome', 30);
            $table->text('feedback');
            $table->string('followup_status', 30);
            $table->timestamp('next_followup_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
            $table->index(['followup_status', 'next_followup_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_followup_calls');
    }
};
