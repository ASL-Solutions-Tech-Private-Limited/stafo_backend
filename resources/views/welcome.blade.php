@extends('frontend.layouts.master')
@section('title', 'STAFO — HR, Payroll & Workforce Management')

@section('css')
<style>
:root {
  --primary: #424096;
  --primary2: #5b57c7;
  --ink: #17172a;
  --muted: #68687a;
  --soft: #f6f6fb;
  --line: #e7e7f0;
  --white: #fff;
  --green: #16a36a;
  --shadow: 0 24px 70px rgba(45,42,110,.13);
  --radius: 22px;
}

.stafo-home-wrapper {
  color: var(--ink);
  background: #fff;
  line-height: 1.55;
  font-family: 'Plus Jakarta Sans', Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

.stafo-home-wrapper a {
  text-decoration: none;
  color: inherit;
}

.stafo-home-wrapper .container {
  width: min(1160px, calc(100% - 40px));
  margin: auto;
}

/* Button Utilities */
.btn-stafo {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: 11px;
  font-weight: 700;
  font-size: 14px;
  border: 1px solid var(--line);
  transition: all 0.2s ease;
  cursor: pointer;
  text-decoration: none;
}
.btn-stafo:hover {
  transform: translateY(-2px);
}
.btn-stafo-primary {
  background: var(--primary);
  color: #fff !important;
  border-color: var(--primary);
  box-shadow: 0 10px 25px rgba(66, 64, 150, 0.25);
}
.btn-stafo-primary:hover {
  background: var(--primary2);
  color: #fff !important;
  box-shadow: 0 14px 28px rgba(66, 64, 150, 0.35);
}
.btn-stafo-ghost {
  background: #fff;
  color: var(--ink) !important;
  border-color: var(--line);
}
.btn-stafo-ghost:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}

/* Hero Section */
.hero {
  padding: 70px 0 65px;
  background: radial-gradient(circle at 85% 10%, #ececff 0, transparent 32%), linear-gradient(180deg, #fff, #fbfbfe);
}
.hero-grid {
  display: grid;
  grid-template-columns: 1fr 1.05fr;
  gap: 55px;
  align-items: center;
}
.hero-badge {
  display: inline-flex;
  padding: 7px 14px;
  border-radius: 999px;
  background: #f0efff;
  color: var(--primary);
  font-weight: 800;
  font-size: 12px;
  margin-bottom: 18px;
  border: 1px solid rgba(66, 64, 150, 0.15);
}
.hero-title {
  font-size: clamp(38px, 4.8vw, 62px);
  line-height: 1.06;
  letter-spacing: -2.5px;
  font-weight: 800;
  margin: 0 0 20px;
  color: var(--ink);
}
.hero p {
  font-size: 17px;
  color: var(--muted);
  max-width: 590px;
  margin: 0 0 26px;
  line-height: 1.6;
}
.hero-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.micro {
  display: flex;
  gap: 18px;
  flex-wrap: wrap;
  color: #777789;
  font-size: 12px;
  margin-top: 18px;
  font-weight: 600;
}
.micro span:before {
  content: "✓";
  color: var(--green);
  font-weight: 900;
  margin-right: 5px;
}

/* Dashboard Mockup */
.dashboard {
  background: #16162b;
  border-radius: 25px;
  padding: 14px;
  box-shadow: var(--shadow);
  transform: rotate(0.4deg);
  transition: transform 0.3s ease;
}
.dashboard:hover {
  transform: rotate(0deg) scale(1.01);
}
.dash-top {
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #fff;
  padding: 0 9px;
  font-size: 12px;
}
.dots {
  display: flex;
  gap: 6px;
}
.dots i {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #777;
  display: inline-block;
}
.dash-body {
  background: #f7f7fb;
  border-radius: 15px;
  padding: 18px;
  color: var(--ink);
}
.dash-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}
.dash-head b {
  font-size: 15px;
}
.dash-head small {
  color: #7b7b8b;
  font-weight: 500;
}
.stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 9px;
}
.stat {
  background: #fff;
  padding: 12px 10px;
  border-radius: 12px;
  border: 1px solid #eee;
  text-align: left;
}
.stat small {
  color: #858595;
  font-size: 10px;
  display: block;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.stat strong {
  display: block;
  font-size: 20px;
  margin-top: 2px;
  color: var(--ink);
}
.panels {
  display: grid;
  grid-template-columns: 1.15fr .85fr;
  gap: 10px;
  margin-top: 10px;
}
.panel {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 14px;
}
.bar {
  height: 8px;
  border-radius: 8px;
  background: #e9e9f3;
  margin: 10px 0;
  overflow: hidden;
}
.bar b {
  display: block;
  height: 100%;
  width: 86%;
  background: var(--primary);
  border-radius: 8px;
}

/* Trust Bar */
.trust {
  padding: 24px 0;
  border-bottom: 1px solid var(--line);
  border-top: 1px solid var(--line);
  background: #fff;
}
.trust-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}
.trust-title {
  font-weight: 800;
  font-size: 14px;
  color: var(--ink);
}
.trust-metrics {
  display: flex;
  gap: 28px;
  color: #777;
  font-size: 13px;
}
.trust-metrics b {
  color: var(--ink);
  font-size: 15px;
  margin-right: 4px;
}

