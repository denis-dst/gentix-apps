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
        Schema::table('transactions', function (Blueprint $table) {
            // Index for filtering event transactions by payment status and date
            $table->index(['event_id', 'payment_status', 'created_at'], 'idx_tx_event_status_created');
            $table->index(['tenant_id', 'created_at'], 'idx_tx_tenant_created');
        });

        Schema::table('tickets', function (Blueprint $table) {
            // Index for ticket status aggregations and lookups per event
            $table->index(['event_id', 'status'], 'idx_tickets_event_status');
            $table->index(['transaction_id', 'status'], 'idx_tickets_tx_status');
            $table->index(['event_id', 'ticket_category_id', 'status'], 'idx_tickets_event_cat_status');
        });

        Schema::table('events', function (Blueprint $table) {
            // Index for organizer event listings
            $table->index(['tenant_id', 'status', 'event_start_date'], 'idx_events_tenant_status_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_tx_event_status_created');
            $table->dropIndex('idx_tx_tenant_created');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('idx_tickets_event_status');
            $table->dropIndex('idx_tickets_tx_status');
            $table->dropIndex('idx_tickets_event_cat_status');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('idx_events_tenant_status_date');
        });
    }
};
