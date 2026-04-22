<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Superadmins are exempt
        if ($user && $user->hasRole('superadmin')) {
            return $next($request);
        }

        if ($user && $user->tenant) {
            if ($user->tenant->status !== 'active') {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = $user->tenant->status === 'deleted' 
                    ? 'Your organization account has been deleted.' 
                    : 'Your organization account is currently suspended.';

                return redirect()->route('login')->withErrors(['email' => $message]);
            }
        }

        return $next($request);
    }
}
