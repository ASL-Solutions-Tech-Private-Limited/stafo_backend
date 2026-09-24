@extends('employee.layouts.app')

@section('title', 'My Documents & KYC Verification | STAFO HRMS')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="fw-bold text-dark mb-0">Documents & KYC Verification</h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-shield-halved me-1"></i> Self-Service KYC Portal
                </span>
            </div>
            <p class="text-muted small mb-0">Direct real-time government verification for your identity and compliance records.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3 shadow-xs">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @php
        $isAadharVerified = ($employee_info->aadhar_verify === 'Yes' || $employee_info->aadhar_verify === '1' || strtolower((string)$employee_info->aadhar_verify) === 'yes');
        $isPanVerified = ($employee_info->pan_verify === 'Yes' || $employee_info->pan_verify === '1' || strtolower((string)$employee_info->pan_verify) === 'yes');
        $isVoterVerified = ($employee_info->voter_verify === 'Yes' || $employee_info->voter_verify === '1' || strtolower((string)$employee_info->voter_verify) === 'yes');
        $isDlVerified = ($employee_info->dl_verify === 'Yes' || $employee_info->dl_verify === '1' || strtolower((string)$employee_info->dl_verify) === 'yes');

        $verifiedCount = ($isAadharVerified ? 1 : 0) + ($isPanVerified ? 1 : 0) + ($isVoterVerified ? 1 : 0) + ($isDlVerified ? 1 : 0);
        $completionPct = round(($verifiedCount / 4) * 100);
    @endphp

    <input type="hidden" name="user_id" id="user_id" value="{{ $employee_info->id }}">

    <!-- Hero KYC Progress Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 kyc-hero-card">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-7 col-lg-8">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="kyc-avatar-ring {{ $verifiedCount >= 2 ? 'is-verified' : 'is-pending' }}">
                            <i class="fa-solid {{ $verifiedCount >= 4 ? 'fa-award' : ($verifiedCount >= 2 ? 'fa-shield-check' : 'fa-hourglass-half') }}"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0">
                                @if($verifiedCount == 4)
                                    All Documents Verified (100%)
                                @elseif($verifiedCount >= 2)
                                    KYC Active & Verified ({{ $completionPct }}%)
                                @else
                                    Verification Incomplete ({{ $completionPct }}%)
                                @endif
                            </h4>
                        </div>
                    </div>

                    <!-- Mini status pills -->
                    <div class="d-flex flex-wrap gap-2 mt-3 pt-2 border-top border-light-subtle">
                        <span class="kyc-mini-pill {{ $isAadharVerified ? 'verified' : 'unverified' }}">
                            <i class="fa-solid {{ $isAadharVerified ? 'fa-circle-check text-success' : 'fa-circle-dot text-muted' }}"></i> Aadhaar
                        </span>
                        <span class="kyc-mini-pill {{ $isPanVerified ? 'verified' : 'unverified' }}">
                            <i class="fa-solid {{ $isPanVerified ? 'fa-circle-check text-success' : 'fa-circle-dot text-muted' }}"></i> PAN Card
                        </span>
                        <span class="kyc-mini-pill {{ $isVoterVerified ? 'verified' : 'unverified' }}">
                            <i class="fa-solid {{ $isVoterVerified ? 'fa-circle-check text-success' : 'fa-circle-dot text-muted' }}"></i> Voter ID
                        </span>
                        <span class="kyc-mini-pill {{ $isDlVerified ? 'verified' : 'unverified' }}">
                            <i class="fa-solid {{ $isDlVerified ? 'fa-circle-check text-success' : 'fa-circle-dot text-muted' }}"></i> Driving License
                        </span>
                    </div>
                </div>

                <div class="col-12 col-md-5 col-lg-4 text-md-end">
                    <div class="p-3 bg-white bg-opacity-75 rounded-4 border shadow-xs d-inline-block text-start w-100" style="max-width: 320px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-dark">Verification Meter</span>
                            <span class="badge {{ $verifiedCount >= 2 ? 'bg-success' : 'bg-warning text-dark' }} px-2 py-0.5 rounded-pill fw-bold">
                                {{ $verifiedCount }}/4 Completed
                            </span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 999px; background-color: #e2e8f0;">
                            <div class="progress-bar {{ $verifiedCount == 4 ? 'bg-primary' : 'bg-success' }} progress-bar-striped progress-bar-animated rounded-pill" 
                                role="progressbar" style="width: {{ $completionPct }}%;" aria-valuenow="{{ $completionPct }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">
                            <i class="fa-solid fa-lock text-success me-1"></i> Once verified, details cannot be altered without admin review.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Cards Grid -->
    <div class="row g-4 mb-4">

        <!-- 1. Aadhaar Card -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 kyc-card {{ $isAadharVerified ? 'is-verified' : '' }}" id="card_aadhar">
                <div class="kyc-card-flag-bar"></div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="kyc-icon-badge bg-primary-subtle text-primary">
                                    <i class="fa-solid fa-id-card"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="fw-bold text-dark mb-0">Aadhaar Card</h5>
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">UIDAI</span>
                                    </div>
                                    <small class="text-muted">12-digit Unique Identification Authority of India</small>
                                </div>
                            </div>
                            @if($isAadharVerified)
                                <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-semibold shadow-xs">
                                    <i class="fa-solid fa-circle-check me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-semibold shadow-xs">
                                    <i class="fa-solid fa-clock me-1"></i> Unverified
                                </span>
                            @endif
                        </div>

                        <!-- Aadhaar Input -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="aadhar" class="form-label fw-semibold text-dark small mb-0">
                                    Aadhaar Number
                                </label>
                                @if($isAadharVerified)
                                    <span class="text-success small fw-semibold" style="font-size: 0.74rem;">
                                        <i class="fa-solid fa-lock me-1"></i> Locked & Verified
                                    </span>
                                @else
                                    <span class="text-muted small" id="aadhar_counter" style="font-size: 0.74rem;">12 Digits</span>
                                @endif
                            </div>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-fingerprint"></i>
                                </span>
                                @if($isAadharVerified)
                                    <input type="text" class="form-control border-start-0 font-monospace bg-light text-muted fw-bold" 
                                        name="aadhar_display" id="aadhar_display"
                                        value="{{ '•••• •••• ' . substr($employee_info->aadhar, -4) }}" 
                                        readonly disabled>
                                    <input type="hidden" name="aadhar" id="aadhar" value="{{ $employee_info->aadhar }}">
                                @else
                                    <input type="text" class="form-control border-start-0 font-monospace fw-semibold" 
                                        name="aadhar" id="aadhar" maxlength="14"
                                        value="{{ old('aadhar', $employee_info->aadhar) }}" 
                                        placeholder="1234 5678 9012"
                                        oninput="formatAadhaarInput(this)">
                                @endif
                            </div>

                            @if($isAadharVerified)
                                <div class="verified-audit-note mt-2">
                                    <i class="fa-solid fa-shield-check text-success me-1"></i>
                                    <span>Digitally verified via UIDAI One-Time Password (OTP) gateway.</span>
                                </div>
                            @else
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    An OTP will be dispatched to your UIDAI registered mobile number for authentication.
                                </small>
                            @endif
                        </div>

                        <!-- OTP Section for Aadhaar (Revealed only when OTP requested) -->
                        @if(!$isAadharVerified)
                        <div class="mb-3 p-3 bg-light rounded-4 border kyc-otp-box shadow-xs" id="aadhar_otp_group" style="display:none;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-1.5">
                                    <i class="fa-solid fa-key text-primary"></i>
                                    <span class="fw-bold text-dark small">Enter 6-Digit OTP</span>
                                </div>
                                <div id="otp_timer_wrap">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle" id="otp_timer_text">01:00</span>
                                </div>
                            </div>

                            <div class="input-group input-group-lg mb-2">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-mobile-screen"></i></span>
                                <input type="text" class="form-control border-start-0 font-monospace text-center fw-bold fs-5" 
                                    name="aadhar_otp" id="aadhar_otp" maxlength="6" 
                                    placeholder="••••••" autocomplete="one-time-code">
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-muted" style="font-size: 0.73rem;">
                                    Check SMS received on your registered Aadhaar mobile.
                                </small>
                                <a href="javascript:void(0)" onclick="resendAadharOtp()" id="resend_otp_link" 
                                    class="text-primary text-decoration-none fw-semibold small disabled-link">
                                    <i class="fa-solid fa-arrows-rotate me-1"></i> Resend OTP
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="pt-3 border-top mt-3">
                        @if($isAadharVerified)
                            <div class="btn-verified-status">
                                <i class="fa-solid fa-circle-check text-success fs-5 me-2"></i>
                                <div class="text-start">
                                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">Identity Verified & Secured</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Government Aadhaar record active</div>
                                </div>
                            </div>
                        @else
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary px-3 rounded-3" id="aadhar_update_btn" type="button" onclick="updateData('aadhar')" title="Save number for later">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Save
                                </button>
                                <button class="btn btn-primary flex-grow-1 shadow-sm rounded-3 py-2 fw-semibold" id="aadhar_button" type="button" onclick="verifyData('aadhar')">
                                    <i class="fa-solid fa-paper-plane me-1.5"></i> Send OTP for Verification
                                </button>
                                <button class="btn btn-success flex-grow-1 shadow-sm rounded-3 py-2 fw-semibold" id="aadhar_otp_button" type="button" onclick="verifyData('aadhar-otp')" style="display:none">
                                    <i class="fa-solid fa-check-double me-1.5"></i> Submit & Verify OTP
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. PAN Card -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 kyc-card {{ $isPanVerified ? 'is-verified' : '' }}" id="card_pan">
                <div class="kyc-card-accent-bar bg-success"></div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="kyc-icon-badge bg-success-subtle text-success">
                                    <i class="fa-solid fa-credit-card"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="fw-bold text-dark mb-0">PAN Card</h5>
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">INCOME TAX</span>
                                    </div>
                                    <small class="text-muted">Permanent Account Number (10 Alphanumeric Characters)</small>
                                </div>
                            </div>
                            @if($isPanVerified)
                                <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-semibold shadow-xs">
                                    <i class="fa-solid fa-circle-check me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-semibold shadow-xs">
                                    <i class="fa-solid fa-clock me-1"></i> Unverified
                                </span>
                            @endif
                        </div>

                        <!-- PAN Input -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="pan" class="form-label fw-semibold text-dark small mb-0">
                                    PAN Number
                                </label>
                                @if($isPanVerified)
                                    <span class="text-success small fw-semibold" style="font-size: 0.74rem;">
                                        <i class="fa-solid fa-lock me-1"></i> Locked & Verified
                                    </span>
                                @else
                                    <span class="text-muted small" style="font-size: 0.74rem;">Format: ABCDE1234F</span>
                                @endif
                            </div>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-id-badge"></i>
                                </span>
                                @if($isPanVerified)
                                    <input type="text" class="form-control border-start-0 font-monospace bg-light text-muted fw-bold text-uppercase" 
                                        name="pan_display" id="pan_display"
                                        value="{{ substr($employee_info->pan, 0, 2) . '•••••' . substr($employee_info->pan, -2) }}" 
                                        readonly disabled>
                                    <input type="hidden" name="pan" id="pan" value="{{ $employee_info->pan }}">
                                @else
                                    <input type="text" class="form-control border-start-0 font-monospace fw-semibold text-uppercase" 
                                        name="pan" id="pan" maxlength="10"
                                        value="{{ old('pan', $employee_info->pan) }}" 
                                        placeholder="e.g. ABCDE1234F"
                                        oninput="this.value = this.value.toUpperCase()">
                                @endif
                            </div>

                            @if($isPanVerified)
                                <div class="verified-audit-note mt-2">
                                    <i class="fa-solid fa-shield-check text-success me-1"></i>
                                    <span>Verified directly with Income Tax Department records.</span>
                                </div>
                            @else
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    Instant real-time verification against NSDL tax database.
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-top mt-3">
                        @if($isPanVerified)
                            <div class="btn-verified-status">
                                <i class="fa-solid fa-circle-check text-success fs-5 me-2"></i>
                                <div class="text-start">
                                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">Identity Verified & Secured</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Income Tax record validated</div>
                                </div>
                            </div>
                        @elseif(empty($employee_info->pan))
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary w-100 shadow-sm rounded-3 py-2 fw-semibold" type="button" onclick="updateData('pan')">
                                    <i class="fa-solid fa-floppy-disk me-1.5"></i> Save PAN Number
                                </button>
                            </div>
                        @else
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary px-3 rounded-3" type="button" onclick="updateData('pan')">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update
                                </button>
                                <button class="btn btn-success flex-grow-1 shadow-sm rounded-3 py-2 fw-semibold" type="button" onclick="verifyData('pan')">
                                    <i class="fa-solid fa-shield-halved me-1.5"></i> Verify PAN Instantly
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Voter ID Card -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 kyc-card {{ $isVoterVerified ? 'is-verified' : '' }}" id="card_voter">
                <div class="kyc-card-accent-bar bg-warning"></div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="kyc-icon-badge bg-warning-subtle text-warning">
                                    <i class="fa-solid fa-address-card"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="fw-bold text-dark mb-0">Voter ID (EPIC)</h5>
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">ECI</span>
                                    </div>
                                    <small class="text-muted">Election Commission of India Electoral Photo ID</small>
                                </div>
                            </div>
                            @if($isVoterVerified)
                                <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-semibold shadow-xs">
                                    <i class="fa-solid fa-circle-check me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-semibold shadow-xs">
                                    <i class="fa-solid fa-clock me-1"></i> Unverified
                                </span>
                            @endif
                        </div>

                        <!-- Voter Input -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="voter" class="form-label fw-semibold text-dark small mb-0">
                                    Voter ID / EPIC Number
                                </label>
                                @if($isVoterVerified)
                                    <span class="text-success small fw-semibold" style="font-size: 0.74rem;">
                                        <i class="fa-solid fa-lock me-1"></i> Locked & Verified
                                    </span>
                                @else
                                    <span class="text-muted small" style="font-size: 0.74rem;">e.g. WBF1234567</span>
                                @endif
                            </div>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-check-to-slot"></i>
                                </span>
                                @if($isVoterVerified)
                                    <input type="text" class="form-control border-start-0 font-monospace bg-light text-muted fw-bold text-uppercase" 
                                        name="voter_display" id="voter_display"
                                        value="{{ $employee_info->voter }}" 
                                        readonly disabled>
                                    <input type="hidden" name="voter" id="voter" value="{{ $employee_info->voter }}">
                                @else
                                    <input type="text" class="form-control border-start-0 font-monospace fw-semibold text-uppercase" 
                                        name="voter" id="voter"
                                        value="{{ old('voter', $employee_info->voter) }}" 
                                        placeholder="Enter EPIC number"
                                        oninput="this.value = this.value.toUpperCase()">
                                @endif
                            </div>

                            @if($isVoterVerified)
                                <div class="verified-audit-note mt-2">
                                    <i class="fa-solid fa-shield-check text-success me-1"></i>
                                    <span>Verified with Election Commission Electoral registry.</span>
                                </div>
                            @else
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    Enter alphanumeric EPIC code found on your Voter ID card.
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-top mt-3">
                        @if($isVoterVerified)
                            <div class="btn-verified-status">
                                <i class="fa-solid fa-circle-check text-success fs-5 me-2"></i>
                                <div class="text-start">
                                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">Identity Verified & Secured</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Electoral Commission record active</div>
                                </div>
                            </div>
                        @elseif(empty($employee_info->voter))
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary w-100 shadow-sm rounded-3 py-2 fw-semibold" type="button" onclick="updateData('voter')">
                                    <i class="fa-solid fa-floppy-disk me-1.5"></i> Save Voter ID
                                </button>
                            </div>
                        @else
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary px-3 rounded-3" type="button" onclick="updateData('voter')">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update
                                </button>
                                <button class="btn btn-warning flex-grow-1 shadow-sm rounded-3 py-2 fw-bold text-dark" type="button" onclick="verifyData('voter')">
                                    <i class="fa-solid fa-shield-halved me-1.5"></i> Verify Voter ID
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Driving License -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 kyc-card {{ $isDlVerified ? 'is-verified' : '' }}" id="card_dl">
                <div class="kyc-card-accent-bar bg-info"></div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="kyc-icon-badge bg-info-subtle text-info">
                                    <i class="fa-solid fa-car"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="fw-bold text-dark mb-0">Driving License</h5>
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">PARIVAHAN</span>
                                    </div>
                                    <small class="text-muted">Transport Authority Motor Driving License (MoRTH)</small>
                                </div>
                            </div>
                            @if($isDlVerified)
                                <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-semibold shadow-xs">
                                    <i class="fa-solid fa-circle-check me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-semibold shadow-xs">
                                    <i class="fa-solid fa-clock me-1"></i> Unverified
                                </span>
                            @endif
                        </div>

                        <!-- Driving License Input -->
                        <div class="mb-3">
                            <div class="row g-2">
                                <div class="col-12 col-sm-7">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="driving_license" class="form-label fw-semibold text-dark small mb-0">
                                            License Number
                                        </label>
                                        @if($isDlVerified)
                                            <span class="text-success small fw-semibold" style="font-size: 0.74rem;">
                                                <i class="fa-solid fa-lock me-1"></i> Locked
                                            </span>
                                        @endif
                                    </div>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-id-card-clip"></i></span>
                                        @if($isDlVerified)
                                            <input type="text" class="form-control border-start-0 font-monospace bg-light text-muted fw-bold text-uppercase" 
                                                name="driving_license_display" id="driving_license_display"
                                                value="{{ $employee_info->driving_license }}" 
                                                readonly disabled>
                                            <input type="hidden" name="driving_license" id="driving_license" value="{{ $employee_info->driving_license }}">
                                        @else
                                            <input type="text" class="form-control border-start-0 font-monospace fw-semibold text-uppercase" 
                                                name="driving_license" id="driving_license"
                                                value="{{ old('driving_license', $employee_info->driving_license) }}" 
                                                placeholder="DL-0420110012345"
                                                oninput="this.value = this.value.toUpperCase()">
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 col-sm-5">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="dob" class="form-label fw-semibold text-dark small mb-0">
                                            DOB (as in DL)
                                        </label>
                                    </div>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-calendar-day"></i></span>
                                        @if($isDlVerified)
                                            <input type="text" class="form-control border-start-0 bg-light text-muted fw-bold" 
                                                name="dob_display" id="dob_display"
                                                value="{{ $employee_info->dob ? date('d M Y', strtotime($employee_info->dob)) : 'Verified' }}" 
                                                readonly disabled>
                                            <input type="hidden" name="dob" id="dob" value="{{ $employee_info->dob }}">
                                        @else
                                            <input type="date" class="form-control border-start-0" 
                                                name="dob" id="dob"
                                                value="{{ old('dob', $employee_info->dob ? date('Y-m-d', strtotime($employee_info->dob)) : '') }}">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($isDlVerified)
                                <div class="verified-audit-note mt-2">
                                    <i class="fa-solid fa-shield-check text-success me-1"></i>
                                    <span>Verified with Ministry of Road Transport & Highways (MoRTH).</span>
                                </div>
                            @else
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    Enter DL number & Date of Birth exactly matching your physical license.
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-top mt-3">
                        @if($isDlVerified)
                            <div class="btn-verified-status">
                                <i class="fa-solid fa-circle-check text-success fs-5 me-2"></i>
                                <div class="text-start">
                                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">Identity Verified & Secured</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Parivahan license active</div>
                                </div>
                            </div>
                        @elseif(empty($employee_info->driving_license))
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary w-100 shadow-sm rounded-3 py-2 fw-semibold" type="button" onclick="updateData('driving_license')">
                                    <i class="fa-solid fa-floppy-disk me-1.5"></i> Save Driving License
                                </button>
                            </div>
                        @else
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary px-3 rounded-3" type="button" onclick="updateData('driving_license')">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Update
                                </button>
                                <button class="btn btn-info text-white flex-grow-1 shadow-sm rounded-3 py-2 fw-semibold" type="button" onclick="verifyData('driving_license')">
                                    <i class="fa-solid fa-shield-halved me-1.5"></i> Verify DL Instantly
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Inlined Scoped Styles for KYC Presentation -->
<style>
    .kyc-hero-card {
        background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .kyc-avatar-ring {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .kyc-avatar-ring.is-verified {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 2px solid rgba(16, 185, 129, 0.3);
    }
    .kyc-avatar-ring.is-pending {
        background: rgba(245, 158, 11, 0.12);
        color: #d97706;
        border: 2px solid rgba(245, 158, 11, 0.3);
    }
    .kyc-mini-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 600;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
    }
    .kyc-mini-pill.verified {
        border-color: rgba(16, 185, 129, 0.4);
        background: rgba(16, 185, 129, 0.05);
        color: #065f46;
    }
    .kyc-card {
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
    }
    .kyc-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08) !important;
    }
    .kyc-card.is-verified {
        border-left: 4px solid #10b981 !important;
    }
    .kyc-card-flag-bar {
        height: 4px;
        width: 100%;
        background: linear-gradient(90deg, #ff9933 33.33%, #ffffff 33.33%, #ffffff 66.66%, #138808 66.66%);
        border-bottom: 1px solid #e2e8f0;
    }
    .kyc-card-accent-bar {
        height: 4px;
        width: 100%;
    }
    .kyc-icon-badge {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .verified-audit-note {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.76rem;
        color: #059669;
        font-weight: 500;
        background: rgba(16, 185, 129, 0.08);
        padding: 0.45rem 0.75rem;
        border-radius: 8px;
    }
    .btn-verified-status {
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.25);
        border-radius: 12px;
        padding: 0.65rem 1rem;
        width: 100%;
    }
    .disabled-link {
        pointer-events: none;
        opacity: 0.5;
    }
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .shadow-xs {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    /* Dark Mode Adjustments */
    [data-theme="dark"] .kyc-hero-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .kyc-card {
        background: #1e293b !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .kyc-mini-pill {
        background: #0f172a !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        color: #cbd5e1 !important;
    }
    [data-theme="dark"] .btn-verified-status {
        background: rgba(16, 185, 129, 0.15) !important;
        border-color: rgba(16, 185, 129, 0.35) !important;
    }
    [data-theme="dark"] .btn-verified-status .text-dark {
        color: #f1f5f9 !important;
    }
</style>
@endsection

@section('js')
<script>
    // State of verification for each document to prevent duplicate execution
    const verifiedStatus = {
        aadhar: {{ $isAadharVerified ? 'true' : 'false' }},
        pan: {{ $isPanVerified ? 'true' : 'false' }},
        voter: {{ $isVoterVerified ? 'true' : 'false' }},
        driving_license: {{ $isDlVerified ? 'true' : 'false' }}
    };

    /**
     * Format Aadhaar number with spaces (1234 5678 9012)
     */
    function formatAadhaarInput(input) {
        var value = input.value.replace(/\D/g, '');
        var formatted = '';
        for (var i = 0; i < value.length && i < 12; i++) {
            if (i > 0 && i % 4 === 0) {
                formatted += ' ';
            }
            formatted += value[i];
        }
        input.value = formatted;
        
        var counter = document.getElementById('aadhar_counter');
        if (counter) {
            var rawLen = value.length > 12 ? 12 : value.length;
            counter.innerText = rawLen + ' / 12 Digits';
            if (rawLen === 12) {
                counter.className = 'text-success fw-bold small';
            } else {
                counter.className = 'text-muted small';
            }
        }
    }

    /**
     * Save/Update document number without immediate verification
     */
    function updateData(type) {
        if (verifiedStatus[type]) {
            Swal.fire({
                title: 'Document Already Verified',
                text: 'This document is permanently locked and cannot be edited. Please contact HR for assistance.',
                icon: 'info',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        var inputEl = document.getElementById(type);
        var rawData = inputEl ? inputEl.value.trim() : '';

        // Clean spaces from Aadhaar
        var data = (type === 'aadhar') ? rawData.replace(/\s+/g, '') : rawData;
        var user_id = document.getElementById('user_id').value;
        var url = "{{ route('employee.documents.updateData') }}";

        if (!data) {
            Swal.fire({
                title: 'Empty Field',
                text: 'Please enter a valid document number first.',
                icon: 'warning',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }

        Swal.fire({
            title: 'Saving Document...',
            text: 'Updating your profile records securely.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: url,
            type: 'PUT',
            data: {
                id: user_id,
                data: data,
                type: type,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                Swal.fire({
                    title: 'Saved Successfully',
                    text: response.message || 'Document details saved on your profile.',
                    icon: 'success',
                    timer: 1400,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                var errText = 'Failed to update document details.';
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) {
                        errText = res.message;
                    }
                } catch(e) {}

                Swal.fire({
                    title: 'Unable to Save',
                    text: errText,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    }

    var aadhar_request_id = '';
    var otpTimerInterval = null;

    /**
     * Start 60 second countdown for OTP Resend
     */
    function startOtpCountdown(durationSeconds) {
        var timerEl = document.getElementById('otp_timer_text');
        var resendLink = document.getElementById('resend_otp_link');
        var timeLeft = durationSeconds;

        if (resendLink) {
            resendLink.classList.add('disabled-link');
        }

        if (otpTimerInterval) {
            clearInterval(otpTimerInterval);
        }

        otpTimerInterval = setInterval(function() {
            var mins = Math.floor(timeLeft / 60);
            var secs = timeLeft % 60;
            var formatted = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;

            if (timerEl) {
                timerEl.innerText = formatted;
            }

            if (timeLeft <= 0) {
                clearInterval(otpTimerInterval);
                if (timerEl) timerEl.innerText = 'Expired';
                if (resendLink) {
                    resendLink.classList.remove('disabled-link');
                }
            }
            timeLeft -= 1;
        }, 1000);
    }

    function resendAadharOtp() {
        var resendLink = document.getElementById('resend_otp_link');
        if (resendLink && resendLink.classList.contains('disabled-link')) {
            return;
        }
        verifyData('aadhar');
    }

    /**
     * Verify document with government gateway via QuickEKYC API
     */
    function verifyData(type) {
        var baseType = (type === 'aadhar-otp') ? 'aadhar' : type;
        if (verifiedStatus[baseType]) {
            Swal.fire({
                title: 'Already Verified',
                text: 'This document is already verified and locked.',
                icon: 'info',
                confirmButtonColor: '#10b981'
            });
            return;
        }

        var user_id = document.getElementById('user_id').value;
        var url = "{{ url('api/document-verify') }}";
        var number = '';
        var otp = '';
        var dob = '';

        if (type === 'aadhar') {
            var rawAadhar = $('#aadhar').val() || '';
            number = rawAadhar.replace(/\s+/g, '').trim();

            if (!number || number.length !== 12 || isNaN(number)) {
                Swal.fire({
                    title: 'Invalid Aadhaar',
                    text: 'Please enter a complete 12-digit Aadhaar number.',
                    icon: 'warning',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }
        } else if (type === 'aadhar-otp') {
            var rawAadhar = $('#aadhar').val() || '';
            number = rawAadhar.replace(/\s+/g, '').trim();
            otp = $('#aadhar_otp').val().trim();

            if (!otp || otp.length < 6) {
                Swal.fire({
                    title: 'Enter Complete OTP',
                    text: 'Please enter the 6-digit OTP received on your mobile.',
                    icon: 'warning',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }
            if (!aadhar_request_id) {
                Swal.fire({
                    title: 'OTP Session Expired',
                    text: 'Please click "Send OTP for Verification" to generate a fresh OTP.',
                    icon: 'error',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }
        } else if (type === 'driving_license') {
            number = $('#driving_license').val().trim();
            dob = $('#dob').val().trim();
            if (!number) {
                Swal.fire('License Number Required', 'Please enter your Driving License number.', 'warning');
                return;
            }
            if (!dob) {
                Swal.fire('Date of Birth Required', 'Please enter your Date of Birth as printed on your driving license.', 'warning');
                return;
            }
        } else {
            var el = document.getElementById(type);
            number = el ? el.value.trim() : '';
            if (!number) {
                Swal.fire('Required', 'Please enter the document number first.', 'warning');
                return;
            }
        }

        // Show loading modal with clear message
        var loadingTitle = 'Contacting Gateway...';
        if (type === 'aadhar') loadingTitle = 'Requesting Aadhaar OTP...';
        if (type === 'aadhar-otp') loadingTitle = 'Verifying OTP with UIDAI...';
        if (type === 'pan') loadingTitle = 'Verifying PAN with Income Tax Dept...';

        Swal.fire({
            title: loadingTitle,
            text: 'Please wait while we communicate with the government verification gateway.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                employee_id: user_id,
                number: number,
                type: type,
                request_id: aadhar_request_id,
                client_id: aadhar_request_id,
                otp: otp,
                dob: dob,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                var res;
                try {
                    res = typeof response === 'object' ? response : JSON.parse(response);
                } catch(e) {
                    res = response;
                }

                var isSuccess = false;
                if (res) {
                    var status = res.status;
                    var code = res.code || res.status_code;
                    if (status === 'success' || status === true || (typeof status === 'string' && status.toLowerCase() === 'success') || code == 200) {
                        isSuccess = true;
                    }
                }

                if (isSuccess) {
                    if (type === 'aadhar') {
                        // Capture request_id or client_id from response
                        aadhar_request_id = res.request_id || (res.data && (res.data.client_id || res.data.request_id)) || '';
                        $('#aadhar_otp_group').slideDown();
                        $('#aadhar_button').hide();
                        $('#aadhar_update_btn').hide();
                        $('#aadhar_otp_button').show();
                        $('#aadhar_otp').focus();

                        startOtpCountdown(60);

                        Swal.fire({
                            title: 'OTP Sent Successfully!',
                            text: res.message || 'OTP has been dispatched to your UIDAI registered mobile number.',
                            icon: 'success',
                            confirmButtonColor: '#4f46e5'
                        });
                    } else if (type === 'aadhar-otp') {
                        Swal.fire({
                            title: 'Aadhaar Verified!',
                            text: 'Your Aadhaar identity is verified and locked successfully.',
                            icon: 'success',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Document Verified!',
                            text: 'Your document has been verified against government records successfully.',
                            icon: 'success',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            location.reload();
                        });
                    }
                } else {
                    var errMsg = (res && (res.message || res.error || (res.data && res.data.message))) || 'Verification could not be completed. Please re-check document details.';
                    Swal.fire({
                        title: 'Verification Failed',
                        text: errMsg,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            },
            error: function(xhr) {
                var errText = 'An error occurred while contacting the verification service.';
                try {
                    var errRes = JSON.parse(xhr.responseText);
                    if (errRes && (errRes.message || errRes.error)) {
                        errText = errRes.message || errRes.error;
                    }
                } catch(e) {}

                Swal.fire({
                    title: 'Gateway Error',
                    text: errText,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    }
</script>
@endsection
