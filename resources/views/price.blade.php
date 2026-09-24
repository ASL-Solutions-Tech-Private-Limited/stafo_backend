@extends('frontend.layouts.master')
@section('title', 'Pricing Plans | STAFO — HR, Payroll & Workforce Management')
@section('heading', 'Pricing Plans')

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
}

body {
  font-family: 'Plus Jakarta Sans', Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
  color: var(--ink);
  background-color: #ffffff;
}

.pricing-page-header {
  padding: 60px 0 35px;
  background: radial-gradient(circle at 50% 10%, #ececff 0%, transparent 60%), linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  text-align: center;
}

.pricing-badge {
  display: inline-flex;
  padding: 6px 14px;
  border-radius: 999px;
  background: #f0efff;
  color: var(--primary);
  font-weight: 800;
  font-size: 12px;
  margin-bottom: 16px;
  border: 1px solid rgba(66, 64, 150, 0.15);
}

.pricing-title {
  font-size: clamp(32px, 4.2vw, 50px);
  font-weight: 800;
  line-height: 1.1;
  letter-spacing: -1.8px;
  color: var(--ink);
  margin-bottom: 14px;
}

.pricing-lead {
  font-size: 16px;
  color: var(--muted);
  max-width: 620px;
  margin: 0 auto;
  line-height: 1.6;
}

/* Pricing Card Component */
.plan-card-custom {
  background: #ffffff;
  border: 1px solid var(--line);
  border-radius: 20px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  flex-direction: column;
  height: 100%;
  position: relative;
  overflow: hidden;
}

.plan-card-custom:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 45px rgba(30, 30, 70, 0.1);
  border-color: #cbd5e1;
}

.plan-card-custom.featured {
  border: 2px solid var(--primary);
  box-shadow: 0 20px 50px rgba(66, 64, 150, 0.15);
}

.plan-ribbon {
  position: absolute;
  top: 16px;
  right: 18px;
  background: #eef2ff;
  color: var(--primary);
  font-size: 11px;
  font-weight: 800;
  padding: 4px 12px;
  border-radius: 999px;
  border: 1px solid rgba(66, 64, 150, 0.2);
}

.plan-card-body {
  padding: 30px 26px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.plan-icon-wrap {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: #eeedff;
  color: var(--primary);
  display: grid;
  place-items: center;
  font-size: 20px;
  margin-bottom: 18px;
}

.plan-name {
  font-size: 22px;
  font-weight: 800;
  color: var(--ink);
  margin-bottom: 6px;
}

.plan-desc {
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 20px;
  min-height: 38px;
  line-height: 1.45;
}

/* Period Switcher Select */
.period-select-box {
  background: #f8fafc;
  border: 1px solid var(--line);
  color: var(--ink);
  font-weight: 600;
  font-size: 13px;
  border-radius: 10px;
  padding: 10px 14px;
  cursor: pointer;
  margin-bottom: 18px;
  transition: all 0.2s ease;
  width: 100%;
}
.period-select-box:focus {
  border-color: var(--primary);
  outline: none;
  box-shadow: 0 0 0 3px rgba(66, 64, 150, 0.15);
}

.plan-price-row {
  margin-bottom: 24px;
  padding-bottom: 18px;
  border-bottom: 1px solid var(--line);
}

.plan-price-val {
  font-size: 34px;
  font-weight: 900;
  color: var(--primary);
  line-height: 1;
  display: flex;
  align-items: baseline;
  gap: 4px;
}
.plan-price-val small {
  font-size: 13px;
  font-weight: 500;
  color: var(--muted);
}

.plan-original-price {
  font-size: 14px;
  color: #94a3b8;
  text-decoration: line-through;
  margin-left: 8px;
  font-weight: 500;
}

/* Feature List */
.plan-features-list {
  list-style: none;
  padding: 0;
  margin: 0 0 28px 0;
  flex: 1;
}
.plan-features-list li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 13.5px;
  color: var(--ink);
  margin-bottom: 12px;
  line-height: 1.45;
}
.plan-features-list li i.fa-check-circle {
  color: var(--green);
  font-size: 15px;
  margin-top: 2px;
  flex-shrink: 0;
}
.plan-features-list li i.fa-times-circle {
  color: #ef4444;
  font-size: 15px;
  margin-top: 2px;
  flex-shrink: 0;
}
.plan-sub-val {
  font-size: 11px;
  color: var(--muted);
  font-weight: 600;
}

