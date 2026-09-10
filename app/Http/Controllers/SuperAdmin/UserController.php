<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;

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

        if ($tenantId) {
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
        ];

        $tenants = Tenant::orderBy('name')->get();

        return view('superadmin.users.index', compact('users', 'counts', 'tenants', 'role', 'search', 'tenantId'));
    }
}
