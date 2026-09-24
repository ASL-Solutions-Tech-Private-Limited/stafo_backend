<!-- Top Announcement Bar -->
<div class="stafo-top-announcement">
  <div class="container d-flex flex-wrap justify-content-between align-items-center py-1 px-3">
    <div class="d-flex align-items-center gap-2">
      <span class="stafo-badge-pill">✨ NEW</span>
      <span class="announcement-text">
        <strong>India's Next-Gen HRMS:</strong> AI Face-ID Punch & GPS Geofencing is live!
      </span>
    </div>
    <div class="d-none d-lg-flex align-items-center gap-3">
      <span class="announcement-text"><i class="fa-solid fa-gift text-warning me-1"></i> 15-Day Free Trial (No Card Required)</span>
      <span class="text-white-50">•</span>
      <a href="tel:+916292252470" class="announcement-call-link">
        <i class="fa-solid fa-phone me-1 text-warning"></i> +91 6292252470
      </a>
    </div>
  </div>
</div>

<!-- Main Sticky Header -->
<header class="site-header stafo-navbar-wrapper">
  <div class="container">
    <nav class="navbar navbar-expand-lg navbar-light py-2 d-flex align-items-center justify-content-between">
      
      <!-- Left: Logo & Category Chip -->
      <div class="d-flex align-items-center gap-2">
        <a class="navbar-brand d-flex align-items-center text-decoration-none m-0 p-0" href="{{ route('index') }}">
          <img src="{{ asset('assets/images/icon/c_logo.png') }}" width="50" height="50" alt="STAFO Logo" class="stafo-header-logo">
        </a>
        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fw-semibold d-none d-sm-inline-block" style="font-size: 0.72rem; letter-spacing: 0.3px;">
          <i class="fa-solid fa-shield-halved text-success me-1"></i> HRMS & Payroll
        </span>
      </div>

      <!-- Mobile Toggle Button -->
      <button class="navbar-toggler border-0 shadow-none d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#stafoNavCollapse"
        aria-controls="stafoNavCollapse" aria-expanded="false" aria-label="Toggle navigation"> 
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Center & Right: Collapsible Content -->
      <div class="collapse navbar-collapse justify-content-between" id="stafoNavCollapse">
        
        <!-- Center: Floating Glass Nav Capsule -->
        <div class="mx-auto my-2 my-lg-0">
          <div class="stafo-nav-capsule">
            <a class="stafo-capsule-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
              <i class="fa-solid fa-house-chimney nav-icon"></i>
              <span>Home</span>
            </a>
            <a class="stafo-capsule-link {{ request()->is('price') ? 'active' : '' }}" href="{{ route('price') }}">
              <i class="fa-solid fa-tags nav-icon"></i>
              <span>Pricing</span>
            </a>
            <a class="stafo-capsule-link {{ request()->is('about-us') ? 'active' : '' }}" href="{{ route('aboutUs') }}">
              <i class="fa-solid fa-building nav-icon"></i>
              <span>About Us</span>
            </a>
            <a class="stafo-capsule-link {{ request()->is('contact-us') ? 'active' : '' }}" href="{{ route('contactUs') }}">
              <i class="fa-solid fa-envelope nav-icon"></i>
              <span>Contact</span>
            </a>
          </div>
        </div>

        <!-- Right: Action CTAs -->
        <div class="d-flex align-items-center gap-2 pt-2 pt-lg-0">
          <a class="btn-signin-ghost" href="{{ route('login') }}">
            <i class="fa-solid fa-arrow-right-to-bracket"></i>
            <span>Sign In</span>
          </a>
          <button type="button" class="btn-demo-glow" data-bs-toggle="modal" data-bs-target="#callbackDemoModal">
            <i class="fa-regular fa-calendar-check"></i>
            <span>Book Free Demo</span>
            <i class="fa-solid fa-arrow-right arrow-icon"></i>
          </button>
        </div>

      </div>

    </nav>
  </div>
