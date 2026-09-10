<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('korwil_member_consents');
        Schema::dropIfExists('korwil_allocations');
        Schema::dropIfExists('korwils');

        // Komunitas / Koordinator Wilayah Suporter
        Schema::create('korwils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('coordinator_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name'); // e.g. Korwil Jakarta Barat, Viking Bekasi, Bonek Surabaya Timur
            $table->string('code')->nullable();
            $table->string('region')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_verified']);
        });

        // Alokasi Kuota Matchday untuk Korwil
        Schema::create('korwil_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('korwil_id')->constrained('korwils')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('ticket_category_id')->constrained('ticket_categories')->cascadeOnDelete();
            $table->integer('allocated_quota')->default(0);
            $table->integer('used_quota')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('approved');
            $table->timestamps();

            $table->unique(['korwil_id', 'event_id', 'ticket_category_id']);
        });

        // Persetujuan NIK Anggota Rombongan Korwil (Consent Management)
        Schema::create('korwil_member_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('korwil_allocation_id')->constrained('korwil_allocations')->cascadeOnDelete();
            $table->foreignId('tenant_member_id')->nullable()->constrained('tenant_members')->nullOnDelete();
            $table->string('nik', 20);
            $table->string('full_name');
            $table->string('phone', 25)->nullable();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->enum('consent_status', ['pending', 'approved_by_member', 'rejected_by_member', 'cancelled'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['korwil_allocation_id', 'nik']);
            $table->index(['tenant_member_id', 'consent_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('korwil_member_consents');
        Schema::dropIfExists('korwil_allocations');
        Schema::dropIfExists('korwils');
    }
};
