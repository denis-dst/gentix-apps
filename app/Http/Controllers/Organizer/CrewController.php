<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CrewController extends Controller
{
    public function index()
    {
        $crews = User::where('tenant_id', auth()->user()->tenant_id)
            ->where(function($q) {
                $q->whereHas('roles', function($rq) {
                    $rq->whereIn('name', ['Petugas Loket', 'Petugas Gate', 'loket', 'gate']);
                });
            })
            ->paginate(10);
        return view('organizer.crews.index', compact('crews'));
    }

    public function create()
    {
        return view('organizer.crews.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:loket,gate',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        // Map role name
        $roleName = $validated['role'] === 'loket' ? 'Petugas Loket' : 'Petugas Gate';
        
        // Ensure role exists
        Role::firstOrCreate(['name' => $roleName]);
        
        $user->assignRole($roleName);

        return redirect()->route('organizer.crews.index')->with('success', 'Crew member added successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $user->delete();
        return redirect()->route('organizer.crews.index')->with('success', 'Crew member deleted.');
    }
}
