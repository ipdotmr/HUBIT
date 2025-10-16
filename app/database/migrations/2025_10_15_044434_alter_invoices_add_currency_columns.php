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
            $table->string('currency', 3)->default('MRU')->change();
            $table->decimal('subtotal', 18, 2)->default(0)->change();
            $table->decimal('tax', 18, 2)->default(0)->change();
            $table->decimal('discount', 18, 2)->default(0)->change();
            $table->decimal('total', 18, 2)->default(0)->change();
            $table->decimal('paid', 18, 2)->default(0)->change();
            $table->decimal('balance', 18, 2)->storedAs('total - paid')->after('paid');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('balance');
            $table->string('currency', 3)->default('USD')->change();
            $table->decimal('subtotal', 10, 2)->default(0)->change();
            $table->decimal('tax', 10, 2)->default(0)->change();
            $table->decimal('discount', 10, 2)->default(0)->change();
            $table->decimal('total', 10, 2)->default(0)->change();
            $table->decimal('paid', 10, 2)->default(0)->change();
        });
    }
};
