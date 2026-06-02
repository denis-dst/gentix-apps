<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        if ($user && $user->hasRole('Superadmin')) {
            return $next($request);
        }

        if ($user && $user->tenant) {
            if ($user->tenant->status !== 'active') {
                Log::warning('Login redirected because tenant is inactive', [
                    'user_id' => $user->id,
                    'tenant_id' => $user->tenant->id,
                    'tenant_status' => $user->tenant->status,
                ]);

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
