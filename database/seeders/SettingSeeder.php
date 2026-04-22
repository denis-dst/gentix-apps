<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'app_name', 'value' => 'GenTix', 'group' => 'general'],
            ['key' => 'app_tagline', 'value' => 'Connecting Generations Through Every Gate', 'group' => 'general'],
            ['key' => 'meta_description', 'value' => 'GenTix is the ultimate destination for discovery and access to the world\'s most exciting live events.', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'support@gentix.test', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '+62 812 3456 7890', 'group' => 'general'],
            ['key' => 'address', 'value' => 'Jl. Sudirman No. 123, Jakarta, Indonesia', 'group' => 'general'],
            
            // Social Media
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/gentix', 'group' => 'social'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com/gentix', 'group' => 'social'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/gentix', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/gentix', 'group' => 'social'],
            
            // Appearance
            ['key' => 'hero_title', 'value' => 'Connecting Generations Through Every Gate.', 'group' => 'appearance'],
            ['key' => 'hero_subtitle', 'value' => 'Bridging the gap between Generation and Tickets. Experience high-tech event management that\'s simple enough for everyone.', 'group' => 'appearance'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
