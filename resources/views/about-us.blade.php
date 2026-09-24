@extends('frontend.layouts.master') 
@section('title', 'About Us | STAFO - Leading HRMS Solution Provider in India')
@section('heading', 'About Us')

@section('css')
<style>
  :root {
    --primary: #424096;
    --primary2: #5b57c7;
    --primary-soft: #f0efff;
    --ink: #17172a;
    --muted: #68687a;
    --soft: #f6f6fb;
    --line: #e7e7f0;
    --white: #ffffff;
    --green: #16a36a;
    --shadow-sm: 0 4px 20px rgba(66, 64, 150, 0.06);
    --shadow-md: 0 12px 35px rgba(66, 64, 150, 0.10);
    --shadow-lg: 0 24px 60px rgba(45, 42, 110, 0.14);
    --radius-sm: 12px;
    --radius-md: 18px;
    --radius-lg: 24px;
  }

  /* Page Wrapper */
  .stafo-about-page {
    font-family: 'Plus Jakarta Sans', Inter, -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--ink);
    background: #ffffff;
    overflow-x: hidden;
  }

  /* Pill Eyebrow */
  .stafo-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 18px;
    border-radius: 999px;
    background: var(--primary-soft);
    color: var(--primary);
    border: 1px solid rgba(66, 64, 150, 0.16);
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    margin-bottom: 18px;
  }

  /* Hero Section */
  .about-hero {
    padding: 70px 0 60px;
    background: radial-gradient(circle at 10% 20%, rgba(66, 64, 150, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(91, 87, 199, 0.06) 0%, transparent 50%),
                #ffffff;
    position: relative;
  }

  .about-hero-title {
    font-size: clamp(2.2rem, 4vw, 3.2rem);
    font-weight: 800;
    line-height: 1.18;
    color: var(--ink);
    margin-bottom: 20px;
    letter-spacing: -0.025em;
  }

  .about-hero-title .gradient-text {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary2) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .about-hero-lead {
    font-size: 1.12rem;
    color: var(--muted);
    line-height: 1.7;
    margin-bottom: 30px;
    max-width: 580px;
  }

  .about-hero-badge-strip {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
  }

  .about-hero-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid var(--line);
    padding: 9px 16px;
    border-radius: 12px;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--ink);
    box-shadow: var(--shadow-sm);
  }

  .about-hero-tag i {
    color: var(--green);
  }

  /* Visual Frame with Glow */
  .about-visual-frame {
    position: relative;
    padding: 14px;
    background: linear-gradient(135deg, rgba(66, 64, 150, 0.15) 0%, rgba(91, 87, 199, 0.08) 100%);
    border-radius: var(--radius-lg);
    border: 1px solid rgba(66, 64, 150, 0.18);
  }

  .about-visual-frame img {
    border-radius: calc(var(--radius-lg) - 6px);
    width: 100%;
    height: auto;
    object-fit: cover;
    box-shadow: var(--shadow-md);
  }

  .floating-stat-badge {
    position: absolute;
    bottom: -15px;
    left: 25px;
    background: #ffffff;
    border-radius: var(--radius-md);
    padding: 14px 22px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--line);
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .floating-stat-badge .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
  }

  /* Stats Metric Strip */
  .about-stats-section {
    padding: 45px 0 50px;
    background: var(--soft);
    border-top: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
  }

  .stat-metric-card {
    background: #ffffff;
    border-radius: var(--radius-md);
    padding: 26px 20px;
    border: 1px solid var(--line);
    box-shadow: var(--shadow-sm);
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
  }

  .stat-metric-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: rgba(66, 64, 150, 0.3);
  }

  .stat-metric-num {
    font-size: 2.3rem;
    font-weight: 800;
    color: var(--primary);
    line-height: 1.1;
    margin-bottom: 6px;
    letter-spacing: -0.02em;
  }

  .stat-metric-label {
    font-size: 0.92rem;
    color: var(--muted);
    font-weight: 600;
    margin: 0;
  }

  /* Mission & Vision Section */
  .mission-vision-section {
    padding: 90px 0 80px;
    background: #ffffff;
  }

  .mv-card {
    border-radius: var(--radius-lg);
    padding: 40px 36px;
    border: 1px solid var(--line);
    background: #ffffff;
    box-shadow: var(--shadow-sm);
    height: 100%;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .mv-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--primary2) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .mv-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: rgba(66, 64, 150, 0.25);
  }

  .mv-card:hover::before {
    opacity: 1;
  }

  .mv-icon {
    width: 58px;
    height: 58px;
    border-radius: 16px;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
    margin-bottom: 22px;
  }

  .mv-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--ink);
    margin-bottom: 14px;
    letter-spacing: -0.015em;
  }

  .mv-text {
    font-size: 0.98rem;
    color: var(--muted);
    line-height: 1.7;
    margin: 0;
  }

  /* Core Values Accordion + Cards */
  .values-section {
    padding: 85px 0;
    background: #fbfbfe;
    border-top: 1px solid var(--line);
  }

  .section-header {
    margin-bottom: 50px;
  }

  .section-title {
    font-size: clamp(2rem, 3.2vw, 2.6rem);
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -0.02em;
    margin-bottom: 14px;
  }

  .section-sub {
    font-size: 1.05rem;
    color: var(--muted);
    max-width: 620px;
    line-height: 1.65;
  }

  .values-accordion .accordion-item {
    border: 1px solid var(--line);
    border-radius: 16px !important;
    margin-bottom: 14px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: var(--shadow-sm);
  }

  .values-accordion .accordion-button {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--ink);
    background: #ffffff;
    padding: 18px 24px;
    box-shadow: none !important;
  }

  .values-accordion .accordion-button:not(.collapsed) {
    color: var(--primary);
    background: var(--primary-soft);
  }

  .values-accordion .accordion-body {
    padding: 18px 24px 22px;
    color: var(--muted);
    font-size: 0.95rem;
    line-height: 1.7;
  }

  .value-feature-box {
    background: #ffffff;
    border-radius: var(--radius-md);
    padding: 28px 24px;
    border: 1px solid var(--line);
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    height: 100%;
  }

  .value-feature-box:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: rgba(66, 64, 150, 0.25);
  }

  .value-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 13px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary2) 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.18rem;
    margin-bottom: 18px;
    box-shadow: 0 8px 18px rgba(66, 64, 150, 0.22);
  }

  .value-feature-title {
    font-size: 1.12rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 8px;
  }

  .value-feature-desc {
    font-size: 0.9rem;
    color: var(--muted);
    line-height: 1.6;
    margin: 0;
  }

  /* Why Choose STAFO 4-Grid */
  .why-choose-section {
    padding: 90px 0 100px;
    background: #ffffff;
  }

  .why-card {
    background: #ffffff;
    border: 1px solid var(--line);
    border-radius: var(--radius-md);
    padding: 34px 24px;
    text-align: center;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    height: 100%;
  }

  .why-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: rgba(66, 64, 150, 0.3);
  }

  .why-img-wrap {
    height: 75px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 22px;
  }

  .why-img-wrap img {
    max-height: 60px;
    max-width: 100%;
    object-fit: contain;
  }

  .why-badge {
    display: inline-block;
    font-size: 0.76rem;
    font-weight: 700;
    color: var(--primary);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 8px;
  }

  .why-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--ink);
    margin-bottom: 10px;
  }

  .why-desc {
    font-size: 0.88rem;
    color: var(--muted);
    line-height: 1.65;
    margin: 0;
  }

  /* Bottom CTA Banner */
  .about-cta-strip {
    background: linear-gradient(135deg, #15152a 0%, #22204d 50%, #424096 100%);
    border-radius: var(--radius-lg);
    padding: 55px 45px;
    color: #ffffff;
    box-shadow: 0 25px 60px -15px rgba(21, 21, 42, 0.4);
    position: relative;
    overflow: hidden;
    margin-bottom: 70px;
  }

  .about-cta-strip::after {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 260px;
    height: 260px;
    background: radial-gradient(circle, rgba(91, 87, 199, 0.4), transparent 70%);
    border-radius: 50%;
  }

  .cta-heading {
    font-size: clamp(1.8rem, 3vw, 2.3rem);
    font-weight: 800;
    line-height: 1.25;
    margin-bottom: 12px;
  }

  .cta-sub {
    font-size: 1.02rem;
    color: #cbd5e1;
    margin-bottom: 0;
    max-width: 560px;
  }

  .btn-stafo-white {
    background: #ffffff;
    color: var(--primary);
    font-weight: 700;
    font-size: 0.98rem;
    padding: 13px 26px;
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }

  .btn-stafo-white:hover {
    background: #f0efff;
    color: var(--primary2);
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.22);
  }

  .btn-stafo-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.25);
    font-weight: 700;
    font-size: 0.98rem;
    padding: 13px 26px;
    border-radius: 12px;
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }

  .btn-stafo-outline:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    transform: translateY(-2px);
  }

  @media (max-width: 991.98px) {
    .about-hero {
      padding: 45px 0 40px;
    }
    .about-visual-frame {
      margin-top: 35px;
    }
    .about-cta-strip {
      padding: 38px 26px;
      text-align: center;
    }
    .about-cta-strip .d-flex {
      justify-content: center !important;
      margin-top: 24px;
    }
  }
