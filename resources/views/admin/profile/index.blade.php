@extends('admin.layouts.layout')

@section('title', 'Admin Profile & Security')

@section('content')
<div class="container-fluid px-0">

    <!-- Top Breadcrumb & Page Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Admin Profile</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0 text-dark fs-4">Admin Profile &amp; Security</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1.5 fw-semibold">
                <i class="fa-solid fa-shield-halved me-1"></i> Super Admin Privileges
            </span>
        </div>
    </div>

    <!-- Session Feedback Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3 p-3" role="alert">
            <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="fa-solid fa-circle-check fs-5"></i>
            </div>
            <div>
                <strong class="d-block fw-bold text-success-emphasis">Success!</strong>
                <span class="text-success-emphasis small">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4 d-flex align-items-start gap-3 p-3" role="alert">
            <div class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                <i class="fa-solid fa-triangle-exclamation fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <strong class="d-block fw-bold text-danger-emphasis">Please correct the following:</strong>
                <ul class="mb-0 ps-3 small text-danger-emphasis mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column: Profile Card & Quick Info -->
        <div class="col-lg-4 col-xl-4">
            <!-- Main User Identity Card -->
            <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
                <!-- Decorative Top Banner -->
                <div style="height: 110px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0284c7 100%);" class="position-relative">
                    <span class="position-absolute top-0 end-0 m-3 badge bg-white bg-opacity-20 text-white border border-white border-opacity-25 rounded-pill px-2.5 py-1 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                        Master Hub
                    </span>
                </div>

                <div class="card-body p-4 text-center position-relative" style="margin-top: -55px;">
                    <!-- Avatar with live preview -->
                    <div class="position-relative d-inline-block mb-3">
                        <div class="rounded-circle p-1 bg-white shadow-sm" style="width: 104px; height: 104px;">
                            @if($user->image && file_exists(public_path('uploads/' . $user->image)))
                                <img id="profileAvatarPreview" src="{{ asset('uploads/' . $user->image) }}" alt="{{ $user->name }}" class="rounded-circle w-100 h-100 object-fit-cover">
                            @else
                                <div id="profileAvatarPreviewPlaceholder" class="w-100 h-100 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-3">
                                    {{ strtoupper(substr($user->name ?? 'A', 0, 2)) }}
                                </div>
                                <img id="profileAvatarPreview" src="" alt="Avatar" class="rounded-circle w-100 h-100 object-fit-cover d-none">
                            @endif
                        </div>
                        <label for="adminProfileImageInput" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow border border-white cursor-pointer" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;" title="Upload new photo">
                            <i class="fa-solid fa-camera" style="font-size: 0.75rem;"></i>
                        </label>
                    </div>

                    <h5 class="fw-bold mb-1 text-dark">{{ $user->name }}</h5>
                    <p class="text-muted small mb-2">{{ $user->email }}</p>

                    <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 bg-danger bg-opacity-10 text-danger rounded-pill fw-semibold small mb-3">
                        <i class="fa-solid fa-shield-halved" style="font-size: 0.75rem;"></i> Super Admin
                    </div>

                    <hr class="opacity-50 my-3">

                    <!-- Meta Details -->
                    <div class="text-start">
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-light">
                            <span class="text-muted small"><i class="fa-solid fa-phone me-2 text-primary"></i> Phone</span>
                            <span class="fw-semibold text-dark small">{{ $user->phone ?? 'Not specified' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-light">
                            <span class="text-muted small"><i class="fa-solid fa-fingerprint me-2 text-primary"></i> Admin ID</span>
                            <span class="badge bg-light text-secondary border px-2 py-1">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-light">
                            <span class="text-muted small"><i class="fa-regular fa-calendar-check me-2 text-primary"></i> Member Since</span>
                            <span class="text-dark small">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2">
                            <span class="text-muted small"><i class="fa-solid fa-circle-check me-2 text-success"></i> Security Status</span>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 small">Active &amp; Secure</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Security Advice Card -->
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-light">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="rounded-circle bg-warning bg-opacity-20 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-lock" style="font-size: 0.85rem;"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-0">Security Guidelines</h6>
                </div>
                <p class="text-muted small mb-0 lh-base">
                    Ensure your account uses a strong password with letters, numbers, and symbols. Never share your administrative credentials with unauthorized personnel.
                </p>
            </div>
        </div>

        <!-- Right Column: Edit Forms (Profile Details & Password) -->
        <div class="col-lg-8 col-xl-8">
            <!-- 1. Edit Profile Form Card -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-user-pen"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 fs-6">Personal &amp; Contact Information</h5>
                                <p class="text-muted small mb-0">Update your public identity and administrative contact details.</p>
                            </div>
                        </div>
                        <span class="badge bg-light text-muted border px-2 py-1 small">Step 1 of 2</span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="adminProfileUpdateForm">
                        @csrf

                        <!-- Hidden Image Input triggered by avatar button -->
                        <input type="file" name="image" id="adminProfileImageInput" class="d-none" accept="image/jpeg,image/png,image/jpg,image/webp">

                        <div class="row g-3 mb-3">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <label for="admin_name" class="form-label small fw-semibold text-dark">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa-regular fa-user"></i>
                                    </span>
                                    <input type="text" name="name" id="admin_name" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required placeholder="e.g. Super Admin">
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email Address -->
                            <div class="col-md-6">
                                <label for="admin_email" class="form-label small fw-semibold text-dark">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa-regular fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" id="admin_email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required placeholder="e.g. admin@stafo.in">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone Number -->
                            <div class="col-md-6">
                                <label for="admin_phone" class="form-label small fw-semibold text-dark">
                                    Phone Number
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa-solid fa-phone"></i>
                                    </span>
                                    <input type="text" name="phone" id="admin_phone" class="form-control border-start-0 ps-0 @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +91 9876543210">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Photo Selector Input (Visible Alternative) -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">
                                    Profile Picture <span class="text-muted fw-normal">(JPG, PNG, WebP up to 3MB)</span>
                                </label>
                                <div class="input-group">
                                    <input type="file" id="adminProfileImageInputVisible" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                                </div>
                                <div class="form-text text-muted small" style="font-size: 0.75rem;">
                                    Square ratio recommended (e.g. 400x400).
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-2 border-top">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm d-flex align-items-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i> Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. Change Password Form Card -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 fs-6">Change Account Password</h5>
                                <p class="text-muted small mb-0">Update your security passkey regularly to protect master access.</p>
                            </div>
                        </div>
                        <span class="badge bg-light text-muted border px-2 py-1 small">Step 2 of 2</span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.profile.changePassword') }}" method="POST" id="adminPasswordChangeForm">
                        @csrf

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label small fw-semibold text-dark">
                                Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password" name="current_password" id="current_password" class="form-control border-start-0 border-end-0 ps-0 @error('current_password') is-invalid @enderror" required placeholder="Enter your current password">
                                <button type="button" class="input-group-text bg-light border-start-0 text-muted toggle-password-btn" data-target="current_password" title="Show / Hide Password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- New Password -->
                            <div class="col-md-6">
                                <label for="new_password" class="form-label small fw-semibold text-dark">
                                    New Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </span>
                                    <input type="password" name="new_password" id="new_password" class="form-control border-start-0 border-end-0 ps-0 @error('new_password') is-invalid @enderror" required placeholder="Min 6 characters">
                                    <button type="button" class="input-group-text bg-light border-start-0 text-muted toggle-password-btn" data-target="new_password" title="Show / Hide Password">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text text-muted small" style="font-size: 0.75rem;">
                                    Must be at least 6 characters.
                                </div>
                                @error('new_password')
                                    <div class="invalid-feedback d-block small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm New Password -->
                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label small fw-semibold text-dark">
                                    Confirm New Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa-solid fa-check-double"></i>
                                    </span>
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control border-start-0 border-end-0 ps-0" required placeholder="Repeat new password">
                                    <button type="button" class="input-group-text bg-light border-start-0 text-muted toggle-password-btn" data-target="new_password_confirmation" title="Show / Hide Password">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-2 border-top">
                            <button type="submit" class="btn btn-danger px-4 py-2 rounded-3 fw-semibold shadow-sm d-flex align-items-center gap-2">
                                <i class="fa-solid fa-key"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Password Visibility Eye Toggle
    const toggleBtns = document.querySelectorAll('.toggle-password-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // 2. Avatar Live Image Preview
    const hiddenFileInput = document.getElementById('adminProfileImageInput');
    const visibleFileInput = document.getElementById('adminProfileImageInputVisible');
    const previewImg = document.getElementById('profileAvatarPreview');
    const placeholder = document.getElementById('profileAvatarPreviewPlaceholder');

    function handleFile(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('d-none');
                if (placeholder) {
                    placeholder.classList.add('d-none');
                }
            };
            reader.readAsDataURL(file);
        }
    }

    if (hiddenFileInput) {
        hiddenFileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                handleFile(this.files[0]);
            }
        });
    }

    if (visibleFileInput) {
        visibleFileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                // Sync with hidden form input
                const dt = new DataTransfer();
                dt.items.add(this.files[0]);
                hiddenFileInput.files = dt.files;
                handleFile(this.files[0]);
            }
        });
    }
});
</script>
@endsection
