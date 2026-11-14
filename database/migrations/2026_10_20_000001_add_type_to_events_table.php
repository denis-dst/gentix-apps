<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'type')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('type', 50)->default('concert')->after('city');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'type')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
