<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('season_passes');
        Schema::create('season_passes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tenant_member_id')->constrained('tenant_members')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->string('name'); // e.g. Season Pass Liga 1 2026/2027
            $table->string('season_name'); // e.g. 2026/2027
            $table->enum('pass_type', ['full_season', 'half_season_1', 'half_season_2', 'custom_bundle'])->default('full_season');
            $table->foreignId('ticket_category_id')->nullable()->constrained('ticket_categories')->nullOnDelete(); // Default category/tribun
            $table->string('seat_number')->nullable();
            $table->string('pass_code')->unique(); // e.g. SP-2026-00081
            $table->decimal('price_paid', 14, 2)->default(0);
            $table->integer('total_matches')->default(17);
            $table->integer('claimed_matches')->default(0);
            $table->integer('claim_window_days_before')->default(5); // H-X hari link klaim tiket dibuka
            $table->enum('status', ['active', 'expired', 'suspended', 'cancelled'])->default('active');
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'user_id']);
            $table->index('pass_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season_passes');
    }
};
