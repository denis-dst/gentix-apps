<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::updateOrCreate(
            ['key' => 'wristband_league_logo'],
            ['value' => null, 'group' => 'appearance']
        );
    }

    public function down(): void
    {
        Setting::where('key', 'wristband_league_logo')->delete();
    }
};
