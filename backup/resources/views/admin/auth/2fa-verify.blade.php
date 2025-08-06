@extends('admin.layouts.auth-master')

@section('title', 'Two-Factor Authentication')

@section('container')
<div class="row g-0 vh-100">
    <!-- Left purple sidebar -->
    <div class="col-md-6  d-flex align-items-center justify-content-center" style="background-color:#775DA6" >
        <div class="text-center text-white">
            <h1 class="display-4 fw-bold">PRIME EDGE</h1>
        </div>
    </div>
    
    <!-- Right content area -->
    <div class="col-md-6 d-flex align-items-center">
        <div class="w-100 px-4 px-lg-5">
            <div class="mb-5">
                <h3 class="fw-bold">Log in to your account</h3>
                <p class="text-muted">Two-factor authentication required</p>
            </div>
            
            @if(session('toast_error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('toast_error') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.2fa.validate') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="code" class="form-label">Verification Code</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                           id="code" name="code" required autocomplete="off" autofocus
                           inputmode="numeric" minlength="6" maxlength="6" pattern="[0-9]*"
                           placeholder="Enter the 6-digit code">
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Enter the 6-digit code from your authenticator app</div>
                </div>
                
                <div class="mb-4 d-flex justify-content-between align-items-center">
                   <!--  <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="stay_login" name="stay_login">
                        <label class="form-check-label" for="stay_login">
                            Stay signed in
                        </label>
                    </div> -->
                    
                    <div>
                        <a href="#" class="text-decoration-none" id="useRecoveryLink">Use recovery code</a>
                    </div>
                </div>
                
                <div class="d-grid mb-4">
                    <button type="submit" style="background-color:#775DA6"  class="btn btn-primary">
                        Sign In
                    </button>
                </div>
            </form>
            
            <!-- Recovery code form (hidden by default) -->
            <div id="recoveryCodeForm" style="display:none;">
                <div class="mb-4">
                    <h5>Recovery Code</h5>
                    <p class="text-muted">Enter one of your recovery codes</p>
                    
                    <form method="POST" action="{{ route('admin.2fa.validate') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <input type="text" class="form-control" 
                                id="recovery-code" name="code" required 
                                placeholder="Enter your recovery code">
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">
                                Verify with Recovery Code
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <a href="#" class="text-decoration-none" id="useCodeLink">
                                Use authenticator code instead
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none text-muted">
                        <i class="fas fa-arrow-left me-1"></i> Back to Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const normalForm = document.querySelector('form');
        const recoveryForm = document.getElementById('recoveryCodeForm');
        const useRecoveryLink = document.getElementById('useRecoveryLink');
        const useCodeLink = document.getElementById('useCodeLink');
        
        useRecoveryLink.addEventListener('click', function(e) {
            e.preventDefault();
            normalForm.style.display = 'none';
            recoveryForm.style.display = 'block';
        });
        
        useCodeLink.addEventListener('click', function(e) {
            e.preventDefault();
            recoveryForm.style.display = 'none';
            normalForm.style.display = 'block';
        });
    });
</script>
@endsection