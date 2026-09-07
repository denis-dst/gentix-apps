<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        $globalNotifications = [
            'email_notifications_enabled' => Setting::where('key', 'global_email_notifications_enabled')->value('value') ?? true,
            'wa_notifications_enabled' => Setting::where('key', 'global_wa_notifications_enabled')->value('value') ?? true,
        ];
        $tenantRegistrationEnabled = (bool) (Setting::where('key', 'tenant_registration_enabled')->value('value') ?? true);

        return view('superadmin.settings.index', compact('settings', 'globalNotifications', 'tenantRegistrationEnabled'));
    }

    public function update(Request $request)
    {
        // Handle global notification settings
        Setting::updateOrCreate(
            ['key' => 'global_email_notifications_enabled'],
            ['value' => $request->boolean('global_email_notifications_enabled') ? '1' : '0', 'group' => 'notifications']
        );
        Setting::updateOrCreate(
            ['key' => 'global_wa_notifications_enabled'],
            ['value' => $request->boolean('global_wa_notifications_enabled') ? '1' : '0', 'group' => 'notifications']
        );

        // Handle tenant self-registration toggle
        Setting::updateOrCreate(
            ['key' => 'tenant_registration_enabled'],
            ['value' => $request->boolean('tenant_registration_enabled') ? '1' : '0', 'group' => 'features']
        );
        
        $fileKeys = ['app_logo', 'app_favicon', 'app_icon', 'wristband_league_logo'];

        foreach ($fileKeys as $key) {
            if (!$request->hasFile($key)) {
                continue;
            }

            $path = $request->file($key)->store('settings', 'public');
            Setting::updateOrCreate(['key' => $key], [
                'value' => $path,
                'group' => Setting::where('key', $key)->value('group') ?? 'appearance',
            ]);
        }

        $excludedKeys = [
            '_token',
            'global_email_notifications_enabled',
            'global_wa_notifications_enabled',
            'tenant_registration_enabled',
        ];

        foreach ($request->except($excludedKeys) as $key => $value) {
            if (!in_array($key, $fileKeys)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group' => Setting::where('key', $key)->value('group') ?? 'appearance']
                );
            }
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
