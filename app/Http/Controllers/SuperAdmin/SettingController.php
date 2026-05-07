<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('superadmin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
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

        foreach ($request->except('_token') as $key => $value) {
            if (!in_array($key, $fileKeys)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => Setting::where('key', $key)->value('group') ?? 'appearance']);
            }
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}
