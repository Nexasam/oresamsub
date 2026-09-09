<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('standalone_wallet_entries')) {
            Schema::create('standalone_wallet_entries', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->foreignUuid('standalone_website_id');
                $table->string('transaction_id');
                $table->string('type', 12);
                $table->string('category', 24);
                $table->decimal('amount', 18, 2);
                $table->decimal('balance_before', 18, 2);
                $table->decimal('balance_after', 18, 2);
                $table->string('client_reference')->nullable();
                $table->string('purpose', 255);
                $table->string('provider_reference')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        $this->ensureConstraintsAndIndexes();
    }

    public function down(): void
    {
        Schema::dropIfExists('standalone_wallet_entries');
    }

    private function ensureConstraintsAndIndexes(): void
    {
        $indexes = [
            [['transaction_id'], 'swe_transaction_unique', true],
            [['type'], 'swe_type_index', false],
            [['category'], 'swe_category_index', false],
            [['provider_reference'], 'swe_provider_unique', true],
            [['standalone_website_id', 'client_reference'], 'swe_site_reference_unique', true],
        ];

        foreach ($indexes as [$columns, $name, $unique]) {
            if (! Schema::hasIndex('standalone_wallet_entries', $columns)) {
                Schema::table('standalone_wallet_entries', function (Blueprint $table) use ($columns, $name, $unique): void {
                    $unique ? $table->unique($columns, $name) : $table->index($columns, $name);
                });
            }
        }

        if (! Schema::hasForeignKey('standalone_wallet_entries', ['standalone_website_id'])) {
            Schema::table('standalone_wallet_entries', function (Blueprint $table): void {
                $table->foreign('standalone_website_id', 'swe_site_foreign')
                    ->references('id')->on('standalone_websites')->cascadeOnDelete();
            });
        }
    }
};
