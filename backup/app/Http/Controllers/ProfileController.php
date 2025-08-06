<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }


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
