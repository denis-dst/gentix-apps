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
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->string('badge_text')->nullable()->after('nik_restriction_message')->comment('Dynamic badge text shown on ticket card');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->text('terms_conditions')->nullable()->after('description')->comment('General terms and conditions for the event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->dropColumn('badge_text');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('terms_conditions');
        });
    }
};
