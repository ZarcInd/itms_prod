<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    { 
        if (!Auth::guard('admin')->check()) {
            $adminId = Cookie::get('admin_auth');
            if ($adminId) {
                $adminId = Crypt::decrypt($adminId);
                Auth::guard('admin')->loginUsingId($adminId);
            }else {
                return redirect()->route('admin_login')
                    ->with('toast_error', 'Please login to access this page.');
            }
        }

        return $next($request);
    }
}
