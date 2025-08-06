<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SendMail;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Mail\MyDemoMail;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */


    public function create()
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
                // If decryption fails, proceed to login page
            }
        }
        return view('admin.auth.login'); // your login blade file
    }

    /**
     * Handle an incoming authentication request.
     */


    public function store(AdminLoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
            $request->session()->regenerate();
            // ✅ Get the authenticated user (admin guard)
            // Get the authenticated user
            $user = Auth::guard('admin')->user();

            // Check if user is active
            if ($user->role === 'sub-admin' && $user->status != 1) {
                Auth::guard('admin')->logout();
                $forgetCookie = Cookie::forget('admin_auth');

                return redirect()->route('admin_login')
                    ->with('toast_error', 'Your account is inactive.')
                    ->withCookie($forgetCookie);
            }
            
            // Check if 2FA is enabled
            if ($user->two_factor_enabled) {
                Auth::guard('admin')->logout();

                // Store user ID in session for 2FA verification
                session(['admin_2fa_user_id' => $user->id]);

                // Redirect to 2FA verification page
                return redirect()->route('admin.2fa.verify');
            }

            // Regenerate session for security
            $request->session()->regenerate();

            // Create admin auth cookie
            $cookie = Cookie::make(
                'admin_auth',
                encrypt($user->id),
                $request->boolean('stay_login') ? 60 * 24 * 7 : 60 * 24 * 1
            );

            return redirect()
                ->intended(route('vehicles.index', absolute: false))
                ->with('toast_success', 'Login successful!')
                ->cookie($cookie);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors(['email' => $e->getMessage()])
                ->withInput($request->only('email'))
                ->with('toast_error', 'Invalid login credentials');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login')->withCookie(Cookie::forget('admin_auth'));
    }


    


    public function forget_view($email = null)
    {
        return view('admin.auth.forgot-password')->with('email', $email);
    }


    public function send_otp(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        if ($request->has('otp')) {
            $otp = $request->otp;
        } else {
            $otp = null;
        }
        $email = $request->email;
        $user = Admin::where('email', $request->email)->get();
        if (count($user) > 0) {
            $gen_otp = rand(1000, 9999);
            $email_response = $this->send_mail_with_otp($email, $gen_otp);
            setcookie('otp', $gen_otp, time() + (3600), "/"); // 86400 = 1 day
            if ($email_response == true) {
                Admin::where('id', $user[0]->id)
                    ->update(['otp' => $gen_otp, 'updated_at' => date("Y-m-d")]);
                return response()->json(["success" => ['OTP Successfully sent to your Email id:' . $email]]);
            } else {
                return response()->json(["error" => ['Failed to try again']]);
            }
        } else {
            return response()->json(["error" => ['Email does not exist']]);
        }
    }

    public function send_mail_with_otp($email, $gen_otp)
    {

        $mailData = [
            'type' => 'send_otp',
            'subject' => 'Recover Password With OTP Code',
            'title' => 'Recover Password With OTP Code',
            'otp' => $gen_otp,
        ];

        try {

            Mail::to($email)->send(new MyDemoMail($mailData));
            // Mail::to($email)->send(new WelcomeEmail($details));

            return true;
        } catch (\Exception $e) {

            // Log the error message for debugging
            \Log::error('Mail sending failed: ' . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function forget_send_otp_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }


        if ($request->has(['otp1', 'otp2', 'otp3', 'otp4'])) {
            $otp = implode('', [$request->otp1, $request->otp2, $request->otp3, $request->otp4]);
        } else {
            $otp = null;
        }
        $email = $request->email;
        $user = Admin::where('email', $email)->get();
        if ($otp != null && $otp != '') {

            $user = Admin::where('email', $email)->first();

            // Check Condition Mobile No. Found or Not
            if ($user->otp == $otp) {
                return view('admin.auth.make-password')->with('email', $email);
            } else {
                return back()->with("error", "Incorrect  OTP");
            }
        }
    }


    public function create_new_password(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'exists:admins,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        // If validation fails, return JSON response for AJAX
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get the email and hash the password
            $email = $request->email;
            $password = Hash::make($request->password);

            // Update the admin's password
            $updated = Admin::where('email', $email)->update(['otp' => null,'password' => $password]);

            // Check if the update was successful
            if ($updated) {
                // Check if it's an AJAX request
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Password updated successfully!',
                        'redirect' => route('admin_login')
                    ]);
                }
                   // For non-AJAX requests, redirect with success message
                return redirect()->route('admin_login')->with("toast_success", "Password Updated Successfully");
            } else {
                // If update failed, return appropriate response
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to update password. Please try again.'
                    ]);
                }

                // For non-AJAX requests
                return back()->with("toast_error", "Having Some Error in Update");
            }
        } catch (\Exception $e) {
            // Handle exceptions and return appropriate response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }

            // For non-AJAX requests
            return back()->with("toast_error", "An error occurred. Please try again.");
        }
    }
}
