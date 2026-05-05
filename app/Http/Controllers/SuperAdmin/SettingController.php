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
        $fileKeys = ['app_logo', 'app_favicon', 'app_icon'];

        foreach ($request->except('_token') as $key => $value) {
            if (in_array($key, $fileKeys) && $request->hasFile($key)) {
                $path = $request->file($key)->store('settings', 'public');
                Setting::where('key', $key)->update(['value' => $path]);
            } else if (!in_array($key, $fileKeys)) {
                Setting::where('key', $key)->update(['value' => $value]);
            }
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}
