@extends('user.layouts.app')
@section('title', 'Payment Failed | STAFO HRMS')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            <!-- Failure Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Card Header with Brand Logo -->
                <div class="p-4 bg-white border-bottom text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light p-2 rounded-3 border mb-3" style="width: 64px; height: 64px;">
                        <img src="{{ asset('assets/images/icon/c_logo.png') }}" alt="STAFO Logo" style="max-height: 48px; max-width: 48px; object-fit: contain;">
                    </div>
                    <h5 class="fw-bold text-dark mb-0">STAFO HRMS Platform</h5>
                    <small class="text-muted">Payment Notice</small>
                </div>

                <!-- Error Icon & Status -->
                <div class="p-4 text-center" style="background: linear-gradient(180deg, #fef2f2 0%, #ffffff 100%);">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white shadow-sm mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Payment Unsuccessful</h3>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">
                        Your payment could not be processed at this time. No worries — no subscription changes were made to your account.
                    </p>
                </div>

                <!-- Details & Troubleshooting -->
                <div class="p-4 pt-0">
                    <div class="border rounded-3 p-3 bg-light bg-opacity-50 mb-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Transaction Reference</small>
                                <div class="font-monospace small text-dark fw-semibold">{{ $request->txnid ?? 'N/A' }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Status</small>
                                <div><span class="badge bg-danger bg-opacity-10 text-danger fw-semibold">{{ $request->status ?? 'Failed / Cancelled' }}</span></div>
                            </div>
                            @if (!empty($request->error_Message) || !empty($request->message))
                                <div class="col-12">
                                    <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Reason</small>
                                    <div class="text-danger small mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $request->error_Message ?? $request->message }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Helpful Suggestions -->
                    <div class="alert alert-warning border-warning border-opacity-25 rounded-3 mb-4 d-flex align-items-start gap-3">
                        <i class="fa-solid fa-lightbulb text-warning fs-5 mt-1 flex-shrink-0"></i>
                        <div class="small">
                            <strong>Common causes & tips:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                <li>Payment cancelled in UPI app (Google Pay / PhonePe) before entering PIN.</li>
                                <li>Daily bank transaction limit or temporary bank server downtime.</li>
                                <li>If money was deducted from your account, it will be automatically refunded by your bank within 3 to 5 business days.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-column flex-sm-row align-items-center gap-2 pt-2 border-top">
                        <a href="{{ url('contact-us') }}" class="btn btn-outline-secondary w-100 py-2 fw-semibold" style="border-radius: 8px;">
                            <i class="fa-solid fa-headset me-1"></i> Contact Support
                        </a>
                        <a href="{{ route('company-packages.index') }}" class="btn btn-primary w-100 py-2 fw-semibold" style="border-radius: 8px; background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); border: none;">
                            <i class="fa-solid fa-rotate-left me-1"></i> Try Again
                        </a>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="card-footer bg-light p-3 text-center text-muted small border-top">
                    Need urgent help? Reach out to our HR onboarding team anytime.
                </div>
            </div>

        </div>
    </div>
</div>
@endsection