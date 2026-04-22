<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('ticket_category_id')->constrained('ticket_categories')->cascadeOnDelete();
            $table->string('ticket_code')->unique(); // Unique QR code for E-Voucher
            $table->string('wristband_qr')->nullable()->unique(); // Linked physical wristband/RFID
            $table->enum('status', ['available', 'reserved', 'sold', 'redeemed', 'void'])->default('available');
            $table->timestamp('redeemed_at')->nullable();
            $table->foreignId('redeemed_by')->nullable()->constrained('users');
            $table->json('visitor_data')->nullable(); // Specific visitor info if different from purchaser
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index('ticket_code');
            $table->index('wristband_qr');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
