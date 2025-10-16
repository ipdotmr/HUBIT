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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['domain', 'hosting', 'addon']);
            $table->string('sku')->nullable();
            $table->string('fqdn')->nullable();
            $table->integer('years')->nullable();
            $table->json('config')->nullable();
            $table->decimal('price', 18, 2);
            $table->string('currency', 3)->default('MRU');
            $table->timestamps();

            $table->index('cart_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
