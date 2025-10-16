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
        if (!Schema::hasTable('domains')) {
            Schema::create('domains', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained()->cascadeOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->string('domain');
                $table->string('registrar')->nullable();
                $table->string('status')->default('pending');
                $table->date('registered_at')->nullable();
                $table->date('expires_at')->nullable();
                $table->boolean('auto_renew')->default(true);
                $table->boolean('privacy_enabled')->default(false);
                $table->boolean('lock_enabled')->default(true);
                $table->json('nameservers')->nullable();
                $table->text('epp_code')->nullable();
                $table->json('whois_data')->nullable();
                $table->json('registrar_meta')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                $table->index(['client_id', 'status']);
                $table->unique('domain');
                $table->index('expires_at');
            });
        }

        Schema::create('domain_dns_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained('domains')->cascadeOnDelete();
            $table->string('type'); // A, AAAA, CNAME, MX, TXT, CAA
            $table->string('host');
            $table->text('value');
            $table->integer('priority')->nullable(); // For MX, SRV
            $table->integer('ttl')->default(3600);
            $table->json('meta')->nullable();
            $table->timestamps();
            
            $table->index(['domain_id', 'type', 'host']);
        });

        Schema::create('domain_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained('domains')->cascadeOnDelete();
            $table->integer('years');
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending'); // pending, paid, completed, failed
            $table->date('old_expiry')->nullable();
            $table->date('new_expiry')->nullable();
            $table->timestamps();
        });

        Schema::create('service_upgrade_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('from_plan')->nullable();
            $table->string('to_plan');
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending'); // pending, paid, completed, failed
            $table->json('meta')->nullable();
            $table->timestamps();
            
            $table->index(['service_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_upgrade_events');
        Schema::dropIfExists('domain_renewals');
        Schema::dropIfExists('domain_dns_records');
    }
};
