<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('type')->default('wallet'); // wallet/bank
            $table->string('status')->default(1); // 0 - pending, 1- approved, -1 - rejected, 2 - refunded
            $table->string('reference')->unique();
            $table->string('description')->nullable();
            $table->decimal('balance_before', 15, 2)->default(0); // balance before transfer
            $table->decimal('balance_after', 15, 2)->default(0);  // balance after transfer
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
