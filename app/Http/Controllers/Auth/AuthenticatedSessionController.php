<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        $roles = $user->roles()->pluck('name')->all();

        if ($user->hasRole('Superadmin')) {
            return redirect()->route('superadmin.dashboard');
        }
        
        if ($user->hasRole('Penyedia Event')) {
            return redirect()->route('organizer.dashboard');
        }

        if ($user->hasRole('Petugas Loket')) {
            return redirect()->route('organizer.redeem.index');
        }

        if ($user->hasRole('Petugas Gate')) {
            return redirect()->route('organizer.gate.index');
        }

        Log::warning('Login authenticated without matching dashboard role', [
            'user_id' => $user->id,
            'roles' => $roles,
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
