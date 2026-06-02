<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('customer_gender', ['ikhwan', 'akhwat'])->nullable()->after('customer_nik');
            $table->string('customer_umroh_answer')->nullable()->after('customer_gender');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['customer_gender', 'customer_umroh_answer']);
        });
    }
};
