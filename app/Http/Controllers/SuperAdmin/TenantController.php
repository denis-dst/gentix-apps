<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tenant;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount('events')->paginate(10);
        return view('superadmin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.tenants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tenants,email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        Tenant::create($validated);

        return redirect()->route('superadmin.tenants.index')->with('success', 'Organizer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        return view('superadmin.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant)
    {
        return view('superadmin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tenants,email,' . $tenant->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($tenant->name !== $validated['name']) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        $tenant->update($validated);

        return redirect()->route('superadmin.tenants.index')->with('success', 'Organizer updated successfully.');
    }

    /**
     * Display a listing of the trashed resources.
     */
    public function trash()
    {
        $tenants = Tenant::onlyTrashed()->paginate(10);
        return view('superadmin.tenants.trash', compact('tenants'));
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $tenant = Tenant::withTrashed()->findOrFail($id);
        $tenant->update(['status' => 'inactive']); // Reset to inactive on restore
        $tenant->restore();

        return redirect()->route('superadmin.tenants.trash')->with('success', 'Organizer restored successfully.');
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        $tenant = Tenant::withTrashed()->findOrFail($id);
        $tenant->forceDelete();

        return redirect()->route('superadmin.tenants.trash')->with('success', 'Organizer permanently deleted.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->update(['status' => 'deleted']);
        $tenant->delete();
        return redirect()->route('superadmin.tenants.index')->with('success', 'Organizer moved to trash.');
    }
}
