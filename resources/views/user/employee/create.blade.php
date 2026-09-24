@extends('user.layouts.app')

@section('title', 'Add New Employee | STAFO HRMS')

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
                <h3 class="fw-bold text-dark mb-1">Onboard New Employee</h3>
                <p class="text-muted small mb-0">Create staff profile, set designation, contact credentials, and compensation structure</p>
            </div>
            <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Employee List
            </a>
        </div>

        <form action="{{ route('user.employees.store') }}" method="POST" id="addEmployeeForm">
            @csrf

            <!-- Section 1: Personal & Contact Credentials -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-tie text-primary"></i> Personal & Contact Credentials
                </h5>
                <div class="row g-3">
                    
                    <!-- Name Input -->
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label fw-semibold text-dark">
                            Full Legal Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-user text-muted"></i></span>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="e.g. Rajesh Sharma" minlength="6" maxlength="55" required>
                        </div>
                        <small class="text-muted d-block mt-1">Minimum 6 characters, legal name as per ID.</small>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label fw-semibold text-dark">
                            Work Email Address <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-envelope text-muted"></i></span>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" placeholder="e.g. rajesh@company.com" required>
                        </div>
                        <small class="text-muted d-block mt-1">Used for employee login & salary notifications.</small>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone Input -->
                    <div class="col-12 col-md-6">
                        <label for="phone" class="form-label fw-semibold text-dark">
                            Mobile Phone Number <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-phone text-muted"></i></span>
                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}" placeholder="e.g. 9876543210"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                   maxlength="10" minlength="10" required>
                        </div>
                        <small class="text-muted d-block mt-1">10-digit mobile number for mobile punch-in OTPs.</small>
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Designation Select -->
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label for="designation_id" class="form-label fw-semibold text-dark mb-0">
                                Designation (Role)
                            </label>
                            <a href="{{ route('designations.create') }}" target="_blank" class="small text-primary text-decoration-none" title="Create a new designation in a new tab">
                                <i class="fa-solid fa-plus-circle"></i> Add Designation
                            </a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-id-badge text-primary"></i></span>
                            <select name="designation_id" id="designation_id" class="form-select @error('designation_id') is-invalid @enderror" onchange="syncCreateDesignationToPosition(this)">
                                <option value="">-- Select Designation (Role) --</option>
                                @foreach ($designations as $desig)
                                    <option value="{{ $desig->id }}" 
                                            data-name="{{ $desig->name }}" 
                                            {{ old('designation_id') == $desig->id ? 'selected' : '' }}>
                                        {{ $desig->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted d-block mt-1">Designation acts as the employee's role. Module permissions are assigned based on this.</small>
                        @error('designation_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Position / Title Input -->
                    <div class="col-12 col-md-6">
                        <label for="position" class="form-label fw-semibold text-dark">
                            Position / Custom Title
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-briefcase text-muted"></i></span>
                            <input type="text" name="position" id="positionInput" class="form-control @error('position') is-invalid @enderror" 
                                   value="{{ old('position') }}" placeholder="Auto-filled from Designation or custom title">
                        </div>
                        <small class="text-muted d-block mt-1">Title displayed on employee profile and payslips.</small>
                        @error('position')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Section 2: Compensation & Payroll -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-indian-rupee-sign text-success"></i> Compensation & Salary Details
                </h5>
                <div class="row g-3">
                    
                    <!-- Salary Input -->
                    <div class="col-12 col-md-6">
                        <label for="salary" class="form-label fw-semibold text-dark">
                            Monthly Basic Salary (₹)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white fw-bold text-success">₹</span>
                            <input type="number" step="0.01" name="salary" id="salary" class="form-control @error('salary') is-invalid @enderror" 
                                   value="{{ old('salary') }}" placeholder="e.g. 35000"
                                   oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                        </div>
                        <small class="text-muted d-block mt-1">Base salary used as baseline for salary components calculation.</small>
                        @error('salary')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 d-flex align-items-center">
                        <div class="p-3 bg-white rounded-3 border w-100">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="fa-solid fa-circle-info text-primary fs-5"></i>
                                <div>
                                    <span class="fw-bold text-dark d-block">Department & Branch Assignment</span>
                                    <span>Branch, Department, and Shift timings can be assigned directly from the Employee Directory after creation.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Type / Punch Mode Selection -->
                    <div class="col-12 mt-3 pt-3 border-top">
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
                            $currentAttType = strtolower(old('attendance_type', 'geo'));
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
                    <i class="fa-solid fa-user-plus me-1"></i> Save Employee Profile
                </button>
            </div>
        </form>

    </div>
</div>
    <script>
        function syncCreateDesignationToPosition(selectEl) {
            const opt = selectEl.options[selectEl.selectedIndex];
            if (opt && opt.dataset.name) {
                const pos = document.getElementById('positionInput');
                if (pos && (!pos.value || pos.value.trim() === '')) {
                    pos.value = opt.dataset.name;
                }
            }
        }
    </script>
@endsection
