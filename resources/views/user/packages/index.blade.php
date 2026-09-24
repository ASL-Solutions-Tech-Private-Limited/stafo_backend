@extends('user.layouts.app')
@section('title', 'Company Packages & Subscription | STAFO HRMS')

@section('css')
    <style>
        /* Pricing Page Styling - Aligned with STAFO Theme */
        .pricing-hero {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.04) 0%, rgba(14, 165, 233, 0.06) 100%);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 2.5rem 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .pricing-hero::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Current Subscription Banner */
        .current-plan-card {
            border: 1px solid #c7d2fe;
            background: linear-gradient(135deg, #eef2ff 0%, #f0f9ff 100%);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            transition: all 0.2s ease;
        }

        /* Pricing Cards */
        .plan-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .plan-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.12), 0 4px 12px -2px rgba(15, 23, 42, 0.06);
            border-color: #cbd5e1;
        }

        .plan-card.popular-card {
            border: 2px solid #4f46e5;
            box-shadow: 0 12px 28px -6px rgba(79, 70, 229, 0.16);
            background: #ffffff;
        }

        .plan-card.popular-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 36px -6px rgba(79, 70, 229, 0.24);
        }

        .popular-badge-floating {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            font-size: 0.725rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 4px 16px;
            border-radius: 9999px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
            white-space: nowrap;
        }

        .current-plan-badge-floating {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            font-size: 0.725rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 4px 16px;
            border-radius: 9999px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
            white-space: nowrap;
        }

        .plan-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .plan-price-val {
            font-size: 2.25rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .discount-pill {
            background-color: #ecfdf5;
            color: #059669;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 6px;
            border: 1px solid #a7f3d0;
        }

        .feature-bullet {
            font-size: 0.875rem;
            color: #334155;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 0.65rem;
            line-height: 1.4;
        }

        .feature-bullet i {
            margin-top: 2px;
            flex-shrink: 0;
        }

        .btn-upgrade-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        .btn-upgrade-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        }

        .btn-upgrade-outline {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #1e293b;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .btn-upgrade-outline:hover {
            border-color: #4f46e5;
            color: #4f46e5;
            background: #f8fafc;
            transform: translateY(-1px);
        }

        /* Feature Comparison Table */
        .comparison-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
        }

        .comparison-table {
            margin-bottom: 0;
        }

        .comparison-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 16px 14px;
            vertical-align: middle;
            border: none;
        }

        .comparison-table th.feature-col-header {
            text-align: left;
            width: 32%;
        }

        .comparison-table td {
            padding: 13px 14px;
            vertical-align: middle;
            font-size: 0.875rem;
            border-color: #f1f5f9;
        }

        .comparison-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .comparison-table td.feature-title-cell {
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Trust Badges */
        .trust-badge-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem 1rem;
            text-align: center;
            transition: all 0.2s ease;
        }

        .trust-badge-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        /* FAQ Section */
        .faq-card .accordion-button {
            font-weight: 600;
            color: #1e293b;
            background-color: #ffffff;
            padding: 1.1rem 1.25rem;
            box-shadow: none;
            border-radius: 10px !important;
        }

        .faq-card .accordion-button:not(.collapsed) {
            color: #4f46e5;
            background-color: #f5f3ff;
        }

        .faq-card .accordion-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px !important;
            margin-bottom: 0.75rem;
            overflow: hidden;
        }

        .faq-card .accordion-body {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.6;
            padding: 1rem 1.25rem 1.25rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid px-2 px-md-3 py-2">

        <!-- Header Banner -->
        <div class="pricing-hero mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-3 py-1 rounded-pill mb-2" style="font-size: 0.8rem;">
                        <i class="fa-solid fa-gem me-1"></i> Flexible & Scalable HRMS Plans
                    </span>
                    <h2 class="fw-extrabold text-dark mb-2" style="font-size: 1.85rem; letter-spacing: -0.02em;">
                        Choose the Perfect Plan for Your Workforce
                    </h2>
                    <p class="text-muted mb-0" style="font-size: 0.95rem; max-width: 650px;">
                        Automate employee attendance, GPS location tracking, compliant payroll calculation, leave workflows, and task tracking seamlessly.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="#comparisonTableSection" class="btn btn-outline-primary px-3 py-2 fw-semibold" style="border-radius: 8px;">
                        <i class="fa-solid fa-list-check me-1"></i> Compare Features
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Subscription Summary Banner -->
        @php
            $hasActivePlan = false;
            $daysRemaining = 0;
            if (!empty($company->package_id) && !empty($company->subscription_end)) {
                $daysRemaining = round((strtotime($company->subscription_end) - time()) / 86400);
                $hasActivePlan = ($daysRemaining >= 0);
            }
        @endphp

        @if (!empty($company->package_id))
            <div class="current-plan-card mb-4 shadow-xs">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white shadow-xs" style="width: 46px; height: 46px; font-size: 1.25rem;">
                            <i class="fa-solid fa-crown"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-primary text-white text-uppercase fw-semibold" style="font-size: 0.7rem;">Your Current Plan</span>
                                <h5 class="fw-bold text-dark mb-0">{{ $company->package->package_name ?? 'Subscribed Plan' }}</h5>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                                <span><i class="fa-regular fa-calendar-check text-success me-1"></i> Started: <strong>{{ date('d M, Y', strtotime($company->subscription_start)) }}</strong></span>
                                <span><i class="fa-regular fa-calendar-xmark text-danger me-1"></i> Valid Till: <strong>{{ date('d M, Y', strtotime($company->subscription_end)) }}</strong></span>
                                @if(!empty($company->max_employee_add))
                                    <span><i class="fa-solid fa-users text-primary me-1"></i> Max Staff Limit: <strong>{{ $company->max_employee_add }}</strong></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        @if ($hasActivePlan)
                            <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-semibold">
                                <i class="fa-solid fa-circle-check me-1"></i> Active ({{ $daysRemaining }} days remaining)
                            </span>
                        @else
                            <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill fw-semibold">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> Plan Expired
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Pricing Cards Section -->
        <div class="row g-4 mb-5 justify-content-center">
            @foreach ($packages as $index => $package)
                @php
                    $isCurrentPlan = ($company->package_id == $package->id && $hasActivePlan);
                    // Make middle plan or 90 days plan "Most Popular"
                    $isPopular = ($package->days == 90 || $package->package_name == 'Premium Suite' || $loop->iteration == 2);
                    $discountPrice = (float)($package->discount_price ?: $package->price);
                    $originalPrice = (float)$package->price;
                    $hasDiscount = ($originalPrice > $discountPrice);
                    $discountPercentage = $hasDiscount ? round((($originalPrice - $discountPrice) / $originalPrice) * 100) : 0;

                    // PayU credentials & hash calculation
                    $merchantKey = '1AJhSD';
                    $salt = 'tBjCq35cgf3f12ya0usuhEtH9IJ7pSyq';
                    $firstname = $company->company_name ?? 'Company User';
                    $email = $company->email ?? 'info@stafo.in';
                    $amount = $discountPrice;
                    $phone = $company->mobile_no ?? '9999999999';
                    $productinfo = $package->package_name;
                    $txnid = uniqid();
                    $duration = $package->days;
                    $user_id = $company->id;
                    $package_id = $package->id;

                    $hash_string = $merchantKey . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' . $firstname . '|' . $email .'|'.$user_id.'|'.$duration.'|'.$package_id.'||||||||' . $salt;
                    $hash = hash('sha512', $hash_string);
                    $payuUrl = "https://secure.payu.in/_payment";

                    $formData = [
                        'key' => $merchantKey,
                        'txnid' => $txnid,
                        'amount' => $amount,
                        'productinfo' => $productinfo,
                        'firstname' => $firstname,
                        'email' => $email,
                        'phone' => $phone,
                        'udf1' => $user_id,
                        'udf2' => $duration,
                        'udf3' => $package_id,
                        'surl' => url('success'),
                        'furl' => url('failure'),
                        'hash' => $hash
                    ];
                @endphp

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="plan-card p-4 {{ $isPopular ? 'popular-card' : '' }}">
                        
                        <!-- Floating Badge -->
                        @if ($isCurrentPlan)
                            <div class="current-plan-badge-floating">
                                <i class="fa-solid fa-circle-check me-1"></i> Your Active Plan
                            </div>
                        @elseif ($isPopular)
                            <div class="popular-badge-floating">
                                <i class="fa-solid fa-fire me-1"></i> Most Popular Choice
                            </div>
                        @elseif ($package->days >= 180)
                            <div class="popular-badge-floating" style="background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); box-shadow: 0 4px 12px rgba(14, 165, 233, 0.35);">
                                <i class="fa-solid fa-shield-halved me-1"></i> Best Value
                            </div>
                        @endif

                        <!-- Plan Header -->
                        <div class="d-flex align-items-center justify-content-between mb-3 {{ ($isPopular || $isCurrentPlan || $package->days >= 180) ? 'mt-2' : '' }}">
                            <div>
                                <h4 class="fw-bold text-dark mb-1">{{ $package->package_name }}</h4>
                                <span class="text-muted small">
                                    @if ($package->days < 30)
                                        {{ $package->days }} Days Validity
                                    @else
                                        {{ round($package->days / 30) }} Months ({{ $package->days }} Days)
                                    @endif
                                </span>
                            </div>

                            <div class="plan-icon-wrapper {{ $isPopular ? 'bg-primary bg-opacity-10 text-primary' : ($package->days >= 180 ? 'bg-info bg-opacity-10 text-info' : 'bg-secondary bg-opacity-10 text-secondary') }}">
                                @if ($package->days <= 7)
                                    <i class="fa-solid fa-bolt-lightning"></i>
                                @elseif ($isPopular)
                                    <i class="fa-solid fa-gem"></i>
                                @else
                                    <i class="fa-solid fa-crown"></i>
                                @endif
                            </div>
                        </div>

                        <!-- Price Tag -->
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="plan-price-val">₹{{ number_format($discountPrice, 0) }}</span>
                                <span class="text-muted small">
                                    @if ($package->days < 30)
                                        / {{ $package->days }} days
                                    @else
                                        / {{ round($package->days / 30) }} mos
                                    @endif
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-2 mt-2">
                                @if ($hasDiscount)
                                    <span class="text-muted text-decoration-line-through small">₹{{ number_format($originalPrice, 0) }}</span>
                                    <span class="discount-pill"><i class="fa-solid fa-tag me-1"></i>{{ $discountPercentage }}% OFF</span>
                                @endif
                                <span class="text-muted small ms-auto">+ GST applicable</span>
                            </div>
                        </div>

                        <!-- Top Key Features List -->
                        <div class="plan-features mb-4 flex-grow-1">
                            <small class="text-uppercase fw-bold text-muted d-block mb-3" style="font-size: 0.725rem; letter-spacing: 0.05em;">
                                What's included:
                            </small>

                            <!-- Access Employees Limit -->
                            @php
                                $empFeature = $package->features->firstWhere('name', 'Access Employees');
                                $empCount = $empFeature ? ($empFeature->pivot->feature_value ?? '20') : '20';
                            @endphp
                            <div class="feature-bullet">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span>Add up to <strong>{{ $empCount }} Employees</strong></span>
                            </div>

                            <div class="feature-bullet">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span>Real-time Attendance & Geofencing</span>
                            </div>

                            <div class="feature-bullet">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span>1-Click Automated Payroll Generation</span>
                            </div>

                            <div class="feature-bullet">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span>Live GPS Location Tracking</span>
                            </div>

                            <div class="feature-bullet">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span>Leave & Shift Management</span>
                            </div>

                            @if ($package->days >= 30)
                                <div class="feature-bullet">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Task Management & Live Tracking</span>
                                </div>
                                <div class="feature-bullet">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Export Salary Slips & Excel Reports</span>
                                </div>
                            @endif

                            @if ($package->days >= 90)
                                <div class="feature-bullet">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Priority 24/7 Dedicated Support</span>
                                </div>
                            @endif
                        </div>

                        <!-- PayU Checkout Action Button -->
                        <div class="mt-auto pt-2">
                            <form action="{{ $payuUrl }}" method="post" name="payuForm_{{ $package->id }}">
                                @foreach ($formData as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach

                                @if ($isCurrentPlan)
                                    <button type="submit" class="btn btn-outline-success w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px;">
                                        <i class="fa-solid fa-rotate me-1"></i> Renew Active Plan
                                    </button>
                                @elseif ($isPopular)
                                    <button type="submit" class="btn btn-upgrade-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                                        <span>Get Started Now</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-upgrade-outline w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                                        <span>Choose Plan</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                @endif
                            </form>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <!-- Trust Badges Strip -->
        <div class="row g-3 mb-5">
            <div class="col-6 col-md-3">
                <div class="trust-badge-card">
                    <div class="text-warning fs-3 mb-2"><i class="fa-solid fa-bolt-lightning"></i></div>
                    <h6 class="fw-bold text-dark mb-1">Instant Activation</h6>
                    <small class="text-muted">Subscription activates immediately after successful payment</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-badge-card">
                    <div class="text-success fs-3 mb-2"><i class="fa-solid fa-shield-halved"></i></div>
                    <h6 class="fw-bold text-dark mb-1">100% Secure Checkout</h6>
                    <small class="text-muted">Bank-grade 256-bit encrypted transactions via PayU India</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-badge-card">
                    <div class="text-info fs-3 mb-2"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <h6 class="fw-bold text-dark mb-1">GST Tax Invoices</h6>
                    <small class="text-muted">Official GST invoice generated for input tax credit claims</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="trust-badge-card">
                    <div class="text-primary fs-3 mb-2"><i class="fa-solid fa-headset"></i></div>
                    <h6 class="fw-bold text-dark mb-1">Dedicated HR Support</h6>
                    <small class="text-muted">Live chat and phone support for onboarding & query assistance</small>
                </div>
            </div>
        </div>

        <!-- Detailed Feature Comparison Table Section -->
        <div class="comparison-card shadow-sm mb-5" id="comparisonTableSection">
            <div class="p-4 border-bottom bg-light bg-opacity-50">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">Detailed Feature Comparison</h4>
                        <p class="text-muted small mb-0">Compare all modules, tools, and limits included in each STAFO package</p>
                    </div>
                    <span class="badge bg-light text-muted border px-3 py-2">
                        <i class="fa-solid fa-table-columns me-1"></i> {{ count($packages) }} Plans Compared
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table comparison-table align-middle">
                    <thead>
                        <tr>
                            <th class="feature-col-header ps-4">Core HRMS Capabilities</th>
                            @foreach ($packages as $package)
                                <th class="text-center" style="min-width: 170px;">
                                    <div class="fw-bold fs-6 mb-1">{{ $package->package_name }}</div>
                                    <div class="text-white-50 small fw-normal">
                                        ₹{{ number_format($package->discount_price ?: $package->price, 0) }}
                                        ({{ $package->days < 30 ? $package->days . ' Days' : round($package->days / 30) . ' Mos' }})
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Validity Row -->
                        <tr>
                            <td class="feature-title-cell ps-4">
                                <i class="fa-regular fa-clock text-primary fs-6"></i>
                                <span>Package Validity Period</span>
                            </td>
                            @foreach ($packages as $package)
                                <td class="text-center fw-semibold text-dark">
                                    {{ $package->days < 30 ? $package->days . ' Days' : round($package->days / 30) . ' Months (' . $package->days . ' Days)' }}
                                </td>
                            @endforeach
                        </tr>

                        <!-- Pricing Row -->
                        <tr>
                            <td class="feature-title-cell ps-4">
                                <i class="fa-solid fa-indian-rupee-sign text-success fs-6"></i>
                                <span>Subscription Cost</span>
                            </td>
                            @foreach ($packages as $package)
                                <td class="text-center">
                                    <span class="fw-bold text-dark fs-6">₹{{ number_format($package->discount_price ?: $package->price, 2) }}</span>
                                    @if ($package->discount_price && $package->discount_price < $package->price)
                                        <div class="text-muted text-decoration-line-through small" style="font-size: 0.775rem;">
                                            ₹{{ number_format($package->price, 2) }}
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        <!-- Dynamic Feature Rows -->
                        @php
                            $featureIconMap = [
                                'Access Employees' => 'fa-solid fa-users text-primary',
                                'Attendance Management' => 'fa-solid fa-calendar-check text-success',
                                'Payroll Generation' => 'fa-solid fa-receipt text-warning',
                                'Employee Directory' => 'fa-solid fa-address-book text-info',
                                'Basic Reports' => 'fa-solid fa-chart-pie text-purple',
                                'Real Time Locetion Tracking' => 'fa-solid fa-location-dot text-danger',
                                'Real Time Location Tracking' => 'fa-solid fa-location-dot text-danger',
                                'Performance Dashboard' => 'fa-solid fa-gauge-high text-primary',
                                'Task management' => 'fa-solid fa-list-check text-info',
                                'Chat Support' => 'fa-solid fa-comments text-success',
                                'Email Support' => 'fa-solid fa-envelope text-secondary',
                                '24/7 Premium Support' => 'fa-solid fa-headset text-warning',
                                'Leave Management' => 'fa-solid fa-plane-departure text-info',
                                'Branch & Department' => 'fa-solid fa-sitemap text-primary',
                                'Holiday Mangement' => 'fa-solid fa-umbrella-beach text-success',
                                'Holiday Management' => 'fa-solid fa-umbrella-beach text-success',
                                'Shift Managemnt' => 'fa-solid fa-clock text-warning',
                                'Shift Management' => 'fa-solid fa-clock text-warning',
                                'Rank List' => 'fa-solid fa-trophy text-warning',
                                'Trip Management' => 'fa-solid fa-route text-success',
                            ];
                        @endphp

                        @foreach ($features as $feature)
                            <tr>
                                <td class="feature-title-cell ps-4">
                                    <i class="{{ $featureIconMap[$feature->name] ?? 'fa-solid fa-circle-check text-primary' }} fs-6"></i>
                                    <div>
                                        <span class="d-block">{{ $feature->name }}</span>
                                    </div>
                                </td>

                                @foreach ($packages as $package)
                                    @php
                                        $pkgFeature = $package->features->firstWhere('id', $feature->id);
                                        $val = $pkgFeature ? trim((string)$pkgFeature->pivot->feature_value) : null;
                                        $isIncluded = ($pkgFeature && ($val !== 'No' && $val !== 'no' && $val !== '0'));
                                    @endphp

                                    <td class="text-center">
                                        @if ($isIncluded)
                                            @if ($val && !in_array(strtolower($val), ['yes', '1', '']))
                                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-semibold">
                                                    {{ $val }}
                                                </span>
                                            @else
                                                <span class="text-success fs-5" title="Feature Included">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted opacity-35 fs-6" title="Not Included">
                                                <i class="fa-solid fa-minus"></i>
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <td class="ps-4 fw-semibold text-muted">Ready to upgrade?</td>
                            @foreach ($packages as $package)
                                <td class="text-center p-3">
                                    <button type="button" class="btn btn-sm btn-primary px-3 py-1 fw-semibold rounded-pill" onclick="document.forms['payuForm_{{ $package->id }}'].submit()">
                                        Choose {{ $package->package_name }}
                                    </button>
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <div class="text-center mb-4">
                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold px-3 py-1 rounded-pill mb-2" style="font-size: 0.8rem;">
                        Got Questions?
                    </span>
                    <h3 class="fw-bold text-dark">Frequently Asked Questions</h3>
                    <p class="text-muted small">Everything you need to know about STAFO packages, payments, and renewals</p>
                </div>

                <div class="faq-card accordion" id="packageFaqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeadingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne" aria-expanded="true" aria-controls="faqCollapseOne">
                                <i class="fa-regular fa-circle-question text-primary me-2"></i> How quickly does my subscription activate after payment?
                            </button>
                        </h2>
                        <div id="faqCollapseOne" class="accordion-collapse collapse show" aria-labelledby="faqHeadingOne" data-bs-parent="#packageFaqAccordion">
                            <div class="accordion-body">
                                Activation is 100% automated and instant. As soon as your payment is confirmed by PayU, your company account is upgraded immediately with full access to all features.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeadingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
                                <i class="fa-regular fa-circle-question text-primary me-2"></i> Can I upgrade or renew before my current plan expires?
                            </button>
                        </h2>
                        <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadingTwo" data-bs-parent="#packageFaqAccordion">
                            <div class="accordion-body">
                                Yes! You can renew or switch to a higher tier plan at any time. When you purchase a package, your subscription duration is smoothly extended so you never lose access to your staff records.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeadingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree">
                                <i class="fa-regular fa-circle-question text-primary me-2"></i> What payment methods are supported?
                            </button>
                        </h2>
                        <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqHeadingThree" data-bs-parent="#packageFaqAccordion">
                            <div class="accordion-body">
                                We support all major Indian and international payment options via PayU, including UPI (Google Pay, PhonePe, Paytm), Credit Cards, Debit Cards, Net Banking across 50+ banks, and Corporate Wallets.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqHeadingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFour" aria-expanded="false" aria-controls="faqCollapseFour">
                                <i class="fa-regular fa-circle-question text-primary me-2"></i> Do I get a GST-compliant tax invoice?
                            </button>
                        </h2>
                        <div id="faqCollapseFour" class="accordion-collapse collapse" aria-labelledby="faqHeadingFour" data-bs-parent="#packageFaqAccordion">
                            <div class="accordion-body">
                                Absolutely. Once your payment succeeds, an automated GST invoice with your company’s GSTIN and registered business address is sent directly to your registered company email.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection