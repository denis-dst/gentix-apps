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
        Schema::create('wristband_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->cascadeOnDelete();
            $table->foreignId('ticket_category_id')->nullable()->constrained('ticket_categories')->cascadeOnDelete();
            $table->string('name')->default('Wristband Template');
            $table->string('mode')->default('default'); // 'default' or 'custom'
            $table->string('background_image')->nullable();
            $table->json('columns_config')->nullable();
            $table->json('layout_settings')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'event_id']);
            $table->index('ticket_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wristband_templates');
    }
};
