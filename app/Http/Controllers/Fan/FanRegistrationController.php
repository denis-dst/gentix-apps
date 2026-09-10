<?php

namespace App\Http\Controllers\Fan;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantMember;
use App\Models\MembershipTier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class FanRegistrationController extends Controller
{
    /**
     * Display public fan registration page for a specific club.
     */
    public function show(string $slug)
    {
        // Find tenant by exact slug or prefix matching
        $tenant = Tenant::where('slug', $slug)
            ->orWhere('slug', 'like', $slug . '-%')
            ->first();

        if (!$tenant) {
            // Try matching by name
            $tenant = Tenant::where('name', 'like', '%' . $slug . '%')->first();
        }

        if (!$tenant) {
            abort(404, 'Klub sepakbola / Tenant tidak ditemukan.');
        }

        $tiers = MembershipTier::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->get();

        return view('fan.register', compact('tenant', 'tiers'));
    }

    /**
     * Handle public fan registration submission.
     */
    public function store(Request $request, string $slug)
    {
        $tenant = Tenant::where('slug', $slug)
            ->orWhere('slug', 'like', $slug . '-%')
            ->orWhere('name', 'like', '%' . $slug . '%')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'membership_tier_id' => ['nullable', 'exists:membership_tiers,id'],
        ]);

        // Normalize phone to Indonesian format
        $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // 1. Create User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $phone,
            'password' => Hash::make($validated['password']),
            'tenant_id' => $tenant->id,
            'is_active' => true,
        ]);

        // 2. Generate unique member number e.g. FAN-PERSIPURA-0001
        $initials = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $tenant->name), 0, 4));
        $memberNumber = 'FAN-' . $initials . '-' . rand(10000, 99999);

        // 3. Create Tenant Member Profile
        TenantMember::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'membership_tier_id' => $validated['membership_tier_id'] ?? null,
            'member_number' => $memberNumber,
            'full_name_ktp' => $validated['name'],
            'nik' => $validated['nik'],
            'phone' => $phone,
            'kyc_status' => 'unverified',
            'points_balance' => 0,
            'joined_at' => now(),
            'status' => 'active',
        ]);

        // 4. Log the user in
        Auth::login($user);

        return redirect()->route('fan.dashboard')->with('success', "Selamat datang di Komunitas Resmi {$tenant->name}! Silakan lengkapi verifikasi KYC Anda untuk klaim tiket & diskon.");
    }
}
