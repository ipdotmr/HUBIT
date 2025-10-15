<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('next_due_at')->nullable();
            $table->string('billing_cycle');
            $table->decimal('recurring_amount', 10, 2)->default(0);
            $table->string('provisioner')->nullable();
            $table->json('provision_ref')->nullable();
            $table->text('credentials')->nullable();
            $table->json('config')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_id', 'status']);
            $table->index('status');
            $table->index('next_due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
