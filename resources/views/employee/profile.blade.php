@extends('employee.layouts.app')

@section('title', 'My Profile | STAFO HRMS')

@section('content')
<style>
    /* Responsive Styling & Polish */
    .profile-hero-card {
        background: #ffffff;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        transition: all 0.2s ease;
    }

    .profile-avatar-wrapper {
        position: relative;
        display: inline-block;
        width: 105px;
        height: 105px;
    }
    @media (min-width: 768px) {
        .profile-avatar-wrapper {
            width: 120px;
            height: 120px;
        }
    }

    .profile-avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #ffffff;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
    }

    .profile-avatar-btn {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #059669;
        color: #ffffff;
        border: 2px solid #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.18);
        transition: transform 0.2s ease, background-color 0.2s ease;
    }
    .profile-avatar-btn:hover {
        transform: scale(1.08);
        background: #047857;
        color: #ffffff;
    }

    /* Info Tiles */
    .profile-info-tile {
        background-color: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 0.75rem;
        padding: 0.75rem 0.85rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .profile-info-tile:hover {
        background-color: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    .profile-info-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #64748b;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .profile-info-val {
        font-size: 0.88rem;
        font-weight: 600;
        color: #0f172a;
        word-break: break-word;
        overflow-wrap: break-word;
    }

    /* Mobile Nav Tabs Scrollable */
    .profile-nav-scroll {
        overflow-x: auto;
        flex-wrap: nowrap !important;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .profile-nav-scroll::-webkit-scrollbar {
        display: none;
    }
    .profile-nav-scroll .nav-link {
        white-space: nowrap;
        border-radius: 0.5rem;
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }

    /* Robust Modal Scrolling on All Devices & Mobile */
    #editProfileModal .modal-dialog {
        display: flex;
        align-items: center;
        min-height: calc(100% - 1rem);
    }
    #editProfileModal form.modal-content {
        max-height: calc(100vh - 2rem);
        max-height: calc(100dvh - 2rem);
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
    }
    #editProfileModal .modal-body {
        flex: 1 1 auto !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-y !important;
        overscroll-behavior: contain !important;
    }
    @media (max-width: 575.98px) {
        #editProfileModal .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
            min-height: calc(100dvh - 1rem);
        }
        #editProfileModal form.modal-content {
            max-height: calc(100dvh - 1rem) !important;
            border-radius: 1rem !important;
        }
        #editProfileModal .modal-body {
            max-height: calc(100dvh - 130px) !important;
            padding: 1rem !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
        .profile-page-title {
            font-size: 1.25rem !important;
        }
    }
</style>

