<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\GateLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperadminController extends Controller
{
    /**
     * Manajemen Multi-Tenant
     */
    public function listTenants()
    {
        return response()->json(Tenant::withCount('events')->get());
    }

    public function createTenant(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:tenants,slug',
            'email' => 'required|email|unique:tenants,email',
        ]);

        $tenant = Tenant::create($validated);
        return response()->json(['message' => 'Tenant created', 'data' => $tenant], 201);
    }

    public function updateTenantStatus(Request $request, Tenant $tenant)
    {
        $request->validate(['status' => 'required|in:active,suspended,deleted']);
        $tenant->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated']);
    }

    /**
     * Monitoring Infrastruktur (Health-check)
     */
    public function getInfrasHealth()
    {
        $tenants = Tenant::select('id', 'name', 'synced_at')
            ->orderBy('synced_at', 'desc')
            ->get();
        
        return response()->json([
            'server_time' => now(),
            'tenants_sync_status' => $tenants
        ]);
    }

    /**
     * Manajemen Resolusi Konflik (Override Log)
     */
    public function overrideTransaction(Request $request, Transaction $transaction)
    {
        // Superadmin bypass logic for error connections
        $transaction->update([
            'payment_status' => $request->status,
            'meta' => array_merge($transaction->meta ?? [], [
                'override_by' => auth()->id(),
                'override_at' => now(),
                'reason' => $request->reason
            ])
        ]);

        return response()->json(['message' => 'Transaction overridden successfully']);
    }
}
