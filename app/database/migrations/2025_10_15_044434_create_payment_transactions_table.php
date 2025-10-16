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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_account_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('method', ['bank_transfer', 'cash', 'stripe', 'paypal']);
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected']);
            $table->string('currency', 3);
            $table->decimal('amount', 18, 2);
            $table->decimal('fx_rate', 18, 8)->nullable();
            $table->decimal('amount_home', 18, 2)->nullable();
            $table->json('evidence')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
