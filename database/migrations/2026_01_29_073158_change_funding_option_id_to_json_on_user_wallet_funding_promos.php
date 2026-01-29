<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_wallet_funding_promos', function (Blueprint $table) {

            // Drop old column
            $table->dropColumn('funding_option_id');
        });

        Schema::table('user_wallet_funding_promos', function (Blueprint $table) {

            // Add new JSON column
            $table->json('funding_option_ids')
                ->nullable()
                ->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_wallet_funding_promos', function (Blueprint $table) {

            // Drop JSON column
            $table->dropColumn('funding_option_ids');
        });

        Schema::table('user_wallet_funding_promos', function (Blueprint $table) {

            // Restore original column
            $table->string('funding_option_id')
                ->after('user_id');
        });
    }
};
