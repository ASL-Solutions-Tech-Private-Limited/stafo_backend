@extends('frontend.layouts.master')
@section('title', 'Sign In | STAFO - Enterprise HRMS & Payroll Platform')
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

  /* Subtle background ambient floating orbs */
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

  /* ========================================================
     Left Column: STAFO Brand Showcase Panel
     ======================================================== */
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

  .stafo-hrms-panel::before {
    content: '';
    position: absolute;
    bottom: -80px;
    left: -80px;
    width: 280px;
    height: 280px;
    background: radial-gradient(circle, rgba(66, 64, 150, 0.35), transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  /* Brand Header Badge */
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

  /* Interactive Feature Cards */
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
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .stafo-feature-card:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(4px);
    border-color: rgba(91, 87, 199, 0.5);
    box-shadow: 0 8px 24px -6px rgba(0, 0, 0, 0.3);
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

  /* Simulated Live Punch Pill (Floating Micro-Card) */
  .stafo-live-pill {
    background: rgba(15, 23, 42, 0.75);
    border: 1px solid rgba(91, 87, 199, 0.35);
    border-radius: 14px;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 16px 0 6px 0;
    backdrop-filter: blur(10px);
    animation: floatLivePill 4s ease-in-out infinite alternate;
  }

  @keyframes floatLivePill {
    0% { transform: translateY(0px); }
    100% { transform: translateY(-4px); }
  }

  .stafo-live-pill .pill-badge {
    background: rgba(22, 163, 106, 0.2);
    color: #34d399;
    border: 1px solid rgba(22, 163, 106, 0.4);
    font-size: 0.7rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 20px;
    letter-spacing: 0.3px;
  }

  /* Social Proof & Metrics Strip */
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
    font-size: 1.35rem;
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

  /* ========================================================
     Right Column: Ultra-Modern Login Card
     ======================================================== */
  .stafo-login-box {
    background: #ffffff;
    border-radius: 28px;
    padding: 44px 40px;
    box-shadow: 0 25px 60px -15px rgba(21, 21, 42, 0.12), 0 1px 3px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(231, 231, 240, 0.9);
    position: relative;
    z-index: 1;
  }

  /* Header Branding with STAFO Logo Icon */
  .stafo-portal-header {
    text-align: center;
    margin-bottom: 26px;
  }

  .stafo-card-logo-badge {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
  }

  .stafo-card-logo-img {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    box-shadow: 0 8px 18px -3px rgba(66, 64, 150, 0.25), 0 0 0 3px rgba(66, 64, 150, 0.12);
    object-fit: contain;
    transition: transform 0.3s ease;
  }

  .stafo-card-logo-img:hover {
    transform: scale(1.05) rotate(1deg);
  }

  .stafo-brand-text {
    text-align: left;
  }

  .stafo-brand-name {
    font-size: 1.45rem;
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
    font-size: 0.76rem;
    color: var(--stafo-slate-500);
    margin: 0;
    font-weight: 600;
    letter-spacing: 0.3px;
  }

  .stafo-portal-title {
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--stafo-slate-900);
    letter-spacing: -0.02em;
    margin-bottom: 6px;
  }

  .stafo-portal-subtitle {
    color: var(--stafo-slate-500);
    font-size: 0.91rem;
    margin-bottom: 0;
  }

  /* Segmented Method Selector Tabs */
  .stafo-auth-tabs {
    display: flex;
    background: #f1f5f9;
    border-radius: 14px;
    padding: 4px;
    margin-bottom: 22px;
    border: 1px solid #e2e8f0;
  }

  .stafo-auth-tab-btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 14px;
    font-size: 0.86rem;
    font-weight: 700;
    color: #475569;
    background: transparent;
    border: none;
    border-radius: 11px;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .stafo-auth-tab-btn.active {
    background: #ffffff;
    color: var(--primary);
    box-shadow: 0 3px 10px rgba(66, 64, 150, 0.12);
  }

  .stafo-auth-tab-btn i {
    font-size: 0.88rem;
  }

  .stafo-auth-tab-btn.active i {
    color: var(--primary);
  }

  /* Form Input Styling */
  .stafo-input-group {
    position: relative;
    display: flex;
    align-items: center;
  }

  .stafo-input-icon {
    position: absolute;
    left: 16px;
    color: #94a3b8;
    font-size: 1.05rem;
    pointer-events: none;
    z-index: 4;
    transition: color 0.2s ease;
  }

  .stafo-form-control {
    width: 100%;
    height: 52px;
    font-size: 0.96rem;
    border-radius: 14px;
    border: 1.5px solid #e2e8f0;
    padding: 12px 16px 12px 48px;
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

  .stafo-form-control.is-valid {
    border-color: var(--green);
    background-color: #ffffff;
  }

  .stafo-form-control.is-invalid {
    border-color: #ef4444;
    background-color: #ffffff;
  }

  .stafo-password-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 1.05rem;
    cursor: pointer;
    padding: 6px;
    z-index: 5;
    transition: color 0.2s ease;
  }

  .stafo-password-toggle:hover {
    color: var(--stafo-slate-900);
  }

  /* OTP Verification Wrapper */
  .stafo-otp-wrapper {
    background: linear-gradient(180deg, var(--primary-soft) 0%, #fbfbfe 100%);
    border: 1.5px dashed rgba(66, 64, 150, 0.35);
    border-radius: 18px;
    padding: 20px;
    margin-bottom: 20px;
  }

  .stafo-otp-input {
    letter-spacing: 12px;
    font-size: 1.5rem;
    font-weight: 800;
    text-align: center;
    height: 56px;
    color: var(--primary);
    border-radius: 14px;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    transition: all 0.2s ease;
  }

  .stafo-otp-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(66, 64, 150, 0.15);
    outline: none;
  }

  /* Action Buttons */
  .stafo-btn-submit {
    height: 52px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary2) 100%);
    color: #ffffff;
    font-weight: 700;
    font-size: 1.02rem;
    border: none;
    border-radius: 14px;
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

  .stafo-btn-submit:active {
    transform: translateY(0);
  }

  .stafo-btn-submit:disabled {
    opacity: 0.75;
    cursor: not-allowed;
  }

  /* Quick Demo Credentials Pill */
  .stafo-demo-strip {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 8px 14px;
    margin-top: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.78rem;
  }

  .stafo-demo-chip {
    background: var(--primary-soft);
    color: var(--primary);
    border: 1px solid rgba(66, 64, 150, 0.25);
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
  }

  .stafo-demo-chip:hover {
    background: var(--primary);
    color: #ffffff;
  }

  /* Security Badges Footer */
  .stafo-security-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    color: #94a3b8;
    font-size: 0.76rem;
    font-weight: 600;
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid #f1f5f9;
  }

  .stafo-security-footer span {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  /* Responsive Rules */
  @media (max-width: 991.98px) {
    .stafo-auth-wrapper {
      padding: 30px 0 50px 0;
    }
    .stafo-hrms-panel {
      margin-bottom: 24px;
      padding: 34px 26px;
    }
    .stafo-login-box {
      padding: 32px 24px;
    }
    .stafo-panel-title {
      font-size: 1.75rem;
    }
  }
</style>
@endsection

@section('content') 

      <!-- HRMS Login Section -->
      <section class="stafo-auth-wrapper">
        <div class="container position-relative" style="z-index: 1;">
          <div class="row align-items-stretch justify-content-center g-4 g-lg-5">
            
            <!-- Left: STAFO Brand Platform Showcase -->
            <div class="col-lg-6 col-12 d-flex">
              <div class="stafo-hrms-panel w-100">
                <div>
                  
                  <!-- Brand Pill Badge -->
                  <div class="stafo-brand-chip">
                    <span class="pulse-dot"></span>
                    <i class="fa-solid fa-cloud text-info"></i> STAFO Cloud HRMS Platform
                  </div>

                  <h1 class="stafo-panel-title">
                    Empower Your Workforce. <br>
                    <span class="stafo-text-gradient">Automate Everything.</span>
                  </h1>
                  
                  <p class="stafo-panel-sub">
                    India's premier synchronized Web & Mobile HRMS for Attendance, Geofencing, Automated Payroll, and Employee Self-Service.
                  </p>

                  <!-- Live Punch Simulation Micro-Card -->
                  <div class="stafo-live-pill">
                    <div class="d-flex align-items-center gap-2.5">
                      <i class="fa-solid fa-fingerprint text-success fs-5"></i>
                      <div>
                        <div class="fw-bold text-white small" style="line-height: 1.2;">Live Attendance Verified</div>
                        <div class="text-white-50" style="font-size: 0.72rem;">GPS Geofence + AI Face-ID Punch</div>
                      </div>
                    </div>
                    <span class="pill-badge"><i class="fa-solid fa-circle-check me-1"></i>100% Proxy-Free</span>
                  </div>

                  <!-- Key STAFO Highlights -->
                  <div class="stafo-feature-card">
                    <div class="stafo-feature-icon-box">
                      <i class="fa-solid fa-camera-retro"></i>
                    </div>
                    <div>
                      <div class="stafo-feature-title">AI Face-ID & Geo-Fenced Punch</div>
                      <div class="stafo-feature-desc">100% proxy-free biometric mobile & kiosk check-ins with precise GPS tracking.</div>
                    </div>
                  </div>

                  <div class="stafo-feature-card">
                    <div class="stafo-feature-icon-box">
                      <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                      <div class="stafo-feature-title">1-Click Statutory Compliant Payroll</div>
                      <div class="stafo-feature-desc">Automated PF, ESIC, PT, TDS calculations with instant encrypted digital payslips.</div>
                    </div>
                  </div>

                  <div class="stafo-feature-card">
                    <div class="stafo-feature-icon-box">
                      <i class="fa-solid fa-mobile-screen-button"></i>
                    </div>
                    <div>
                      <div class="stafo-feature-title">24/7 Employee Self-Service</div>
                      <div class="stafo-feature-desc">Seamless leave workflows, shift swaps, expense claims, and document downloads.</div>
                    </div>
                  </div>
                </div>

                <!-- Live Social Proof & Trust Strip -->
                <div class="stafo-trust-strip">
                  <div class="stafo-stat-item">
                    <div class="stafo-stat-num">32+</div>
                    <div class="stafo-stat-label">Enterprises</div>
                  </div>
                  <div class="stafo-stat-item">
                    <div class="stafo-stat-num">10,000+</div>
                    <div class="stafo-stat-label">Daily Punches</div>
                  </div>
                  <div class="stafo-stat-item">
                    <div class="stafo-stat-num">99.99%</div>
                    <div class="stafo-stat-label">Uptime SLA</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right: Interactive Login Card -->
            <div class="col-lg-6 col-12 d-flex">
              <div class="stafo-login-box w-100 my-auto">
                
                <!-- Card Header with Official STAFO Logo -->
                <div class="stafo-portal-header">
                  <div class="stafo-card-logo-badge">
                    <img src="{{ asset('assets/images/icon/c_logo.png') }}" 
                         alt="STAFO Official Brand Logo" 
                         class="stafo-card-logo-img">
                    <div class="stafo-brand-text">
                      <h3 class="stafo-brand-name">ST<span>Δ</span>FO</h3>
                      <p class="stafo-brand-tagline">HRMS & Workforce Platform</p>
                    </div>
                  </div>
                  <h2 class="stafo-portal-title">Sign In to STAFO</h2>
                  <p class="stafo-portal-subtitle">Unified Portal for Company Administrators & Employees</p>
                </div>

                <!-- Sign-In Method Segmented Tabs -->
                <div class="stafo-auth-tabs">
                  <button type="button" class="stafo-auth-tab-btn active" id="tab_mobile">
                    <i class="fa-solid fa-mobile-screen"></i>
                    <span>Mobile & OTP</span>
                  </button>
                  <button type="button" class="stafo-auth-tab-btn" id="tab_email">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Corporate Email</span>
                  </button>
                </div>

                <!-- Login Form -->
                <form id="login-form" action="{{ route('authenticate') }}" method="post">
                  @csrf
                  <div class="messages"></div>

                  <!-- Universal Input (Mobile / Email) -->
                  <div class="form-group mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label for="email" class="form-label fw-semibold small text-dark mb-0" id="email_label">
                        Mobile Number or Corporate Email <span class="text-danger">*</span>
                      </label>
                      <span id="input_mode_badge" class="badge bg-light text-secondary border fw-medium" style="font-size: 0.7rem;">
                        <i class="fa-solid fa-wand-magic-sparkles text-primary me-1"></i>Auto-Detect
                      </span>
                    </div>
                    
                    <div class="stafo-input-group">
                      <input type="text" 
                             class="stafo-form-control @error('email') is-invalid @enderror" 
                             id="email"
                             name="email" 
                             placeholder="Enter 10-digit mobile or company email" 
                             value="{{ old('email') }}" 
                             autocomplete="username"
                             required>
                      <i class="fa-solid fa-mobile-screen stafo-input-icon" id="main_input_icon"></i>
                    </div>
                    
                    <div id="email_validation_msg" class="small mt-1.5" style="display:none;"></div>
                    
                    @if ($errors->has('email'))
                      <div class="text-danger small mt-1.5 fw-medium">
                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first('email') }}
                      </div>
                    @endif
                  </div>

                  <!-- Step 2A: Password Field (Visible for Corporate Email Login) -->
                  <div class="form-group mb-3" id="password_field" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <label for="password" class="form-label fw-semibold small text-dark mb-0">Password <span class="text-danger">*</span></label>
                      <a href="#" class="small text-decoration-none text-muted" style="font-size: 0.8rem;">Forgot Password?</a>
                    </div>
                    <div class="stafo-input-group">
                      <input type="password" 
                             class="stafo-form-control @error('password') is-invalid @enderror"
                             id="password" 
                             name="password" 
                             placeholder="Enter your account password"
                             style="padding-right: 46px;">
                      <i class="fa-solid fa-lock stafo-input-icon"></i>
                      <button class="stafo-password-toggle" type="button" id="toggle-password" title="Toggle password visibility">
                        <i class="fa-solid fa-eye-slash"></i>
                      </button>
                    </div>
                    @if ($errors->has('password'))
                      <div class="text-danger small mt-1.5 fw-medium">
                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first('password') }}
                      </div>
                    @endif
                  </div>

                  <!-- Step 2B: OTP Field (Visible for Mobile Number Login) -->
                  <div class="stafo-otp-wrapper" id="otp_field" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="fw-bold small text-dark">
                        <i class="fa-solid fa-shield-halved text-primary me-1"></i> 6-Digit OTP Verification
                      </span>
                      <a href="javascript:void(0)" class="small text-primary text-decoration-none fw-semibold" id="edit_phone_btn">
                        <i class="fa-solid fa-pen-to-square me-1"></i>Change Number
                      </a>
                    </div>
                    <div>
                      <input type="text" 
                             class="form-control stafo-otp-input" 
                             id="otp" 
                             name="otp" 
                             placeholder="••••••" 
                             maxlength="6" 
                             inputmode="numeric" 
                             autocomplete="one-time-code">
                      @if ($errors->has('otp'))
                        <span class="text-danger small mt-1 d-block">{{ $errors->first('otp') }}</span>
                      @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2.5 pt-1">
                      <small class="text-muted">Enter the 6-digit OTP received via SMS.</small>
                      <a href="javascript:void(0)" class="text-primary small text-decoration-none fw-semibold switch-to-otp" id="resend_otp_link">
                        <i class="fa-solid fa-arrows-rotate me-1"></i>Resend OTP
                      </a>
                    </div>
                  </div>

                  <!-- Form Action Buttons -->
                  <div class="mt-4">
                    <button type="button" class="stafo-btn-submit" id="submit_button">
                      <span>Continue</span> <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="stafo-btn-submit" id="login_button" style="display:none;">
                      <i class="fa-solid fa-right-to-bracket"></i> <span>Sign In to Dashboard</span>
                    </button>
                  </div>
                </form>

                <!-- Quick Demo Login Helper for Evaluators / Test Visitors -->
                <div class="stafo-demo-strip">
                  <span class="text-muted"><i class="fa-solid fa-flask text-warning me-1"></i> Instant Demo:</span>
                  <a href="javascript:void(0)" class="stafo-demo-chip" id="fill_demo_mobile" title="Click to auto-test with demo phone">
                    <i class="fa-solid fa-bolt text-warning me-1"></i>Use Demo: 9999999999
                  </a>
                </div>

                <!-- New Account Link -->
                <div class="text-center mt-3 pt-2">
                  <span class="text-muted small">New to STAFO HRMS?</span>
                  <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none ms-1 small">
                    Start 15-Day Free Trial <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.72rem;"></i>
                  </a>
                </div>

                <!-- Security Trust Footer -->
                <div class="stafo-security-footer">
                  <span><i class="fa-solid fa-lock text-primary"></i> 256-Bit SSL</span>
                  <span>•</span>
                  <span><i class="fa-solid fa-shield-check text-primary"></i> ISO 27001</span>
                  <span>•</span>
                  <span><i class="fa-solid fa-server text-primary"></i> MeitY Compliant Cloud</span>
                </div>

              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- HRMS Login End -->

