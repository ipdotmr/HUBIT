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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('currency', 3);
            $table->decimal('balance', 18, 2)->default(0);
            $table->timestamps();

            $table->unique(['client_id', 'currency']);
            $table->index('client_id');
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['credit', 'debit', 'adjustment', 'refund']);
            $table->decimal('amount', 18, 2);
            $table->string('currency', 3);
            $table->string('reference')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
