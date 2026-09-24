@extends('frontend.layouts.master')
@section('title', 'Sign Up | STAFO - Enterprise HRMS & Payroll Platform')
@section('heading', '')

@section('css')
<style>
  /* Suppress the empty hero banner from master layout */
  .hero-banner {
    display: none !important;
  }

  :root {
    --primary: #424096;
    --primary2: #5b57c7;
    --primary-soft: #f0efff;
    --ink: #17172a;
    --muted: #68687a;
    --soft: #f6f6fb;
    --line: #e7e7f0;
    --green: #16a36a;
    --stafo-slate-900: #17172a;
    --stafo-slate-700: #334155;
    --stafo-slate-500: #68687a;
    --stafo-slate-200: #e7e7f0;
    --stafo-slate-100: #f1f5f9;
    --stafo-slate-50: #f8fafc;
  }

  /* Page Canvas Mesh Gradient */
  .stafo-auth-wrapper {
    background: radial-gradient(circle at 15% 20%, rgba(66, 64, 150, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(91, 87, 199, 0.08) 0%, transparent 50%),
                linear-gradient(135deg, #fbfbfe 0%, #f6f6fb 45%, #f0efff 100%);
    min-height: calc(100vh - 130px);
    display: flex;
    align-items: center;
    padding: 45px 0 65px 0;
    position: relative;
    overflow: hidden;
  }

  /* Ambient floating orbs */
  .stafo-auth-wrapper::before {
    content: '';
    position: absolute;
    top: -120px;
    right: -100px;
    width: 480px;
    height: 480px;
    background: radial-gradient(circle, rgba(91, 87, 199, 0.12), transparent 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
  }

  .stafo-auth-wrapper::after {
    content: '';
    position: absolute;
    bottom: -140px;
    left: -120px;
    width: 440px;
    height: 440px;
    background: radial-gradient(circle, rgba(66, 64, 150, 0.12), transparent 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
  }

  /* Left Column: Benefits Showcase Panel */
  .stafo-hrms-panel {
    background: linear-gradient(150deg, #15152a 0%, #1e1d44 50%, #2b286d 100%);
    color: #ffffff;
    border-radius: 28px;
    padding: 46px 42px;
    box-shadow: 0 25px 60px -12px rgba(21, 21, 42, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.08);
    position: relative;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .stafo-hrms-panel::after {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 280px;
    height: 280px;
    background: radial-gradient(circle, rgba(91, 87, 199, 0.3), transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  .stafo-brand-chip {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 6px 16px;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(91, 87, 199, 0.4);
    color: #c4c1ff;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 22px;
    backdrop-filter: blur(8px);
    width: fit-content;
  }

  .stafo-brand-chip .pulse-dot {
    width: 8px;
    height: 8px;
    background: #817eed;
    border-radius: 50%;
    box-shadow: 0 0 0 4px rgba(129, 126, 237, 0.4);
    animation: stafoPulse 2s infinite;
  }

  @keyframes stafoPulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.3); opacity: 0.7; }
  }

  .stafo-panel-title {
    font-size: 2.15rem;
    font-weight: 800;
    line-height: 1.22;
    color: #ffffff;
    margin-bottom: 12px;
    letter-spacing: -0.02em;
  }

  .stafo-text-gradient {
    background: linear-gradient(135deg, #817eed 0%, #b3b0ff 65%, #ffffff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .stafo-panel-sub {
    color: #cbd5e1;
    font-size: 0.96rem;
    line-height: 1.6;
    margin-bottom: 28px;
    max-width: 500px;
  }

  .stafo-feature-card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 14px 18px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.09);
    backdrop-filter: blur(12px);
    border-radius: 18px;
    margin-bottom: 12px;
    transition: all 0.3s ease;
  }

  .stafo-feature-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 13px;
    background: linear-gradient(135deg, rgba(66, 64, 150, 0.45) 0%, rgba(91, 87, 199, 0.4) 100%);
    border: 1px solid rgba(129, 126, 237, 0.4);
    color: #c4c1ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
  }

  .stafo-feature-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 2px;
  }

  .stafo-feature-desc {
    font-size: 0.81rem;
    color: #94a3b8;
    margin-bottom: 0;
    line-height: 1.45;
  }

  .stafo-trust-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 22px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 20px;
  }

  .stafo-stat-item {
    text-align: left;
  }

  .stafo-stat-num {
    font-size: 1.25rem;
    font-weight: 800;
    color: #a5a2ff;
    line-height: 1;
    margin-bottom: 4px;
  }

  .stafo-stat-label {
    font-size: 0.72rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
  }

  /* Right Column: Register Card */
  .stafo-register-box {
    background: #ffffff;
    border-radius: 28px;
    padding: 42px 38px;
    box-shadow: 0 25px 60px -15px rgba(21, 21, 42, 0.12), 0 1px 3px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(231, 231, 240, 0.9);
    position: relative;
    z-index: 1;
  }

  .stafo-portal-header {
    text-align: center;
    margin-bottom: 24px;
  }

  .stafo-card-logo-badge {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
  }

  .stafo-card-logo-img {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    box-shadow: 0 8px 18px -3px rgba(66, 64, 150, 0.25), 0 0 0 3px rgba(66, 64, 150, 0.12);
    object-fit: contain;
  }

  .stafo-brand-name {
    font-size: 1.4rem;
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -0.5px;
    color: var(--stafo-slate-900);
    margin: 0;
  }

  .stafo-brand-name span {
    color: var(--primary);
  }

  .stafo-brand-tagline {
    font-size: 0.75rem;
    color: var(--stafo-slate-500);
    margin: 0;
    font-weight: 600;
  }

  .stafo-portal-title {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--stafo-slate-900);
    letter-spacing: -0.02em;
    margin-bottom: 4px;
  }

  .stafo-portal-subtitle {
    color: var(--stafo-slate-500);
    font-size: 0.88rem;
    margin-bottom: 0;
  }

  /* Form Inputs */
  .stafo-input-group {
    position: relative;
    display: flex;
    align-items: center;
  }

  .stafo-input-icon {
    position: absolute;
    left: 16px;
    color: #94a3b8;
    font-size: 1rem;
    pointer-events: none;
    z-index: 4;
    transition: color 0.2s ease;
  }

  .stafo-form-control {
    width: 100%;
    height: 48px;
    font-size: 0.92rem;
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    padding: 10px 16px 10px 46px;
    transition: all 0.25s ease;
    color: var(--stafo-slate-900);
    font-weight: 500;
    background: #f8fafc;
  }

  .stafo-form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(66, 64, 150, 0.14);
    background: #ffffff;
    outline: none;
  }

  .stafo-form-control:focus ~ .stafo-input-icon {
    color: var(--primary);
  }

  .stafo-btn-submit {
    height: 50px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary2) 100%);
    color: #ffffff;
    font-weight: 700;
    font-size: 1rem;
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 24px -4px rgba(66, 64, 150, 0.4);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    cursor: pointer;
    letter-spacing: 0.2px;
  }

  .stafo-btn-submit:hover {
    background: linear-gradient(135deg, #37357d 0%, #4c49aa 100%);
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 14px 28px -4px rgba(66, 64, 150, 0.5);
  }

  .stafo-security-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    color: #94a3b8;
    font-size: 0.74rem;
    font-weight: 600;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
  }

  .stafo-security-footer span {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  @media (max-width: 991.98px) {
    .stafo-auth-wrapper {
      padding: 30px 0 50px 0;
    }
    .stafo-hrms-panel {
      margin-bottom: 24px;
      padding: 32px 24px;
    }
    .stafo-register-box {
      padding: 30px 22px;
    }
  }
</style>
@endsection

@section('content')
<section class="stafo-auth-wrapper">
  <div class="container position-relative" style="z-index: 1;">
    <div class="row align-items-stretch justify-content-center g-4 g-lg-5">
      
      <!-- Left: Free Trial Benefits Panel -->
      <div class="col-lg-5 col-12 d-flex">
        <div class="stafo-hrms-panel w-100">
          <div>
            
            <div class="stafo-brand-chip">
              <span class="pulse-dot"></span>
              <i class="fa-solid fa-gift text-info"></i> 15-Day Full Access Trial
            </div>

            <h1 class="stafo-panel-title">
              Modernize Your HR. <br>
              <span class="stafo-text-gradient">Zero Setup Fees.</span>
            </h1>
            
            <p class="stafo-panel-sub">
              Experience the all-in-one HRMS built specifically for Indian businesses. Automate attendance, payroll, leaves, and team operations.
            </p>

            <div class="stafo-feature-card">
              <div class="stafo-feature-icon-box">
                <i class="fa-solid fa-bolt"></i>
              </div>
              <div>
                <div class="stafo-feature-title">Instant Setup in Under 5 Mins</div>
                <div class="stafo-feature-desc">Invite employees and start capturing attendance immediately.</div>
              </div>
            </div>

            <div class="stafo-feature-card">
              <div class="stafo-feature-icon-box">
                <i class="fa-solid fa-shield-check"></i>
              </div>
              <div>
                <div class="stafo-feature-title">Full Feature Access Included</div>
                <div class="stafo-feature-desc">AI Face-ID, GPS tracking, and automated statutory payroll.</div>
              </div>
            </div>

            <div class="stafo-feature-card">
              <div class="stafo-feature-icon-box">
                <i class="fa-solid fa-headset"></i>
              </div>
              <div>
                <div class="stafo-feature-title">Dedicated Onboarding Support</div>
                <div class="stafo-feature-desc">Free setup assistance from our product experts whenever you need.</div>
              </div>
            </div>
          </div>

          <div class="stafo-trust-strip">
            <div class="stafo-stat-item">
              <div class="stafo-stat-num">0 ₹</div>
              <div class="stafo-stat-label">No Card Required</div>
            </div>
            <div class="stafo-stat-item">
              <div class="stafo-stat-num">15 Days</div>
              <div class="stafo-stat-label">Free Unlimited</div>
            </div>
            <div class="stafo-stat-item">
              <div class="stafo-stat-num">100%</div>
              <div class="stafo-stat-label">Data Privacy</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Register Card -->
      <div class="col-lg-7 col-12 d-flex">
        <div class="stafo-register-box w-100 my-auto">
          
          <div class="stafo-portal-header">
            <div class="stafo-card-logo-badge">
              <img src="{{ asset('assets/images/icon/c_logo.png') }}" 
                   alt="STAFO Brand Logo" 
                   class="stafo-card-logo-img">
              <div class="text-start">
                <h3 class="stafo-brand-name">ST<span>Δ</span>FO</h3>
                <p class="stafo-brand-tagline">HRMS & Workforce Platform</p>
              </div>
            </div>
            <h2 class="stafo-portal-title">Create Your STAFO Account</h2>
            <p class="stafo-portal-subtitle">Set up your company workspace and start your 15-day free trial</p>
          </div>

          <form id="signup-form" method="post" action="{{ route('store') }}">
            @csrf
            <div class="messages"></div>

            <div class="row g-3">
              <!-- Full Name -->
              <div class="col-12">
                <label class="form-label fw-semibold small text-dark mb-1" for="Name">
                  Full Name <span class="text-danger">*</span>
                </label>
                <div class="stafo-input-group">
                  <input type="text" 
                         class="stafo-form-control @error('name') is-invalid @enderror" 
                         id="Name" 
                         placeholder="Enter your full name" 
                         name="name" 
                         value="{{ old('name') }}" 
                         required>
                  <i class="fa-solid fa-user stafo-input-icon"></i>
                </div>
                @error('name')
                  <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                @enderror
              </div>

              <!-- Corporate Email -->
              <div class="col-md-6 col-12">
                <label class="form-label fw-semibold small text-dark mb-1" for="Email">
                  Corporate Email <span class="text-danger">*</span>
                </label>
                <div class="stafo-input-group">
                  <input type="email" 
                         class="stafo-form-control @error('email') is-invalid @enderror" 
                         id="Email" 
                         placeholder="name@company.com" 
                         name="email" 
                         value="{{ old('email') }}" 
                         required>
                  <i class="fa-solid fa-envelope stafo-input-icon"></i>
                </div>
                @error('email')
                  <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                @enderror
              </div>

              <!-- Mobile Number -->
              <div class="col-md-6 col-12">
                <label class="form-label fw-semibold small text-dark mb-1" for="Number">
                  Mobile Number <span class="text-danger">*</span>
                </label>
                <div class="stafo-input-group">
                  <input type="text" 
                         class="stafo-form-control @error('mobile_no') is-invalid @enderror" 
                         id="Number" 
                         placeholder="10-digit mobile number" 
                         name="mobile_no" 
                         value="{{ old('mobile_no') }}" 
                         maxlength="10" 
                         oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                         required>
                  <i class="fa-solid fa-phone stafo-input-icon"></i>
                </div>
                @error('mobile_no')
                  <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                @enderror
              </div>

              <!-- Password -->
              <div class="col-md-6 col-12">
                <label class="form-label fw-semibold small text-dark mb-1" for="signup-password">
                  Password <span class="text-danger">*</span>
                </label>
                <div class="stafo-input-group">
                  <input type="password" 
                         class="stafo-form-control @error('password') is-invalid @enderror" 
                         id="signup-password" 
                         placeholder="Create secure password" 
                         name="password" 
                         required>
                  <i class="fa-solid fa-lock stafo-input-icon"></i>
                </div>
                @error('password')
                  <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                @enderror
              </div>

              <!-- Confirm Password -->
              <div class="col-md-6 col-12">
                <label class="form-label fw-semibold small text-dark mb-1" for="Com-password">
                  Confirm Password <span class="text-danger">*</span>
                </label>
                <div class="stafo-input-group">
                  <input type="password" 
                         class="stafo-form-control" 
                         id="Com-password" 
                         name="password_confirmation" 
                         placeholder="Re-type password" 
                         required>
                  <i class="fa-solid fa-shield stafo-input-icon"></i>
                </div>
                @error('password_confirmation')
                  <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                @enderror
              </div>

              <!-- Referral Code (Optional) -->
              <div class="col-12">
                <label class="form-label fw-semibold small text-dark mb-1" for="referral_code">
                  Referral Code <span class="text-muted fw-normal">(Optional)</span>
                </label>
                <div class="stafo-input-group">
                  <input type="text" 
                         name="referral_code" 
                         id="referral_code" 
                         class="stafo-form-control @error('referral_code') is-invalid @enderror" 
                         value="{{ old('referral_code') }}" 
                         placeholder="Enter referral or partner code if any">
                  <i class="fa-solid fa-tag stafo-input-icon"></i>
                </div>
                @error('referral_code')
                  <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                @enderror
              </div>

              <!-- Terms & Privacy Checkbox -->
              <div class="col-12 mt-3">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="customCheck1" required>
                  <label class="form-check-label small text-muted ms-1" for="customCheck1">
                    I agree to STAFO's <a href="#" class="text-primary text-decoration-none fw-semibold">Terms of Service</a> and <a href="#" class="text-primary text-decoration-none fw-semibold">Privacy Policy</a>
                  </label>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="col-12 mt-3">
                <button type="submit" class="stafo-btn-submit">
                  <span>Start 15-Day Free Trial</span>
                  <i class="fa-solid fa-arrow-right"></i>
                </button>
              </div>

            </div>
          </form>

          <!-- Existing Account Link -->
          <div class="text-center mt-3 pt-2">
            <span class="text-muted small">Already have an account?</span>
            <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none ms-1 small">
              Sign In <i class="fa-solid fa-arrow-right" style="font-size: 0.72rem;"></i>
            </a>
          </div>

          <!-- Security Badges Footer -->
          <div class="stafo-security-footer">
            <span><i class="fa-solid fa-lock text-primary"></i> 256-Bit SSL</span>
            <span>•</span>
            <span><i class="fa-solid fa-shield-check text-primary"></i> ISO 27001</span>
            <span>•</span>
            <span><i class="fa-solid fa-server text-primary"></i> MeitY Compliant</span>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>
@endsection

@section('js')
@endsection
