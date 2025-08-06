@extends('admin.layouts.master')

@section('title', '2FA Recovery Codes')

@section('container')
<main class="main-content">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Two-Factor Authentication Recovery Codes</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Two-factor authentication has been enabled successfully!
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6 class="fw-bold"><i class="fas fa-exclamation-triangle"></i> IMPORTANT: Save Your Recovery Codes</h6>
                        <p>Keep these recovery codes in a secure place. They can be used to access your account if you 
                           lose your 2FA device.</p>
                        <p class="mb-0"><strong>Each code can only be used once.</strong></p>
                    </div>
                    
                    <div class="bg-light p-3 mb-4 recovery-codes">
                        <div class="row">
                            @foreach(array_chunk($recoveryCodes, ceil(count($recoveryCodes) / 2)) as $chunk)
                                <div class="col-md-6">
                                    @foreach($chunk as $code)
                                        <code class="d-block mb-2">{{ $code }}</code>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary" id="copyCodesBtn">
                            <i class="fas fa-copy"></i> Copy Codes
                        </button>
                        
                        <a href="{{ route('admin.profile') }}" class="btn btn-primary">
                            Continue to Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
<script>
    document.getElementById('copyCodesBtn').addEventListener('click', function() {
        // Get all the codes
        const codeElements = document.querySelectorAll('.recovery-codes code');
        let allCodes = '';
        
        codeElements.forEach(function(code) {
            allCodes += code.textContent + '\n';
        });
        
        // Copy to clipboard
        navigator.clipboard.writeText(allCodes.trim()).then(function() {
            // Show success message
            const btn = document.getElementById('copyCodesBtn');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-check"></i> Codes Copied!';
            btn.classList.replace('btn-secondary', 'btn-success');
            
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.classList.replace('btn-success', 'btn-secondary');
            }, 2000);
        });
    });
</script>
@endsection
