<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of system users (Tenants, Gate Officers, Redeem Officers).
     */
    public function index(Request $request)
    {
        $role = $request->query('role');
        $search = $request->query('search');
        $tenantId = $request->query('tenant_id');

        $query = User::with(['tenant', 'roles'])
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Superadmin');
            });

        if ($role) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if ($tenantId === 'unassigned') {
            $query->whereNull('tenant_id');
        } elseif ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Counts for tab badges
        $counts = [
            'all' => User::whereDoesntHave('roles', fn($q) => $q->where('name', 'Superadmin'))->count(),
            'tenant' => User::role('Penyedia Event')->count(),
            'gate' => User::role('Petugas Gate')->count(),
            'redeem' => User::role('Petugas Loket')->count(),
            'unassigned' => User::whereDoesntHave('roles', fn($q) => $q->where('name', 'Superadmin'))
                ->whereNull('tenant_id')
                ->count(),
        ];

        $tenants = Tenant::orderBy('name')->get();

        return view('superadmin.users.index', compact('users', 'counts', 'tenants', 'role', 'search', 'tenantId'));
    }

    /**
     * Assign or reassign a staff member to a tenant (or unassign).
     */
    public function assignTenant(Request $request, User $user)
    {
        if ($user->hasRole('Superadmin')) {
            return back()->with('error', 'Tidak dapat mengubah tenant akun Superadmin.');
        }

        $validated = $request->validate([
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $user->update([
            'tenant_id' => $validated['tenant_id'] ?: null,
        ]);

        if ($validated['tenant_id']) {
            $tenant = Tenant::find($validated['tenant_id']);
            $roleName = $user->getRoleNames()->first() ?? 'Petugas';
            return back()->with('success', "Berhasil! {$user->name} ({$roleName}) kini resmi diklaim dan bertugas di tenant: {$tenant->name}.");
        }

        return back()->with('success', "Penugasan tenant untuk {$user->name} berhasil dilepas (status belum diklaim).");
    }

    /**
     * Batch assign multiple staff members to a tenant.
     */
    public function batchAssignTenant(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'Superadmin'))
            ->get();

        if ($users->isEmpty()) {
            return back()->with('error', 'Tidak ada pengguna valid yang dipilih.');
        }

        $count = 0;
        foreach ($users as $user) {
            $user->update([
                'tenant_id' => $validated['tenant_id'] ?: null,
            ]);
            $count++;
        }

        if ($validated['tenant_id']) {
            $tenant = Tenant::find($validated['tenant_id']);
            return back()->with('success', "Berhasil! {$count} petugas berhasil diklaim dan ditugaskan ke tenant {$tenant->name}.");
        }

        return back()->with('success', "Berhasil melepas penugasan tenant untuk {$count} petugas.");
    }

    /**
     * Store a newly created officer/staff account directly from Superadmin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:6',
            'role' => 'required|in:Petugas Loket,Petugas Gate,Penyedia Event',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'tenant_id' => $validated['tenant_id'] ?: null,
            'is_active' => true,
        ]);

        Role::firstOrCreate(['name' => $validated['role']]);
        $user->assignRole($validated['role']);

        $tenantName = $validated['tenant_id'] ? Tenant::find($validated['tenant_id'])->name : 'Belum Ditugaskan';
        return back()->with('success', "Petugas {$user->name} ({$validated['role']}) berhasil didaftarkan untuk bertugas di {$tenantName}.");
    }
}