/* Plan Button */
.btn-plan-action {
  width: 100%;
  padding: 13px 20px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  transition: all 0.2s ease;
  border: 1px solid var(--primary);
  background: var(--primary);
  color: #ffffff !important;
  box-shadow: 0 10px 25px rgba(66, 64, 150, 0.2);
}
.btn-plan-action:hover {
  background: var(--primary2);
  transform: translateY(-2px);
  box-shadow: 0 14px 28px rgba(66, 64, 150, 0.3);
}
.plan-card-custom:not(.featured) .btn-plan-action {
  background: #ffffff;
  color: var(--primary) !important;
  border-color: var(--line);
  box-shadow: none;
}
.plan-card-custom:not(.featured) .btn-plan-action:hover {
  background: #f8fafc;
  border-color: var(--primary);
}

/* Features Included Across All Plans */
.included-box {
  background: var(--soft);
  border: 1px solid var(--line);
  border-radius: 22px;
  padding: 40px;
  margin-top: 50px;
}
.included-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  text-align: center;
}
.included-item {
  padding: 15px;
}
.included-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: #ffffff;
  color: var(--primary);
  display: grid;
  place-items: center;
  font-size: 18px;
  margin: 0 auto 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.04);
}
.included-item h5 {
  font-size: 15px;
  font-weight: 700;
  color: var(--ink);
  margin-bottom: 4px;
}
.included-item p {
  font-size: 12px;
  color: var(--muted);
  margin: 0;
}

/* Help & Contact Banner */
.custom-plan-cta {
  background: #ffffff;
  border: 1px solid var(--line);
  border-radius: 20px;
  padding: 30px;
  margin-top: 35px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
}

@media (max-width: 900px) {
  .included-grid {
    grid-template-columns: 1fr 1fr;
  }
}
@media (max-width: 600px) {
  .included-grid {
    grid-template-columns: 1fr;
  }
}
</style>
@endsection

