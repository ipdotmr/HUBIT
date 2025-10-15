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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value_encrypted')->nullable();
            $table->string('type')->default('string'); // string, int, bool, json, array
            $table->string('scope')->default('global'); // global, admin, client
            $table->boolean('is_secret')->default(false);
            $table->json('meta')->nullable(); // validation rules, description, etc
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('key');
            $table->index('scope');
        });

        Schema::create('organization_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->text('value_encrypted')->nullable();
            $table->string('type')->default('string');
            $table->string('scope')->default('organization');
            $table->boolean('is_secret')->default(false);
            $table->json('meta')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->unique(['organization_id', 'key']);
            $table->index('key');
        });

        Schema::create('product_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->text('value_encrypted')->nullable();
            $table->string('type')->default('string');
            $table->boolean('is_secret')->default(false);
            $table->json('meta')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->unique(['product_id', 'key']);
            $table->index('key');
        });

        Schema::create('settings_audit', function (Blueprint $table) {
            $table->id();
            $table->string('setting_type'); // system, organization, product
            $table->unsignedBigInteger('setting_id')->nullable();
            $table->string('key');
            $table->text('old_value')->nullable(); // redacted if secret
            $table->text('new_value')->nullable(); // redacted if secret
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['setting_type', 'setting_id']);
            $table->index('changed_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_audit');
        Schema::dropIfExists('product_settings');
        Schema::dropIfExists('organization_settings');
        Schema::dropIfExists('system_settings');
    }
};
