<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('season_name')->nullable()->after('city'); // e.g. Liga 1 2026/2027
            $table->string('home_team_name')->nullable()->after('season_name');
            $table->string('away_team_name')->nullable()->after('home_team_name');
            $table->string('home_team_logo')->nullable()->after('away_team_name');
            $table->string('away_team_logo')->nullable()->after('home_team_logo');
            $table->integer('home_score')->nullable()->after('away_team_logo');
            $table->integer('away_score')->nullable()->after('home_score');
            $table->boolean('allow_season_pass')->default(false)->after('away_score'); // Apakah match ini masuk paket Season Pass
            $table->integer('season_pass_claim_days_before')->default(5)->after('allow_season_pass'); // H-X hari buka klaim
            $table->boolean('is_member_priority_sale')->default(false)->after('season_pass_claim_days_before');
            $table->dateTime('member_priority_start_at')->nullable()->after('is_member_priority_sale');
            $table->dateTime('general_sale_start_at')->nullable()->after('member_priority_start_at');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'season_name',
                'home_team_name',
                'away_team_name',
                'home_team_logo',
                'away_team_logo',
                'home_score',
                'away_score',
                'allow_season_pass',
                'season_pass_claim_days_before',
                'is_member_priority_sale',
                'member_priority_start_at',
                'general_sale_start_at'
            ]);
        });
    }
};
