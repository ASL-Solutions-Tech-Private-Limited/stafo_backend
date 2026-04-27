@extends('frontend.layouts.master')
@section('title', 'Sign In | STAFO - Leading HRMS Solution Provider in India')
@section('heading', '')
@section('content') 

      <!--login start-->

      <section>
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-7 col-12">
              <img class="img-fluid" src="assets/images/login.png" alt="">
            </div>
            <div class="col-lg-5 col-12">
              <div>
                <h2 class="mb-3">Sign In</h2>
                <form id="login-form" action="{{ route('authenticate') }}" method="post">
                @csrf
                  <div class="messages"></div>
                  <div class="form-group">
                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" placeholder="Enter your Email or Phone Number" value="{{ old('email') }}">
                    @if ($errors->has('email'))
                        <div class="help-block with-errors">{{ $errors->first('email') }}</div>
                    @endif
                    
                  </div>
                  <div class="form-group"  id="password_field" style="display:none;">
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" name="password" placeholder="Enter your password">
                    <!-- <span class="show-password" id="toggle-password">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span> -->
                    @if ($errors->has('password'))
                        <div class="help-block with-errors">{{ $errors->first('password') }}</div>
                    @endif
                  </div>

                  <div class="mb-3" id="otp_field" style="display:none;">
                    <label for="otp" class="form-label">OTP</label>
                    <div class="password">
                        <input type="text"
                            class="form-control" id="otp" name="otp" placeholder="Enter OTP">
                        <span class="show-password">
                            <!-- <i class="fa-solid fa-eye-slash"></i> -->
                        </span>

                        @if ($errors->has('password'))
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                </div>
                  <!-- <div class="form-group mt-4 mb-5">
                    <div class="remember-checkbox d-flex align-items-center justify-content-between">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="check1">
                        <label class="form-check-label" for="check1">Remember me</label>
                      </div> <a class="btn-link" href="#">Forgot Password?</a>
                    </div>
                  </div>  -->
                  <!-- <a href="#" class="btn btn-primary">Login Now</a> -->
                  <span class="btn btn-primary" id="submit_button">Submit</span>
                <button type="submit" class="btn btn-primary" id="login_button" style="display:none;">Login</button>
                </form>
                <div class="d-flex align-items-center mt-4"> <span class="text-muted me-1">Don't have an account?</span>
                  <a href="{{ route('register') }}">Sign Up</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!--login end-->



    @endsection

    @section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('sweetalert::alert')
    <script>
        // jQuery for password visibility toggle
        $('#toggle-password').click(function () {
            var passwordField = $('#password');
            var icon = $(this).find('i');

            // Toggle password visibility
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text'); // Show password
                icon.removeClass('fa-eye-slash').addClass('fa-eye'); // Change icon to eye
            } else {
                passwordField.attr('type', 'password'); // Hide password
                icon.removeClass('fa-eye').addClass('fa-eye-slash'); // Change icon to eye-slash
            }
        });

        $(document).ready(function() {
            $('#submit_button').click(function() {
                var email = $('#email').val();
                if (email == '') {                    
                    Swal.fire({
                        title: '',
                        text: "Please enter email or phone number",
                        //icon: 'warning',                        
                    }).then((result) => {
                        
                    });
                    return false;
                }
                var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                // Regular expression for phone (basic format, can be more complex)
                var phonePattern = /^[0-9]{10}$/;
                // Check if the input is a valid email
                if (emailPattern.test(email)) {
                    $('#password_field').show();
                    $('#login_button').show();
                    $('#submit_button').hide();
                }
                // Check if the input is a valid phone number
                else if (phonePattern.test(email)) {
                    //$('#otp_field').show();
                    $('#login_button').show();
                    $('#submit_button').hide();
                    $.ajax({
                        url: "{{ route('sendOtp') }}",
                        type: 'POST',
                        data: {
                            mobile_number: email,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success == true) {
                                $('#otp_field').show();
                            } else {
                                Swal.fire({
                                    title: 'User Not Found!',
                                    text: "Please register your mobile number as a company user. Then try login.",
                                    icon: 'warning',
                                    //showCancelButton: true,
                                    //confirmButtonColor: '#3085d6',
                                    //cancelButtonColor: '#d33',
                                    //confirmButtonText: 'Yes, delete it!'
                                }).then((result) => {
                                    
                                });
                            }
                            
                            //console.log(response);
                        }
                    });
                } else {                    
                    Swal.fire({
                        title: 'Invalid!',
                        text: "The input is neither a valid email nor a valid phone number.",
                        icon: 'warning',                        
                    }).then((result) => {
                        
                    });
                }
            });

        });
    </script>


@endsection