<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gate_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('gate_name')->nullable();
            $table->enum('type', ['IN', 'OUT']);
            $table->timestamp('scanned_at');
            $table->string('device_id')->nullable();
            $table->foreignId('scanned_by')->nullable()->constrained('users');
            $table->json('meta')->nullable(); // For sync tracking
            $table->timestamps();

            $table->index(['ticket_id', 'type', 'scanned_at']);
            $table->index(['event_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gate_logs');
    }
};