</header>

<style>
  /* ========================================================
     STAFO Header & Navigation Styles
     ======================================================== */
  .stafo-top-announcement {
    background: linear-gradient(90deg, #15152a 0%, #22204d 50%, #424096 100%);
    color: #ffffff;
    font-size: 0.8125rem;
    font-weight: 500;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  .stafo-badge-pill {
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff;
    font-size: 0.6875rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    padding: 2px 8px;
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    display: inline-block;
  }
  .announcement-text {
    color: #f1f5f9;
  }
  .announcement-call-link {
    color: #fef08a;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
  }
  .announcement-call-link:hover {
    color: #ffffff;
    text-decoration: underline;
  }

  /* Sticky Navbar Wrapper */
  .stafo-navbar-wrapper {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    position: sticky !important;
    top: 0 !important;
    z-index: 1050 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05) !important;
    transition: all 0.3s ease;
    padding: 0 !important;
  }

  .stafo-header-logo {
    object-fit: contain;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s ease;
  }
  .stafo-header-logo:hover {
    transform: scale(1.05);
  }

  /* Center Floating Glass Nav Capsule */
  .stafo-nav-capsule {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 9999px;
    padding: 4px 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
  }

  .stafo-capsule-link {
    padding: 7px 18px;
    border-radius: 9999px;
    font-size: 0.88rem;
    font-weight: 600;
    color: #475569;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap;
  }
  .stafo-capsule-link .nav-icon {
    font-size: 0.8rem;
    opacity: 0.7;
    transition: transform 0.2s ease, opacity 0.2s ease;
  }
  .stafo-capsule-link:hover {
    color: #0f172a;
    background: rgba(255, 255, 255, 0.7);
  }
  .stafo-capsule-link:hover .nav-icon {
    opacity: 1;
    transform: translateY(-1px);
  }
  .stafo-capsule-link.active {
    background: #ffffff;
    color: #424096;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(66, 64, 150, 0.12);
  }
  .stafo-capsule-link.active .nav-icon {
    color: #424096;
    opacity: 1;
  }

  /* Sign In Button */
  .btn-signin-ghost {
    color: #334155;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 8px 16px;
    border-radius: 9999px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    white-space: nowrap;
  }
  .btn-signin-ghost:hover {
    background: #f1f5f9;
    color: #424096;
  }

  /* Book Free Demo Glow Button */
  .btn-demo-glow {
    background: linear-gradient(135deg, #424096 0%, #5b57c7 100%);
    color: #ffffff !important;
    font-weight: 700;
    font-size: 0.875rem;
    padding: 9px 20px;
    border-radius: 9999px;
    border: none;
    box-shadow: 0 4px 14px -1px rgba(66, 64, 150, 0.4), 0 2px 4px -1px rgba(66, 64, 150, 0.2);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
  }
  .btn-demo-glow .arrow-icon {
    font-size: 0.72rem;
    transition: transform 0.2s ease;
  }
  .btn-demo-glow:hover {
    background: linear-gradient(135deg, #37357d 0%, #424096 100%);
    box-shadow: 0 6px 20px rgba(66, 64, 150, 0.5);
    transform: translateY(-1px);
    color: #ffffff !important;
  }
  .btn-demo-glow:hover .arrow-icon {
    transform: translateX(3px);
  }

  /* Responsive Mobile Menu */
  @media (max-width: 991.98px) {
    .stafo-nav-capsule {
      flex-direction: column;
      width: 100%;
      border-radius: 16px;
      padding: 8px;
      background: #f8fafc;
      box-shadow: none;
    }
    .stafo-capsule-link {
      width: 100%;
      padding: 10px 16px;
      border-radius: 12px;
      justify-content: flex-start;
    }
    .stafo-navbar-wrapper .navbar-collapse {
      background: #ffffff;
      padding: 16px;
      border-radius: 16px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
      margin-top: 10px;
      border: 1px solid #e2e8f0;
    }
  }
</style>