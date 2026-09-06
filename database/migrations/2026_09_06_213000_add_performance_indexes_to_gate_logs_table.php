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
        Schema::table('gate_logs', function (Blueprint $table) {
            $table->index(['ticket_id', 'scanned_at'], 'idx_gate_logs_ticket_scanned_at');
            $table->index(['event_id', 'gate_name', 'scanned_at'], 'idx_gate_logs_event_gate_scanned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gate_logs', function (Blueprint $table) {
            $table->dropIndex('idx_gate_logs_ticket_scanned_at');
            $table->dropIndex('idx_gate_logs_event_gate_scanned');
        });
    }
};
