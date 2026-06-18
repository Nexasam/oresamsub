<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automations', function (Blueprint $table) {

            // Core API config
            $table->json('network_plans')->nullable();
            $table->json('request_params')->nullable();
            $table->json('request_headers')->nullable();
            $table->string('http_verb')->nullable(); // POST / GET

            // Success / failure logic
            $table->json('success_condition')->nullable();
            $table->text('success_response')->nullable();
            $table->text('failed_response')->nullable();

            // Response codes
            $table->string('success_code')->nullable();
            $table->string('failure_code')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('automations', function (Blueprint $table) {

            $table->dropColumn([
                'network_plans',
                'request_params',
                'request_headers',
                'http_verb',
                'success_condition',
                'success_response',
                'failed_response',
                'success_code',
                'failure_code',
            ]);
        });
    }
};