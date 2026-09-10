<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('tenant_members');
        Schema::create('tenant_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('membership_tier_id')->nullable()->constrained('membership_tiers')->nullOnDelete();
            $table->string('member_number')->unique(); // e.g. GNTX-PRSKT-000123
            $table->string('full_name_ktp')->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('phone', 25)->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('address')->nullable();
            $table->string('avatar')->nullable();
            $table->string('ktp_photo')->nullable();
            $table->string('face_photo')->nullable();
            $table->enum('kyc_status', ['unverified', 'pending', 'verified', 'rejected'])->default('unverified');
            $table->text('kyc_reject_reason')->nullable();
            $table->timestamp('kyc_verified_at')->nullable();
            $table->foreignId('kyc_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('points_balance')->default(0);
            $table->unsignedBigInteger('korwil_id')->nullable(); // Relasi ke korwil nanti
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('expired_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'expired'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'user_id']);
            $table->index(['tenant_id', 'nik']);
            $table->index(['tenant_id', 'kyc_status']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_members');
    }
};
