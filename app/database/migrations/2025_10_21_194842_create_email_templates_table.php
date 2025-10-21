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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Template identifier (e.g., 'welcome_email', 'order_confirmation')
            $table->string('type'); // 'incoming' or 'outgoing'
            $table->string('category')->nullable(); // e.g., 'client', 'order', 'invoice', 'support'
            $table->text('description')->nullable();
            
            $table->string('subject_en');
            $table->text('body_en');
            
            $table->string('subject_ar')->nullable();
            $table->text('body_ar')->nullable();
            
            $table->string('subject_fr')->nullable();
            $table->text('body_fr')->nullable();
            
            $table->json('variables')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