@endsection

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@include('sweetalert::alert')
<script>
    // Password visibility toggle
    $('#toggle-password').click(function () {
        var passwordField = $('#password');
        var icon = $(this).find('i');

        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });

    // Mobile / Email validation helper function
    function validateInput(onBlurOrSubmit) {
        var rawVal = $('#email').val() || '';
        var trimmed = rawVal.trim();
        var $input = $('#email');
        var $msg = $('#email_validation_msg');
        var $icon = $('#main_input_icon');
        var $badge = $('#input_mode_badge');

        // Empty state
        if (trimmed === '') {
            $input.removeAttr('maxlength').removeAttr('pattern').attr('inputmode', 'text');
            $input.removeClass('is-valid is-invalid');
            $msg.hide().empty();
            $badge.html('<i class="fa-solid fa-wand-magic-sparkles text-primary me-1"></i>Auto-Detect');
            return { type: 'empty', valid: false };
        }

        // Detect if user typed a mobile number (starts with digit or +)
        var isPhone = /^[0-9+]/.test(trimmed);

        if (isPhone) {
            // Highlight Mobile Tab
            $('#tab_mobile').addClass('active');
            $('#tab_email').removeClass('active');
            $icon.attr('class', 'fa-solid fa-mobile-screen stafo-input-icon');
            $badge.html('<i class="fa-solid fa-mobile-screen text-primary me-1"></i>Mobile Mode');

            // Apply mobile validation attributes
            $input.attr('maxlength', '10');
            $input.attr('inputmode', 'numeric');
            $input.attr('pattern', '[0-9]{10}');
            $input.attr('title', 'Please enter a 10-digit mobile number');

            // Filter out non-numeric characters
            var digits = trimmed.replace(/\D/g, '');

            // Handle country code (+91) or leading 0 if pasted
            if (digits.length > 10) {
                if (digits.startsWith('91')) {
                    digits = digits.substring(2);
                } else if (digits.startsWith('0')) {
                    digits = digits.substring(1);
                }
            }
            digits = digits.slice(0, 10);

            if (rawVal !== digits) {
                $input.val(digits);
            }

            // Check Indian mobile validation rules (starts with 6, 7, 8, or 9)
            if (digits.length === 10) {
                if (/^[6-9][0-9]{9}$/.test(digits)) {
                    $input.removeClass('is-invalid').addClass('is-valid');
                    $msg.removeClass('text-danger text-muted').addClass('text-success')
                        .html('<i class="fa-solid fa-circle-check me-1"></i> Valid 10-digit mobile number').show();
                    return { type: 'mobile', valid: true, value: digits };
                } else {
                    $input.removeClass('is-valid').addClass('is-invalid');
                    $msg.removeClass('text-success text-muted').addClass('text-danger')
                        .html('<i class="fa-solid fa-circle-exclamation me-1"></i> Mobile number should start with 6, 7, 8, or 9').show();
                    return { type: 'mobile', valid: false, value: digits, message: 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.' };
                }
            } else {
                $input.removeClass('is-valid');
                if (onBlurOrSubmit) {
                    $input.addClass('is-invalid');
                    $msg.removeClass('text-success text-muted').addClass('text-danger')
                        .html('<i class="fa-solid fa-circle-exclamation me-1"></i> Please enter a complete 10-digit mobile number (' + digits.length + '/10)').show();
                } else {
                    $input.removeClass('is-invalid');
                    $msg.removeClass('text-danger text-success').addClass('text-muted')
                        .html('<i class="fa-solid fa-mobile-screen me-1"></i> Mobile number: ' + digits.length + '/10 digits').show();
                }
                return { type: 'mobile', valid: false, value: digits, message: 'Please enter a valid 10-digit mobile number.' };
            }
        } else {
            // Email mode
            $('#tab_email').addClass('active');
            $('#tab_mobile').removeClass('active');
            $icon.attr('class', 'fa-solid fa-envelope stafo-input-icon');
            $badge.html('<i class="fa-solid fa-envelope text-primary me-1"></i>Email Mode');

            $input.removeAttr('maxlength').removeAttr('pattern').attr('inputmode', 'email');
            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (emailPattern.test(trimmed)) {
                $input.removeClass('is-invalid').addClass('is-valid');
                $msg.removeClass('text-danger text-muted').addClass('text-success')
                    .html('<i class="fa-solid fa-circle-check me-1"></i> Valid corporate email format').show();
                return { type: 'email', valid: true, value: trimmed };
            } else {
                $input.removeClass('is-valid');
                if (onBlurOrSubmit && trimmed.length > 0) {
                    $input.addClass('is-invalid');
                    $msg.removeClass('text-success text-muted').addClass('text-danger')
                        .html('<i class="fa-solid fa-circle-exclamation me-1"></i> Please enter a valid corporate email address').show();
                } else {
                    $input.removeClass('is-invalid');
                    $msg.hide().empty();
                }
                return { type: 'email', valid: false, value: trimmed, message: 'Please enter a valid corporate email address.' };
            }
        }
    }

    $(document).ready(function() {
        // Tab Clicks
        $('#tab_mobile').click(function() {
            $(this).addClass('active');
            $('#tab_email').removeClass('active');
            $('#main_input_icon').attr('class', 'fa-solid fa-mobile-screen stafo-input-icon');
            $('#email').attr('placeholder', 'Enter 10-digit mobile number');
            $('#input_mode_badge').html('<i class="fa-solid fa-mobile-screen text-primary me-1"></i>Mobile Mode');
            if ($('#email').val().length > 0 && isNaN($('#email').val())) {
                $('#email').val('');
            }
            $('#email').focus();
        });

        $('#tab_email').click(function() {
            $(this).addClass('active');
            $('#tab_mobile').removeClass('active');
            $('#main_input_icon').attr('class', 'fa-solid fa-envelope stafo-input-icon');
            $('#email').attr('placeholder', 'Enter corporate email (e.g. admin@company.com)');
            $('#input_mode_badge').html('<i class="fa-solid fa-envelope text-primary me-1"></i>Email Mode');
            if ($('#email').val().length > 0 && !isNaN($('#email').val())) {
                $('#email').val('');
            }
            $('#email').focus();
        });

        // Quick demo filler
        $('#fill_demo_mobile').click(function() {
            $('#email').val('9999999999');
            validateInput(false);
            $('#submit_button').click();
        });

        // Initial check if value exists
        if ($('#email').val().trim() !== '') {
            validateInput(false);
        }

        // Real-time input validation
        $('#email').on('input keyup paste', function() {
            validateInput(false);
        });

        $('#email').on('blur', function() {
            validateInput(true);
        });

        // If user changes email/phone after password or OTP is open, reset view to step 1
        $('#email').on('input', function() {
            if ($('#password_field').is(':visible') || $('#otp_field').is(':visible')) {
                $('#password_field').hide();
                $('#otp_field').hide();
                $('#login_button').hide();
                $('#submit_button').show();
            }
        });

        // Allow changing number from OTP view
        $('#edit_phone_btn').click(function() {
            $('#otp_field').slideUp();
            $('#login_button').hide();
            $('#submit_button').show();
            $('#email').focus();
        });

        // Restrict OTP field to digits only
        $('#otp').on('input keyup paste', function() {
            var digits = $(this).val().replace(/\D/g, '').slice(0, 6);
            $(this).val(digits);
        });

        // Form submission interceptor
        $('#login-form').on('submit', function(e) {
            if ($('#submit_button').is(':visible')) {
                e.preventDefault();
                $('#submit_button').click();
                return false;
            }

            if ($('#otp_field').is(':visible')) {
                var otp = $('#otp').val().trim();
                if (!otp) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'OTP Required',
                        text: 'Please enter the 6-digit OTP sent to your registered mobile number.',
                        icon: 'warning',
                        confirmButtonColor: '#424096'
                    });
                    $('#otp').focus();
                    return false;
                }
            }

            if ($('#password_field').is(':visible')) {
                var pwd = $('#password').val();
                if (!pwd) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Password Required',
                        text: 'Please enter your account password.',
                        icon: 'warning',
                        confirmButtonColor: '#424096'
                    });
                    $('#password').focus();
                    return false;
                }
            }
        });

        // Submit Step 1 Button
        $('#submit_button').click(function() {
            var result = validateInput(true);

            if (result.type === 'empty') {
                Swal.fire({
                    title: '',
                    text: "Please enter your registered 10-digit mobile number or corporate email.",
                    icon: 'warning',
                    confirmButtonColor: '#424096'
                });
                $('#email').focus();
                return false;
            }

            // Mobile number mode
            if (result.type === 'mobile') {
                if (!result.valid) {
                    Swal.fire({
                        title: 'Invalid Mobile Number',
                        text: result.message || "Please enter a valid 10-digit mobile number.",
                        icon: 'warning',
                        confirmButtonColor: '#424096'
                    });
                    $('#email').focus();
                    return false;
                }

                var phone = result.value;
                var $btn = $(this);
                $btn.html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Sending OTP...').prop('disabled', true);

                $.ajax({
                    url: "{{ route('sendOtp') }}",
                    type: 'POST',
                    data: {
                        mobile_number: phone,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $btn.html('<span>Continue</span> <i class="fa-solid fa-arrow-right"></i>').prop('disabled', false);
                        if (response.success == true) {
                            $('#otp_field').slideDown();
                            $('#login_button').show();
                            $btn.hide();

                            var successMsg = response.message || 'OTP sent successfully to your registered number.';
                            if (response.otp && (phone === '8888888888' || phone === '9999999999')) {
                                $('#otp').val(response.otp);
                                successMsg += ' (Auto-filled Demo OTP: ' + response.otp + ')';
                            }

                            Swal.fire({
                                title: 'OTP Sent!',
                                text: successMsg,
                                icon: 'success',
                                timer: 3500,
                                timerProgressBar: true,
                                confirmButtonColor: '#424096'
                            });
                            $('#otp').focus();
                        } else {
                            Swal.fire({
                                title: 'Account Not Found!',
                                text: response.message || "No registered company or active employee account found with this number.",
                                icon: 'warning',
                                confirmButtonColor: '#424096'
                            });
                        }
                    },
                    error: function() {
                        $btn.html('<span>Continue</span> <i class="fa-solid fa-arrow-right"></i>').prop('disabled', false);
                        Swal.fire({
                            title: 'Network Error',
                            text: "Failed to send OTP. Please check your network connection and try again.",
                            icon: 'error',
                            confirmButtonColor: '#424096'
                        });
                    }
                });
            }
            // Email mode
            else if (result.type === 'email') {
                if (!result.valid) {
                    Swal.fire({
                        title: 'Invalid Email!',
                        text: result.message || "Please enter a valid corporate email address.",
                        icon: 'warning',
                        confirmButtonColor: '#424096'
                    });
                    $('#email').focus();
                    return false;
                }

                $('#password_field').slideDown();
                $('#login_button').show();
                $('#submit_button').hide();
                $('#password').focus();
            }
        });

        // Resend OTP handler
        $('#login-form').on('click', '.switch-to-otp, #resend_otp_link', function() {
            var phone = $('#email').val().trim();
            if (!phone) return;
            var $link = $(this);
            $link.html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Resending...');
            $.ajax({
                url: "{{ route('sendOtp') }}",
                type: 'POST',
                data: {
                    mobile_number: phone,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $link.html('<i class="fa-solid fa-arrows-rotate me-1"></i>Resend OTP');
                    if (response.success == true) {
                        var successMsg = response.message || 'OTP sent to registered mobile.';
                        if (response.otp && (phone === '8888888888' || phone === '9999999999')) {
                            $('#otp').val(response.otp);
                            successMsg += ' (Demo OTP: ' + response.otp + ')';
                        }
                        Swal.fire({
                            title: 'OTP Resent!',
                            text: successMsg,
                            icon: 'success',
                            confirmButtonColor: '#424096'
                        });
                    } else {
                        Swal.fire({
                            title: 'Notice',
                            text: response.message || 'Unable to resend OTP.',
                            icon: 'warning',
                            confirmButtonColor: '#424096'
                        });
                    }
                },
                error: function() {
                    $link.html('<i class="fa-solid fa-arrows-rotate me-1"></i>Resend OTP');
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to resend OTP. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#424096'
                    });
                }
            });
        });
    });
</script>
@endsection