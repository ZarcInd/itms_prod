<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminTwoFactorAuthenticationIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get user from session
        $userId = session('admin_2fa_user_id');
        
        if (!$userId) {
            return redirect()->route('admin_login');
        }
        
        // Check if it's a 2FA verification route
        if ($request->routeIs('admin.2fa.*')) {
            return $next($request);
        }
        
        // Redirect back to 2FA verification
        return redirect()->route('admin.2fa.verify');
    
    }
}
