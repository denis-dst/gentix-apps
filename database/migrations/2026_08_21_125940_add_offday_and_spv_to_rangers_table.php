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
        Schema::table('rangers', function (Blueprint $table) {
            $table->boolean('is_offday')->default(false)->after('gender');
            $table->boolean('is_spv')->default(false)->after('is_offday');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rangers', function (Blueprint $table) {
            $table->dropColumn(['is_offday', 'is_spv']);
        });
    }
};
