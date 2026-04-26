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
        ]);

        $tenant = Tenant::findOrFail(auth()->user()->tenant_id);
        $tenant->update([
            'terms_conditions' => $request->terms_conditions,
        ]);

        return redirect()->back()->with('success', 'Syarat & Ketentuan berhasil diperbarui.');
    }
}
