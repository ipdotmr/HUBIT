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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hostname');
            $table->string('ip_address');
            $table->enum('type', ['cpanel', 'plesk', 'directadmin', 'custom'])->default('cpanel');
            $table->integer('port')->default(2087);
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->text('access_hash')->nullable();
            $table->boolean('use_ssl')->default(true);
            $table->integer('max_accounts')->default(0);
            $table->integer('active_accounts')->default(0);
            $table->string('nameserver1')->nullable();
            $table->string('nameserver2')->nullable();
            $table->string('nameserver3')->nullable();
            $table->string('nameserver4')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->enum('status', ['online', 'offline', 'maintenance'])->default('online');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
