@extends('user.layouts.app')
@section('title', 'Payment Successful | STAFO HRMS')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            <!-- Success Receipt Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Card Header with Brand Logo -->
                <div class="p-4 bg-white border-bottom text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light p-2 rounded-3 border mb-3" style="width: 64px; height: 64px;">
                        <img src="{{ asset('assets/images/icon/c_logo.png') }}" alt="STAFO Logo" style="max-height: 48px; max-width: 48px; object-fit: contain;">
                    </div>
                    <h5 class="fw-bold text-dark mb-0">STAFO HRMS Platform</h5>
                    <small class="text-muted">Subscription Payment Receipt</small>
                </div>

                <!-- Success Icon & Header -->
                <div class="p-4 text-center" style="background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white shadow-sm mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Payment Successful!</h3>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">
                        Thank you for subscribing to <strong>STAFO {{ $package->package_name ?? 'HRMS' }}</strong>. Your company account is now fully active with instant access to all package features.
                    </p>
                </div>

                <!-- Transaction Details Table -->
                <div class="p-4 pt-0">
                    <div class="border rounded-3 p-3 bg-light bg-opacity-50 mb-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Subscribed Package</small>
                                <div class="fw-bold text-dark fs-6">{{ $package->package_name ?? 'Standard Plan' }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Amount Paid</small>
                                <div class="fw-bold text-success fs-5">₹{{ number_format((float)($request->amount ?? $package->discount_price ?? $package->price), 2) }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Transaction Reference</small>
                                <div class="font-monospace small text-dark fw-semibold">{{ $request->txnid ?? 'TXN' . time() }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Payment Gateway</small>
                                <div><span class="badge bg-primary bg-opacity-10 text-primary fw-semibold"><i class="fa-solid fa-shield-halved me-1"></i> PayU Verified</span></div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Activation Date</small>
                                <div class="text-dark small fw-medium">{{ date('d M, Y') }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Valid Till</small>
                                <div class="text-success small fw-bold">{{ date('d M, Y', strtotime($subscription)) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Instant Features Available -->
                    <div class="mb-4">
                        <small class="text-uppercase fw-bold text-muted d-block mb-2" style="font-size: 0.725rem; letter-spacing: 0.05em;">Included with your plan:</small>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 small text-dark">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Real-time GPS Tracking & Geofencing</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 small text-dark">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Automated Payroll & Salary Slips</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 small text-dark">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Leave, Shift & Attendance Logs</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2 small text-dark">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <span>Priority 24/7 Dedicated Support</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-column flex-sm-row align-items-center gap-2 pt-2 border-top">
                        <a href="{{ route('subscriptionDetails') }}" class="btn btn-outline-primary w-100 py-2 fw-semibold" style="border-radius: 8px;">
                            <i class="fa-solid fa-file-invoice me-1"></i> Subscription Details
                        </a>
                        <a href="{{ url('dashboard') }}" class="btn btn-primary w-100 py-2 fw-semibold" style="border-radius: 8px; background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); border: none;">
                            <i class="fa-solid fa-gauge-high me-1"></i> Go to Dashboard
                        </a>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="card-footer bg-light p-3 text-center text-muted small border-top">
                    An official GST invoice has been emailed to your registered company address.
                </div>
            </div>

        </div>
    </div>
</div>
@endsection