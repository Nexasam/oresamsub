<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ore_whatsapp_configs', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->string('phone_number_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ore_whatsapp_configs');
    }
};
