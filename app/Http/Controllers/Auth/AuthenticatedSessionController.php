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

        Log::info('Login authenticated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $roles,
            'session_id' => $request->session()->getId(),
            'session_path' => config('session.path'),
            'session_domain' => config('session.domain'),
            'session_secure' => config('session.secure'),
        ]);

        if ($user->hasRole('Superadmin')) {
            Log::info('Login redirecting', ['user_id' => $user->id, 'route' => 'superadmin.dashboard']);
            return redirect()->route('superadmin.dashboard');
        }
        
        if ($user->hasRole('Penyedia Event')) {
            Log::info('Login redirecting', ['user_id' => $user->id, 'route' => 'organizer.dashboard']);
            return redirect()->route('organizer.dashboard');
        }

        if ($user->hasRole('Petugas Loket')) {
            Log::info('Login redirecting', ['user_id' => $user->id, 'route' => 'organizer.redeem.index']);
            return redirect()->route('organizer.redeem.index');
        }

        if ($user->hasRole('Petugas Gate')) {
            Log::info('Login redirecting', ['user_id' => $user->id, 'route' => 'organizer.gate.index']);
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
