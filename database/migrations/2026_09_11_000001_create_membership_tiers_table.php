<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name'); // e.g. Free Supporter, Silver, Gold, Platinum
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->integer('validity_days')->default(365);
            $table->integer('early_access_hours')->default(0); // Jam lebih awal akses tiket sebelum general sale
            $table->decimal('ticket_discount_percent', 5, 2)->default(0);
            $table->json('benefits')->nullable(); // List of perks/benefits
            $table->string('badge_color')->nullable(); // Hex color e.g. #FFD700
            $table->string('card_template_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};
