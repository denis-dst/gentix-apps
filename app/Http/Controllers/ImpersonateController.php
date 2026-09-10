<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImpersonateController extends Controller
{
    /**
     * Start impersonating a user.
     */
    public function impersonate(Request $request, User $user)
    {
        $currentUser = Auth::user();
        $isSuperadmin = $currentUser && $currentUser->hasRole('Superadmin');
        $isImpersonating = $request->session()->has('impersonator_id');

        // Only Superadmins or currently active impersonators are permitted
        if (!$isSuperadmin && !$isImpersonating) {
            abort(403, 'Akses tidak diizinkan. Hanya Superadmin yang dapat melakukan impersonasi.');
        }

        // Determine the root admin ID
        $originalAdminId = $request->session()->get('impersonator_id', $currentUser->id);
        $originalAdmin = User::find($originalAdminId);

        if (!$originalAdmin || !$originalAdmin->hasRole('Superadmin')) {
            abort(403, 'Sesi administrator tidak valid.');
        }

        // Disallow impersonating any Superadmin account
        if ($user->hasRole('Superadmin')) {
            return back()->with('error', 'Tidak dapat melakukan impersonasi pada akun Superadmin.');
        }

        // Disallow impersonating oneself
        if ($user->id === $currentUser->id) {
            return back()->with('error', 'Anda saat ini sudah login dengan akun ini.');
        }

        // Log the impersonation action
        Log::info('Superadmin started impersonating user', [
            'admin_id' => $originalAdminId,
            'admin_name' => $originalAdmin->name,
            'target_user_id' => $user->id,
            'target_user_name' => $user->name,
            'target_role' => $user->getRoleNames()->first(),
            'target_tenant_id' => $user->tenant_id,
        ]);

        // Login as the target user
        Auth::login($user);

        // Regenerate session to prevent fixation while preserving impersonation keys
        $request->session()->regenerate();
        $request->session()->put('impersonator_id', $originalAdminId);
        $request->session()->put('impersonated_user_id', $user->id);

        $roleName = $user->getRoleNames()->first() ?? 'Pengguna';
        return redirect()->route('dashboard')->with('success', "Berhasil masuk sebagai {$user->name} ({$roleName}).");
    }

    /**
     * Leave impersonation and restore original Superadmin session.
     */
    public function leave(Request $request)
    {
        $originalAdminId = $request->session()->get('impersonator_id');

        if (!$originalAdminId) {
            return redirect()->route('dashboard');
        }

        $adminUser = User::find($originalAdminId);

        if (!$adminUser || !$adminUser->hasRole('Superadmin')) {
            $request->session()->forget(['impersonator_id', 'impersonated_user_id']);
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Sesi Superadmin tidak valid atau telah berakhir.']);
        }

        Log::info('Superadmin left impersonation', [
            'admin_id' => $adminUser->id,
            'previous_user_id' => Auth::id(),
        ]);

        Auth::login($adminUser);
        $request->session()->forget(['impersonator_id', 'impersonated_user_id']);
        $request->session()->regenerate();

        return redirect()->route('superadmin.dashboard')->with('success', 'Kembali ke sesi Superadmin.');
    }
}
