<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('fan_points_ledger');
        Schema::create('fan_points_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('tenant_member_id')->constrained('tenant_members')->cascadeOnDelete();
            $table->enum('type', ['earn', 'redeem', 'expired', 'adjustment'])->default('earn');
            $table->enum('source', [
                'ticket_purchase', 
                'membership_upgrade', 
                'score_prediction', 
                'quiz', 
                'match_attendance', 
                'manual_bonus',
                'ticket_discount'
            ])->default('ticket_purchase');
            $table->integer('points'); // Positif jika earn, negatif jika redeem
            $table->unsignedBigInteger('balance_after')->default(0);
            $table->string('reference_type')->nullable(); // Model class e.g. App\Models\Invoice
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description');
            $table->timestamps();

            $table->index(['tenant_id', 'tenant_member_id']);
            $table->index(['tenant_id', 'source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fan_points_ledger');
    }
};