<div class="container-fluid p-0">
    <!-- Header: Fully Responsive (Stack on Mobile, Row on Tablet/Desktop) -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2.5 mb-3 mb-md-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold text-dark mb-0 profile-page-title">
                    <i class="fa-solid fa-id-badge text-primary me-1.5"></i>My Profile
                </h4>
                <span class="badge {{ $employee->status == 1 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $employee->status == 1 ? 'text-success' : 'text-danger' }} rounded-pill px-2 py-1 font-monospace" style="font-size: 0.7rem;">
                    {{ $employee->emp_id ?? 'STAFF' }}
                </span>
            </div>
            <span class="text-muted small d-none d-sm-block mt-0.5">Personal details, employment information, KYC, and bank details</span>
        </div>

        <div class="d-flex align-items-center gap-2 mt-1 mt-sm-0">
            <button type="button" class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center flex-grow-1 flex-sm-grow-0" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="fa-solid fa-user-pen me-1.5"></i> Edit Profile
            </button>
            <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-inline-flex align-items-center">
                <i class="fa-solid fa-arrow-left me-1"></i> <span class="d-none d-sm-inline">Dashboard</span><span class="d-sm-none">Back</span>
            </a>
        </div>
    </div>

    <!-- Validation Errors Alert -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 rounded-md-4 mb-3 mb-md-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="fa-solid fa-circle-exclamation fs-5 me-2 mt-0.5 text-danger"></i>
                <div class="flex-grow-1">
                    <strong class="d-block" style="font-size: 0.9rem;">Please review the following errors:</strong>
                    <ul class="mb-0 mt-1 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 g-md-4">
        <!-- ================= LEFT COLUMN: HERO PROFILE CARD ================= -->
        <div class="col-lg-4">
            <div class="profile-hero-card p-3 p-md-4 text-center">
                <!-- Avatar with Quick Camera Trigger -->
                <div class="profile-avatar-wrapper mx-auto mb-2.5">
                    @if($employee->image_url)
                        <img src="{{ $employee->image_url }}" alt="{{ $employee->name }}" 
                             id="profileAvatarPreviewMain"
                             class="profile-avatar-img">
                    @else
                        <div id="profileAvatarFallbackMain" class="profile-avatar-img bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold fs-2">
                            {{ strtoupper(substr($employee->name ?? 'E', 0, 2)) }}
                        </div>
                    @endif
                    <button type="button" class="profile-avatar-btn"
                            data-bs-toggle="modal" data-bs-target="#editProfileModal"
                            title="Change Profile Photo">
                        <i class="fa-solid fa-camera" style="font-size: 0.8rem;"></i>
                    </button>
                </div>

                <!-- Name & Title -->
                <h5 class="fw-bold text-dark mb-0.5 text-break" style="font-size: 1.15rem;">{{ $employee->name }}</h5>
                <p class="text-muted small mb-2 text-break">{{ $employee->position ?: ($employee->designation->name ?? 'Staff Member') }}</p>

                <!-- Status Badges -->
                <div class="d-flex flex-wrap justify-content-center gap-1.5 mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary font-monospace px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-hashtag me-1 opacity-75"></i>{{ $employee->emp_id ?? 'N/A' }}
                    </span>
                    <span class="badge {{ $employee->status == 1 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $employee->status == 1 ? 'text-success' : 'text-danger' }} px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-circle {{ $employee->status == 1 ? 'text-success' : 'text-danger' }} me-1" style="font-size: 0.45rem;"></i>
                        {{ $employee->status == 1 ? 'Active' : 'Inactive' }}
                    </span>
                    @if($employee->department)
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1 rounded-pill d-none d-sm-inline-block" style="font-size: 0.72rem;">
                            {{ $employee->department->name }}
                        </span>
                    @endif
                </div>

                <!-- Edit Profile CTA Button -->
                <button type="button" class="btn btn-outline-primary btn-sm w-100 rounded-pill mb-3 fw-semibold shadow-none py-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fa-solid fa-pen-to-square me-1.5"></i> Edit Profile Details
                </button>

                <!-- Employment Information (HR Managed) -->
                <div class="border-top pt-3 text-start">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold text-dark" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-building me-1 text-secondary"></i> Employment Details
                        </span>
                        <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">Company Managed</span>
                    </div>

                    <div class="list-group list-group-flush small">
                        <div class="list-group-item px-0 py-1.5 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted"><i class="fa-regular fa-building me-1.5 text-secondary"></i>Company</span>
                            <span class="fw-semibold text-dark text-end text-break ms-2">{{ $employee->company->company_name ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-1.5 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted"><i class="fa-solid fa-sitemap me-1.5 text-secondary"></i>Department</span>
                            <span class="fw-semibold text-dark text-end text-break ms-2">{{ $employee->department->name ?? 'General' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-1.5 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted"><i class="fa-solid fa-code-branch me-1.5 text-secondary"></i>Branch</span>
                            <span class="fw-semibold text-dark text-end text-break ms-2">{{ $employee->branch->branch_name ?? 'Main Branch' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-1.5 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted"><i class="fa-regular fa-calendar me-1.5 text-secondary"></i>Joining Date</span>
                            <span class="fw-semibold text-dark text-end ms-2">{{ $employee->date_of_joining ? Carbon\Carbon::parse($employee->date_of_joining)->format('d M, Y') : 'N/A' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-1.5 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted"><i class="fa-regular fa-clock me-1.5 text-secondary"></i>Shift</span>
                            <span class="fw-semibold text-dark text-end ms-2">{{ $employee->shift->name ?? 'Regular Shift' }}</span>
                        </div>
                        <div class="list-group-item px-0 py-1.5 d-flex justify-content-between align-items-center border-0">
                            <span class="text-muted"><i class="fa-regular fa-envelope me-1.5 text-secondary"></i>Official Email</span>
                            <span class="fw-semibold text-dark text-end font-monospace text-break ms-2" style="font-size: 0.78rem;">{{ $employee->official_email_id ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= RIGHT COLUMN: INFORMATION CARDS ================= -->
        <div class="col-lg-8">
            <!-- 1. Personal Information Card -->
            <div class="profile-hero-card p-3 p-md-4 mb-3 mb-md-4">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2.5 mb-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="fa-solid fa-user text-primary me-2"></i> Personal Information
                    </h6>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 d-inline-flex align-items-center" style="font-size: 0.78rem;" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fa-solid fa-pen me-1 text-primary"></i> Edit
                    </button>
                </div>

                <div class="row g-2.5 g-md-3">
                    <div class="col-12 col-sm-6">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-solid fa-signature text-primary"></i> Full Name</div>
                            <div class="profile-info-val">{{ $employee->name }}</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-solid fa-phone text-success"></i> Phone Number</div>
                            <div class="profile-info-val font-monospace">{{ $employee->phone ?: 'Not Provided' }}</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-regular fa-envelope text-info"></i> Personal Email</div>
                            <div class="profile-info-val">{{ $employee->email ?: 'Not Provided' }}</div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-6">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-regular fa-calendar text-warning"></i> Date of Birth</div>
                            <div class="profile-info-val">{{ $employee->date_of_birth ? Carbon\Carbon::parse($employee->date_of_birth)->format('d M, Y') : 'Not Set' }}</div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-4">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-solid fa-venus-mars text-primary"></i> Gender</div>
                            <div class="profile-info-val">{{ ucfirst($employee->gender ?? 'Not Set') }}</div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-4">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-solid fa-heart text-danger"></i> Marital Status</div>
                            <div class="profile-info-val">{{ ucfirst($employee->marital_status ?? 'Not Set') }}</div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-4">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-solid fa-droplet text-danger"></i> Blood Group</div>
                            <div class="profile-info-val">
                                @if($employee->blood_group)
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-0.5 rounded-pill">{{ $employee->blood_group }}</span>
                                @else
                                    <span class="text-muted">Not Set</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-solid fa-user-shield text-secondary"></i> Guardian / Father Name</div>
                            <div class="profile-info-val">{{ $employee->guardian_name ?: 'Not Provided' }}</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-solid fa-map-pin text-primary"></i> Postal PIN Code</div>
                            <div class="profile-info-val font-monospace">{{ $employee->pin ?: 'Not Provided' }}</div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-solid fa-location-dot text-danger"></i> Residential Address</div>
                            <div class="profile-info-val">{{ $employee->address ?: 'Not Provided' }}{{ $employee->city ? ', ' . ($employee->city->name ?? '') : '' }}{{ $employee->state ? ', ' . ($employee->state->name ?? '') : '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Bank Account Details Card -->
            <div class="profile-hero-card p-3 p-md-4 mb-3 mb-md-4">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2.5 mb-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="fa-solid fa-building-columns text-success me-2"></i> Bank Account Information
                    </h6>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 d-inline-flex align-items-center" style="font-size: 0.78rem;" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fa-solid fa-pen me-1 text-success"></i> {{ $employee->bankAccount ? 'Edit' : 'Add' }}
                    </button>
                </div>

                @if($employee->bankAccount)
                    <div class="row g-2.5 g-md-3">
                        <div class="col-12 col-sm-6">
                            <div class="profile-info-tile">
                                <div class="profile-info-label"><i class="fa-solid fa-landmark text-success"></i> Bank Name</div>
                                <div class="profile-info-val">{{ $employee->bankAccount->bank_name ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="profile-info-tile">
                                <div class="profile-info-label"><i class="fa-solid fa-user-check text-success"></i> Account Holder Name</div>
                                <div class="profile-info-val">{{ $employee->bankAccount->account_holder_name ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="profile-info-tile">
                                <div class="profile-info-label"><i class="fa-solid fa-money-check text-success"></i> Account Number</div>
                                <div class="profile-info-val font-monospace fs-6">{{ $employee->bankAccount->account_number ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="profile-info-tile">
                                <div class="profile-info-label"><i class="fa-solid fa-barcode text-success"></i> IFSC Code</div>
                                <div class="profile-info-val font-monospace">{{ $employee->bankAccount->ifsc_code ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="profile-info-tile">
                                <div class="profile-info-label"><i class="fa-solid fa-location-arrow text-success"></i> Branch Name / Address</div>
                                <div class="profile-info-val">{{ $employee->bankAccount->branch_name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-light p-3.5 rounded-3 text-center border">
                        <div class="text-success mb-2"><i class="fa-solid fa-building-columns fs-2 opacity-75"></i></div>
                        <h6 class="fw-bold text-dark mb-1">No Bank Account Added</h6>
                        <p class="text-muted small mb-3">Add your bank account details so that automated monthly salary payouts can be credited directly.</p>
                        <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3.5 py-1.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fa-solid fa-plus me-1"></i> Add Bank Details Now
                        </button>
                    </div>
                @endif
            </div>

            <!-- 3. Statutory & Government IDs Card -->
            <div class="profile-hero-card p-3 p-md-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom pb-2.5 mb-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="fa-solid fa-id-card text-info me-2"></i> Statutory & Government IDs
                    </h6>
                    <div class="d-flex align-items-center gap-1.5">
                        <a href="{{ route('employee.documents') }}" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-1" style="font-size: 0.76rem;">
                            <i class="fa-solid fa-file-shield me-1"></i> KYC Docs
                        </a>
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1" style="font-size: 0.78rem;" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fa-solid fa-pen me-1 text-info"></i> Edit
                        </button>
                    </div>
                </div>

                <div class="row g-2.5 g-md-3">
                    <div class="col-12 col-sm-6">
                        <div class="profile-info-tile">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="profile-info-label mb-0"><i class="fa-regular fa-address-card text-primary"></i> PAN Number</div>
                                @if(strtolower((string)$employee->pan_verify) === 'yes' || $employee->pan_verify === '1')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill" style="font-size: 0.68rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i>Verified
                                    </span>
                                @endif
                            </div>
                            <div class="profile-info-val font-monospace">{{ $employee->pan ?: 'Not Provided' }}</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <div class="profile-info-tile">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="profile-info-label mb-0"><i class="fa-solid fa-fingerprint text-warning"></i> Aadhaar Number</div>
                                @if(strtolower((string)$employee->aadhar_verify) === 'yes' || $employee->aadhar_verify === '1')
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill" style="font-size: 0.68rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i>Verified
                                    </span>
                                @endif
                            </div>
                            <div class="profile-info-val font-monospace">{{ $employee->aadhar ?: 'Not Provided' }}</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-4">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-solid fa-hashtag text-secondary"></i> UAN Number</div>
                            <div class="profile-info-val font-monospace">{{ $employee->uan ?: 'Not Set' }}</div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-4">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-solid fa-shield text-secondary"></i> PF Number</div>
                            <div class="profile-info-val font-monospace">{{ $employee->pf_number ?: 'Not Set' }}</div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-4">
                        <div class="profile-info-tile">
                            <div class="profile-info-label"><i class="fa-solid fa-heart-pulse text-danger"></i> ESI Number</div>
                            <div class="profile-info-val font-monospace">{{ $employee->esi_number ?: 'Not Set' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL: EDIT EMPLOYEE PROFILE ================= -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-3 rounded-md-4 overflow-hidden">
            @csrf
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white border-0 px-3 px-md-4 py-3 flex-shrink-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-pen fs-5"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="editProfileModalLabel" style="font-size: 1.1rem;">Update Profile</h5>
                        <small class="text-white-50" style="font-size: 0.75rem;">Keep your personal, contact, and bank details accurate</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-3 p-md-4">
                    <!-- Nav Tabs (Scrollable on small screens) -->
                    <div class="bg-light p-1.5 rounded-3 mb-3 mb-md-4 border">
                        <ul class="nav nav-pills profile-nav-scroll gap-1" id="profileModalTabs" role="tablist">
                            <li class="nav-item flex-grow-1" role="presentation">
                                <button class="nav-link active fw-semibold text-center w-100" id="tab-personal-btn" data-bs-toggle="pill" data-bs-target="#tab-personal" type="button" role="tab">
                                    <i class="fa-solid fa-user me-1.5"></i> Personal
                                </button>
                            </li>
                            <li class="nav-item flex-grow-1" role="presentation">
                                <button class="nav-link fw-semibold text-center w-100" id="tab-bank-btn" data-bs-toggle="pill" data-bs-target="#tab-bank" type="button" role="tab">
                                    <i class="fa-solid fa-building-columns me-1.5"></i> Bank Details
                                </button>
                            </li>
                            <li class="nav-item flex-grow-1" role="presentation">
                                <button class="nav-link fw-semibold text-center w-100" id="tab-statutory-btn" data-bs-toggle="pill" data-bs-target="#tab-statutory" type="button" role="tab">
                                    <i class="fa-solid fa-id-card me-1.5"></i> Statutory IDs
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="profileModalTabContent">
                        <!-- ================= TAB 1: Personal Info ================= -->
                        <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">
                            <!-- Photo Upload Area -->
                            <div class="d-flex align-items-center gap-3 p-2.5 p-md-3 bg-light rounded-3 mb-3 border">
                                <div class="position-relative" style="width: 64px; height: 64px; flex-shrink: 0;">
                                    @if($employee->image_url)
                                        <img src="{{ $employee->image_url }}" alt="{{ $employee->name }}" 
                                             id="modalAvatarPreview"
                                             class="rounded-circle shadow-sm border border-2 border-primary w-100 h-100" style="object-fit: cover;">
                                    @else
                                        <div id="modalAvatarFallback" class="rounded-circle bg-primary bg-opacity-10 text-primary w-100 h-100 d-flex align-items-center justify-content-center fw-bold fs-3 border border-2 border-primary border-opacity-25">
                                            {{ strtoupper(substr($employee->name ?? 'E', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <label for="profileImageInput" class="form-label fw-semibold mb-1 small text-dark">
                                        Upload Profile Photo
                                    </label>
                                    <input type="file" class="form-control form-control-sm" id="profileImageInput" name="image" accept="image/jpeg,image/png,image/jpg,image/webp">
                                    <small class="text-muted d-block mt-0.5" style="font-size: 0.7rem;">Max 3MB (JPG, PNG, WEBP)</small>
                                </div>
                            </div>

                            <div class="row g-2.5 g-md-3">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $employee->name) }}" required placeholder="Enter your full name">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted">Mobile Phone <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="phone" value="{{ old('phone', $employee->phone) }}" required maxlength="10" placeholder="10-digit mobile number">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted">Personal Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', $employee->email) }}" placeholder="e.g. employee@gmail.com">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted">Date of Birth</label>
                                    <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth ? Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') : '') }}">
                                </div>

                                <div class="col-6 col-sm-4">
                                    <label class="form-label fw-semibold small text-muted">Gender</label>
                                    <select class="form-select" name="gender">
                                        <option value="">Select</option>
                                        <option value="male" {{ strtolower((string)$employee->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ strtolower((string)$employee->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ strtolower((string)$employee->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <div class="col-6 col-sm-4">
                                    <label class="form-label fw-semibold small text-muted">Marital Status</label>
                                    <select class="form-select" name="marital_status">
                                        <option value="">Select</option>
                                        <option value="Single" {{ strtolower((string)$employee->marital_status) === 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married" {{ strtolower((string)$employee->marital_status) === 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="Divorced" {{ strtolower((string)$employee->marital_status) === 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="Widowed" {{ strtolower((string)$employee->marital_status) === 'widowed' ? 'selected' : '' }}>Widowed</option>
                                    </select>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <label class="form-label fw-semibold small text-muted">Blood Group</label>
                                    <select class="form-select" name="blood_group">
                                        <option value="">Select Blood Group</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                            <option value="{{ $bg }}" {{ (string)$employee->blood_group === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted">Father / Guardian Name</label>
                                    <input type="text" class="form-control" name="guardian_name" value="{{ old('guardian_name', $employee->guardian_name) }}" placeholder="Father or spouse name">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted">Postal PIN Code</label>
                                    <input type="text" class="form-control" name="pin" value="{{ old('pin', $employee->pin) }}" maxlength="10" placeholder="e.g. 110001">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-muted">Residential Address</label>
                                    <textarea class="form-control" name="address" rows="2" placeholder="Full home address (Flat, Building, Street, Area)">{{ old('address', $employee->address) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- ================= TAB 2: Bank Account Details ================= -->
                        <div class="tab-pane fade" id="tab-bank" role="tabpanel">
                            <div class="alert alert-info border-0 rounded-3 small mb-3 py-2 px-3">
                                <i class="fa-solid fa-circle-info me-1"></i> Ensure your bank details match your passbook/cheque for smooth payroll credit.
                            </div>

                            <div class="row g-2.5 g-md-3">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted">Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name" value="{{ old('bank_name', $employee->bankAccount->bank_name ?? '') }}" placeholder="e.g. State Bank of India, HDFC Bank">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted">Account Holder Name</label>
                                    <input type="text" class="form-control" name="account_holder_name" value="{{ old('account_holder_name', $employee->bankAccount->account_holder_name ?? $employee->name) }}" placeholder="Name printed on passbook">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted">Account Number</label>
                                    <input type="text" class="form-control font-monospace" name="account_number" value="{{ old('account_number', $employee->bankAccount->account_number ?? '') }}" placeholder="Enter bank account number">
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted">IFSC Code</label>
                                    <input type="text" class="form-control font-monospace text-uppercase" name="ifsc_code" value="{{ old('ifsc_code', $employee->bankAccount->ifsc_code ?? '') }}" maxlength="11" placeholder="e.g. SBIN0001234">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-muted">Branch Name / Address</label>
                                    <input type="text" class="form-control" name="branch_name" value="{{ old('branch_name', $employee->bankAccount->branch_name ?? '') }}" placeholder="e.g. Main Market Branch, New Delhi">
                                </div>
                            </div>
                        </div>

                        <!-- ================= TAB 3: Statutory IDs ================= -->
                        <div class="tab-pane fade" id="tab-statutory" role="tabpanel">
                            <div class="alert alert-light border rounded-3 small mb-3 py-2 px-3">
                                <i class="fa-solid fa-shield-halved me-1 text-primary"></i> Documents once verified by HR cannot be edited. To upload proof documents, visit <a href="{{ route('employee.documents') }}" class="fw-bold">My Documents</a>.
                            </div>

                            <div class="row g-2.5 g-md-3">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted d-flex align-items-center justify-content-between">
                                        <span>PAN Card Number</span>
                                        @if(strtolower((string)$employee->pan_verify) === 'yes' || $employee->pan_verify === '1')
                                            <span class="badge bg-success bg-opacity-10 text-success"><i class="fa-solid fa-lock me-1"></i>Verified</span>
                                        @endif
                                    </label>
                                    <input type="text" class="form-control font-monospace text-uppercase" name="pan" value="{{ old('pan', $employee->pan) }}" 
                                           maxlength="10" placeholder="e.g. ABCDE1234F"
                                           {{ (strtolower((string)$employee->pan_verify) === 'yes' || $employee->pan_verify === '1') ? 'readonly disabled' : '' }}>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-semibold small text-muted d-flex align-items-center justify-content-between">
                                        <span>Aadhaar Card Number</span>
                                        @if(strtolower((string)$employee->aadhar_verify) === 'yes' || $employee->aadhar_verify === '1')
                                            <span class="badge bg-success bg-opacity-10 text-success"><i class="fa-solid fa-lock me-1"></i>Verified</span>
                                        @endif
                                    </label>
                                    <input type="text" class="form-control font-monospace" name="aadhar" value="{{ old('aadhar', $employee->aadhar) }}" 
                                           maxlength="16" placeholder="12-digit Aadhaar number"
                                           {{ (strtolower((string)$employee->aadhar_verify) === 'yes' || $employee->aadhar_verify === '1') ? 'readonly disabled' : '' }}>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <label class="form-label fw-semibold small text-muted">UAN Number</label>
                                    <input type="text" class="form-control font-monospace" name="uan" value="{{ old('uan', $employee->uan) }}" placeholder="12-digit UAN">
                                </div>

                                <div class="col-6 col-sm-4">
                                    <label class="form-label fw-semibold small text-muted">PF Number</label>
                                    <input type="text" class="form-control font-monospace" name="pf_number" value="{{ old('pf_number', $employee->pf_number) }}" placeholder="PF Account No">
                                </div>

                                <div class="col-6 col-sm-4">
                                    <label class="form-label fw-semibold small text-muted">ESI Number</label>
                                    <input type="text" class="form-control font-monospace" name="esi_number" value="{{ old('esi_number', $employee->esi_number) }}" placeholder="17-digit ESI No">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer (Sticky-friendly on mobile) -->
                <div class="modal-footer bg-light px-3 px-md-4 py-2.5 border-top d-flex justify-content-between flex-shrink-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold rounded-pill shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1.5"></i> Save Changes
                    </button>
                </div>
            </form>
    </div>
</div>

<script>
    // Live Image Preview when file selected in modal
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('profileImageInput');
        if (input) {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var modalPreview = document.getElementById('modalAvatarPreview');
                        var modalFallback = document.getElementById('modalAvatarFallback');
                        if (modalPreview) {
                            modalPreview.src = e.target.result;
                        } else if (modalFallback) {
                            var img = document.createElement('img');
                            img.id = 'modalAvatarPreview';
                            img.src = e.target.result;
                            img.className = 'rounded-circle shadow-sm border border-2 border-primary w-100 h-100';
                            img.style.objectFit = 'cover';
                            modalFallback.parentNode.replaceChild(img, modalFallback);
                        }
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>
@endsection
