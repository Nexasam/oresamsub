<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('language_lines', function (Blueprint $table) {
            $table->string('key', 500)->change();
        });
    }

    public function down()
    {
        Schema::table('language_lines', function (Blueprint $table) {
            $table->string('key', 191)->change(); // assuming 191 was the original
        });
    }
};
