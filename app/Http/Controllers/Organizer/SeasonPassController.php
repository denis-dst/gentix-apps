<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\SeasonPass;
use App\Models\SeasonPassClaim;
use App\Models\TicketCategory;
use App\Models\Event;
use App\Models\TenantMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SeasonPassController extends Controller
{
    private function getTenantId()
    {
        return Auth::user()->tenant_id;
    }

    public function index(Request $request)
    {
        $tenantId = $this->getTenantId();

        $query = SeasonPass::where('tenant_id', $tenantId)
            ->with([
                'user:id,name,email',
                'member:id,user_id,member_number,full_name_ktp,nik',
                'category:id,name,hex_color',
                'claims.event:id,name'
            ])
            ->latest();

        if ($request->filled('pass_type')) {
            $query->where('pass_type', $request->pass_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pass_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('nik', 'like', "%{$search}%")
                         ->orWhere('member_number', 'like', "%{$search}%");
                  });
            });
        }

        $passes = $query->paginate(20)->withQueryString();
        $categories = TicketCategory::where('tenant_id', $tenantId)->get(['id', 'name', 'event_id']);

        $passAgg = SeasonPass::where('tenant_id', $tenantId)
            ->selectRaw("
                COUNT(*) as total_passes,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_passes
            ")
            ->first();

        $totalClaims = SeasonPassClaim::where('tenant_id', $tenantId)->count();

        $stats = [
            'total_passes' => (int) ($passAgg->total_passes ?? 0),
            'active_passes' => (int) ($passAgg->active_passes ?? 0),
            'total_claims' => $totalClaims,
        ];

        return view('organizer.season-passes.index', compact('passes', 'categories', 'stats'));
    }

    public function create()
    {
        $tenantId = $this->getTenantId();
        $members = TenantMember::where('tenant_id', $tenantId)
            ->select(['id', 'user_id', 'member_number', 'full_name_ktp'])
            ->with('user:id,name,email')
            ->get();
        $categories = TicketCategory::where('tenant_id', $tenantId)->get(['id', 'name', 'event_id']);

        return view('organizer.season-passes.form', compact('members', 'categories'));
    }

    public function store(Request $request)
    {
        $tenantId = $this->getTenantId();

        $validated = $request->validate([
            'tenant_member_id' => 'required|exists:tenant_members,id',
            'name' => 'required|string|max:255',
            'season_name' => 'required|string|max:100',
            'pass_type' => 'required|in:full_season,half_season_1,half_season_2,custom_bundle',
            'ticket_category_id' => 'nullable|exists:ticket_categories,id',
            'seat_number' => 'nullable|string|max:50',
            'price_paid' => 'required|numeric|min:0',
            'total_matches' => 'required|integer|min:1',
            'claim_window_days_before' => 'required|integer|min:1|max:30',
        ]);

        $member = TenantMember::findOrFail($validated['tenant_member_id']);
        if ($member->tenant_id !== $tenantId) {
            abort(403);
        }

        $passCode = 'SP-' . strtoupper(Str::random(8));

        SeasonPass::create([
            'tenant_id' => $tenantId,
            'user_id' => $member->user_id,
            'tenant_member_id' => $member->id,
            'name' => $validated['name'],
            'season_name' => $validated['season_name'],
            'pass_type' => $validated['pass_type'],
            'ticket_category_id' => $validated['ticket_category_id'] ?? null,
            'seat_number' => $validated['seat_number'] ?? null,
            'pass_code' => $passCode,
            'price_paid' => $validated['price_paid'],
            'total_matches' => $validated['total_matches'],
            'claimed_matches' => 0,
            'claim_window_days_before' => $validated['claim_window_days_before'],
            'status' => 'active',
            'valid_until' => now()->addYear(),
        ]);

        return redirect()->route('organizer.season-passes.index')->with('success', "Season Pass {$passCode} berhasil diterbitkan.");
    }
}
