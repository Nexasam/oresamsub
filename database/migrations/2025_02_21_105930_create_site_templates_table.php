<?php

use App\Models\SiteTemplate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('site_templates')) {
            Schema::create('site_templates', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('template_name')->default('template_1');
                $table->timestamps();
            });
        }

        // SiteTemplate::create([]);
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_templates');
    }
};