</style>
@endsection

@section('content')
<div class="stafo-about-page">

  <!-- Hero Section -->
  <section class="about-hero">
    <div class="container">
      <div class="row align-items-center justify-content-between g-5">
        <div class="col-12 col-lg-6">
          <div class="stafo-pill">
            <i class="fa-solid fa-layer-group"></i> About STAFO HRMS
          </div>
          <h1 class="about-hero-title">
            India's Trusted Platform for <br>
            <span class="gradient-text">Smart Workforce Management</span>
          </h1>
          <p class="about-hero-lead">
            STAFO is a unified, cloud-first HRMS designed for Indian enterprises, SMEs, and fast-growing teams. From AI Face-ID biometric attendance to 1-click statutory payroll and GPS field operations, we eliminate manual chaos and empower modern workplaces.
          </p>

          <div class="about-hero-badge-strip">
            <div class="about-hero-tag">
              <i class="fa-solid fa-circle-check"></i>
              <span>100% Statutory Compliant</span>
            </div>
            <div class="about-hero-tag">
              <i class="fa-solid fa-circle-check"></i>
              <span>Proxy-Free Attendance</span>
            </div>
            <div class="about-hero-tag">
              <i class="fa-solid fa-circle-check"></i>
              <span>Made in India, for India</span>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-6">
          <div class="about-visual-frame">
            <img src="{{ asset('assets/images/about/01.png') }}" alt="STAFO Smart Workforce Management" class="img-fluid">
            
            <div class="floating-stat-badge d-none d-sm-flex">
              <div class="stat-icon">
                <i class="fa-solid fa-award"></i>
              </div>
              <div>
                <div class="fw-bold" style="font-size: 1rem; color: var(--ink);">#1 Choice for Growing Teams</div>
                <div class="text-muted" style="font-size: 0.8rem;">Trusted by Indian Startups & SMEs</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Metric Strip -->
  <section class="about-stats-section">
    <div class="container">
      <div class="row g-4">
        <div class="col-6 col-lg-3">
          <div class="stat-metric-card">
            <div class="stat-metric-num">10,000+</div>
            <p class="stat-metric-label">Active Punches Daily</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-metric-card">
            <div class="stat-metric-num">99.9%</div>
            <p class="stat-metric-label">Platform Uptime SLA</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-metric-card">
            <div class="stat-metric-num">24/7</div>
            <p class="stat-metric-label">Priority Support Available</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-metric-card">
            <div class="stat-metric-num">15 Days</div>
            <p class="stat-metric-label">Free Unlimited Trial</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Mission & Vision -->
  <section class="mission-vision-section">
    <div class="container">
      <div class="row justify-content-center text-center mb-5">
        <div class="col-lg-8">
          <div class="stafo-pill">
            <i class="fa-solid fa-compass"></i> Purpose & Ambition
          </div>
          <h2 class="section-title">Empowering Every Indian Business to Scale</h2>
          <p class="section-sub mx-auto">
            Our driving goal is to democratize enterprise-grade HR technology so that companies of all sizes can operate smoothly, compliantly, and transparently.
          </p>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-md-6">
          <div class="mv-card">
            <div class="mv-icon">
              <i class="fa-solid fa-bullseye"></i>
            </div>
            <h3 class="mv-title">Our Mission</h3>
            <p class="mv-text">
              To build accessible, reliable, and delightful workforce tools that free HR managers and business owners from administrative drudgery. We strive to replace disconnected spreadsheets with effortless automation, so teams can focus on what matters most — people and growth.
            </p>
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="mv-card">
            <div class="mv-icon">
              <i class="fa-solid fa-eye"></i>
            </div>
            <h3 class="mv-title">Our Vision</h3>
            <p class="mv-text">
              To become India's premier integrated HRMS and payroll ecosystem, renowned for speed, intuitive mobile usability, and zero-compromise statutory compliance. We envision a future where every employee experiences smooth, transparent payroll and self-service.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Core Values & Innovations -->
  <section class="values-section">
    <div class="container">
      <div class="row align-items-center justify-content-between g-5">
        
        <!-- Left: Values Accordion -->
        <div class="col-12 col-lg-5">
          <div class="section-header mb-4">
            <div class="stafo-pill">
              <i class="fa-solid fa-heart"></i> Core Values
            </div>
            <h2 class="section-title">What Sets STAFO Apart</h2>
            <p class="section-sub">
              Built on deep customer empathy, product innovation, and rigorous Indian statutory standards.
            </p>
          </div>

          <div class="accordion values-accordion" id="valuesAccordion">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  <i class="fa-solid fa-sparkles text-primary me-2"></i> Delivering Real-World Value
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#valuesAccordion">
                <div class="accordion-body">
                  We engineer real solutions to everyday HR hurdles — saving hours every week, lowering processing costs, and ensuring accurate attendance records from day one.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  <i class="fa-solid fa-location-dot text-primary me-2"></i> Supporting Indian Businesses
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#valuesAccordion">
                <div class="accordion-body">
                  Made in India, for India. We build tailored support for regional compliance (PF, ESI, Professional Tax, TDS), Hindi and English language accessibility, and local support teams.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  <i class="fa-solid fa-mobile-screen text-primary me-2"></i> Mobile-First Simplicity
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#valuesAccordion">
                <div class="accordion-body">
                  STAFO is built for everyone — regardless of technical familiarity. Employees can punch attendance, view payslips, and apply for leaves in seconds from Android & iOS.
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: 4 Value Feature Cards -->
        <div class="col-12 col-lg-7">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="value-feature-box">
                <div class="value-icon-box">
                  <i class="fa-solid fa-fingerprint"></i>
                </div>
                <h4 class="value-feature-title">AI Face-ID Attendance</h4>
                <p class="value-feature-desc">
                  Eliminate buddy punching with contactless facial recognition and live GPS geofencing on staff mobile phones.
                </p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="value-feature-box">
                <div class="value-icon-box">
                  <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h4 class="value-feature-title">Live Field Tracking</h4>
                <p class="value-feature-desc">
                  Monitor field executives, sales reps, and remote teams with battery-optimized live route tracking and stopover logs.
                </p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="value-feature-box">
                <div class="value-icon-box">
                  <i class="fa-solid fa-calculator"></i>
                </div>
                <h4 class="value-feature-title">Automated Payroll</h4>
                <p class="value-feature-desc">
                  One-click salary disbursement with automated overtime, leaves, PF, ESI, and instant PDF payslip distribution.
                </p>
              </div>
            </div>

            <div class="col-md-6">
              <div class="value-feature-box">
                <div class="value-icon-box">
                  <i class="fa-solid fa-users-gear"></i>
                </div>
                <h4 class="value-feature-title">Employee Self-Service</h4>
                <p class="value-feature-desc">
                  Empower team members with quick leave requests, reimbursement claims, company holidays, and personal tax sheets.
                </p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Why Choose STAFO 4-Grid -->
  <section class="why-choose-section">
    <div class="container">
      <div class="row justify-content-center text-center mb-5">
        <div class="col-12 col-md-10 col-lg-8">
          <div class="stafo-pill">
            <i class="fa-solid fa-circle-check"></i> Built for Scale
          </div>
          <h2 class="section-title">Why Companies Choose STAFO</h2>
          <p class="section-sub mx-auto">
            A comprehensive, digital-first partner engineered to replace fragmented legacy tools with unified modern workflows.
          </p>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="why-card">
            <div class="why-img-wrap">
              <img src="{{ asset('assets/images/about/mobile.svg') }}" alt="Mobile-First Architecture">
            </div>
            <span class="why-badge">Intuitive UI</span>
            <h4 class="why-title">Mobile-First</h4>
            <p class="why-desc">Full portal access via native Android & iOS mobile apps and lightning-fast web console.</p>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="why-card">
            <div class="why-img-wrap">
              <img src="{{ asset('assets/images/about/security.svg') }}" alt="Advanced Enterprise Security">
            </div>
            <span class="why-badge">Enterprise Safety</span>
            <h4 class="why-title">Bank-Grade Security</h4>
            <p class="why-desc">256-bit encryption, role-based access control, and compliant Indian cloud data centers.</p>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="why-card">
            <div class="why-img-wrap">
              <img src="{{ asset('assets/images/about/location.svg') }}" alt="Real-Time Location Monitoring">
            </div>
            <span class="why-badge">GPS Technology</span>
            <h4 class="why-title">Live Geofencing</h4>
            <p class="why-desc">Accurate boundary definitions for job sites, branches, and client visit tracking in real time.</p>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="why-card">
            <div class="why-img-wrap">
              <img src="{{ asset('assets/images/about/payroll.svg') }}" alt="Instant Payroll Calculations">
            </div>
            <span class="why-badge">Zero Errors</span>
            <h4 class="why-title">1-Click Payroll</h4>
            <p class="why-desc">Synchronized timesheets directly feed payroll algorithms, eliminating calculation mistakes.</p>
          </div>
        </div>
      </div>

      <!-- Bottom CTA Strip -->
      <div class="row mt-5 pt-3">
        <div class="col-12">
          <div class="about-cta-strip">
            <div class="row align-items-center justify-content-between g-4 position-relative" style="z-index: 2;">
              <div class="col-12 col-lg-8">
                <h3 class="cta-heading">Ready to Modernize Your Workforce Management?</h3>
                <p class="cta-sub">
                  Join hundreds of forward-thinking businesses. Start your 15-day free trial today without any credit card.
                </p>
              </div>
              <div class="col-12 col-lg-4 text-lg-end">
                <div class="d-flex align-items-center justify-content-lg-end gap-3 flex-wrap">
                  <a href="{{ route('register') }}" class="btn-stafo-white">
                    <span>Start Free Trial</span>
                    <i class="fa-solid fa-arrow-right"></i>
                  </a>
                  <a href="{{ route('contactUs') }}" class="btn-stafo-outline">
                    <span>Contact Sales</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>

</div>
@endsection