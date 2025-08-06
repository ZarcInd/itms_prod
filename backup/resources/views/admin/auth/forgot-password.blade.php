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
    <style>
.otp-container {
  display: flex;
  gap: 10px;
}

.otp-input {
  width: 50px;
  height: 50px;
  text-align: center;
  font-size: 24px;
  border: 2px solid #ccc;
  border-radius: 5px;
  outline: none;
  transition: border-color 0.3s;
}

.otp-input:focus {
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
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
                <h2 class="login-header">Forgat Password</h2>
                <form id="myform" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" id="reset-email" :value="old('email')" onChange="convertToSlug(this.value)" autofocus autocomplete="username" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group" id="otp_t" style="display: none;">
                        <label for="password">Password</label>
                        <div class="otp-container">
                                <input type="number" name="otp1" value="{{old('otp1')}}" maxlength="1" class="otp-input" />
                                <input type="number" name="otp2" value="{{old('otp2')}}" maxlength="1" class="otp-input" />
                                <input type="number" name="otp3" value="{{old('otp3')}}" maxlength="1" class="otp-input" />
                                <input type="number" name="otp4" value="{{old('otp4')}}" maxlength="1" class="otp-input" />
                        </div>
                    </div>
                    <div class="form-footer" id="fort_btn" style="display: none; padding-bottom: 2rem;">
                            <button type="submit" class="sign-in-btn login-btn btn btn-primary">Reset my password</button>
                    </div><!-- End .form-footer -->
                </form>

                <form id="regForm">
                        <div class="input-group required-field" style="display: none;">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email_id" value="{{ old('email') }}" required>
                        </div>
                        
                        <div class="pb-5">
                            <button id="submit_button" type="submit" class="sign-in-btn login-btn rig_btn2 otp realizer_button  hover_effect" >Send OTP</button>
                        </div>

                </form>
            </div>
        </div>
    </div>
    <div id="dropDownSelect1"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
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

    function convertToSlug( str ) {
                
                $('#email_id').val(str);
                //return str;
              }
  
              $("#regForm").on("submit", function(e) {
                          e.preventDefault();
                          $('#submit_button').html('<i class="fa fa-spinner fa fa-spin"><i>').prop('disabled', true);
                          var postAddressData = new FormData(document.getElementById("regForm"));
                          postAddressData.append("_token", "{{ csrf_token() }}");
  
                          $.ajax({
                              type: "POST",
                              async: true,
                              contentType: false,
                              dataType: "json",
                              data: postAddressData,
                              processData: false,
                              url: "{{ route('admin.send_otp') }}",
                              beforeSend: function() {
                                  $(".user_save_btn").html(
                                      '<div class="bounce-loader cart_loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>'
                                  );
                              },
                              success: function(data) {
                                  var error_msg = "";
                                  console.log(data);
                                  jQuery.each(data.success, function(key, value) {
                                      alert(data.success);
                                      $("#otp_t").css("display", "block");
                                      $("#fort_btn").css("display", "block");
                                      $('#submit_button').html('Request New OTP Code').prop('disabled', false);
                                  });
                                  jQuery.each(data.error, function(key, value) {
                                      alert(data.error);
                                      $('#submit_button').html('Request New OTP Code').prop('disabled', false);
                                  });
                              },
                          });
                      });
                      
                      
                      
                      document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
                        input.addEventListener('input', (event) => {
                          const { value } = event.target;
                          if (value.length === 1 && index < inputs.length - 1) {
                            inputs[index + 1].focus();
                          }
                        });
                      
                        input.addEventListener('keydown', (event) => {
                          if (event.key === 'Backspace' && index > 0 && input.value === '') {
                            inputs[index - 1].focus();
                          }
                        });
                      });
  
    
</script>
</body>
</html>