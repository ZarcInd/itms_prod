<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime Edge - Login</title>
    <link rel="stylesheet" href="{{ asset('public/backend/css/loginstyle.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
                <form id="loginForm" method="POST" action="{{ route('admin_post_login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email/Phone no.</label>
                        <input type="text" id="email" name="email" :value="old('email')" autofocus autocomplete="username" placeholder="Enter your email or phone no." required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                    </div>
                    <div class="form-footer">
                        <label class="remember-me">
                            <input type="checkbox" id="remember" name="stay_login"> Stay signed in
                        </label>
                        <a href="{{url('admin/forgot-password')}}" class="forgot-password">Forgot password?</a>
                    </div>
                    <button type="submit" class="sign-in-btn">Sign In</button>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('public/backend/js/loginscript.js')}}"></script>
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
</script>
</body>
</html>