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
            $table->foreignId('ticket_category_id')->nullable()->after('event_id')->constrained('ticket_categories');
            $table->integer('quantity')->default(1)->after('ticket_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['ticket_category_id']);
            $table->dropColumn(['ticket_category_id', 'quantity']);
        });
    }
};
