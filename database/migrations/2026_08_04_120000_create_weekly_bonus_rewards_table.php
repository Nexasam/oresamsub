<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonuses', function (Blueprint $table) {
            $table->dateTime('ends_at')->nullable()->change();
        });

        Schema::create('weekly_bonus_rewards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bonus_id')->constrained('bonuses')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->decimal('qualifying_volume', 15, 2);
            $table->decimal('reward_amount', 15, 2);
            $table->json('transaction_categories')->nullable();
            $table->timestamps();

            $table->unique(['bonus_id', 'user_id', 'week_start'], 'weekly_bonus_campaign_user_week_unique');
            $table->index(['week_start', 'week_end'], 'weekly_bonus_period_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_bonus_rewards');
        Schema::table('bonuses', function (Blueprint $table) {
            $table->dateTime('ends_at')->nullable(false)->change();
        });
    }
};