/* Section Shared */
.section {
  padding: 85px 0;
}
.soft {
  background: var(--soft);
}
.eyebrow {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 1.6px;
  color: var(--primary);
  font-weight: 900;
  margin-bottom: 10px;
}
.section h2 {
  font-size: clamp(28px, 3.8vw, 44px);
  line-height: 1.12;
  letter-spacing: -1.8px;
  margin: 0 0 15px;
  font-weight: 800;
  color: var(--ink);
}
.lead {
  color: var(--muted);
  font-size: 16px;
  max-width: 690px;
  line-height: 1.6;
}

/* Problem Section */
.problem {
  display: grid;
  grid-template-columns: .8fr 1.2fr;
  gap: 60px;
  align-items: start;
}
.problem-list {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.problem-card {
  padding: 20px;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.02);
}
.problem-card b {
  display: block;
  margin-bottom: 6px;
  font-size: 15px;
  color: #a34d5b;
}
.problem-card span {
  font-size: 13px;
  color: var(--muted);
  line-height: 1.45;
  display: block;
}

/* Feature Grid */
.feature-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-top: 36px;
}
.feature-card {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 18px;
  padding: 24px;
  transition: all 0.25s ease;
}
.feature-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 15px 35px rgba(20, 20, 50, .08);
  border-color: #cbd5e1;
}
.feature-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: #eeedff;
  color: var(--primary);
  display: grid;
  place-items: center;
  font-weight: 900;
  margin-bottom: 16px;
  font-size: 16px;
}
.feature-card h3 {
  margin: 0 0 8px;
  font-size: 18px;
  font-weight: 700;
  color: var(--ink);
}
.feature-card p {
  margin: 0;
  color: var(--muted);
  font-size: 14px;
  line-height: 1.5;
}

/* Split Visuals */
.split {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}
.visual {
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 24px;
  padding: 26px;
  box-shadow: 0 18px 50px rgba(30, 30, 70, .08);
}
.flow {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}
.flow-item {
  text-align: center;
  padding: 16px 8px;
  border: 1px solid var(--line);
  border-radius: 15px;
  background: #fafafc;
}
.flow-item b {
  display: block;
  font-size: 13px;
  margin-top: 4px;
  color: var(--ink);
}
.flow-item span {
  font-size: 11px;
  color: var(--muted);
}

/* Steps */
.steps {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
  margin-top: 35px;
}
.step {
  position: relative;
  text-align: center;
}
.step-num {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: var(--primary);
  color: #fff;
  display: grid;
  place-items: center;
  margin: 0 auto 10px;
  font-weight: 900;
  box-shadow: 0 6px 16px rgba(66, 64, 150, 0.3);
}
.step b {
  font-size: 13px;
  color: var(--ink);
}
.step p {
  font-size: 11px;
  color: var(--muted);
  margin-top: 3px;
}

/* Payroll Section */
.payroll {
  background: linear-gradient(135deg, #1f1d46, #424096);
  color: #fff;
}
.payroll h2 {
  color: #fff;
}
.payroll .lead {
  color: #d9d8f1;
}
.payroll .eyebrow {
  color: #bdbaff;
}
.pay-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 50px;
  align-items: center;
}
.salary-card {
  background: #fff;
  color: var(--ink);
  border-radius: 22px;
  padding: 26px;
  box-shadow: 0 25px 70px rgba(0, 0, 0, .25);
}
.salary-line {
  display: flex;
  justify-content: space-between;
  padding: 11px 0;
  border-bottom: 1px solid var(--line);
  font-size: 13px;
}
.salary-total {
  display: flex;
  justify-content: space-between;
  padding-top: 17px;
  font-weight: 900;
  font-size: 20px;
  color: var(--ink);
}
.tags {
  display: flex;
  flex-wrap: wrap;
  gap: 9px;
  margin-top: 25px;
}
.tag {
  background: rgba(255, 255, 255, .12);
  border: 1px solid rgba(255, 255, 255, .22);
  padding: 7px 13px;
  border-radius: 999px;
  font-size: 12px;
  color: #ffffff;
  font-weight: 600;
}

