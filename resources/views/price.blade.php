@extends('frontend.layouts.master')
@section('title', 'Features & Pricing | STAFO - Leading HRMS Solution Provider in India')
@section('heading', 'Features & Pricing')
@section('css')
 <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .plan-header {
      text-align: center;
      margin-bottom: 3rem;
      padding: 2rem 0;
    }

    .plan-header h1 {
      color: #0d19fa;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .plan-header p {
      color: #525252;
      font-size: 1.1rem;
    }

    .price-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: none;
      border-radius: 15px;
      overflow: hidden;
    }

    .price-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .card-body {
      padding: 2rem;
    }

    .plan-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      font-size: 1.5rem;
      color: white;
    }

    .basic-icon {
      background: #2ecc71;
    }

    .pro-icon {
      background: #3498db;
    }

    .premium-icon {
      background: #9b59b6;
    }

    .enterprise-icon {
      background: #34495e;
    }

    .card-title {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: #2c3e50;
    }

    .period-select {
      position: relative;
      background: #f8f9fa;
      border: none;
      border-radius: 10px;
      padding: 0.75rem;
      margin-bottom: 1.5rem;
      font-weight: 500;
      letter-spacing: 0.05rem;
      box-shadow: inset 2px 2px 5px #BABECC,
        inset -5px -5px 10px #ffffff73;
      appearance: none;
      -webkit-appearance: none;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .period-select:focus {
      outline: none;
      box-shadow: inset 2px 2px 5px #BABECC,
        inset -5px -5px 10px #ffffff73;
    }

    .select-wrapper {
      position: relative;
      width: 100%;
    }

    .navbar-nav .nav-link {
      font-weight: 100
    }

    .select-wrapper::after {
      content: "\f078";
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      pointer-events: none;
      font-size: 0.8rem;
      color: #666;
      transition: transform 0.3s ease;
    }

    .select-wrapper:hover::after {
      transform: translateY(-50%) rotate(180deg);
    }

    .price {
      font-size: 1.8rem;
      font-weight: 700;
      color: #01a479;
      margin: 1rem 0;
    }

    .price1 {
      font-size: 2rem;
      font-weight: 700;
      color: #01a479;
      margin: 0;
    }

    .basic-price {
      color: #2c3e50;
      text-decoration: line-through;
    }

    .price small {
      font-size: 1rem;
      color: #666;
      text-decoration: line-through;
    }

    .feature-list {
      display: flex;
      flex-direction: column;
      margin: 2rem 0 2rem 0.75rem;
      padding-left: 0;
    }

    .feature-list li {
      margin-bottom: 1rem;
      display: flex;
      align-items: flex-start;
      position: relative;
      letter-spacing: 0.05rem;

    }

    .feature-list li span {
      position: relative;
      padding-left: 1rem;
      font-weight: 600;
    }

    .feature-list i {
      color: #01a479;
      margin-right: 0.75rem;
      margin-top: 0.25rem;
      width: 1rem;
      text-align: center;
      flex-shrink: 0;
    }

    .sub-feature {
      padding-left: 3.25rem;
      color: #666;
      font-size: 0.9rem;
      margin-top: 0.5rem;
      position: relative;
    }

    .sub-feature::before {
      content: "-";
      position: absolute;
      left: 1.5rem;
      color: #666;
    }

    .btn-custom {
      padding: 0.75rem 2rem;
      font-weight: 600;
      border-radius: 4rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    #plan_button1 {
      margin-top: 4.25rem !important;
    }

    #plan_button2 {
      margin-top: 6.7rem !important;
    }

    .included-features {
      text-align: center;
      margin-top: 4rem;
      padding: 2rem;
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .included-features h3 {
      color: #01a479;
      margin-bottom: 2rem;
      padding-bottom: 2rem;
    }

    .contact-section {
      text-align: center;
      margin-top: 3rem;
    }

    .contact-button {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 1rem 2rem;
      font-weight: 600;
      border-radius: 10px;
      background: #01a479;
      color: white;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .contact-button:hover {
      background: #108364;
      color: white;
      transform: translateY(-2px);
    }
  </style>
@endsection
@section('content')
      <!--pricing start-->
      <div class="container my-4">
        <div class="row justify-content-center text-center">
          <div class="col-lg-8">
            <div class="mb-0">
              <h2><span class="font-w-4 d-block">Simple, Fair and</span> affordable prices for all.</h2>
              <p class="lead mb-0">We use the latest technologies it voluptatem accusantium doloremque laudantium.</p>
            </div>
          </div>
        </div>
      </div>
      <section class="position-relative bg-light pt-0 z-index-1">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-12 z-index-1">
              <div class="row align-items-center mt-4">
                <!-- Plan A -->
                 @foreach ($packages as $package)
                <div class="col-12 col-lg-4 col-md-6 mb-4">
                  <div class="card h-100 price-card shadow">
                    <div class="card-body">
                      <div class="plan-icon basic-icon">
                        <i class="fas fa-user-tie"></i>
                      </div>
                      <h5 class="card-title text-center">{{ $package->package_name }}</h5>
                      <p class="text-center text-muted mb-4">{{ strip_tags($package->description) }}</p>
                      <div class="select-wrapper">
                        <div class="select-wrapper">
                          <select class="form-select period-select"
                            onchange="updatePrice(this, {{ $package->id }})">
                            <option value="monthly" data-price="{{ $package->monthly_price }}" data-discount-price="{{ $package->monthly_discount_price }}">Monthly</option>
                            <option value="quarterly" data-price="{{ $package->quarterly_price }}" data-discount-price="{{ $package->quarterly_discount_price }}">Quarterly</option>
                            <option value="half-yearly" data-price="{{ $package->halfyearly_price }}" data-discount-price="{{ $package->halfyearly_discount_price }}">Half-Yearly</option>
                            <option value="yearly" data-price="{{ $package->yearly_price }}" data-discount-price="{{ $package->yearly_discount_price }}">Yearly</option>
                          </select>
                        </div>
                      </div>
                      <div class="price text-center"><span id="basic-price{{ $package->id }}" class="basic-price">₹{{ number_format($package->monthly_price, 2) }}</span><small>/monthly</small></div>
                      <div class="price1 text-center"><span id="discount-price{{ $package->id }}">₹{{ number_format($package->monthly_discount_price, 2) }}</span><small
                          id="basic-month">/monthly</small>
                      </div>
                      <ul class="feature-list list-unstyled">
                        @foreach ($package->features as $feature)
                          
                            <li>
                              @if ($feature->pivot->feature_value == 'No')
                                <i class="fas fa-times-circle text-danger"></i>
                              @else
                                <i class="fas fa-check-circle"></i>
                              @endif
                              <div>
                                <span>{{ $feature->name }}</span>
                                @if ($feature->pivot->feature_value != 'Yes' && $feature->pivot->feature_value != 'No')
                                  <div class="sub-feature">{{ $feature->pivot->feature_value }}</div>
                                @endif
                              </div>
                            </li>
                          
                        @endforeach
                      </ul>
                      <a class="btn btn-custom btn-outline-success w-100" href="{{ route('login')}}">Buy Now</a>
                    </div>
                  </div>
                </div>
                @endforeach
                
              </div>


              <!-- Included Features Section -->
              <section class="included-features col">

                <h3><i class="fas fa-gift me-2"></i>All Plans Include</h3>
                <div class="row row-cols-1 row-cols-md-4 g-4">
                  <div class="col">
                    <i class="fas fa-mobile-alt fa-2x mb-3 text-primary"></i>
                    <h5>Web + App Access</h5>
                  </div>
                  <div class="col">
                    <i class="fas fa-tachometer-alt fa-2x mb-3 text-primary"></i>
                    <h5>Admin & Employee Dashboards</h5>
                  </div>
                  <div class="col">
                    <i class="fas fa-shield-alt fa-2x mb-3 text-primary"></i>
                    <h5>Data Security & Cloud Backup</h5>
                  </div>
                  <div class="col">
                    <i class="fas fa-headset fa-2x mb-3 text-primary"></i>
                    <h5>Free Setup Support</h5>
                  </div>
                </div>
              </section>

              <!-- Contact Section -->
              <div class="contact-section col">
                <!-- <a href="#" class="contact-button">
                  <i class="fas fa-calendar-check"></i>
                  Book a Free Demo
                </a> -->
                <p class="mt-3">Need help choosing a plan? Call us at <strong>+91 6292252470</strong></p>
              </div>
            </div>
          </div>
        </div>
      </section>


      <!--pricing end-->
@endsection

@section('js')

<script>
    function updatePrice(select,package_id) {
      const basicPriceView = document.getElementById('basic-price' + package_id);
      const discountPriceView = document.getElementById('discount-price' + package_id);
      
      const selectedOption = select.options[select.selectedIndex];
      const price = selectedOption.getAttribute('data-price');
      const discountPrice = selectedOption.getAttribute('data-discount-price');
      basicPriceView.textContent = '₹' + parseFloat(price).toFixed(2);
      discountPriceView.textContent = '₹' + parseFloat(discountPrice).toFixed(2);
      
    }
  </script>

@endsection