<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA setup form
     */
    public function setup()
    {
        $user = Auth::guard('admin')->user();
        
        // If 2FA is already enabled, redirect to profile page
        if ($user->two_factor_enabled) {
            return redirect()->route('admin.profile')->with('toast_info', '2FA is already enabled.');
        }
        
        // Generate new secret key if not exists
        if (empty($user->two_factor_secret)) {
            $google2fa = new Google2FA();
            $user->two_factor_secret = $google2fa->generateSecretKey();
            $user->save();
        }
        
        // Generate QR code
        $qrCodeUrl = $this->generateQrCodeUrl($user);
        $qrCode = $this->generateQrCode($qrCodeUrl);
        return view('admin.auth.2fa-setup', [
            'qrCode' => $qrCode,
            'secret' => $user->two_factor_secret
        ]);
    }
    
    /**
     * Enable 2FA after verification
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        $user = Auth::guard('admin')->user();
        $google2fa = new Google2FA();
        
        // Verify the code
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);
        
        if ($valid) {
            // Enable 2FA
            $user->two_factor_enabled = true;
            
            // Generate recovery codes
            $recoveryCodes = $this->generateRecoveryCodes();
            $user->two_factor_recovery_codes = json_encode($recoveryCodes);
            
            $user->save();
            
            return view('admin.auth.2fa-recovery-codes', [
                'recoveryCodes' => $recoveryCodes
            ]);
        }
        
        return back()->with('toast_error', 'Invalid verification code. Please try again.');
    }
    
    /**
     * Disable 2FA after password confirmation
     */
    public function disable(Request $request)
    { 

        
        $request->validate([
            'password' => 'required|string',
        ]);
        
        $user = Auth::guard('admin')->user();
        
        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->with('toast_error', 'Incorrect password.');
        }
        
        // Disable 2FA
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();
        
        return redirect()->route('admin.profile')->with('toast_success', '2FA has been disabled.');
    }
    
    /**
     * Show the 2FA verification form during login
     */
    public function verify()
    {
        return view('admin.auth.2fa-verify');
    }
    
    /**
     * Verify the 2FA code during login
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);
        
        // Get the user ID from session
        $userId = session('admin_2fa_user_id');
        if (!$userId) {
            return redirect()->route('admin_login')->with('toast_error', 'Session expired. Please login again.');
        }
        
        $user = \App\Models\Admin::find($userId);
        if (!$user) {
            return redirect()->route('admin_login')->with('toast_error', 'User not found.');
        }
        
        // Check if it's a recovery code
        if (strlen($request->code) > 6) {
            return $this->validateRecoveryCode($user, $request->code);
        }
        
        // Verify the 2FA code
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);
        
        if ($valid) {
            // Complete the login process
            Auth::guard('admin')->login($user);
            $request->session()->regenerate();
            $request->session()->forget('admin_2fa_user_id');
            
            // Create the admin auth cookie
            $cookie = \Illuminate\Support\Facades\Cookie::make(
                'admin_auth', 
                encrypt($user->id), 
                $request->boolean('stay_login') ? 60 * 24 * 7 : 60 * 24 * 1
            );
            
            return redirect()
                ->intended(route('vehicles.index', absolute: false))
                ->with('toast_success', 'Login successful!')
                ->cookie($cookie);
        }
        
        return back()->with('toast_error', 'Invalid verification code. Please try again.');
    }
    
    /**
     * Validate a recovery code
     */
    private function validateRecoveryCode($user, $code)
    {
        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
        
        if (!is_array($recoveryCodes) || !in_array($code, $recoveryCodes)) {
            return back()->with('toast_error', 'Invalid recovery code.');
        }
        
        // Remove the used recovery code
        $recoveryCodes = array_diff($recoveryCodes, [$code]);
        $user->two_factor_recovery_codes = json_encode(array_values($recoveryCodes));
        $user->save();
        
        // Complete the login process
        Auth::guard('admin')->login($user);
        session()->regenerate();
        session()->forget('admin_2fa_user_id');
        
        // Create the admin auth cookie
        $cookie = \Illuminate\Support\Facades\Cookie::make(
            'admin_auth', 
            encrypt($user->id), 
            request()->boolean('stay_login') ? 60 * 24 * 7 : 60 * 24 * 1
        );
        
        return redirect()
            ->intended(route('vehicles.index', absolute: false))
            ->with('toast_success', 'Login successful! You used a recovery code. Please generate new recovery codes for future use.')
            ->cookie($cookie);
    }
    
    /**
     * Generate a QR code URL for the Google Authenticator app
     */
    private function generateQrCodeUrl($user)
    {
        $google2fa = new Google2FA();
        $App_name = 'Prime Edge';
        return $google2fa->getQRCodeUrl(
            $App_name, // Your application name
            $user->email,
            $user->two_factor_secret
        );
    }
    
    /**
     * Generate a QR code SVG image
     */
    private function generateQrCode($url)
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        
        $writer = new Writer($renderer);
        return $writer->writeString($url);
    }
    
    /**
     * Generate recovery codes
     */
    private function generateRecoveryCodes($count = 8)
    {
        $recoveryCodes = [];
        
        for ($i = 0; $i < $count; $i++) {
            $recoveryCodes[] = $this->generateRandomString(10);
        }
        
        return $recoveryCodes;
    }
    
    /**
     * Generate a random string
     */
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $result;
    }
}

