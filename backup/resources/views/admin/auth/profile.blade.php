@extends('admin/layouts/master')
@section('container')
<main class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">User Profile</h2>
        
        <div class="row">
            <!-- Profile Section -->
            <div class="col-lg-4 col-md-5 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="position-relative mx-auto mb-3" style="width: 120px;">
                        @if($user->profile_image && file_exists(public_path($user->profile_image)))
                         <img src="{{url('public/'.$user->profile_image)}}" alt="Profile Image" class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                        <img src="{{url('public/1142545574.png')}}" alt="Profile Image" class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                        @endif
                           <button class="btn btn-sm position-absolute bottom-0 end-0 rounded-circle" 
                                   style="background-color: #7e57c2; color: white;">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                        <h4 class="fw-bold">{{ $user->name }}</h4>
                        <p class="text-muted">System {{ $user->role }}</p>
                        <div class="d-grid gap-2">
                            <button class="btn" style="background-color: #7e57c2; color: white;" id="pencil">
                                <!-- <i class="bi bi-envelope-fill me-2"></i> -->
                            </button>
                        </div>
                    </div>
                    <!-- <div class="card-footer bg-light">
                        <div class="d-flex justify-content-around">
                            <div class="text-center">
                                <div class="fw-bold">54</div>
                                <div class="small text-muted">Vehicles</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold">142</div>
                                <div class="small text-muted">Activities</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-bold">8</div>
                                <div class="small text-muted">Alerts</div>
                            </div>
                        </div>
                    </div> -->
                </div>
                
                <!-- Quick Stats -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Account Info</h5>
                        <button class="btn btn-sm btn-link text-muted p-0">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Email Address</div>
                            <div>{{ $user->email }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Phone Number</div>
                            <div>{{ $user->phone ?? 'Not provided' }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small">Role</div>
                            <div>
                                <span class="badge" style="background-color: #7e57c2;">{{ $user->role }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            @php
                             $user = Auth::guard('admin')->user();
                            @endphp
                            @if($user->two_factor_enabled != 1)
                            <div class="badge" style="background-color: #7e57c2;">Enable Two-Factor Authentication</div>
                            <div>
                                <a href="{{ route('admin.2fa.setup') }}" class="text-muted small" ><b>Click a Link Enable 2FA</b></a>
                            </div>
                            @else
                            <div class="badge" style="background-color: #7e57c2;">Disable Two-Factor Authentication</div>
                            <div>
                            <form action="{{ route('admin.2fa.disable') }}" method="POST" style="display: inline;">
                                @csrf
                                <div class="mb-3">
                                     <label for="current_password" class="form-label">Current Password</label>
                                
                                 <div class="input-group mt-2">
                                   
                                    <input type="password" class="form-control" id="password_a" name="password">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_a">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                </div>
                                <button type="submit" class="btn btn-sm mt-2" style="background-color: #7e57c2; color: white;">Disable 2FA</button>
                            </form>
                            </div>
                            @endif      
                        </div>
                        <div>
                            <!-- <div class="text-muted small">Member Since</div>
                            <div>{{ $user->created_at->format('F d, Y') }}</div> -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Change Password & Settings -->
            <div class="col-lg-8 col-md-7 col-sm-12">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center" 
                         style="border-bottom: 2px solid #7e57c2;">
                        <h5 class="mb-0">Change Password</h5>
                        <span class="badge bg-light text-dark">Security</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.profile.update-password') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%; background-color: #7e57c2;" 
                                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="password-strength"></div>
                                </div>
                                <small class="text-muted" id="password-strength-text">Password strength: Too weak</small>
                            </div>
                            <div class="mb-4">
                                <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_confirmation">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-lg" style="background-color: #7e57c2; color: white;">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Account Settings -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center"
                         style="border-bottom: 2px solid #7e57c2;">
                        <h5 class="mb-0">Account Settings</h5>
                        <span class="badge bg-light text-dark">Preferences</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.profile.update') }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="{{ $user->phone }}" disabled>
                            </div>
                            <!-- <div class="mb-4">
                                <label for="timezone" class="form-label">Timezone</label>
                                <select class="form-select" id="timezone" name="timezone">
                                    <option value="UTC-08:00" {{ $user->timezone == 'UTC-08:00' ? 'selected' : '' }}>UTC-08:00 Pacific Time</option>
                                    <option value="UTC-05:00" {{ $user->timezone == 'UTC-05:00' ? 'selected' : '' }}>UTC-05:00 Eastern Time</option>
                                    <option value="UTC+00:00" {{ $user->timezone == 'UTC+00:00' ? 'selected' : '' }}>UTC+00:00 Greenwich Mean Time</option>
                                    <option value="UTC+01:00" {{ $user->timezone == 'UTC+01:00' ? 'selected' : '' }}>UTC+01:00 Central European Time</option>
                                </select>
                            </div> -->
                            <!-- <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" 
                                           {{ $user->email_notifications ? 'checked' : '' }}
                                           style="background-color: #7e57c2; border-color: #7e57c2;">
                                    <label class="form-check-label" for="email_notifications">Email Notifications</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications"
                                           {{ $user->sms_notifications ? 'checked' : '' }}
                                           style="background-color: #7e57c2; border-color: #7e57c2;">
                                    <label class="form-check-label" for="sms_notifications">SMS Notifications</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="two_factor_auth" name="two_factor_auth" 
                                           {{ $user->two_factor_auth ? 'checked' : '' }}
                                           style="background-color: #7e57c2; border-color: #7e57c2;">
                                    <label class="form-check-label" for="two_factor_auth">Two-Factor Authentication</label>
                                </div>
                            </div> -->
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">Cancel</button>
                                <button type="submit" class="btn" style="background-color: #7e57c2; color: white;">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add a meta tag for CSRF token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
    // Password toggle visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.querySelector('i').classList.remove('bi-eye');
                this.querySelector('i').classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                this.querySelector('i').classList.remove('bi-eye-slash');
                this.querySelector('i').classList.add('bi-eye');
            }
        });
    });
    
    // Password strength meter
    document.getElementById('new_password').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('password-strength');
        const strengthText = document.getElementById('password-strength-text');
        
        // Calculate password strength
        let strength = 0;
        const regexes = [
            /[a-z]+/, // lowercase
            /[A-Z]+/, // uppercase
            /[0-9]+/, // numbers
            /[$@#&!]+/, // special characters
            /.{8,}/ // at least 8 characters
        ];
        
        // Check each criteria
        regexes.forEach(regex => {
            if (regex.test(password)) {
                strength += 1;
            }
        });
        
        // Add extra points for length
        if (password.length > 12) strength += 1;
        
        // Calculate percentage
        let percentage = 0;
        let text = '';
        let color = '';
        
        switch(strength) {
            case 0:
                percentage = 0;
                text = 'Password strength: Too weak';
                color = '#dc3545'; // red
                break;
            case 1:
            case 2:
                percentage = 25;
                text = 'Password strength: Weak';
                color = '#dc3545'; // red
                break;
            case 3:
                percentage = 50;
                text = 'Password strength: Medium';
                color = '#ffc107'; // yellow
                break;
            case 4:
                percentage = 75;
                text = 'Password strength: Strong';
                color = '#28a745'; // green
                break;
            case 5:
            case 6:
                percentage = 100;
                text = 'Password strength: Very strong';
                color = '#28a745'; // green
                break;
        }
        
        // Update UI
        strengthBar.style.width = percentage + '%';
        strengthBar.style.backgroundColor = color;
        strengthText.textContent = text;
        strengthText.style.color = color;
    });
    
    // Confirm password validation
    document.getElementById('new_password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('new_password').value;
        const confirmPassword = this.value;
        
        if (password === confirmPassword && password !== '') {
            this.style.borderColor = '#28a745';
        } else {
            this.style.borderColor = '';
        }
    });
    
    // Profile image upload functionality
    document.querySelector('.position-relative button').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Create a file input
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Create FormData
                const formData = new FormData();
                formData.append('profile_image', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Display loading state
                const profileImg = document.querySelector('.position-relative img');
                const originalSrc = profileImg.src;
                profileImg.style.opacity = '0.5';
                
                // Send AJAX request
                fetch('{{ route("admin.profile.update-image") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status == true) {
                        // Update image
                        profileImg.src = '/' + data.path;
                        // Show success message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show mt-3';
                        alert.innerHTML = `
                            Profile image updated successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.querySelector('.card-body').appendChild(alert);
                    } else {
                        // Show error message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-danger alert-dismissible fade show mt-3';
                        alert.innerHTML = `
                            ${data.message || 'Failed to upload image. Please try again.'}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.querySelector('.card-body').appendChild(alert);
                        profileImg.src = originalSrc;
                    }
                    profileImg.style.opacity = '1';
                })
                .catch(error => {
                    console.error('Error:', error);
                    profileImg.style.opacity = '1';
                    profileImg.src = originalSrc;
                    
                    // Show error message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show mt-3';
                    alert.innerHTML = `
                        An error occurred. Please try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.querySelector('.card-body').appendChild(alert);
                });
            }
        });
        
        fileInput.click();
    });
    
    // Form validation for account settings
    document.querySelector('form[action="{{ route("admin.profile.update") }}"]').addEventListener('submit', function(e) {
        let isValid = true;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        
        // Simple email validation
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            isValid = false;
            document.getElementById('email').style.borderColor = '#dc3545';
            
            if (!document.getElementById('email-error')) {
                const errorMsg = document.createElement('div');
                errorMsg.id = 'email-error';
                errorMsg.className = 'text-danger small mt-1';
                errorMsg.textContent = 'Please enter a valid email address';
                document.getElementById('email').insertAdjacentElement('afterend', errorMsg);
            }
        } else {
            document.getElementById('email').style.borderColor = '';
            const errorMsg = document.getElementById('email-error');
            if (errorMsg) errorMsg.remove();
        }
        
        // Simple phone validation (optional field)
        if (phone && !/^[0-9+\-\s()]{7,15}$/.test(phone)) {
            isValid = false;
            document.getElementById('phone').style.borderColor = '#dc3545';
            
            if (!document.getElementById('phone-error')) {
                const errorMsg = document.createElement('div');
                errorMsg.id = 'phone-error';
                errorMsg.className = 'text-danger small mt-1';
                errorMsg.textContent = 'Please enter a valid phone number';
                document.getElementById('phone').insertAdjacentElement('afterend', errorMsg);
            }
        } else {
            document.getElementById('phone').style.borderColor = '';
            const errorMsg = document.getElementById('phone-error');
            if (errorMsg) errorMsg.remove();
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
</script>
@endsection