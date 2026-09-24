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

            <!-- Section 1: Profile Photo & Resume -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-id-badge text-primary"></i> Profile Photo & Resume Document
                </h5>
                <div class="row g-4 align-items-center">
                    
                    <!-- Profile Picture -->
                    <div class="col-12 col-md-6">
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

                    <!-- Resume Document -->
                    <div class="col-12 col-md-6">
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
                        <label class="form-label fw-semibold text-dark">Basic Salary (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white fw-bold text-success">₹</span>
                            <input type="text" name="salary" class="form-control" value="{{ old('salary', $employee->salary) }}" placeholder="Enter salary">
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
    </script>
@endsection
