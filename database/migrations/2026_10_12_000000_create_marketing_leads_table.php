<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_leads', function (Blueprint $table) {
            $table->id();
            $table->string('source')->nullable()->index();
            $table->string('developer_source')->nullable()->index();
            $table->string('domain')->unique();
            $table->string('company_name')->nullable();
            $table->string('app_name')->nullable();
            $table->string('website')->nullable();
            $table->text('phones')->nullable();
            $table->text('emails')->nullable();
            $table->text('whatsapp_links')->nullable();
            $table->string('confidence')->nullable()->index();
            $table->unsignedTinyInteger('confidence_score')->nullable()->index();
            $table->text('signals')->nullable();
            $table->string('play_app_url')->nullable();
            $table->string('package')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('new')->index();
            $table->timestamp('last_imported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_leads');
    }
};
