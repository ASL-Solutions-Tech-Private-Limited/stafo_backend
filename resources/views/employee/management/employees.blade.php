@extends('employee.layouts.app')

@section('title', 'Employee Directory | Management Portal')

@section('css')
<style>
    .stat-card {
        background: var(--bs-card-bg, #ffffff);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 14px;
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px -4px rgba(0,0,0,0.08);
    }
    .badge-stafo {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.76rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 9999px;
        white-space: nowrap;
    }
    .badge-stafo-success {
        background-color: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }
    .badge-stafo-danger {
        background-color: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }
    .avatar-container {
        position: relative;
        display: inline-block;
        flex-shrink: 0;
    }
    .kyc-indicator-dot {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        color: #ffffff;
        border: 2px solid #ffffff;
    }
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: all 0.15s ease;
    }
    [data-theme="dark"] .table thead.table-dark th {
        background-color: #0d1527 !important;
        color: #f8fafc !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .table td {
        background-color: #111c30 !important;
        color: #f8fafc !important;
        border-color: rgba(255, 255, 255, 0.06) !important;
    }
    [data-theme="dark"] .stat-card {
        background: #111c30 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Role Indicator -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-users me-1"></i> Management Portal
                </span>
                <span class="text-muted small">• Staff Directory</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Employees Directory</h3>
            <p class="text-muted small mb-0">Review company staff profiles, KYC compliance status, assigned departments, shifts, and branches.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border px-3 py-2 rounded-3 shadow-2xs font-sans">
                Role: <strong class="text-primary ms-1">{{ Auth::guard('employee')->user()->designation->name ?? 'Management Role' }}</strong>
            </span>
        </div>
    </div>

    <!-- Metric Summary Cards (Matches Company Panel) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3 stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.725rem;">Total Staff</small>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalEmployees }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-users fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.725rem;">Active Staff</small>
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ $activeEmployees }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-user-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('employee.management.employees', array_merge(request()->except('kyc_status'), ['kyc_status' => 'verified'])) }}" class="text-decoration-none">
                <div class="p-3 stat-card {{ request('kyc_status') == 'verified' ? 'border-success shadow-sm bg-success bg-opacity-10' : '' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.725rem;">KYC Verified</small>
                            <h3 class="fw-bold text-success mb-0 mt-1">{{ $verifiedEmployees }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="fa-solid fa-shield-check fs-5"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('employee.management.employees', array_merge(request()->except('kyc_status'), ['kyc_status' => 'unverified'])) }}" class="text-decoration-none">
                <div class="p-3 stat-card {{ request('kyc_status') == 'unverified' ? 'border-danger shadow-sm bg-danger bg-opacity-10' : '' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.725rem;">KYC Pending</small>
                            <h3 class="fw-bold text-danger mb-0 mt-1">{{ $unverifiedEmployees }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="fa-solid fa-shield-halved fs-5"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow-sm border-0 rounded-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="card-body p-4">

            <!-- Page Header & Action Buttons -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Employees Directory</h3>
                    <p class="text-muted small mb-0">Browse and manage company employee records, shifts, branches, departments, and KYC status</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @if(Auth::guard('employee')->user()->hasPermission('employees.create'))
                        <button type="button" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addEmpModal">
                            <i class="fa-solid fa-user-plus me-1"></i> Add Employee
                        </button>
                        <button type="button" class="btn btn-outline-primary px-3 py-2" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fa-solid fa-file-import me-1"></i> Import
                        </button>
                    @endif
                    <a href="{{ route('employee.management.employees.export', request()->all()) }}" class="btn btn-outline-secondary px-3 py-2">
                        <i class="fa-solid fa-file-export me-1"></i> Export
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('employee.management.employees') }}" class="mb-4" id="employeeFilterForm">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="Search name, ID, email, phone..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="branch_id" class="form-select bg-light" onchange="this.form.submit()">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="department_id" class="form-select bg-light" onchange="this.form.submit()">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="designation_id" class="form-select bg-light" onchange="this.form.submit()">
                            <option value="">All Designations</option>
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}" {{ request('designation_id') == $desig->id ? 'selected' : '' }}>{{ $desig->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="kyc_status" class="form-select bg-light" onchange="this.form.submit()">
                            <option value="">All KYC Status</option>
                            <option value="verified" {{ request('kyc_status') == 'verified' ? 'selected' : '' }}>KYC Verified</option>
                            <option value="unverified" {{ request('kyc_status') == 'unverified' ? 'selected' : '' }}>KYC Not Verified</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-1 d-flex gap-1">
                        <button type="submit" class="btn btn-primary w-100" title="Apply Filters">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                        @if(request()->hasAny(['search', 'branch_id', 'department_id', 'designation_id', 'kyc_status', 'per_page']))
                            <a href="{{ route('employee.management.employees') }}" class="btn btn-light border text-muted" title="Clear Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Table (Exact columns matching Company Panel: S.No, Employee, Contact, Department, Branch, Shift, KYC Status, Status, Actions) -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark" style="background: #0f172a;">
                        <tr class="text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            <th style="width: 50px;" class="text-center">#</th>
                            <th style="min-width: 220px;">Employee</th>
                            <th>Contact</th>
                            <th>Department</th>
                            <th>Branch</th>
                            <th>Shift</th>
                            <th class="text-center" style="min-width: 110px;">KYC Status</th>
                            <th class="text-center" style="width: 90px;">Status</th>
                            <th style="width: 130px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $canEditEmp = Auth::guard('employee')->user()->hasPermission('employees.edit');
                            $canDeleteEmp = Auth::guard('employee')->user()->hasPermission('employees.delete');
                        @endphp
                        @forelse ($employees as $index => $employeeRecord)
                            @php
                                $isAadhar = ($employeeRecord->aadhar_verify == 'Yes');
                                $isPan = ($employeeRecord->pan_verify == 'Yes');
                                $isVoter = ($employeeRecord->voter_verify == 'Yes');
                                $isDl = ($employeeRecord->dl_verify == 'Yes');
                                $isVerified = ($isAadhar || $isPan || $isVoter || $isDl);
                            @endphp
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $employees->firstItem() + $index }}</td>
                                
                                <!-- Employee Name & Avatar with KYC indicator -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-container">
                                            @if ($employeeRecord->image && file_exists(public_path('uploads/employees/' . $employeeRecord->image)))
                                                <img src="{{ asset('uploads/employees/' . $employeeRecord->image) }}" alt="{{ $employeeRecord->name }}" 
                                                     class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 1px solid #e2e8f0;">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" 
                                                     style="width: 40px; height: 40px; font-size: 0.85rem; flex-shrink: 0;">
                                                    {{ strtoupper(substr($employeeRecord->name ?? 'E', 0, 2)) }}
                                                </div>
                                            @endif

                                            <!-- KYC Indicator Dot on Avatar -->
                                            @if ($isVerified)
                                                <span class="kyc-indicator-dot bg-success" title="KYC Verified">
                                                    <i class="fa-solid fa-check"></i>
                                                </span>
                                            @else
                                                <span class="kyc-indicator-dot bg-danger" title="KYC Not Verified">
                                                    <i class="fa-solid fa-exclamation"></i>
                                                </span>
                                            @endif
                                        </div>

                                        <div>
                                            <span class="text-dark fw-bold d-block lh-sm mb-1">
                                                {{ $employeeRecord->name }}
                                            </span>
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                @if($employeeRecord->emp_id)
                                                    <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                                        ID: {{ $employeeRecord->emp_id }}
                                                    </span>
                                                @endif
                                                @if($employeeRecord->designation)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                        {{ $employeeRecord->designation->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Contact Info -->
                                <td>
                                    @if($employeeRecord->phone)
                                        <a href="tel:{{ $employeeRecord->phone }}" class="text-dark text-decoration-none small d-block mb-1 fw-medium">
                                            <i class="fa-solid fa-phone text-muted me-1" style="font-size: 0.75rem;"></i>{{ $employeeRecord->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted small d-block mb-1">-</span>
                                    @endif
                                    @if($employeeRecord->email)
                                        <a href="mailto:{{ $employeeRecord->email }}" class="text-muted text-decoration-none small d-block text-truncate" style="max-width: 160px;" title="{{ $employeeRecord->email }}">
                                            <i class="fa-solid fa-envelope text-muted me-1" style="font-size: 0.75rem;"></i>{{ $employeeRecord->email }}
                                        </a>
                                    @endif
                                </td>

                                <!-- Department -->
                                <td>
                                    <span class="badge bg-light text-dark border font-sans" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-layer-group me-1 text-primary"></i>
                                        {{ $employeeRecord->department->name ?? 'Not Assigned' }}
                                    </span>
                                </td>

                                <!-- Branch -->
                                <td>
                                    <span class="text-secondary small">
                                        <i class="fa-solid fa-building me-1 text-success"></i>
                                        {{ $employeeRecord->branch->branch_name ?? 'Not Assigned' }}
                                    </span>
                                </td>

                                <!-- Shift -->
                                <td>
                                    <span class="badge bg-light text-secondary border font-sans" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-clock me-1 text-info"></i>
                                        {{ $employeeRecord->shift->shift_name ?? 'Default' }}
                                    </span>
                                </td>

                                <!-- KYC Status -->
                                <td class="text-center">
                                    @if ($isVerified)
                                        <span class="badge-stafo badge-stafo-success">
                                            <i class="fa-solid fa-circle-check"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge-stafo badge-stafo-danger">
                                            <i class="fa-solid fa-shield-halved"></i> Pending
                                        </span>
                                    @endif
                                </td>

                                <!-- Active/Inactive Status -->
                                <td class="text-center">
                                    @if ($employeeRecord->status == 1)
                                        <span class="badge-stafo badge-stafo-success">Active</span>
                                    @else
                                        <span class="badge-stafo badge-stafo-danger">Inactive</span>
                                    @endif
                                </td>

                                <!-- Actions Column -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <!-- View Profile -->
                                        <button type="button" class="btn btn-sm btn-outline-info action-btn" title="View Details"
                                            data-bs-toggle="modal" data-bs-target="#empModal_{{ $employeeRecord->id }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>

                                        <!-- Edit Employee -->
                                        @if($canEditEmp)
                                            <button type="button" class="btn btn-sm btn-outline-warning action-btn" title="Edit Employee"
                                                data-bs-toggle="modal" data-bs-target="#editEmpModal_{{ $employeeRecord->id }}">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        @endif

                                        <!-- Delete / More dropdown -->
                                        @if($canDeleteEmp)
                                            <button type="button" class="btn btn-sm btn-outline-danger action-btn" title="Delete Employee"
                                                onclick="confirmDelete(event, {{ $employeeRecord->id }})">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                            <form id="delete-form-{{ $employeeRecord->id }}" action="{{ route('employee.management.employees.destroy', $employeeRecord->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-users-slash fs-2 mb-2 d-block opacity-40"></i>
                                    No employees found matching the specified filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination & Per Page Selector -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4 pt-3 border-top">
                <div class="d-flex align-items-center gap-3">
                    <small class="text-muted">
                        Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} entries
                    </small>
                    <div class="d-flex align-items-center gap-1">
                        <label for="perPageSelect" class="text-muted small mb-0 d-none d-sm-inline">Show:</label>
                        <select id="perPageSelect" class="form-select form-select-sm" style="width: auto;" onchange="location = this.value;">
                            @foreach([10, 12, 25, 50, 100] as $size)
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => $size]) }}" {{ request('per_page', 12) == $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if($employees->hasPages())
                    <div>{{ $employees->links('pagination::bootstrap-4') }}</div>
                @endif
            </div>

        </div>
    </div>