@section('content')
<div class="pricing-page-wrapper">
  
  <!-- Header -->
  <section class="pricing-page-header">
    <div class="container">
      <div class="pricing-badge">Simple & Transparent Pricing</div>
      <h1 class="pricing-title">Simple plans for growing teams.</h1>
      <p class="pricing-lead">Choose the perfect HRMS, Attendance & Payroll package for your business size. Zero hidden charges, cancel anytime.</p>
    </div>
  </section>

  <!-- Pricing Cards Section -->
  <section class="py-5 bg-white">
    <div class="container">
      <div class="row g-4 justify-content-center">
        @forelse ($packages as $index => $package)
        <div class="col-12 col-md-6 col-lg-4">
          <div class="plan-card-custom {{ $index == 1 ? 'featured' : '' }}">
            @if($index == 1)
              <div class="plan-ribbon">Most Popular</div>
            @endif

            <div class="plan-card-body">
              <div class="plan-icon-wrap">
                <i class="fa-solid fa-layer-group"></i>
              </div>

              <h3 class="plan-name">{{ $package->package_name }}</h3>
              <p class="plan-desc">{{ strip_tags($package->description) ?: 'Complete HR, Attendance & Payroll toolkit tailored for modern businesses.' }}</p>

              <!-- Billing Period Selector -->
              <div>
                <select class="form-select period-select-box" onchange="updatePrice(this, {{ $package->id }})">
                  <option value="monthly" data-price="{{ $package->monthly_price }}" data-discount-price="{{ $package->monthly_discount_price }}">Billed Monthly</option>
                  <option value="quarterly" data-price="{{ $package->quarterly_price }}" data-discount-price="{{ $package->quarterly_discount_price }}">Billed Quarterly</option>
                  <option value="half-yearly" data-price="{{ $package->halfyearly_price }}" data-discount-price="{{ $package->halfyearly_discount_price }}">Billed Half-Yearly</option>
                  <option value="yearly" data-price="{{ $package->yearly_price }}" data-discount-price="{{ $package->yearly_discount_price }}" selected>Billed Annually (Save)</option>
                </select>
              </div>

              <!-- Price Display -->
              <div class="plan-price-row">
                <div class="plan-price-val">
                  <span id="discount-price{{ $package->id }}">₹{{ number_format($package->yearly_discount_price ?: $package->monthly_discount_price, 0) }}</span>
                  <small id="price-duration{{ $package->id }}">/mo</small>
                  @if($package->monthly_price > $package->monthly_discount_price)
                    <span id="basic-price{{ $package->id }}" class="plan-original-price">₹{{ number_format($package->yearly_price ?: $package->monthly_price, 0) }}</span>
                  @endif
                </div>
              </div>

              <!-- Features Checklist -->
              <ul class="plan-features-list">
                @foreach ($package->features as $feature)
                  <li>
                    @if ($feature->pivot->feature_value == 'No')
                      <i class="fa-regular fa-times-circle text-danger"></i>
                      <span class="text-muted text-decoration-line-through">{{ $feature->name }}</span>
                    @else
                      <i class="fa-solid fa-check-circle"></i>
                      <div>
                        <span class="fw-semibold">{{ $feature->name }}</span>
                        @if ($feature->pivot->feature_value != 'Yes' && $feature->pivot->feature_value != 'No')
                          <span class="plan-sub-val d-block">({{ $feature->pivot->feature_value }})</span>
                        @endif
                      </div>
                    @endif
                  </li>
                @endforeach
              </ul>

              <!-- Action CTA -->
              <a class="btn-plan-action" href="{{ route('register') }}">Start 15-Day Free Trial</a>
            </div>
          </div>
        </div>
        @empty
        <!-- Fallback static modern pricing cards if database is empty -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="plan-card-custom">
            <div class="plan-card-body">
              <div class="plan-icon-wrap"><i class="fa-solid fa-seedling"></i></div>
              <h3 class="plan-name">Starter</h3>
              <p class="plan-desc">For small teams getting organized.</p>
              <div class="plan-price-row">
                <div class="plan-price-val">₹999<small>/mo</small></div>
              </div>
              <ul class="plan-features-list">
                <li><i class="fa-solid fa-check-circle"></i> <span>Attendance & Shifts</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>Leave Management</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>Employee Records</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>Mobile App Access</span></li>
              </ul>
              <a class="btn-plan-action" href="{{ route('register') }}">Get Started</a>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
          <div class="plan-card-custom featured">
            <div class="plan-ribbon">Most Popular</div>
            <div class="plan-card-body">
              <div class="plan-icon-wrap"><i class="fa-solid fa-rocket"></i></div>
              <h3 class="plan-name">Growth</h3>
              <p class="plan-desc">For teams ready to automate HR.</p>
              <div class="plan-price-row">
                <div class="plan-price-val">₹1,999<small>/mo</small></div>
              </div>
              <ul class="plan-features-list">
                <li><i class="fa-solid fa-check-circle"></i> <span>Everything in Starter</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>1-Click Payroll & PF/ESI</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>Download Reports</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>Approval Workflows</span></li>
              </ul>
              <a class="btn-plan-action" href="{{ route('register') }}">Start Trial</a>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
          <div class="plan-card-custom">
            <div class="plan-card-body">
              <div class="plan-icon-wrap"><i class="fa-solid fa-briefcase"></i></div>
              <h3 class="plan-name">Business</h3>
              <p class="plan-desc">For distributed teams and operations.</p>
              <div class="plan-price-row">
                <div class="plan-price-val">₹3,499<small>/mo</small></div>
              </div>
              <ul class="plan-features-list">
                <li><i class="fa-solid fa-check-circle"></i> <span>Everything in Growth</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>Field Force Tracking</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>GPS & Geofencing</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>Task Management</span></li>
              </ul>
              <button type="button" class="btn-plan-action" data-bs-toggle="modal" data-bs-target="#callbackDemoModal">Talk to Us</button>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
          <div class="plan-card-custom">
            <div class="plan-card-body">
              <div class="plan-icon-wrap"><i class="fa-solid fa-building"></i></div>
              <h3 class="plan-name">Enterprise</h3>
              <p class="plan-desc">For larger or specialized requirements.</p>
              <div class="plan-price-row">
                <div class="plan-price-val">Custom</div>
              </div>
              <ul class="plan-features-list">
                <li><i class="fa-solid fa-check-circle"></i> <span>Custom Setup & SLAs</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>Multiple Branches</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>Biometric API Integration</span></li>
                <li><i class="fa-solid fa-check-circle"></i> <span>Dedicated Account Manager</span></li>
              </ul>
              <button type="button" class="btn-plan-action" data-bs-toggle="modal" data-bs-target="#callbackDemoModal">Book Demo</button>
            </div>
          </div>
        </div>
        @endforelse
      </div>

      <!-- Included Across All Plans -->
      <div class="included-box">
        <div class="text-center mb-4">
          <span class="eyebrow" style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: var(--primary); font-weight: 800;">Included Standards</span>
          <h3 style="font-size: 24px; font-weight: 800; color: var(--ink); margin-top: 4px;">All STAFO Plans Include</h3>
        </div>
        <div class="included-grid">
          <div class="included-item">
            <div class="included-icon"><i class="fa-solid fa-mobile-screen"></i></div>
            <h5>Web + Mobile Access</h5>
            <p>Android app for staff & cloud web portal for HR admins.</p>
          </div>
          <div class="included-item">
            <div class="included-icon"><i class="fa-solid fa-chart-pie"></i></div>
            <h5>Admin & Staff Portals</h5>
            <p>Role-based dashboards for HR, branch heads, and employees.</p>
          </div>
          <div class="included-item">
            <div class="included-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <h5>Data Security & Backups</h5>
            <p>256-bit encrypted data with automated cloud backups.</p>
          </div>
          <div class="included-item">
            <div class="included-icon"><i class="fa-solid fa-headset"></i></div>
            <h5>Guided Setup Support</h5>
            <p>Free employee bulk import and policy configuration support.</p>
          </div>
        </div>
      </div>

      <!-- Need Custom Plan Banner -->
      <div class="custom-plan-cta">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div>
            <h4 style="font-size: 19px; font-weight: 800; color: var(--ink); margin-bottom: 4px;">Need a customized plan for 100+ employees?</h4>
            <p style="font-size: 14px; color: var(--muted); margin: 0;">Our enterprise consultants can configure a tailored setup with volume discounts.</p>
          </div>
          <div class="d-flex align-items-center gap-3">
            <a href="tel:+916292252470" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold" style="border-color: var(--line);">
              <i class="fa-solid fa-phone me-1 text-primary"></i> +91 6292252470
            </a>
            <button type="button" class="btn rounded-pill px-4 py-2 fw-bold text-white shadow-sm" style="background: var(--primary);" data-bs-toggle="modal" data-bs-target="#callbackDemoModal">
              Book a Live Demo
            </button>
          </div>
        </div>
      </div>

    </div>
  </section>

</div>
@endsection

@section('js')
<script>
  function updatePrice(select, package_id) {
    const basicPriceView = document.getElementById('basic-price' + package_id);
    const discountPriceView = document.getElementById('discount-price' + package_id);
    const durationView = document.getElementById('price-duration' + package_id);
    
    const selectedOption = select.options[select.selectedIndex];
    const price = selectedOption.getAttribute('data-price');
    const discountPrice = selectedOption.getAttribute('data-discount-price');
    const period = select.value;

    let periodLabel = '/mo';
    if (period === 'quarterly') periodLabel = '/quarter';
    else if (period === 'half-yearly') periodLabel = '/half-yr';
    else if (period === 'yearly') periodLabel = '/yr';

    if (discountPriceView) {
      discountPriceView.textContent = '₹' + Math.round(discountPrice).toLocaleString('en-IN');
    }
    if (basicPriceView && price) {
      basicPriceView.textContent = '₹' + Math.round(price).toLocaleString('en-IN');
    }
    if (durationView) {
      durationView.textContent = periodLabel;
    }
  }
</script>
@endsection