@extends('user.layouts.app')
@section('title', 'Employee Dossier | ' . ($employee->name ?? 'STAFO HRMS'))

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">

        <!-- Top Navigation & Action Bar -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary px-3 py-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Employee List
                </a>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('user.employees.edit', $employee->id) }}" class="btn btn-warning px-3 py-2 text-dark fw-semibold">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Profile
                </a>
                <a href="{{ $employee->bankAccount ? route('editBankAccount', $employee->bankAccount->id) : route('addBankAccount', $employee->id) }}" class="btn btn-outline-primary px-3 py-2">
                    <i class="fa-solid fa-building-columns me-1"></i> {{ $employee->bankAccount ? 'Edit Bank' : 'Add Bank' }}
                </a>
                <a href="{{ route('documentVerification', $employee->id) }}" class="btn btn-outline-info px-3 py-2">
                    <i class="fa-solid fa-certificate me-1"></i> KYC Verification
                </a>
                <a href="{{ route('employee.location', $employee->id) }}" class="btn btn-outline-success px-3 py-2">
                    <i class="fa-solid fa-location-crosshairs me-1"></i> Location Tracking
                </a>
            </div>
        </div>

        <!-- Hero Profile Banner -->
        <div class="p-4 rounded-4 bg-light border mb-4">
            <div class="row align-items-center g-4">
                <div class="col-auto">
                    <div class="rounded-circle overflow-hidden border border-white shadow-sm bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" 
                         style="width: 88px; height: 88px;">
                        @if ($employee->image_url)
                            <img src="{{ $employee->image_url }}" alt="{{ $employee->name }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <span class="fs-1 fw-bold text-primary">{{ strtoupper(substr($employee->name ?? 'E', 0, 1)) }}</span>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h3 class="fw-bold text-dark mb-0">{{ $employee->name }}</h3>
                        <span class="badge-stafo badge-stafo-info font-monospace">
                            ID: {{ $employee->emp_id ?? 'N/A' }}
                        </span>
                        @if ($employee->status == 1)
                            <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
                        @else
                            <span class="badge-stafo badge-stafo-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Inactive</span>
                        @endif
                    </div>
                    <p class="text-muted mb-2">
                        <i class="fa-solid fa-briefcase text-primary me-1"></i> {{ $employee->position ?: 'Staff Member' }}
                        @if($employee->department) &bull; <i class="fa-solid fa-layer-group text-info me-1"></i> {{ $employee->department->name }} @endif
                        @if($employee->branch) &bull; <i class="fa-solid fa-building text-warning me-1"></i> {{ $employee->branch->branch_name }} @endif
                    </p>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                        <span><i class="fa-solid fa-envelope text-primary me-1"></i> {{ $employee->email }}</span>
                        <span><i class="fa-solid fa-phone text-success me-1"></i> {{ $employee->phone }}</span>
                        @if($employee->official_email_id)
                            <span><i class="fa-solid fa-at text-info me-1"></i> {{ $employee->official_email_id }}</span>
                        @endif
                        <span>
                            <i class="fa-solid fa-circle {{ $employee->device_status == 1 ? 'text-success' : 'text-secondary' }} me-1" style="font-size: 8px;"></i>
                            Device: {{ $employee->device_status == 1 ? 'Online' : 'Offline' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Info Grids -->
        <div class="row g-4">
            
            <!-- 1. Employment & Organizational Details -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-building-user text-primary"></i> Employment & Organization
                    </h5>
                    
                    <table class="table table-borderless table-sm mb-0 align-middle">
                        <tbody>
                            <tr>
                                <td class="text-muted ps-0 py-2" style="width: 170px;">Branch:</td>
                                <td class="fw-semibold text-dark py-2">
                                    @if ($employee->branch)
                                        <span class="badge bg-white text-dark border px-2 py-1">
                                            <i class="fa-solid fa-building text-success me-1"></i> {{ $employee->branch->branch_name }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not Assigned</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Department:</td>
                                <td class="fw-semibold text-dark py-2">
                                    @if ($employee->department)
                                        <span class="badge bg-white text-dark border px-2 py-1">
                                            <i class="fa-solid fa-sitemap text-primary me-1"></i> {{ $employee->department->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not Assigned</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Designation:</td>
                                <td class="fw-semibold text-dark py-2">
                                    @if($employee->designation)
                                        <span class="badge bg-white text-dark border px-2 py-1">
                                            <i class="fa-solid fa-id-badge text-primary me-1"></i> {{ $employee->designation->name }}
                                        </span>
                                    @elseif($employee->position)
                                        <span class="badge bg-white text-dark border px-2 py-1">
                                            <i class="fa-solid fa-briefcase text-muted me-1"></i> {{ $employee->position }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not Specified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Access Role:</td>
                                <td class="py-2">
                                    @if($employee->companyRole)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                            <i class="fa-solid fa-user-shield me-1"></i> {{ $employee->companyRole->name }}
                                            <span class="badge bg-primary text-white rounded-pill ms-1" style="font-size: 0.65rem;">{{ $employee->companyRole->permissions->count() }} perms</span>
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1">
                                            <i class="fa-solid fa-user me-1"></i> Standard Staff (Default)
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Employee Type:</td>
                                <td class="text-dark py-2">{{ $employee->employeeType->name ?? 'Permanent / Staff' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Attendance Mode:</td>
                                <td class="py-2">
                                    @php
                                        $shAtt = strtolower($employee->attendance_type ?? 'geo');
                                        if ($shAtt === 'qr') $shAtt = 'qr code';
                                    @endphp
                                    @if($shAtt === 'selfie')
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                            <i class="fa-solid fa-camera me-1"></i> Selfie / Face Attendance
                                        </span>
                                    @elseif($shAtt === 'qr code')
                                        <span class="badge bg-warning bg-opacity-15 text-dark border border-warning border-opacity-50 px-2 py-1">
                                            <i class="fa-solid fa-qrcode text-warning me-1"></i> Company QR Code Attendance
                                        </span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                            <i class="fa-solid fa-location-dot me-1"></i> Geo Location Attendance
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Basic Salary:</td>
                                <td class="fw-bold text-success py-2">₹{{ number_format((float)($employee->salary ?? 0), 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Date of Joining:</td>
                                <td class="text-dark py-2">
                                    {{ $employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d M, Y') : '—' }}
                                </td>
                            </tr>
                            @if($employee->date_of_leaving)
                                <tr>
                                    <td class="text-muted ps-0 py-2">Relieving Date:</td>
                                    <td class="text-danger py-2">
                                        {{ \Carbon\Carbon::parse($employee->date_of_leaving)->format('d M, Y') }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-muted ps-0 py-2">Official Email:</td>
                                <td class="text-dark py-2">{{ $employee->official_email_id ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">ESI Number:</td>
                                <td class="text-dark py-2">{{ $employee->esi_number ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">PF Number:</td>
                                <td class="text-dark py-2">{{ $employee->pf_number ?: '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Shift Timings & Work Schedule -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-clock text-info"></i> Assigned Shift Timings
                        </h5>
                        <a href="{{ route('user.employees.edit', $employee->id) }}" class="small fw-semibold text-primary text-decoration-none">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Change Shifts
                        </a>
                    </div>
                    
                    @php
                        $assignedShiftsList = $employee->shifts && $employee->shifts->count() > 0 
                            ? $employee->shifts 
                            : ($employee->shift ? collect([$employee->shift]) : collect([]));
                    @endphp

                    @if($assignedShiftsList->count() > 0)
                        <div class="d-flex flex-column gap-3">
                            @foreach($assignedShiftsList as $shift)
                                <div class="p-3 bg-white rounded-3 border">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold text-dark fs-6">
                                            <i class="fa-solid fa-business-time text-primary me-2"></i>{{ $shift->shift_name }}
                                        </span>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                            Shift #{{ $shift->id }}
                                        </span>
                                    </div>
                                    <div class="row g-2 small text-muted">
                                        <div class="col-6">
                                            <span class="d-block text-secondary">Working Hours:</span>
                                            <span class="fw-semibold text-dark">
                                                <i class="fa-regular fa-clock me-1 text-info"></i>{{ $shift->start_time }} - {{ $shift->end_time }}
                                            </span>
                                        </div>
                                        @if($shift->half_day_time)
                                            <div class="col-6">
                                                <span class="d-block text-secondary">Half Day Time:</span>
                                                <span class="fw-semibold text-dark">{{ $shift->half_day_time }}</span>
                                            </div>
                                        @endif
                                        @if($shift->late_mark_after)
                                            <div class="col-6">
                                                <span class="d-block text-secondary">Late Mark After:</span>
                                                <span class="fw-semibold text-warning">{{ $shift->late_mark_after }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 bg-white rounded-3 border">
                            <i class="fa-solid fa-calendar-xmark text-muted fs-3 mb-2 d-block opacity-50"></i>
                            <p class="text-muted small mb-2">No shift timings currently assigned to this employee.</p>
                            <a href="{{ route('user.employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-primary px-3">
                                <i class="fa-solid fa-plus me-1"></i> Assign Shift in Edit Profile
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 3. Personal & Identity Data -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-address-card text-primary"></i> Personal & Location Information
                    </h5>
                    
                    <table class="table table-borderless table-sm mb-0 align-middle">
                        <tbody>
                            <tr>
                                <td class="text-muted ps-0 py-2" style="width: 170px;">Date of Birth:</td>
                                <td class="text-dark py-2">
                                    {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d M, Y') : '—' }}
                                    @if($employee->date_of_birth)
                                        <small class="text-muted">({{ \Carbon\Carbon::parse($employee->date_of_birth)->age }} years old)</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Gender:</td>
                                <td class="text-dark py-2">{{ ucfirst($employee->gender ?? '—') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Marital Status:</td>
                                <td class="text-dark py-2">{{ ucfirst($employee->marital_status ?? '—') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Blood Group:</td>
                                <td class="text-dark py-2">
                                    @if($employee->blood_group)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-3">
                                            {{ $employee->blood_group }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Guardian / Father:</td>
                                <td class="text-dark py-2">{{ $employee->guardian_name ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Residential Address:</td>
                                <td class="text-dark py-2">{{ $employee->address ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">City:</td>
                                <td class="text-dark py-2">{{ $employee->city ? ($employee->city->name ?? $employee->city) : '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">State:</td>
                                <td class="text-dark py-2">{{ $employee->state ? ($employee->state->name ?? $employee->state) : '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Country:</td>
                                <td class="text-dark py-2">{{ $employee->country ? ($employee->country->name ?? $employee->country) : '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 py-2">Postal PIN Code:</td>
                                <td class="text-dark py-2">{{ $employee->pin ?: '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Bank Account Details -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-building-columns text-success"></i> Bank Account Information
                        </h5>
                        @if($employee->bankAccount)
                            <a href="{{ route('editBankAccount', $employee->bankAccount->id) }}" class="small fw-semibold text-primary text-decoration-none">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Account
                            </a>
                        @endif
                    </div>

                    @if($employee->bankAccount)
                        <div class="p-3 bg-white rounded-3 border">
                            <table class="table table-borderless table-sm mb-0 align-middle">
                                <tbody>
                                    <tr>
                                        <td class="text-muted ps-0 py-2" style="width: 160px;">Bank Name:</td>
                                        <td class="fw-bold text-dark py-2">{{ $employee->bankAccount->bank_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Account Holder:</td>
                                        <td class="fw-semibold text-dark py-2">{{ $employee->bankAccount->account_holder_name ?? $employee->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Account Number:</td>
                                        <td class="fw-bold text-primary font-monospace py-2 fs-6">{{ $employee->bankAccount->account_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">IFSC Code:</td>
                                        <td class="fw-bold text-dark font-monospace py-2">{{ $employee->bankAccount->ifsc_code }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0 py-2">Bank Branch:</td>
                                        <td class="text-dark py-2">{{ $employee->bankAccount->branch_name ?? '—' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 bg-white rounded-3 border">
                            <i class="fa-solid fa-credit-card text-muted fs-3 mb-2 d-block opacity-50"></i>
                            <p class="text-muted small mb-2">No bank account registered for salary disbursement.</p>
                            <a href="{{ route('addBankAccount', $employee->id) }}" class="btn btn-sm btn-outline-success px-3">
                                <i class="fa-solid fa-plus me-1"></i> Add Bank Account
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 5. Government KYC & Verification Documents -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-shield-check text-warning"></i> Statutory KYC Verification
                        </h5>
                        <a href="{{ route('documentVerification', $employee->id) }}" class="small fw-semibold text-primary text-decoration-none">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Verify Documents
                        </a>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <!-- Aadhar Card -->
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-bold text-dark d-block">
                                    <i class="fa-solid fa-id-card text-primary me-2"></i>Aadhar Card
                                </span>
                                <small class="text-muted font-monospace">{{ $employee->aadhar ?: 'Not Provided' }}</small>
                            </div>
                            <div>
                                @if($employee->aadhar_verify == 'Yes')
                                    <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check me-1"></i> Verified</span>
                                @else
                                    <span class="badge-stafo badge-stafo-warning"><i class="fa-solid fa-clock me-1"></i> Unverified</span>
                                @endif
                            </div>
                        </div>

                        <!-- PAN Card -->
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-bold text-dark d-block">
                                    <i class="fa-solid fa-credit-card text-success me-2"></i>PAN Card
                                </span>
                                <small class="text-muted font-monospace text-uppercase">{{ $employee->pan ?: 'Not Provided' }}</small>
                            </div>
                            <div>
                                @if($employee->pan_verify == 'Yes')
                                    <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check me-1"></i> Verified</span>
                                @else
                                    <span class="badge-stafo badge-stafo-warning"><i class="fa-solid fa-clock me-1"></i> Unverified</span>
                                @endif
                            </div>
                        </div>

                        <!-- Voter ID -->
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-bold text-dark d-block">
                                    <i class="fa-solid fa-check-to-slot text-info me-2"></i>Voter ID (EPIC)
                                </span>
                                <small class="text-muted font-monospace">{{ $employee->voter ?: 'Not Provided' }}</small>
                            </div>
                            <div>
                                @if($employee->voter_verify == 'Yes')
                                    <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check me-1"></i> Verified</span>
                                @else
                                    <span class="badge-stafo badge-stafo-warning"><i class="fa-solid fa-clock me-1"></i> Unverified</span>
                                @endif
                            </div>
                        </div>

                        @if($employee->dl)
                            <!-- Driving License -->
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-bold text-dark d-block">
                                        <i class="fa-solid fa-id-badge text-warning me-2"></i>Driving License
                                    </span>
                                    <small class="text-muted font-monospace">{{ $employee->dl }}</small>
                                </div>
                                <div>
                                    @if($employee->dl_verify == 'Yes')
                                        <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check me-1"></i> Verified</span>
                                    @else
                                        <span class="badge-stafo badge-stafo-warning"><i class="fa-solid fa-clock me-1"></i> Unverified</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($employee->passport)
                            <!-- Passport -->
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-bold text-dark d-block">
                                        <i class="fa-solid fa-passport text-danger me-2"></i>Passport
                                    </span>
                                    <small class="text-muted font-monospace">{{ $employee->passport }}</small>
                                </div>
                                <div>
                                    @if($employee->passport_verify == 'Yes')
                                        <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check me-1"></i> Verified</span>
                                    @else
                                        <span class="badge-stafo badge-stafo-warning"><i class="fa-solid fa-clock me-1"></i> Unverified</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 6. Leave Entitlements & Balance -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-success"></i> Leave Entitlements
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="p-3 bg-white rounded-3 border text-center">
                                <span class="text-muted small d-block mb-1">Privileged Leave</span>
                                <h4 class="fw-bold text-primary mb-0">{{ $employee->privileged_leave ?? 0 }}</h4>
                                <small class="text-muted">Days</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-white rounded-3 border text-center">
                                <span class="text-muted small d-block mb-1">Sick Leave</span>
                                <h4 class="fw-bold text-warning mb-0">{{ $employee->sick_leave ?? 0 }}</h4>
                                <small class="text-muted">Days</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-white rounded-3 border text-center">
                                <span class="text-muted small d-block mb-1">Casual Leave</span>
                                <h4 class="fw-bold text-success mb-0">{{ $employee->casual_leave ?? 0 }}</h4>
                                <small class="text-muted">Days</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small">Employee Leave History:</span>
                            <a href="{{ route('leaveList') }}" class="small fw-semibold text-primary">
                                <i class="fa-solid fa-list-check me-1"></i> View Leave Records
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. Media, Selfies & Attached Documents -->
            <div class="col-12">
                <div class="p-4 bg-light rounded-4 border">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-paperclip text-primary"></i> Uploaded Documents & Media
                        </h5>
                        <a href="{{ $employee->document ? route('employee.documents.edit', ['employeeId' => $employee->id, 'documentId' => $employee->document->id]) : route('employee.documents.create', $employee->id) }}" class="small fw-semibold text-primary text-decoration-none">
                            <i class="fa-solid fa-plus me-1"></i> Upload More Documents
                        </a>
                    </div>

                    <div class="row g-4">
                        
                        <!-- Profile Image -->
                        @if ($employee->image_url)
                            <div class="col-12 col-md-4 col-lg-3">
                                <div class="p-3 bg-white rounded-3 border h-100">
                                    <span class="text-muted small fw-semibold d-block mb-2">Profile Photo</span>
                                    <div class="rounded-3 overflow-hidden border mb-2" style="height: 140px;">
                                        <img src="{{ $employee->image_url }}" alt="Profile Photo" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <a href="{{ $employee->image_url }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fa-solid fa-eye me-1"></i> View Full Image
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Selfie Photo -->
                        @if ($employee->selfie_url)
                            <div class="col-12 col-md-4 col-lg-3">
                                <div class="p-3 bg-white rounded-3 border h-100">
                                    <span class="text-muted small fw-semibold d-block mb-2">Verification Selfie</span>
                                    <div class="rounded-3 overflow-hidden border mb-2" style="height: 140px;">
                                        <img src="{{ $employee->selfie_url }}" alt="Employee Selfie" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <a href="{{ $employee->selfie_url }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fa-solid fa-eye me-1"></i> View Full Selfie
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Resume Document -->
                        @if ($employee->resume_url)
                            <div class="col-12 col-md-4 col-lg-3">
                                <div class="p-3 bg-white rounded-3 border h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <span class="text-muted small fw-semibold d-block mb-2">Resume / Curriculum Vitae</span>
                                        <div class="d-flex align-items-center gap-2 py-3">
                                            <i class="fa-solid fa-file-pdf text-danger fs-1"></i>
                                            <div>
                                                <span class="fw-bold text-dark d-block">Resume File</span>
                                                <small class="text-muted">PDF / Word file</small>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ $employee->resume_url }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fa-solid fa-download me-1"></i> View / Download
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Attached Official Documents from EmployeeDocument -->
                        @php
                            $allDocuments = $employee->documents && $employee->documents->count() > 0 
                                ? $employee->documents 
                                : ($employee->document ? collect([$employee->document]) : collect([]));
                        @endphp

                        @foreach($allDocuments as $doc)
                            <div class="col-12 col-md-4 col-lg-3">
                                <div class="p-3 bg-white rounded-3 border h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <span class="text-muted small fw-semibold d-block mb-2">
                                            {{ $doc->documentType->name ?? 'Uploaded Document' }}
                                        </span>
                                        <div class="d-flex align-items-center gap-2 py-2">
                                            <i class="fa-solid fa-file-lines text-info fs-2"></i>
                                            <div class="text-truncate">
                                                <span class="fw-bold text-dark d-block text-truncate">{{ $doc->document_name ?? 'Document' }}</span>
                                                <small class="text-muted">{{ $doc->created_at ? $doc->created_at->format('d M, Y') : 'Attached' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    @if($doc->file_path)
                                        <a href="{{ asset('uploads/employee_documents/' . $doc->file_path) }}" target="_blank" class="btn btn-outline-info btn-sm w-100 mt-2">
                                            <i class="fa-solid fa-file-arrow-down me-1"></i> View Document
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if(!$employee->image_url && !$employee->selfie_url && !$employee->resume_url && $allDocuments->isEmpty())
                            <div class="col-12 text-center py-4 text-muted">
                                <i class="fa-solid fa-folder-open fs-3 mb-2 d-block opacity-50"></i>
                                No media files, selfies, or documents attached to this profile.
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
