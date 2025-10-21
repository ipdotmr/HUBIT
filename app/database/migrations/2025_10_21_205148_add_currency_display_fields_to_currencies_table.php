<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('symbol', 10)->after('name');
            $table->enum('symbol_position', ['before', 'after'])->default('before')->after('symbol');
            $table->string('prefix', 10)->nullable()->after('symbol_position');
            $table->string('suffix', 10)->nullable()->after('prefix');
            $table->string('format', 50)->default('1,234.56')->after('suffix');
            $table->decimal('exchange_rate_to_usd', 10, 6)->default(1.000000)->after('format');
            $table->boolean('is_default')->default(false)->after('exchange_rate_to_usd');
        });
    }

    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn(['symbol', 'symbol_position', 'prefix', 'suffix', 'format', 'exchange_rate_to_usd', 'is_default']);
        });
    }
};
