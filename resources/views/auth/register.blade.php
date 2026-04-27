@extends('frontend.layouts.master')
@section('title', 'Sign Up | STAFO - Leading HRMS Solution Provider in India')
@section('heading', 'Sign Up')

@section('content')
<div class="register">
        <div class="container">
          <div class="row justify-content-center text-start">
            <div class="col-lg-8 col-md-12">
              <div class="mb-5">
                <h2><span class="font-w-4">Create Your STAFO Account</span> <br /> Sign Up</h2>
                <p class="lead">One platform for HR, payroll, attendance, CRM & more. Try it free now!</p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-8 col-md-10 ms-auto me-auto">
              <div class="register-form ">
                <form id="signup-form" method="post" action="{{ route('store') }}">
                  @csrf
                  <div class="messages"></div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                       
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="Name" placeholder="Enter your Name" name="name" value="{{ old('name') }}">
                        @error('name')
                            <div class="help-block with-errors">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="Email" placeholder="Enter your Email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="help-block with-errors">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <input type="text" class="form-control @error('mobile_no') is-invalid @enderror" id="Number" placeholder="Enter your Mobile Number" name="mobile_no" value="{{ old('mobile_no') }}"  data-error="Mobile number is required">
                        @error('mobile_no')
                            <div class="help-block with-errors">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"   id="signup-password" placeholder="Enter your password" name="password">
                        @error('password')
                            <div class="help-block with-errors">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <input type="password" class="form-control" id="Com-password" name="password_confirmation" placeholder="Enter your Confirm Password">
                        @error('password_confirmation')
                            <div class="help-block with-errors">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <input type="text" name="referral_code" id="referral_code" class="form-control" value="{{ old('referral_code') }}" placeholder="Referral Code (Optional)">
                        @error('referral_code')
                            <div class="help-block with-errors">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="row mt-4">
                    <div class="col-md-12">
                      <div class="remember-checkbox clearfix mb-4">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input float-none" id="customCheck1" required>
                          <label class="form-check-label ms-2" for="customCheck1">I agree to the term of use and privacy policy</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <button type="submit" class="btn btn-primary">Register</button>
                      <span class="mt-4 d-block">Have An Account ? <a href="login.html"><i>Sign In!</i></a></span>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
@endsection

@section('js')
@endsection
