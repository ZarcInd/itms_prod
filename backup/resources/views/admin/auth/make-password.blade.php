<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime Edge - Login</title>
    <link rel="stylesheet" href="{{ asset('public/backend/css/loginstyle.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @include('sweetalert::alert')
<style>
    .swal2-popup.swal2-toast {
        padding: 0.625em;
        box-sizing: border-box;
        font-size: 0.875em;
    }
    
    .swal2-popup.swal2-toast.bg-purple {
        background-color: #6c5ce7 !important;
        color: white !important;
    }
    
    .swal2-popup.swal2-toast.bg-white {
        background-color: white !important;
        color: #333 !important;
        border: 1px solid #ddd;
    }
    
    .toggle-password {
        position: absolute;
        right: 10px;
        top: 70%;
        transform: translateY(-50%);
        cursor: pointer;
    }
    
    .form-group {
        position: relative;
    }
    
    .invalid-feedback {
        color: #dc3545;
        display: block;
        margin-top: 5px;
        font-size: 14px;
    }
    
    #error-message {
        color: #dc3545;
        margin-top: 10px;
        font-size: 14px;
    }
</style>
</head>
<body>
    <div class="split-container">
        <div class="left-panel">
            <h1 class="brand-name">PRIME EDGE</h1>
        </div>
        <div class="right-panel">
            <div class="login-container">
                <h2 class="login-header">Log in to your account</h2>
                <p class="login-subheader">Welcome back! Please enter your details.</p>
                <form id="resetForm" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="reset-email">Email: @if(old('email')=='') {{$email}} @else {{old('email')}} @endif</label>
                        <input type="hidden" class="form-control" id="reset-email" name="email" required value="@if(old('email')=='') {{$email}} @else {{old('email')}} @endif">
                    </div>
                    <div class="form-group">
                        <label for="reset-password">Password</label>
                        <input type="password" class="form-control" id="reset-password" name="password" required>
                        <i class="toggle-password fas fa-eye-slash"></i>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>  
                    <div class="form-group">
                        <label for="reset-confirm">Confirm Password</label>
                        <input type="password" class="form-control" id="reset-confirm" name="password_confirmation" required>
                        <i class="toggle-password fas fa-eye-slash"></i>
                        @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div id="error-message" style="display: none;"></div>
                    <div class="form-footer">
                        <button type="submit" class="sign-in-btn login-btn rig_btn2 otp realizer_button hover_effect">Reset my password</button>    
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="dropDownSelect1"></div>
    <script src="{{ asset('public/backend/js/loginscript.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configure toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        }
        
        // Custom styling
        const purpleStyle = { "background-color": "#6c5ce7", "color": "white" };
        const whiteStyle = { "background-color": "white", "color": "#333", "border": "1px solid #ddd" };
        
        // Show success toast
        @if(session('toast_success'))
            var toast = toastr.success("{{ session('toast_success') }}");
            $(toast.el).css(purpleStyle);
        @endif
        
        // Show error toast
        @if(session('toast_error'))
            toastr.error("{{ session('toast_error') }}");
        @endif
    });

    // Toggle password visibility
    $(".toggle-password").click(function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $(this).parent().find("input");
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    // AJAX form submission
    $(document).ready(function () {
        $('#resetForm').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission
            
            // Password validation
            const password = $('#reset-password').val();
            const confirmPassword = $('#reset-confirm').val();

            if (password !== confirmPassword) {
                $('#error-message').show().text('Passwords do not match.');
                return false;
            }
            
            // Hide error message if passwords match
            $('#error-message').hide();
            
            // AJAX submission
            $.ajax({
                url: "{{ route('create_new_password') }}",
                type: "POST",
                data: $(this).serialize(),
                beforeSend: function() {
                    // Show loading state
                    $('button[type="submit"]').attr('disabled', true).html('Processing...');
                },
                success: function(response) {
                    // Handle success
                    if (response.success) {
                        toastr.success(response.message || 'Password reset successfully!');
                        setTimeout(function() {
                            window.location.href = response.redirect || "{{ route('admin_login') }}";
                        }, 2000);
                    } else {
                        toastr.error(response.message || 'Something went wrong.');
                        $('button[type="submit"]').attr('disabled', false).html('Reset my password');
                    }
                },
                error: function(xhr) {
                    // Handle validation errors
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#error-message').show().text(value[0]);
                        });
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                    $('button[type="submit"]').attr('disabled', false).html('Reset my password');
                }
            });
        });
    });
    </script>
</body>
</html>