/* Mobile Mockup */
.mobile-mock {
  display: flex;
  gap: 16px;
  align-items: center;
  justify-content: center;
}
.phone {
  width: 190px;
  border: 7px solid #18182c;
  background: #f7f7fb;
  border-radius: 28px;
  padding: 13px;
  box-shadow: var(--shadow);
}
.phone:first-child {
  transform: rotate(-3deg);
}
.phone:last-child {
  transform: rotate(3deg);
}
.phone-top {
  height: 80px;
  background: linear-gradient(135deg, #424096, #7773dc);
  border-radius: 17px;
  margin-bottom: 10px;
}
.phone-card {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 11px;
  padding: 10px;
  margin: 7px 0;
  font-size: 10px;
  color: var(--muted);
}
.phone-card b {
  display: block;
  font-size: 12px;
  color: var(--ink);
}

/* Field Section & Map */
.field {
  background: #fafaff;
}
.map {
  height: 310px;
  border-radius: 18px;
  background: radial-gradient(circle at 30% 40%, #d9d8f7 0 3px, transparent 4px), linear-gradient(135deg, #eef0f7, #e1e3ee);
  position: relative;
  overflow: hidden;
}
.map:before, .map:after {
  content: "";
  position: absolute;
  background: #fff;
  height: 20px;
  width: 130%;
  transform: rotate(-24deg);
  left: -20%;
  top: 100px;
  opacity: .8;
}
.map:after {
  transform: rotate(32deg);
  top: 190px;
}
.pin {
  position: absolute;
  width: 32px;
  height: 32px;
  border-radius: 50% 50% 50% 0;
  background: var(--primary);
  transform: rotate(-45deg);
  display: grid;
  place-items: center;
  color: #fff;
  font-size: 12px;
  box-shadow: 0 8px 18px rgba(66, 64, 150, 0.35);
}
.pin span {
  transform: rotate(45deg);
  font-weight: 700;
}
.pin.one { left: 22%; top: 35%; }
.pin.two { left: 62%; top: 52%; }
.pin.three { left: 77%; top: 25%; }
.route {
  position: absolute;
  border: 3px dashed var(--primary);
  width: 48%;
  height: 45%;
  left: 25%;
  top: 30%;
  border-radius: 50%;
  transform: rotate(-12deg);
  opacity: 0.7;
}

/* Industries */
.industries {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-top: 35px;
}
.industry {
  padding: 19px;
  border: 1px solid var(--line);
  border-radius: 15px;
  background: #fff;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.industry:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.04);
}
.industry b {
  font-size: 14px;
  color: var(--ink);
}
.industry p {
  font-size: 12px;
  color: var(--muted);
  margin: 4px 0 0;
}

/* Comparison */
.compare {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-top: 35px;
}
.compare-box {
  border-radius: 20px;
  padding: 27px;
}
.before {
  background: #f5f5f7;
  border: 1px solid #e7e7ee;
}
.after {
  background: #eeedff;
  border: 1px solid #d9d7fb;
}
.compare-box h3 {
  margin: 0 0 17px;
  font-size: 18px;
  font-weight: 800;
}
.compare-box div {
  padding: 9px 0;
  font-size: 14px;
  font-weight: 500;
}
.before div:before {
  content: "×";
  color: #a34d5b;
  font-weight: 900;
  margin-right: 9px;
  font-size: 16px;
}
.after div:before {
  content: "✓";
  color: var(--green);
  font-weight: 900;
  margin-right: 9px;
  font-size: 16px;
}

/* Security */
.security {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}
.security-list {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.security-item {
  border: 1px solid var(--line);
  border-radius: 15px;
  padding: 18px;
  background: #fff;
}
.security-item b {
  font-size: 14px;
  color: var(--ink);
}
.security-item p {
  margin: 4px 0 0;
  font-size: 12px;
  color: var(--muted);
  line-height: 1.45;
}

/* Pricing */
.pricing-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-top: 35px;
}
.price-card {
  border: 1px solid var(--line);
  border-radius: 20px;
  padding: 25px;
  background: #fff;
  transition: all 0.25s ease;
  display: flex;
  flex-direction: column;
}
.price-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.06);
}
.price-card.featured {
  border: 2px solid var(--primary);
  box-shadow: var(--shadow);
  position: relative;
}
.price-card h3 {
  margin: 0 0 7px;
  font-size: 20px;
  font-weight: 800;
  color: var(--ink);
}
.price-card p {
  font-size: 13px;
  color: var(--muted);
  min-height: 40px;
  line-height: 1.45;
}
.price-card ul {
  list-style: none;
  padding: 0;
  margin: 18px 0;
  flex: 1;
}
.price-card li {
  font-size: 13px;
  padding: 7px 0;
  color: var(--ink);
}
.price-card li:before {
  content: "✓";
  color: var(--green);
  font-weight: 900;
  margin-right: 8px;
}

/* FAQ */
.faq {
  max-width: 850px;
  margin: 35px auto 0;
}
.faq details {
  border-bottom: 1px solid var(--line);
  padding: 18px 0;
  transition: all 0.2s ease;
}
.faq summary {
  cursor: pointer;
  font-weight: 700;
  font-size: 16px;
  color: var(--ink);
  outline: none;
  user-select: none;
}
.faq summary::-webkit-details-marker {
  display: none;
}
.faq summary:before {
  content: "+";
  display: inline-block;
  width: 22px;
  font-weight: 700;
  color: var(--primary);
  font-size: 18px;
}
.faq details[open] summary:before {
  content: "−";
}
.faq p {
  color: var(--muted);
  font-size: 14px;
  max-width: 760px;
  margin: 12px 0 0 24px;
  line-height: 1.6;
}

