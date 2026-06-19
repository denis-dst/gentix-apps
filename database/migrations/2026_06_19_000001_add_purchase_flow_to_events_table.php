<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('purchase_flow')->default('redeem')->after('evoucher_info');
            $table->unsignedSmallInteger('thermal_paper_width_mm')->default(80)->after('purchase_flow');
            $table->unsignedSmallInteger('thermal_paper_height_mm')->default(160)->after('thermal_paper_width_mm');
        });

        DB::table('events')
            ->where('is_free', true)
            ->update(['purchase_flow' => 'evoucher']);
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_flow',
                'thermal_paper_width_mm',
                'thermal_paper_height_mm',
            ]);
        });
    }
};
