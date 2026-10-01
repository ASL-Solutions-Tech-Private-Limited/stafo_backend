@extends('user.layouts.app')
@section('title', 'Edit Employee Profile | STAFO HRMS')

@section('css')
<style>
    .attendance-type-card {
        background: #ffffff;
        transition: all 0.2s ease;
        border: 2px solid #e2e8f0 !important;
        cursor: pointer;
    }
    .attendance-type-card:hover {
        border-color: #94a3b8 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08) !important;
    }
    .attendance-type-card:has(input[type="radio"]:checked) {
        border-color: #059669 !important;
        background: rgba(16, 185, 129, 0.04) !important;
        box-shadow: 0 0 0 1px #059669 !important;
    }
    [data-theme="dark"] .attendance-type-card {
        background: #1e293b !important;
        border-color: #334155 !important;
    }
    [data-theme="dark"] .attendance-type-card:hover {
        border-color: #64748b !important;
    }
    [data-theme="dark"] .attendance-type-card:has(input[type="radio"]:checked) {
        border-color: #10b981 !important;
        background: rgba(16, 185, 129, 0.12) !important;
        box-shadow: 0 0 0 1px #10b981 !important;
    }
</style>
@endsection

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Edit Employee Profile</h3>
                <p class="text-muted small mb-0">Update employee personal data, job role, compensation, and statutory documents</p>
            </div>
            <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Employee List
            </a>
        </div>

        <form action="{{ route('user.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Section 1: Profile Photo, Reference Selfie & Resume -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-id-badge text-primary"></i> Profile Photo, Reference Selfie & Resume
                </h5>
                <div class="row g-4 align-items-center">
                    
                    <!-- Profile Picture -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Profile Picture</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle overflow-hidden border shadow-sm" style="width: 64px; height: 64px; flex-shrink: 0;">
                                @if ($employee->image)
                                    <img src="{{ asset('uploads/employees/' . $employee->image) }}" alt="Profile" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fw-bold fs-4">
                                        {{ strtoupper(substr($employee->name ?? 'E', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <input class="form-control" type="file" name="image" accept="image/*">
                                <small class="text-muted">JPG, PNG or WEBP (Max 2MB)</small>
                            </div>
                        </div>
                        @error('image')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Face ID Reference Selfie -->
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-semibold text-dark mb-0">
                                <i class="fa-solid fa-camera text-primary me-1"></i> Reference Selfie (Face-ID)
                            </label>
                            @if ($employee->selfie_url)
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" id="selfieStatusBadge" style="font-size: 0.72rem;">
                                    <i class="fa-solid fa-check-circle me-1"></i> Registered
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill" id="selfieStatusBadge" style="font-size: 0.72rem;">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Not Registered
                                </span>
                            @endif
                        </div>
                        
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle overflow-hidden border shadow-sm position-relative" style="width: 64px; height: 64px; flex-shrink: 0; background: #f8fafc;">
                                <img id="selfiePreviewImg" src="{{ $employee->selfie_url ?? '' }}" alt="Reference Selfie" class="w-100 h-100 object-fit-cover {{ $employee->selfie_url ? '' : 'd-none' }}">
                                <div id="selfiePlaceholder" class="w-100 h-100 bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center text-secondary fs-4 {{ $employee->selfie_url ? 'd-none' : '' }}">
                                    <i class="fa-solid fa-user-astronaut text-muted"></i>
                                </div>
                            </div>
                            
                            <div class="flex-grow-1">
                                <input class="form-control form-control-sm mb-1" type="file" name="selfie_image" id="selfieImageInput" accept="image/*">
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-primary py-0 px-2 fw-semibold" id="btnUploadSelfieAjax" style="font-size: 0.75rem; display: none;">
                                        <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Now
                                    </button>
                                    @if ($employee->selfie_url)
                                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold" id="btnRemoveSelfieAjax" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-trash me-1"></i> Remove
                                        </button>
                                        <a href="{{ $employee->selfie_url }}" target="_blank" id="selfieViewLink" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-up-right-from-square"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold d-none" id="btnRemoveSelfieAjax" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-trash me-1"></i> Remove
                                        </button>
                                        <a href="#" target="_blank" id="selfieViewLink" class="btn btn-sm btn-outline-secondary py-0 px-2 d-none" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-up-right-from-square"></i>
                                        </a>
                                    @endif
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Face-ID reference photo (Max 2MB)</small>
                            </div>
                        </div>
                        @error('selfie_image')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Resume Document -->
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Resume / CV Document</label>
                        <input class="form-control" type="file" name="resume" accept=".pdf,.doc,.docx">
                        @if ($employee->resume)
                            <div class="mt-2">
                                <small class="text-muted">Uploaded file: </small>
                                <a href="{{ asset('uploads/resumes/' . $employee->resume) }}" target="_blank" class="small fw-semibold text-primary">
                                    <i class="fa-solid fa-file-pdf me-1"></i> View Existing Resume
                                </a>
                            </div>
                        @endif
                        @error('resume')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Section 2: Contact & Identity Details -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user text-primary"></i> Personal & Contact Information
                </h5>
                <div class="row g-3">
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Full Legal Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-user text-muted"></i></span>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $employee->name) }}" placeholder="Enter full name" required>
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Work Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-envelope text-muted"></i></span>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $employee->email) }}" placeholder="Enter email" required>
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Mobile Phone Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-phone text-muted"></i></span>
                            <input type="text" class="form-control" name="phone" value="{{ old('phone', $employee->phone) }}" placeholder="Enter 10-digit mobile number" maxlength="10" required>
                        </div>
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Position / Title</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-briefcase text-muted"></i></span>
                            <input type="text" class="form-control" name="position" id="positionInput" value="{{ old('position', $employee->position) }}" placeholder="Auto-filled from Designation or custom">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Monthly CTC (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white fw-bold text-success">₹</span>
                            <input type="text" name="salary" class="form-control" value="{{ old('salary', $employee->salary) }}" placeholder="Enter monthly CTC">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Date of Birth</label>
                        <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark d-block">Gender</label>
                        <div class="d-flex align-items-center gap-4 pt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderMale" value="male"
                                    {{ old('gender', $employee->gender) == 'male' ? 'checked' : '' }}>
                                <label class="form-check-label text-dark" for="genderMale">Male</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="female"
                                    {{ old('gender', $employee->gender) == 'female' ? 'checked' : '' }}>
                                <label class="form-check-label text-dark" for="genderFemale">Female</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderOther" value="other"
                                    {{ old('gender', $employee->gender) == 'other' ? 'checked' : '' }}>
                                <label class="form-check-label text-dark" for="genderOther">Other</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Marital Status</label>
                        <select class="form-select" name="marital_status">
                            <option value="">-- Select Status --</option>
                            <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                            <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Blood Group</label>
                        <select class="form-select" name="blood_group">
                            <option value="">-- Select Blood Group --</option>
                            <option value="A+" {{ old('blood_group', $employee->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_group', $employee->blood_group) == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_group', $employee->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_group', $employee->blood_group) == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="O+" {{ old('blood_group', $employee->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_group', $employee->blood_group) == 'O-' ? 'selected' : '' }}>O-</option>
                            <option value="AB+" {{ old('blood_group', $employee->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_group', $employee->blood_group) == 'AB-' ? 'selected' : '' }}>AB-</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Residential Address</label>
                        <input type="text" class="form-control" name="address" value="{{ old('address', $employee->address) }}" placeholder="Enter full address">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Date of Joining</label>
                        <input type="date" class="form-control" name="date_of_joining" value="{{ old('date_of_joining', $employee->date_of_joining) }}">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Date of Leaving (Relieving Date)</label>
                        <input type="date" class="form-control" name="date_of_leaving" value="{{ old('date_of_leaving', $employee->date_of_leaving) }}">
                    </div>

                </div>
            </div>

            <!-- Section 3: Organization & Shift Assignment -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-sitemap text-primary"></i> Organization & Shift Assignment
                </h5>
                <div class="row g-3">
                    
                    <!-- Branch -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label fw-semibold text-dark">Branch</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-building text-muted"></i></span>
                            <select class="form-select" name="branch_id" id="branch_id">
                                <option value="">-- Select Branch (Optional) --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $employee->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('branch_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label fw-semibold text-dark">Department</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-sitemap text-muted"></i></span>
                            <select class="form-select" name="department_id" id="department_id">
                                <option value="">-- Select Department (Optional) --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('department_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Designation (Role - Company Scoped) -->
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-semibold text-dark mb-0">Designation (Role)</label>
                            <a href="{{ route('designations.create') }}" target="_blank" class="small text-primary text-decoration-none" title="Create a new designation in a new tab">
                                <i class="fa-solid fa-plus-circle"></i> Add Designation
                            </a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-id-badge text-primary"></i></span>
                            <select class="form-select" name="designation_id" id="designation_id" onchange="syncDesignationToPosition(this)">
                                <option value="">-- Select Designation (Role) --</option>
                                @foreach ($designations as $desig)
                                    <option value="{{ $desig->id }}" 
                                            data-name="{{ $desig->name }}"
                                            {{ old('designation_id', $employee->designation_id) == $desig->id ? 'selected' : '' }}>
                                        {{ $desig->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted d-block mt-1">Designation acts as the employee's role. Module permissions are managed based on this under Roles & Permissions.</small>
                        @error('designation_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Shift Assignment -->
                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark mb-2">
                            Assigned Shifts <span class="text-muted fw-normal small">(Select shifts applicable for this employee)</span>
                        </label>
                        <div class="row g-2">
                            @forelse ($shifts as $shift)
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="form-check p-3 bg-white rounded-3 border h-100 d-flex align-items-center gap-2">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="shift_ids[]"
                                            value="{{ $shift->id }}" id="shift_{{ $shift->id }}"
                                            {{ in_array($shift->id, old('shift_ids', $assignedShiftIds ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label text-dark cursor-pointer flex-grow-1" for="shift_{{ $shift->id }}">
                                            <span class="fw-semibold d-block">{{ $shift->shift_name }}</span>
                                            <span class="text-muted small">
                                                <i class="fa-regular fa-clock me-1 text-primary"></i>{{ $shift->start_time }} - {{ $shift->end_time }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="p-3 bg-white rounded-3 border text-muted small">
                                        <i class="fa-solid fa-circle-info text-info me-1"></i> No shifts found for this company. You can create shifts under <strong>Human Resources &rarr; Shift Timings</strong>.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        @error('shift_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Attendance Type / Punch Mode Selection -->
                    <div class="col-12 mt-4 pt-3 border-top">
                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-1">
                            <span>
                                <i class="fa-solid fa-fingerprint text-primary me-1"></i>
                                Attendance Type / Authorized Punch Mode <span class="text-danger">*</span>
                            </span>
                            <span class="badge bg-light text-primary border" style="font-size: 0.72rem;">Mobile & Web Applicable</span>
                        </label>
                        <p class="text-muted small mb-3">
                            Set how this employee must punch attendance. When set, employee can only punch via this authorized method on Web and Mobile app.
                        </p>

                        @php
                            $currentAttType = strtolower(old('attendance_type', $employee->attendance_type ?? 'geo'));
                            if ($currentAttType === 'qr') $currentAttType = 'qr code';
                        @endphp

                        <div class="row g-3">
                            <!-- 1. Geo Location -->
                            <div class="col-12 col-md-4">
                                <label class="attendance-type-card p-3 rounded-3 border h-100 d-block cursor-pointer position-relative shadow-xs" for="att_type_geo">
                                    <div class="d-flex align-items-start gap-3">
                                        <input class="form-check-input mt-1" type="radio" name="attendance_type" id="att_type_geo" value="geo" {{ $currentAttType == 'geo' ? 'checked' : '' }}>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white shadow-xs" style="width: 30px; height: 30px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                                                    <i class="fa-solid fa-location-dot" style="font-size: 0.85rem;"></i>
                                                </span>
                                                <strong class="text-dark">Geo Location</strong>
                                            </div>
                                            <small class="text-muted d-block lh-sm">
                                                Validates GPS coordinates against employee's assigned office branch radius.
                                            </small>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- 2. Selfie / Face Scan -->
                            <div class="col-12 col-md-4">
                                <label class="attendance-type-card p-3 rounded-3 border h-100 d-block cursor-pointer position-relative shadow-xs" for="att_type_selfie">
                                    <div class="d-flex align-items-start gap-3">
                                        <input class="form-check-input mt-1" type="radio" name="attendance_type" id="att_type_selfie" value="selfie" {{ $currentAttType == 'selfie' ? 'checked' : '' }}>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white shadow-xs" style="width: 30px; height: 30px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                                    <i class="fa-solid fa-camera" style="font-size: 0.85rem;"></i>
                                                </span>
                                                <strong class="text-dark">Selfie / Face</strong>
                                            </div>
                                            <small class="text-muted d-block lh-sm">
                                                Captures live camera / webcam photo when punching in & out for facial verification.
                                            </small>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- 3. QR Code -->
                            <div class="col-12 col-md-4">
                                <label class="attendance-type-card p-3 rounded-3 border h-100 d-block cursor-pointer position-relative shadow-xs" for="att_type_qr">
                                    <div class="d-flex align-items-start gap-3">
                                        <input class="form-check-input mt-1" type="radio" name="attendance_type" id="att_type_qr" value="qr code" {{ $currentAttType == 'qr code' ? 'checked' : '' }}>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white shadow-xs" style="width: 30px; height: 30px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                                                    <i class="fa-solid fa-qrcode" style="font-size: 0.85rem;"></i>
                                                </span>
                                                <strong class="text-dark">QR Code</strong>
                                            </div>
                                            <small class="text-muted d-block lh-sm">
                                                Employee must scan the official company QR code using camera scanner.
                                            </small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @error('attendance_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Actions Bar -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Employee Profile
                </button>
            </div>
        </form>

    </div>
</div>
    <script>
        function syncDesignationToPosition(selectEl) {
            const opt = selectEl.options[selectEl.selectedIndex];
            if (opt && opt.dataset.name) {
                const pos = document.getElementById('positionInput');
                if (pos) {
                    pos.value = opt.dataset.name;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const selfieInput = document.getElementById('selfieImageInput');
            const selfiePreviewImg = document.getElementById('selfiePreviewImg');
            const selfiePlaceholder = document.getElementById('selfiePlaceholder');
            const btnUploadAjax = document.getElementById('btnUploadSelfieAjax');
            const btnRemoveAjax = document.getElementById('btnRemoveSelfieAjax');
            const selfieViewLink = document.getElementById('selfieViewLink');
            const selfieStatusBadge = document.getElementById('selfieStatusBadge');
            const employeeId = {{ $employee->id }};
            const uploadUrl = "{{ route('user.employees.selfieUpload', $employee->id) }}";
            const removeUrl = "{{ route('user.employees.selfieRemove', $employee->id) }}";
            const csrfToken = "{{ csrf_token() }}";

            // Live image preview on file choose
            if (selfieInput) {
                selfieInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (event) {
                            selfiePreviewImg.src = event.target.result;
                            selfiePreviewImg.classList.remove('d-none');
                            if (selfiePlaceholder) selfiePlaceholder.classList.add('d-none');
                            if (btnUploadAjax) btnUploadAjax.style.display = 'inline-block';
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Instant AJAX Upload
            if (btnUploadAjax) {
                btnUploadAjax.addEventListener('click', function () {
                    const file = selfieInput.files[0];
                    if (!file) {
                        Swal.fire('No File', 'Please select a photo first.', 'warning');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('selfie_image', file);
                    formData.append('_token', csrfToken);

                    btnUploadAjax.disabled = true;
                    btnUploadAjax.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Uploading...';

                    fetch(uploadUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        btnUploadAjax.disabled = false;
                        btnUploadAjax.innerHTML = '<i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Now';
                        btnUploadAjax.style.display = 'none';

                        if (data.status) {
                            if (data.selfie_url) {
                                selfiePreviewImg.src = data.selfie_url;
                                selfiePreviewImg.classList.remove('d-none');
                                if (selfiePlaceholder) selfiePlaceholder.classList.add('d-none');
                                if (selfieViewLink) {
                                    selfieViewLink.href = data.selfie_url;
                                    selfieViewLink.classList.remove('d-none');
                                }
                            }
                            if (btnRemoveAjax) btnRemoveAjax.classList.remove('d-none');
                            if (selfieStatusBadge) {
                                selfieStatusBadge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill';
                                selfieStatusBadge.innerHTML = '<i class="fa-solid fa-check-circle me-1"></i> Registered';
                            }
                            selfieInput.value = '';

                            Swal.fire({
                                icon: 'success',
                                title: 'Selfie Registered',
                                text: data.message || 'Reference selfie photo updated successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Upload Failed', data.message || 'Could not upload selfie.', 'error');
                        }
                    })
                    .catch(err => {
                        btnUploadAjax.disabled = false;
                        btnUploadAjax.innerHTML = '<i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Now';
                        Swal.fire('Error', 'An unexpected error occurred while uploading.', 'error');
                    });
                });
            }

            // Instant AJAX Remove
            if (btnRemoveAjax) {
                btnRemoveAjax.addEventListener('click', function () {
                    Swal.fire({
                        title: 'Remove Reference Selfie?',
                        text: 'This employee will not be able to punch attendance via Face-ID until a new selfie is registered.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Remove Selfie'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            btnRemoveAjax.disabled = true;

                            fetch(removeUrl, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                btnRemoveAjax.disabled = false;
                                if (data.status) {
                                    selfiePreviewImg.src = '';
                                    selfiePreviewImg.classList.add('d-none');
                                    if (selfiePlaceholder) selfiePlaceholder.classList.remove('d-none');
                                    btnRemoveAjax.classList.add('d-none');
                                    if (selfieViewLink) selfieViewLink.classList.add('d-none');
                                    if (btnUploadAjax) btnUploadAjax.style.display = 'none';
                                    selfieInput.value = '';

                                    if (selfieStatusBadge) {
                                        selfieStatusBadge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill';
                                        selfieStatusBadge.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i> Not Registered';
                                    }

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Removed',
                                        text: 'Reference selfie has been removed.',
                                        timer: 1800,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire('Error', data.message || 'Could not remove selfie.', 'error');
                                }
                            })
                            .catch(err => {
                                btnRemoveAjax.disabled = false;
                                Swal.fire('Error', 'An error occurred while removing selfie.', 'error');
                            });
                        }
                    });
                });
            }
        });
    </script>
@endsection
