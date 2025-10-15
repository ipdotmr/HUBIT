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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('currency', 3)->default('MRU')->after('status');
            $table->decimal('total', 18, 2)->after('currency')->change();
            $table->decimal('paid', 18, 2)->default(0)->after('total');
            $table->decimal('balance', 18, 2)->storedAs('total - paid')->after('paid');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['currency', 'paid', 'balance']);
        });
    }
};
