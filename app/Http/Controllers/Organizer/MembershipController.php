<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use App\Models\TenantMember;
use App\Models\FanPointsLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MembershipController extends Controller
{
    private function getTenantId()
    {
        return Auth::user()->tenant_id;
    }

    // --- MEMBERSHIP TIERS MANAGEMENT ---

    public function tiersIndex()
    {
        $tenantId = $this->getTenantId();
        $tiers = MembershipTier::where('tenant_id', $tenantId)->withCount('members')->latest()->get();

        return view('organizer.membership.tiers.index', compact('tiers'));
    }

    public function tiersCreate()
    {
        return view('organizer.membership.tiers.form');
    }

    public function tiersStore(Request $request)
    {
        $tenantId = $this->getTenantId();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'validity_days' => 'required|integer|min:1',
            'early_access_hours' => 'nullable|integer|min:0',
            'ticket_discount_percent' => 'nullable|numeric|min:0|max:100',
            'badge_color' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'benefits' => 'nullable|array',
            'benefits.*' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $benefits = array_values(array_filter($request->input('benefits', [])));

        MembershipTier::create([
            'tenant_id' => $tenantId,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(4),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'validity_days' => $validated['validity_days'],
            'early_access_hours' => $validated['early_access_hours'] ?? 0,
            'ticket_discount_percent' => $validated['ticket_discount_percent'] ?? 0,
            'benefits' => $benefits,
            'badge_color' => $validated['badge_color'] ?? '#f97316',
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('organizer.membership.tiers.index')->with('success', 'Tier Membership berhasil ditambahkan!');
    }

    public function tiersEdit(MembershipTier $tier)
    {
        if ($tier->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        return view('organizer.membership.tiers.form', compact('tier'));
    }

    public function tiersUpdate(Request $request, MembershipTier $tier)
    {
        if ($tier->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'validity_days' => 'required|integer|min:1',
            'early_access_hours' => 'nullable|integer|min:0',
            'ticket_discount_percent' => 'nullable|numeric|min:0|max:100',
            'badge_color' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'benefits' => 'nullable|array',
            'benefits.*' => 'nullable|string',
        ]);

        $benefits = array_values(array_filter($request->input('benefits', [])));

        $tier->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'validity_days' => $validated['validity_days'],
            'early_access_hours' => $validated['early_access_hours'] ?? 0,
            'ticket_discount_percent' => $validated['ticket_discount_percent'] ?? 0,
            'benefits' => $benefits,
            'badge_color' => $validated['badge_color'] ?? '#f97316',
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('organizer.membership.tiers.index')->with('success', 'Tier Membership berhasil diperbarui!');
    }

    public function tiersDestroy(MembershipTier $tier)
    {
        if ($tier->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $tier->delete();
        return redirect()->route('organizer.membership.tiers.index')->with('success', 'Tier Membership berhasil dihapus.');
    }

    // --- FAN CRM & KYC VERIFICATION ---

    public function membersIndex(Request $request)
    {
        $tenantId = $this->getTenantId();

        $query = TenantMember::where('tenant_id', $tenantId)
            ->with(['user', 'tier', 'korwil'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kyc_status')) {
            $query->where('kyc_status', $request->kyc_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('member_number', 'like', "%{$search}%")
                  ->orWhere('full_name_ktp', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $members = $query->paginate(20)->withQueryString();
        $counts = [
            'total' => TenantMember::where('tenant_id', $tenantId)->count(),
            'verified' => TenantMember::where('tenant_id', $tenantId)->where('kyc_status', 'verified')->count(),
            'pending' => TenantMember::where('tenant_id', $tenantId)->where('kyc_status', 'pending')->count(),
            'rejected' => TenantMember::where('tenant_id', $tenantId)->where('kyc_status', 'rejected')->count(),
        ];

        return view('organizer.membership.members.index', compact('members', 'counts'));
    }

    public function membersShow(TenantMember $member)
    {
        if ($member->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $member->load(['user', 'tier', 'korwil', 'seasonPasses', 'pointsLedger' => fn($q) => $q->latest()->take(10)]);

        return view('organizer.membership.members.show', compact('member'));
    }

    public function verifyKyc(Request $request, TenantMember $member)
    {
        if ($member->tenant_id !== $this->getTenantId()) {
            abort(403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'reject_reason' => 'nullable|string|max:500',
        ]);

        if ($request->action === 'approve') {
            $member->update([
                'kyc_status' => 'verified',
                'kyc_verified_at' => now(),
                'kyc_verified_by' => Auth::id(),
                'kyc_reject_reason' => null,
            ]);

            // Berikan bonus poin registrasi KYC terverifikasi (100 pts)
            $member->increment('points_balance', 100);
            FanPointsLedger::create([
                'tenant_id' => $member->tenant_id,
                'tenant_member_id' => $member->id,
                'type' => 'earn',
                'source' => 'manual_bonus',
                'points' => 100,
                'balance_after' => $member->points_balance,
                'description' => 'Bonus verifikasi data KYC NIK suporter',
            ]);

            return back()->with('success', 'Status KYC Member berhasil diverifikasi dan disetujui (+100 Poin diberikan).');
        } else {
            $member->update([
                'kyc_status' => 'rejected',
                'kyc_reject_reason' => $request->reject_reason ?: 'Foto KTP atau NIK tidak terbaca dengan jelas.',
            ]);

            return back()->with('success', 'Status KYC Member telah ditolak dengan catatan.');
        }
    }
}
