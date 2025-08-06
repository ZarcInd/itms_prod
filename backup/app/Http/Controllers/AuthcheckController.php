<?php

namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

class AuthcheckController extends Controller
{
    public function login_redract(Request $request)
    {       

        
            if (Cookie::has('admin_auth')) {
                try {
                    // Decrypt the cookie to get user ID
                    $adminId = Crypt::decrypt(Cookie::get('admin_auth'));
                    // Log in the user using their ID
                    Auth::guard('admin')->loginUsingId($adminId);
    
                    // Redirect to admin dashboard
                    return redirect()->route('vehicles.index')->with('success', 'Login successful.');
                } catch (\Exception $e) {
                    return redirect()->route('admin_login')
                     ->with('toast_error', 'Please login to access this page.');
                }
            }else{
                return redirect()->route('admin_login')
                ->with('toast_error', 'Please login to access this page.');
            }
           
    }
}