</div>

<!-- Modal: Add Employee (guarded by employees.create) -->
@if(Auth::guard('employee')->user()->hasPermission('employees.create'))
<div class="modal fade" id="addEmpModal" tabindex="-1" aria-labelledby="addEmpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-plus fs-5"></i>
                    <h5 class="modal-title fw-bold" id="addEmpModalLabel">Register New Employee</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employee.management.employees.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Sharma" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Official / Personal Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. rahul@company.com" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" maxlength="10" class="form-control" placeholder="10-digit mobile number" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Salary (₹)</label>
                            <input type="number" name="salary" class="form-control" placeholder="Monthly gross salary" value="0">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Branch Office</label>
                            <select name="branch_id" class="form-select">
                                <option value="">-- Select Branch --</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Designation (Role)</label>
                            <select name="designation_id" class="form-select">
                                <option value="">-- Select Designation --</option>
                                @foreach($designations as $des)
                                    <option value="{{ $des->id }}">{{ $des->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Work Shift</label>
                            <select name="shift_id" class="form-select">
                                <option value="">-- Select Shift --</option>
                                @foreach($shifts as $s)
                                    <option value="{{ $s->id }}">{{ $s->shift_name }} ({{ $s->start_time }} - {{ $s->end_time }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Attendance Type</label>
                            <select name="attendance_type" class="form-select">
                                <option value="geo" selected>Geofenced GPS Location</option>
                                <option value="selfie">Camera Selfie Verification</option>
                                <option value="qr code">QR Code Punching</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Register Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Import Employees (guarded by employees.create) -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-file-excel fs-5"></i>
                    <h5 class="modal-title fw-bold" id="importModalLabel">Import Employees</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employee.management.employees.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Select Excel File (.xlsx, .xls) <span class="text-danger">*</span></label>
                        <input type="file" name="attendance_file" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-semibold text-dark d-block small">Need a template?</span>
                                <small class="text-muted">Download sample format for bulk import</small>
                            </div>
                            <a href="{{ asset('sample_import_files/employees_import.xlsx') }}" class="btn btn-outline-primary btn-sm px-3" download>
                                <i class="fa-solid fa-download me-1"></i> Sample File
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-upload me-1"></i> Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modals: Edit Employee (guarded by employees.edit) -->
@if(Auth::guard('employee')->user()->hasPermission('employees.edit'))
    @foreach ($employees as $employeeRecord)
    <div class="modal fade" id="editEmpModal_{{ $employeeRecord->id }}" tabindex="-1" aria-labelledby="editEmpModalLabel_{{ $employeeRecord->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-warning text-dark py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-pen-to-square fs-5"></i>
                        <h5 class="modal-title fw-bold" id="editEmpModalLabel_{{ $employeeRecord->id }}">Edit Employee Details</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('employee.management.employees.update', $employeeRecord->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $employeeRecord->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ $employeeRecord->email }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" maxlength="10" class="form-control" value="{{ $employeeRecord->phone }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="1" {{ $employeeRecord->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $employeeRecord->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Branch</label>
                                <select name="branch_id" class="form-select">
                                    <option value="">-- Select Branch --</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ $employeeRecord->branch_id == $b->id ? 'selected' : '' }}>{{ $b->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Department</label>
                                <select name="department_id" class="form-select">
                                    <option value="">-- Select Department --</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->id }}" {{ $employeeRecord->department_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Designation</label>
                                <select name="designation_id" class="form-select">
                                    <option value="">-- Select Designation --</option>
                                    @foreach($designations as $des)
                                        <option value="{{ $des->id }}" {{ $employeeRecord->designation_id == $des->id ? 'selected' : '' }}>{{ $des->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Shift</label>
                                <select name="shift_id" class="form-select">
                                    <option value="">-- Select Shift --</option>
                                    @foreach($shifts as $s)
                                        <option value="{{ $s->id }}" {{ $employeeRecord->shift_id == $s->id ? 'selected' : '' }}>{{ $s->shift_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Salary (₹)</label>
                                <input type="number" name="salary" class="form-control" value="{{ $employeeRecord->salary ?? 0 }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Attendance Type</label>
                                <select name="attendance_type" class="form-select">
                                    <option value="geo" {{ $employeeRecord->attendance_type == 'geo' ? 'selected' : '' }}>Geofenced GPS Location</option>
                                    <option value="selfie" {{ $employeeRecord->attendance_type == 'selfie' ? 'selected' : '' }}>Camera Selfie Verification</option>
                                    <option value="qr code" {{ in_array($employeeRecord->attendance_type, ['qr code', 'qr']) ? 'selected' : '' }}>QR Code Punching</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

<!-- Modals: View Employee Details (for all with employees.view) -->
@foreach ($employees as $employeeRecord)
<div class="modal fade" id="empModal_{{ $employeeRecord->id }}" tabindex="-1" aria-labelledby="empModalLabel_{{ $employeeRecord->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold" style="width: 44px; height: 44px;">
                        {{ strtoupper(substr($employeeRecord->name ?? 'E', 0, 2)) }}
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="empModalLabel_{{ $employeeRecord->id }}">{{ $employeeRecord->name }}</h5>
                        <small class="text-light opacity-75">ID: {{ $employeeRecord->emp_id ?? ('EMP-' . str_pad($employeeRecord->id, 5, '0', STR_PAD_LEFT)) }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="fa-solid fa-id-card text-primary me-2"></i>Personal Information</h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                            <li><strong class="text-secondary">Email:</strong> <span class="text-dark">{{ $employeeRecord->email }}</span></li>
                            <li><strong class="text-secondary">Phone:</strong> <span class="text-dark">{{ $employeeRecord->phone ?? '-' }}</span></li>
                            <li><strong class="text-secondary">Date of Birth:</strong> <span class="text-dark">{{ $employeeRecord->date_of_birth ?? '-' }}</span></li>
                            <li><strong class="text-secondary">Gender:</strong> <span class="text-dark">{{ ucfirst($employeeRecord->gender ?? '-') }}</span></li>
                            <li><strong class="text-secondary">Address:</strong> <span class="text-dark">{{ $employeeRecord->address ?? '-' }}</span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="fa-solid fa-briefcase text-success me-2"></i>Employment Details</h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                            <li><strong class="text-secondary">Branch:</strong> <span class="text-dark">{{ $employeeRecord->branch->branch_name ?? 'Not Assigned' }}</span></li>
                            <li><strong class="text-secondary">Department:</strong> <span class="text-dark">{{ $employeeRecord->department->name ?? 'Not Assigned' }}</span></li>
                            <li><strong class="text-secondary">Designation:</strong> <span class="text-dark">{{ $employeeRecord->designation->name ?? ($employeeRecord->position ?? 'Not Assigned') }}</span></li>
                            <li><strong class="text-secondary">Shift:</strong> <span class="text-dark">{{ $employeeRecord->shift->shift_name ?? 'Default' }}</span></li>
                            <li><strong class="text-secondary">Date of Joining:</strong> <span class="text-dark">{{ $employeeRecord->date_of_joining ?? '-' }}</span></li>
                        </ul>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-12">
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="fa-solid fa-shield-check text-warning me-2"></i>KYC Verification Details</h6>
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <div class="p-2.5 bg-light rounded-3 border text-center">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Aadhaar</small>
                                    <strong class="{{ $employeeRecord->aadhar_verify == 'Yes' ? 'text-success' : 'text-danger' }}" style="font-size: 0.8rem;">
                                        {{ $employeeRecord->aadhar_verify == 'Yes' ? 'Verified' : 'Pending' }}
                                    </strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-2.5 bg-light rounded-3 border text-center">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">PAN</small>
                                    <strong class="{{ $employeeRecord->pan_verify == 'Yes' ? 'text-success' : 'text-danger' }}" style="font-size: 0.8rem;">
                                        {{ $employeeRecord->pan_verify == 'Yes' ? 'Verified' : 'Pending' }}
                                    </strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-2.5 bg-light rounded-3 border text-center">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Voter ID</small>
                                    <strong class="{{ $employeeRecord->voter_verify == 'Yes' ? 'text-success' : 'text-danger' }}" style="font-size: 0.8rem;">
                                        {{ $employeeRecord->voter_verify == 'Yes' ? 'Verified' : 'Pending' }}
                                    </strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-2.5 bg-light rounded-3 border text-center">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">Driving License</small>
                                    <strong class="{{ $employeeRecord->dl_verify == 'Yes' ? 'text-success' : 'text-danger' }}" style="font-size: 0.8rem;">
                                        {{ $employeeRecord->dl_verify == 'Yes' ? 'Verified' : 'Pending' }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@if(Auth::guard('employee')->user()->hasPermission('employees.delete'))
<script>
    function confirmDelete(event, employeeId) {
        event.preventDefault();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: "This employee record will be deleted permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${employeeId}`).submit();
                }
            });
        } else {
            if (confirm("Are you sure you want to delete this employee?")) {
                document.getElementById(`delete-form-${employeeId}`).submit();
            }
        }
    }
</script>
@endif

@endsection
