<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantSettingsController extends Controller
{
    public function editTerms()
    {
        $tenant = Tenant::findOrFail(auth()->user()->tenant_id);
        return view('organizer.settings.terms', compact('tenant'));
    }

    public function updateTerms(Request $request)
    {
        $request->validate([
            'terms_conditions' => 'nullable|string',
            'email_notifications_enabled' => 'nullable|boolean',
            'wa_notifications_enabled' => 'nullable|boolean',
        ]);

        $tenant = Tenant::findOrFail(auth()->user()->tenant_id);
        
        $meta = $tenant->meta ?? [];
        $meta['email_notifications_enabled'] = $request->boolean('email_notifications_enabled');
        $meta['wa_notifications_enabled'] = $request->boolean('wa_notifications_enabled');

        $tenant->update([
            'terms_conditions' => $request->terms_conditions,
            'meta' => $meta,
        ]);

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
