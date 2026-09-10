<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('season_pass_claims');
        Schema::create('season_pass_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('season_pass_id')->constrained('season_passes')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete(); // Tiket matchday yang diterbitkan
            $table->timestamp('claimed_at')->useCurrent();
            $table->string('claim_ip')->nullable();
            $table->enum('status', ['claimed', 'cancelled'])->default('claimed');
            $table->timestamps();

            $table->unique(['season_pass_id', 'event_id']); // 1 season pass hanya bisa klaim 1 tiket per match event
            $table->index(['tenant_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season_pass_claims');
    }
};