/* CTA */
.cta {
  padding: 85px 0;
  background: linear-gradient(135deg, #efefff, #f9f9fd);
  text-align: center;
}
.cta h2 {
  max-width: 760px;
  margin: 0 auto 15px;
  font-size: clamp(28px, 3.8vw, 44px);
  letter-spacing: -1.5px;
  font-weight: 800;
}
.cta .lead {
  margin: 0 auto 25px;
  font-size: 17px;
}

/* Responsive */
@media(max-width: 900px) {
  .hero-grid, .problem, .split, .pay-grid, .security {
    grid-template-columns: 1fr;
  }
  .hero {
    padding-top: 50px;
  }
  .feature-grid, .industries, .pricing-grid, .stats, .steps {
    grid-template-columns: 1fr 1fr;
  }
  .split {
    gap: 35px;
  }
}
@media(max-width: 600px) {
  .hero {
    padding: 40px 0;
  }
  .hero-title {
    letter-spacing: -1.5px;
    font-size: 34px;
  }
  .feature-grid, .problem-list, .pricing-grid, .industries, .security-list, .stats, .steps, .panels, .compare {
    grid-template-columns: 1fr;
  }
  .section {
    padding: 60px 0;
  }
  .trust-metrics {
    gap: 12px;
  }
  .mobile-mock {
    transform: scale(0.9);
  }
}
</style>
@endsection

@section('content')
<div class="stafo-home-wrapper">

  <!-- 1. HERO SECTION -->
  <section class="hero">
    <div class="container hero-grid">
      <div>
        <div class="hero-badge">HRMS built for growing businesses in India</div>
        <h1 class="hero-title">Your people, payroll & attendance — all in one place.</h1>
        <p>STAFO brings attendance, payroll, leave, employee management and field operations together in one simple platform — so your HR team can spend less time managing paperwork and more time managing people.</p>
        <div class="hero-actions">
          <a class="btn-stafo btn-stafo-primary" href="{{ route('register') }}">Start Free Trial →</a>
          <button type="button" class="btn-stafo btn-stafo-ghost" data-bs-toggle="modal" data-bs-target="#callbackDemoModal">Book a Live Demo</button>
        </div>
        <div class="micro">
          <span>No setup fee</span>
          <span>Web + Android</span>
          <span>Easy onboarding</span>
        </div>
      </div>

      <!-- Interactive STAFO Dashboard Preview -->
      <div class="dashboard">
        <div class="dash-top">
          <div class="dots"><i></i><i></i><i></i></div>
          <small>STAFO Admin</small>
          <span>•••</span>
        </div>
        <div class="dash-body">
          <div class="dash-head">
            <b>Good morning, Admin</b>
            <small>17 Sep 2026</small>
          </div>
          <div class="stats">
            <div class="stat"><small>Employees</small><strong>126</strong></div>
            <div class="stat"><small>Present</small><strong>108</strong></div>
            <div class="stat"><small>On Leave</small><strong>9</strong></div>
            <div class="stat"><small>Late</small><strong>7</strong></div>
          </div>
          <div class="panels">
            <div class="panel">
              <b style="font-size:13px">Today's Attendance</b>
              <div class="bar"><b></b></div>
              <small style="color:#777">86% attendance recorded</small>
            </div>
            <div class="panel">
              <b style="font-size:13px">Payroll</b>
              <p style="font-size:11px;color:#777;margin:4px 0;">September processing</p>
              <b style="color:#16a36a;font-size:12px">Ready to review</b>
            </div>
          </div>
          <div class="panel" style="margin-top:10px">
            <b style="font-size:13px">Pending approvals</b>
            <p style="font-size:11px;color:#777;margin:4px 0 0;">Leave &nbsp;·&nbsp; Regularization &nbsp;·&nbsp; Expenses</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. TRUST METRICS BAR -->
  <section class="trust">
    <div class="container trust-row">
      <div class="trust-title">Trusted by growing businesses across India</div>
      <div class="trust-metrics">
        <span><b>120+</b> businesses</span>
        <span><b>5,000+</b> employees</span>
        <span><b>Web + Android</b></span>
        <span><b>Indian workflows</b></span>
      </div>
    </div>
  </section>

  <!-- 3. EVERYDAY HR PROBLEM & ONE PLATFORM -->
  <section class="section" id="product">
    <div class="container">
      <div class="problem">
        <div>
          <div class="eyebrow">The everyday HR problem</div>
          <h2>HR shouldn't have to work like this.</h2>
          <p class="lead">Attendance in one place. Leave requests somewhere else. Payroll in Excel. Employees asking HR for the same documents again and again.</p>
        </div>
        <div class="problem-list">
          <div class="problem-card">
            <b>Attendance in spreadsheets</b>
            <span>Manual reconciliation takes time every month.</span>
          </div>
          <div class="problem-card">
            <b>Leave on WhatsApp</b>
            <span>Requests get missed and balances become harder to track.</span>
          </div>
          <div class="problem-card">
            <b>Payroll headaches</b>
            <span>HR spends hours bringing different data together.</span>
          </div>
          <div class="problem-card">
            <b>Scattered field reports</b>
            <span>Managers don't have one clear view of field activity.</span>
          </div>
        </div>
      </div>

      <div style="margin-top:70px">
        <div class="eyebrow">One platform</div>
        <h2>Everything your HR team needs.</h2>
        <p class="lead">Keep the daily workforce work connected instead of moving information between different tools.</p>
      </div>

      <div class="feature-grid">
        <div class="feature-card">
          <div class="feature-icon"><i class="fa-solid fa-check"></i></div>
          <h3>Attendance</h3>
          <p>Track presence, late arrivals, shifts, overtime and attendance requests from one place.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div>
          <h3>Payroll</h3>
          <p>Connect attendance, leave, earnings, deductions and payslips in one workflow.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon"><i class="fa-solid fa-id-card"></i></div>
          <h3>Employees</h3>
          <p>Keep employee details, documents, salary and work history organized.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon"><i class="fa-regular fa-clock"></i></div>
          <h3>Leave</h3>
          <p>Employees request, managers approve and HR stays updated automatically.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon"><i class="fa-solid fa-location-crosshairs"></i></div>
          <h3>Field Force</h3>
          <p>Manage field attendance, visits, locations and workforce activity.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon"><i class="fa-regular fa-file-lines"></i></div>
          <h3>Reports</h3>
          <p>Get the information you need without rebuilding spreadsheets every month.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 4. ATTENDANCE & SIMPLE FLOW -->
  <section class="section soft" id="attendance">
    <div class="container split">
      <div>
        <div class="eyebrow">Attendance</div>
        <h2>Attendance that works beyond the office.</h2>
        <p class="lead">Whether your team works from an office, branch, warehouse or client location, STAFO gives you one place to manage attendance.</p>
        
        <div class="feature-grid" style="grid-template-columns:1fr 1fr;margin-top:25px">
          <div class="feature-card">
            <h3>📸 Face Verification</h3>
            <p>Verify employee identity when they punch in or out.</p>
          </div>
          <div class="feature-card">
            <h3>📍 GPS & Geofencing</h3>
            <p>Restrict attendance to approved locations when required.</p>
          </div>
          <div class="feature-card">
            <h3>🕐 Shift Rules</h3>
            <p>Manage shifts, grace periods, late marks and overtime.</p>
          </div>
          <div class="feature-card">
            <h3>📊 Live Attendance</h3>
            <p>See attendance status across teams and branches.</p>
          </div>
        </div>
      </div>

      <div class="visual">
        <div class="eyebrow">A simple employee flow</div>
        <h3 style="margin:0 0 20px;font-size:20px;font-weight:800;color:var(--ink);">One punch. Your HR workflow stays updated.</h3>
        <div class="flow">
          <div class="flow-item">
            <div style="font-size:24px">📱</div>
            <b>Open STAFO</b>
            <span>Employee starts attendance</span>
          </div>
          <div class="flow-item">
            <div style="font-size:24px">📸</div>
            <b>Verify</b>
            <span>Face verification</span>
          </div>
          <div class="flow-item">
            <div style="font-size:24px">📍</div>
            <b>Location</b>
            <span>GPS / geofence check</span>
          </div>
        </div>
        <div class="flow" style="margin-top:10px">
          <div class="flow-item">
            <div style="font-size:24px">✓</div>
            <b>Attendance</b>
            <span>Punch recorded</span>
          </div>
          <div class="flow-item">
            <div style="font-size:24px">📊</div>
            <b>HR</b>
            <span>Dashboard updated</span>
          </div>
          <div class="flow-item">
            <div style="font-size:24px">₹</div>
            <b>Payroll</b>
            <span>Data ready for payroll</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 5. PAYROLL SECTION -->
  <section class="section payroll" id="payroll">
    <div class="container pay-grid">
      <div>
        <div class="eyebrow">Payroll</div>
        <h2>Payroll without the monthly spreadsheet marathon.</h2>
        <p class="lead">Bring attendance, leave, overtime, deductions and salary calculations together so payroll takes fewer manual steps.</p>
        <div class="tags">
          <span class="tag">PF</span>
          <span class="tag">ESI</span>
          <span class="tag">PT</span>
          <span class="tag">TDS</span>
          <span class="tag">LOP</span>
          <span class="tag">Overtime</span>
          <span class="tag">Reimbursements</span>
          <span class="tag">Loans & Advances</span>
          <span class="tag">Payslips</span>
          <span class="tag">Bank Export</span>
        </div>
      </div>

      <div class="salary-card">
        <div style="font-size:12px;color:#777">September Payroll · Employee</div>
        <h3 style="margin:6px 0 12px;font-size:19px;font-weight:800;">Salary calculation</h3>
        <div class="salary-line">
          <span>Basic + HRA + Allowances</span>
          <b>₹48,000</b>
        </div>
        <div class="salary-line">
          <span>Overtime / Incentives</span>
          <b>₹4,500</b>
        </div>
        <div class="salary-line">
          <span>LOP / Adjustments</span>
          <b>− ₹1,500</b>
        </div>
        <div class="salary-line">
          <span>Statutory deductions</span>
          <b>− ₹5,200</b>
        </div>
        <div class="salary-total">
          <span>Net Salary</span>
          <span>₹45,800</span>
        </div>
        <a class="btn-stafo btn-stafo-primary" style="width:100%;margin-top:18px" href="{{ route('register') }}">See How Payroll Works →</a>
      </div>
    </div>
  </section>

  <!-- 6. EMPLOYEE SELF-SERVICE -->
  <section class="section">
    <div class="container split">
      <div>
        <div class="eyebrow">Employee self-service</div>
        <h2>Give employees their own HR app.</h2>
        <p class="lead">Employees shouldn't need to message HR every time they want to check attendance, apply for leave or download a payslip.</p>
        <div class="feature-grid" style="grid-template-columns:1fr 1fr;margin-top:25px">
          <div class="feature-card">
            <h3>🕐 Attendance</h3>
            <p>Check punches and attendance history.</p>
          </div>
          <div class="feature-card">
            <h3>🏖 Leave</h3>
            <p>Apply and track leave requests.</p>
          </div>
          <div class="feature-card">
            <h3>📄 Payslips</h3>
            <p>Access salary documents when needed.</p>
          </div>
          <div class="feature-card">
            <h3>🔔 Updates</h3>
            <p>Receive company announcements and notifications.</p>
          </div>
        </div>
      </div>

      <div class="mobile-mock">
        <div class="phone">
          <div class="phone-top"></div>
          <div class="phone-card"><b>Good morning</b>Attendance is ready</div>
          <div class="phone-card"><b>Today</b>09:32 AM · Office</div>
          <div class="phone-card"><b>Leave Balance</b>12 days remaining</div>
        </div>
        <div class="phone">
          <div class="phone-top"></div>
          <div class="phone-card"><b>September Payslip</b>₹45,800 generated</div>
          <div class="phone-card"><b>Leave Request</b>Approved by HR</div>
          <div class="phone-card"><b>Announcements</b>2 new company notices</div>
        </div>
      </div>
    </div>
  </section>

  <!-- 7. FIELD FORCE SECTION -->
  <section class="section soft" id="field">
    <div class="container split">
      <div class="visual">
        <div class="map">
          <div class="route"></div>
          <div class="pin one"><span>1</span></div>
          <div class="pin two"><span>2</span></div>
          <div class="pin three"><span>3</span></div>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:14px;font-size:12px">
          <b>Field Team · 18 active</b>
          <span style="color:#16a36a;font-weight:700">● Live</span>
        </div>
      </div>

      <div>
        <div class="eyebrow">Field force</div>
        <h2>Your workforce doesn't always sit in the office.</h2>
        <p class="lead">Manage sales teams, service engineers and field employees without asking them to constantly report where they are.</p>
        <div class="feature-grid" style="grid-template-columns:1fr 1fr;margin-top:25px">
          <div class="feature-card">
            <h3>Live Location</h3>
            <p>See field employees during permitted working hours.</p>
          </div>
          <div class="feature-card">
            <h3>Visit Tracking</h3>
            <p>Record customer or site visits.</p>
          </div>
          <div class="feature-card">
            <h3>Route History</h3>
            <p>Review completed routes and activity.</p>
          </div>
          <div class="feature-card">
            <h3>Travel & Expenses</h3>
            <p>Manage eligible travel and reimbursement workflows.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="container" style="margin-top:65px;text-align:center">
      <div class="eyebrow">The bigger picture</div>
      <h2>From “Where is my team?” to “What did my team accomplish?”</h2>
      <div class="steps">
        <div class="step">
          <div class="step-num">1</div>
          <b>Location</b>
          <p>Know where work happens.</p>
        </div>
        <div class="step">
          <div class="step-num">2</div>
          <b>Visit</b>
          <p>Record the customer/site.</p>
        </div>
        <div class="step">
          <div class="step-num">3</div>
          <b>Task</b>
          <p>Track assigned work.</p>
        </div>
        <div class="step">
          <div class="step-num">4</div>
          <b>Expense</b>
          <p>Capture eligible costs.</p>
        </div>
        <div class="step">
          <div class="step-num">5</div>
          <b>Report</b>
          <p>See the complete picture.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 8. OPERATIONS SECTION -->
  <section class="section">
    <div class="container">
      <div class="eyebrow">Operations</div>
      <h2>Connect attendance with the work employees actually do.</h2>
      <p class="lead">Assign tasks, set deadlines and follow progress without switching between multiple tools.</p>
      <div class="feature-grid">
        <div class="feature-card">
          <div class="feature-icon">01</div>
          <h3>Assign</h3>
          <p>Give employees clear tasks and responsibilities.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">02</div>
          <h3>Prioritize</h3>
          <p>Keep important work visible to the right people.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">03</div>
          <h3>Track</h3>
          <p>See progress without chasing updates.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 9. DASHBOARD OVERVIEW -->
  <section class="section soft">
    <div class="container">
      <div class="eyebrow">Dashboard</div>
      <h2>Open STAFO. Know what's happening.</h2>
      <p class="lead">A single dashboard for HR, managers and business owners.</p>
      
      <div class="dashboard" style="margin-top:30px;transform:none">
        <div class="dash-body">
          <div class="stats">
            <div class="stat"><small>Employees</small><strong>126</strong></div>
            <div class="stat"><small>Present Today</small><strong>108</strong></div>
            <div class="stat"><small>On Leave</small><strong>9</strong></div>
            <div class="stat"><small>Pending</small><strong>7</strong></div>
          </div>
          <div class="panels">
            <div class="panel">
              <b>Attendance overview</b>
              <div class="bar"><b style="width:86%"></b></div>
              <small style="color:#777">Attendance by department, shift and branch</small>
            </div>
            <div class="panel">
              <b>Payroll</b>
              <p style="font-size:12px;color:#777;margin:4px 0">September payroll</p>
              <strong style="color:#16a36a">Ready to review</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 10. SOLUTIONS / INDUSTRIES -->
  <section class="section" id="solutions">
    <div class="container">
      <div class="eyebrow">Solutions</div>
      <h2>Built for businesses with people to manage.</h2>
      <p class="lead">Whether your employees work from one office or across multiple locations, STAFO keeps the essentials connected.</p>
      
      <div class="industries">
        <div class="industry">
          <b>🏢 Growing Companies</b>
          <p>HR, attendance and payroll</p>
        </div>
        <div class="industry">
          <b>🏭 Manufacturing</b>
          <p>Shifts and workforce</p>
        </div>
        <div class="industry">
          <b>🚚 Logistics</b>
          <p>Field teams and operations</p>
        </div>
        <div class="industry">
          <b>💊 Pharma</b>
          <p>Sales representatives and visits</p>
        </div>
        <div class="industry">
          <b>🛍️ FMCG</b>
          <p>Distributed sales teams</p>
        </div>
        <div class="industry">
          <b>🔧 Service Teams</b>
          <p>On-site employees</p>
        </div>
        <div class="industry">
          <b>🏢 Multi-Branch</b>
          <p>Central HR management</p>
        </div>
        <div class="industry">
          <b>💼 Professional Services</b>
          <p>People and project operations</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 11. MAKE THE SWITCH -->
  <section class="section soft">
    <div class="container">
      <div class="eyebrow">Make the switch</div>
      <h2>Moving to STAFO doesn't have to be complicated.</h2>
      <p class="lead">Start with your existing team and bring your HR process into one place.</p>
      <div class="steps">
        <div class="step">
          <div class="step-num">1</div>
          <b>Import employees</b>
          <p>Bring existing employee data.</p>
        </div>
        <div class="step">
          <div class="step-num">2</div>
          <b>Set policies</b>
          <p>Configure shifts, leave and payroll.</p>
        </div>
        <div class="step">
          <div class="step-num">3</div>
          <b>Invite your team</b>
          <p>Employees install the app.</p>
        </div>
        <div class="step">
          <div class="step-num">4</div>
          <b>Start working</b>
          <p>Attendance, leave and payroll connect.</p>
        </div>
        <div class="step">
          <div class="step-num">5</div>
          <b>Get support</b>
          <p>Guidance through setup.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 12. TRUST & SECURITY -->
  <section class="section">
    <div class="container security">
      <div>
        <div class="eyebrow">Trust & security</div>
        <h2>Your employee data deserves serious protection.</h2>
        <p class="lead">Specific information builds more trust than vague claims. We apply industry-standard protections across infrastructure, access, and workforce data.</p>
      </div>
      <div class="security-list">
        <div class="security-item">
          <b>🔐 Secure authentication</b>
          <p>Protect account access with your actual authentication controls.</p>
        </div>
        <div class="security-item">
          <b>👥 Role-based access</b>
          <p>Give HR, managers and employees only the access they need.</p>
        </div>
        <div class="security-item">
          <b>📝 Activity logs</b>
          <p>Keep important workforce actions traceable.</p>
        </div>
        <div class="security-item">
          <b>💾 Backups</b>
          <p>Maintain reliable backup and recovery processes.</p>
        </div>
        <div class="security-item">
          <b>🔒 Protected information</b>
          <p>Apply appropriate safeguards to employee information.</p>
        </div>
        <div class="security-item">
          <b>📤 Data export</b>
          <p>Make it easy for businesses to access their information.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 13. PRICING -->
  <section class="section soft" id="pricing">
    <div class="container">
      <div class="eyebrow">Pricing</div>
      <h2>Simple plans for growing teams.</h2>
      <p class="lead">Transparent, scalable pricing tailored to your workforce size.</p>
      
      <div class="pricing-grid">
        <div class="price-card">
          <h3>Starter</h3>
          <p>For small teams getting organized.</p>
          <ul>
            <li>Attendance</li>
            <li>Leave</li>
            <li>Employees</li>
            <li>Mobile app</li>
          </ul>
          <a class="btn-stafo btn-stafo-ghost" href="{{ route('register') }}" style="width:100%">Get Started</a>
        </div>
        <div class="price-card featured">
          <div class="hero-badge mb-2" style="background:#eef2ff;color:var(--primary)">Most Popular</div>
          <h3>Growth</h3>
          <p>For teams ready to automate HR.</p>
          <ul>
            <li>Everything in Starter</li>
            <li>Payroll</li>
            <li>Reports</li>
            <li>Approvals</li>
          </ul>
          <a class="btn-stafo btn-stafo-primary" href="{{ route('register') }}" style="width:100%">Start Trial</a>
        </div>
        <div class="price-card">
          <h3>Business</h3>
          <p>For distributed teams and operations.</p>
          <ul>
            <li>Everything in Growth</li>
            <li>Field Force</li>
            <li>GPS</li>
            <li>Tasks</li>
          </ul>
          <button type="button" class="btn-stafo btn-stafo-ghost" data-bs-toggle="modal" data-bs-target="#callbackDemoModal" style="width:100%">Talk to Us</button>
        </div>
        <div class="price-card">
          <h3>Enterprise</h3>
          <p>For larger or specialized requirements.</p>
          <ul>
            <li>Custom setup</li>
            <li>Multiple branches</li>
            <li>Integrations</li>
            <li>Dedicated support</li>
          </ul>
          <button type="button" class="btn-stafo btn-stafo-ghost" data-bs-toggle="modal" data-bs-target="#callbackDemoModal" style="width:100%">Book Demo</button>
        </div>
      </div>
    </div>
  </section>

  <!-- 14. WHY STAFO (COMPARISON) -->
  <section class="section">
    <div class="container">
      <div class="eyebrow">Why STAFO</div>
      <h2>Move away from manual HR work.</h2>
      <div class="compare">
        <div class="compare-box before">
          <h3>Before STAFO</h3>
          <div>Excel attendance</div>
          <div>WhatsApp leave requests</div>
          <div>Manual payroll reconciliation</div>
          <div>Scattered employee data</div>
          <div>Manual payslips</div>
          <div>Separate field tracking</div>
        </div>
        <div class="compare-box after">
          <h3>With STAFO</h3>
          <div>Digital attendance</div>
          <div>Structured leave workflows</div>
          <div>Connected payroll</div>
          <div>Centralized employee records</div>
          <div>Employee self-service</div>
          <div>Workforce visibility</div>
        </div>
      </div>
    </div>
  </section>

  <!-- 15. QUESTIONS (FAQ) -->
  <section class="section soft">
    <div class="container">
      <div class="eyebrow">Questions</div>
      <h2>What businesses usually ask.</h2>
      <div class="faq">
        <details>
          <summary>Can employees mark attendance from mobile?</summary>
          <p>Yes, employees can clock in directly from their smartphone using the STAFO Android application with optional GPS geofencing and facial verification.</p>
        </details>
        <details>
          <summary>Can we restrict attendance by location?</summary>
          <p>Yes, where enabled, businesses can use GPS and geofencing rules to restrict punches to approved office branches, warehouses, or customer job sites.</p>
        </details>
        <details>
          <summary>How does Face Verification work?</summary>
          <p>Face verification matches the live selfie captured during clock-in against the employee's registered company profile, ensuring 100% proxy-free attendance.</p>
        </details>
        <details>
          <summary>Does STAFO support payroll?</summary>
          <p>STAFO automatically computes salary based on recorded attendance, leave deductions, overtime, and statutory rules (PF, ESI, Professional Tax, and TDS).</p>
        </details>
        <details>
          <summary>Can employees download payslips?</summary>
          <p>Yes, once monthly salary processing is approved by HR, employees can view and download formatted PDF salary slips directly on the mobile app.</p>
        </details>
        <details>
          <summary>Can we manage multiple branches?</summary>
          <p>Absolutely. You can configure multiple branches, departments, shifts, and assign dedicated managers with custom permissions per location.</p>
        </details>
        <details>
          <summary>Can we import existing employees?</summary>
          <p>Yes, STAFO provides convenient Excel and CSV employee bulk import templates so you can onboard your entire workforce in minutes.</p>
        </details>
        <details>
          <summary>Is employee data secure?</summary>
          <p>STAFO utilizes industry-grade encryption, secure server hosting, regular backups, and strict role-based access control to keep your company information safe.</p>
        </details>
      </div>
    </div>
  </section>

  <!-- 16. FINAL CALL TO ACTION -->
  <section class="cta" id="trial">
    <div class="container">
      <h2>Your HR team has better things to do than manage spreadsheets.</h2>
      <p class="lead">Bring attendance, payroll, leave and workforce operations together with STAFO.</p>
      <div class="hero-actions" style="justify-content:center">
        <a class="btn-stafo btn-stafo-primary" href="{{ route('register') }}">Start Free Trial →</a>
        <button type="button" class="btn-stafo btn-stafo-ghost" data-bs-toggle="modal" data-bs-target="#callbackDemoModal">Book a Live Demo</button>
      </div>
      <div class="micro" style="justify-content:center">
        <span>No setup fee</span>
        <span>Web + Android</span>
        <span>Guided onboarding</span>
      </div>
    </div>
  </section>

</div>

<!-- ================= MODAL: BOOK LIVE DEMO & CALLBACK ================= -->
<div class="modal fade" id="callbackDemoModal" tabindex="-1" aria-labelledby="callbackDemoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
      <div class="modal-header text-white py-3 px-4" style="background: var(--primary);">
        <div class="d-flex align-items-center gap-2">
          <img src="{{ asset('assets/images/icon/c_logo.png') }}" width="38" height="38" alt="STAFO Logo" class="bg-white rounded-circle p-1">
          <div>
            <h5 class="modal-title fw-bold text-white mb-0" id="callbackDemoModalLabel">Book a Free Live Demo</h5>
            <p class="mb-0 text-white-50" style="font-size: 0.75rem;">15-Day Free Trial • No Credit Card Needed</p>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <form id="callbackRequestForm">
          <div class="mb-3">
            <label class="form-label fw-semibold small text-dark">Full Name <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
              <input type="text" class="form-control" name="name" id="callbackName" placeholder="e.g. Ramesh Verma" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small text-dark">Mobile Number <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="fas fa-phone-alt text-muted"></i></span>
              <input type="tel" class="form-control" name="phone" id="callbackPhone" placeholder="10-digit mobile number" pattern="[0-9]{10}" maxlength="10" required>
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold small text-dark">Company Name</label>
              <input type="text" class="form-control" name="company" placeholder="e.g. Acme Corp">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small text-dark">Team Size</label>
              <select class="form-select" name="team_size">
                <option value="1-15">1 - 15 Staff</option>
                <option value="16-50" selected>16 - 50 Staff</option>
                <option value="51-200">51 - 200 Staff</option>
                <option value="200+">200+ Staff</option>
              </select>
            </div>
          </div>

          <button type="submit" id="submitForm" class="btn w-100 rounded-pill py-2 fw-semibold shadow-sm text-white" style="background: var(--primary);">
            <i class="fas fa-paper-plane me-1"></i> Confirm & Book Demo
          </button>
        </form>
      </div>

      <div class="modal-footer bg-light py-2 px-4 justify-content-center text-center">
        <small class="text-muted"><i class="fas fa-lock text-success me-1"></i> Your details are strictly confidential. No spam guaranteed.</small>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  $(document).ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    // Helper: Handle AJAX callback request
    function handleCallbackSubmit(formData, btnElem, modalToHide) {
      var originalBtnHtml = btnElem.html();
      btnElem.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Submitting...');

      $.ajax({
        url: '{{ route('request.callback') }}',
        type: 'POST',
        data: formData,
        success: function(response) {
          btnElem.prop('disabled', false).html(originalBtnHtml);
          if (modalToHide) {
            modalToHide.modal('hide');
          }
          Swal.fire({
            icon: 'success',
            title: 'Thank You!',
            text: response.message || 'Callback request submitted successfully. Our HR expert will contact you shortly.',
            confirmButtonColor: '#424096'
          });
        },
        error: function(xhr) {
          btnElem.prop('disabled', false).html(originalBtnHtml);
          var errMsg = 'There was an error submitting your request. Please check your mobile number and try again.';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errMsg = xhr.responseJSON.message;
          }
          Swal.fire({
            icon: 'error',
            title: 'Submission Failed',
            text: errMsg,
            confirmButtonColor: '#424096'
          });
        }
      });
    }

    // Modal Form Submit
    $('#callbackRequestForm').on('submit', function(e) {
      e.preventDefault();
      var formData = $(this).serialize();
      handleCallbackSubmit(formData, $('#submitForm'), $('#callbackDemoModal'));
    });
  });
</script>
@endsection
