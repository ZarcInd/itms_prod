{{-- resources/views/admin/profile/2fa-recovery-codes.blade.php --}}
@extends('admin.layouts.master')

@section('title', '2FA Recovery Codes')

@section('container')
<main class="main-content">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Setup Two-Factor Authentication</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <p>Enhance your account security by enabling two-factor authentication (2FA). 
                                   Once configured, you'll need to enter a verification code from your 
                                   authenticator app during each login.</p>
                                
                                <h6 class="fw-bold mt-4">Step 1: Scan QR Code</h6>
                                <p>Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.):</p>
                                
                                <div class="text-center my-4">
                                    {!! $qrCode !!}
                                </div>
                                
                                <h6 class="fw-bold mt-4">Step 2: Manual Setup (if needed)</h6>
                                <p>If you can't scan the QR code, enter this setup key manually in your app:</p>
                                <div class="alert alert-info">
                                    <code style="color: white;">{{ $secret }}</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="fw-bold">Step 3: Verify Setup</h6>
                                    <p>Enter the 6-digit verification code from your authenticator app to confirm setup:</p>
                                    
                                    <form action="{{ route('admin.2fa.enable') }}" method="POST">
                                        @csrf
                                        
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Verification Code</label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                                   id="code" name="code" required autocomplete="off" 
                                                   inputmode="numeric" minlength="6" maxlength="6" pattern="[0-9]*">
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                Verify & Enable 2FA
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h6 class="fw-bold">Recommended Authenticator Apps:</h6>
                                <ul>
                                    <li>Google Authenticator (iOS & Android)</li>
                                    <li>Authy (iOS, Android, Desktop)</li>
                                    <li>Microsoft Authenticator (iOS & Android)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
@endsection