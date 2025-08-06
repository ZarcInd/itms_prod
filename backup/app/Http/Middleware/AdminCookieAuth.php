<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\Admin;

class AdminCookieAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin')->user();

        // ✅ If already authenticated
        if ($user) {
            // If 2FA is enabled but not verified
            if ($user->two_factor_enabled && !session('admin_2fa_verified')) {
                if (!$request->routeIs('admin.2fa.*')) {
                    // Store intended URL before forcing 2FA
                    session(['admin_intended_url' => $request->url()]);
                    session(['admin_2fa_user_id' => $user->id]);

                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.2fa.verify');
                }
            }
            return $next($request);
        }

        // ✅ Try cookie-based auth
        if ($request->hasCookie('admin_auth')) {
            try {
                $userId = decrypt($request->cookie('admin_auth'));
                $user = Admin::find($userId);

                if ($user && $user->status == 1) {
                    Auth::guard('admin')->login($user);

                    // Redirect to 2FA if enabled
                    if ($user->two_factor_enabled && !session('admin_2fa_verified')) {
                        session(['admin_intended_url' => $request->url()]);
                        session(['admin_2fa_user_id' => $user->id]);
                        Auth::guard('admin')->logout();

                        return redirect()->route('admin.2fa.verify');
                    }

                    // ✅ 2FA not enabled, redirect to dashboard
                    return redirect()->intended(route('vehicles.index'));
                }
            } catch (\Exception $e) {
                $forgetCookie = Cookie::forget('admin_auth');
                return redirect()->route('admin_login')->withCookie($forgetCookie);
            }
        }

        // ❌ No session or valid cookie
        return redirect()->route('admin_login');
    }
}
