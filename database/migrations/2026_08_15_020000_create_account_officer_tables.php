<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_officer_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->decimal('allocation_weight', 5, 2)->default(0);
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('officer_assignment_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->default('unassigned');
            $table->json('configuration');
            $table->unsignedInteger('total_customers')->default(0);
            $table->foreignUuid('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('customer_officer_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('officer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('batch_id')->nullable()->constrained('officer_assignment_batches')->nullOnDelete();
            $table->foreignUuid('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'ended_at']);
            $table->index(['officer_id', 'ended_at']);
        });

        Schema::table('customer_followup_calls', function (Blueprint $table) {
            $table->foreignUuid('account_officer_id')->nullable()->after('called_by')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customer_followup_calls', fn (Blueprint $table) => $table->dropConstrainedForeignId('account_officer_id'));
        Schema::dropIfExists('customer_officer_assignments');
        Schema::dropIfExists('officer_assignment_batches');
        Schema::dropIfExists('account_officer_profiles');
    }
};
