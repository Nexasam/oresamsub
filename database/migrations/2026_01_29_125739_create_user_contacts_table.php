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
        Schema::create('user_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('phone_number')->index();
            $table->foreignUuid('network_id')->nullable()->index();
            $table->string('product')->nullable()->comment('e.g airtime, data, cable, utility');
            $table->string('product_plan_id')->nullable();
            $table->timestamp('last_used_at')->index();
            $table->timestamps();

            $table->unique(['user_id', 'phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_contacts');
    }
};
