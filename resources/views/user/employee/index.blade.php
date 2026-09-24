@extends('user.layouts.app')
@section('title', 'Employee Directory & Attendance Calendar | STAFO HRMS')

@section('css')
    <link rel="stylesheet" href="{{ asset('main/css/employees.css') }}">
    <style>
        .stat-card {
            transition: all 0.2s ease;
            border-radius: 12px;
            background: #fff;
            border: 1px solid #e2e8f0;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .kyc-badge-btn {
            font-size: 0.775rem;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.15s ease;
        }
        .kyc-badge-btn:hover {
            transform: scale(1.03);
        }
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            transition: all 0.15s ease;
        }
        .action-btn:hover {
            transform: translateY(-1px);
        }
        .avatar-container {
            position: relative;
            flex-shrink: 0;
        }
        .kyc-indicator-dot {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #fff;
            text-decoration: none;
        }
        .pagination-container .pagination {
            margin-bottom: 0;
            gap: 4px;
        }
        .pagination-container .page-item .page-link {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 6px 12px;
            transition: all 0.15s ease-in-out;
        }
        .pagination-container .page-item.active .page-link {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: #fff;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.25);
        }
        .pagination-container .page-item .page-link:hover:not(.disabled) {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            color: #1e293b;
        }
        .pagination-container .page-item.disabled .page-link {
            background-color: #f8fafc;
            border-color: #e2e8f0;
            color: #94a3b8;
        }
        .geo-status-toggle {
            cursor: pointer;
            width: 2.2em;
            height: 1.15em;
        }
        .geo-status-toggle:checked {
            background-color: #10b981;
            border-color: #10b981;
        }

        /* View Toggle Tabs */
        .view-switcher-pill {
            background: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            gap: 4px;
        }
        .view-toggle-btn {
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 0.825rem;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .view-toggle-btn:hover {
            color: #1e293b;
        }
        .view-toggle-btn.active {
            background: #ffffff;
            color: #4f46e5;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.15);
        }

        /* Calendar Grid Styling */
        .calendar-week-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            border-radius: 12px 12px 0 0;
            overflow: hidden;
        }
        .calendar-week-header > div {
            padding: 10px 4px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }
        .calendar-week-header > div.weekend {
            color: #ef4444;
        }

        .calendar-days-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: #e2e8f0;
            gap: 1px;
            border: 1px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 12px 12px;
            overflow: hidden;
        }

        .calendar-day-cell {
            background: #ffffff;
            min-height: 100px;
            padding: 8px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            position: relative;
            cursor: pointer;
            transition: background-color 0.15s ease, box-shadow 0.15s ease;
        }
        .calendar-day-cell:hover {
            background-color: #f8fafc;
            z-index: 2;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
        }
        .calendar-day-cell.other-month {
            background: #f8fafc;
            opacity: 0.4;
            cursor: default;
        }

        /* Status Colors for Calendar Cells */
        .calendar-day-cell.status-present {
            background: #f0fdf4;
            border-top: 3px solid #10b981;
        }
        .calendar-day-cell.status-present:hover {
            background: #dcfce7;
        }
        .calendar-day-cell.status-halfday {
            background: #fefce8;
            border-top: 3px solid #eab308;
        }
        .calendar-day-cell.status-halfday:hover {
            background: #fef9c3;
        }
        .calendar-day-cell.status-leave {
            background: #f0f9ff;
            border-top: 3px solid #0284c7;
        }
        .calendar-day-cell.status-leave:hover {
            background: #e0f2fe;
        }
        .calendar-day-cell.status-holiday {
            background: #faf5ff;
            border-top: 3px solid #a855f7;
        }
        .calendar-day-cell.status-holiday:hover {
            background: #f3e8ff;
        }
        .calendar-day-cell.status-weekend {
            background: #fafbfc;
            border-top: 3px solid #94a3b8;
        }
        .calendar-day-cell.status-absent {
            background: #fef2f2;
            border-top: 3px solid #ef4444;
        }
        .calendar-day-cell.status-absent:hover {
            background: #fee2e2;
        }
        .calendar-day-cell.status-pending {
            background: #fffbeb;
            border-top: 3px solid #f59e0b;
        }

        .day-cell-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .day-cell-number {
            font-weight: 700;
            font-size: 0.82rem;
            color: #1e293b;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .calendar-day-cell.is-today .day-cell-number {
            background: #4f46e5;
            color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.25);
        }

        .cell-badge {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 2px 5px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 2px;
            width: fit-content;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            max-width: 100%;
        }

        .punch-time-chip {
            font-size: 0.68rem;
            font-weight: 500;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
        }
        .work-duration-pill {
            font-size: 0.65rem;
            font-weight: 700;
            background: #e0f2fe;
            color: #0369a1;
            padding: 1px 5px;
            border-radius: 4px;
            margin-top: 3px;
            display: inline-block;
            width: fit-content;
        }

        .cal-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .calendar-day-cell {
                min-height: 75px;
                padding: 4px;
            }
            .punch-time-chip {
                display: none;
            }
            .calendar-week-header > div {
                font-size: 0.65rem;
                padding: 6px 2px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Metric Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3 stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.725rem;">Total Staff</small>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalEmployees ?? $employees->count() }}</h3>
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
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ $activeEmployees ?? $employees->where('status', 1)->count() }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-user-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('employee.index', array_merge(request()->except('kyc_status'), ['kyc_status' => 'verified'])) }}" class="text-decoration-none">
                <div class="p-3 stat-card {{ request('kyc_status') == 'verified' ? 'border-success shadow-sm bg-success bg-opacity-10' : '' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.725rem;">KYC Verified</small>
                            <h3 class="fw-bold text-success mb-0 mt-1">{{ $verifiedEmployees ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="fa-solid fa-shield-check fs-5"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('employee.index', array_merge(request()->except('kyc_status'), ['kyc_status' => 'unverified'])) }}" class="text-decoration-none">
                <div class="p-3 stat-card {{ request('kyc_status') == 'unverified' ? 'border-danger shadow-sm bg-danger bg-opacity-10' : '' }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.725rem;">KYC Pending</small>
                            <h3 class="fw-bold text-danger mb-0 mt-1">{{ $unverifiedEmployees ?? 0 }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="fa-solid fa-shield-halved fs-5"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 emp-data">
        <div class="card-body p-4">
            
            <!-- Page Header & View Switcher Bar -->
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="fw-bold text-dark mb-1" id="mainHeaderTitle">Employees Directory</h3>
                    <p class="text-muted small mb-0" id="mainHeaderSubtitle">Manage employee records, shifts, branches, departments, and KYC document verification</p>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- View Switcher (Directory Table vs Attendance Calendar) -->
                    <div class="view-switcher-pill me-lg-2">
                        <button type="button" class="view-toggle-btn {{ request('view') === 'calendar' ? '' : 'active' }}" id="btnViewTable" onclick="switchDirectoryView('table')">
                            <i class="fa-solid fa-table-list"></i> Directory List
                        </button>
                        <button type="button" class="view-toggle-btn {{ request('view') === 'calendar' ? 'active' : '' }}" id="btnViewCalendar" onclick="switchDirectoryView('calendar')">
                            <i class="fa-solid fa-calendar-days text-primary"></i> Attendance Calendar
                        </button>
                    </div>

                    <a href="{{ route('employee.create') }}" class="btn btn-primary px-3 py-2 fw-semibold">
                        <i class="fa-solid fa-user-plus me-1"></i> Add Employee
                    </a>
                    <button type="button" class="btn btn-outline-primary px-3 py-2" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fa-solid fa-file-import me-1"></i> Import
                    </button>
                    <a href="{{ route('employees.export', request()->all()) }}" class="btn btn-outline-secondary px-3 py-2">
                        <i class="fa-solid fa-file-export me-1"></i> Export
                    </a>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- SECTION 1: DIRECTORY TABLE VIEW                             -->
            <!-- ============================================================ -->
            <div id="directoryTableSection" class="{{ request('view') === 'calendar' ? 'd-none' : '' }}">
                <!-- Enhanced Filter Form with Instant Search -->
                <form action="{{ route('employee.index') }}" method="GET" class="mb-4" id="employeeFilterForm">
                    <div class="row g-2 align-items-center">
                        <!-- Instant Name, Email & Mobile Search -->
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="position-relative">
                                <input type="text" name="name" id="instantSearchInput" class="form-control ps-4 pe-4" 
                                    placeholder="Search by name, email, or mobile..." value="{{ request()->input('name') }}" autocomplete="off">
                                
                            </div>
                        </div>

                        <!-- Branch Filter -->
                        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                            <select name="branch_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Branches</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Department Filter -->
                        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                            <select name="department_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Departments</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- KYC Status Filter -->
                        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                            <select name="kyc_status" class="form-select" onchange="this.form.submit()">
                                <option value="">All KYC Status</option>
                                <option value="verified" {{ request('kyc_status') == 'verified' ? 'selected' : '' }}>KYC Verified</option>
                                <option value="unverified" {{ request('kyc_status') == 'unverified' ? 'selected' : '' }}>KYC Not Verified</option>
                            </select>
                        </div>

                        <!-- Filter & Reset Buttons -->
                        <div class="col-12 col-sm-6 col-md-3 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fa-solid fa-filter me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['name', 'email', 'phone', 'branch_id', 'department_id', 'kyc_status', 'per_page']))
                                <a href="{{ route('employee.index') }}" class="btn btn-light border text-muted px-3" title="Clear Filters">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Employee Directory Table Container -->
                <div id="employeeTableWrapper">
                    <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="employeeDirectoryTable">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="min-width: 220px;">Employee</th>
                                <th>Contact</th>
                                <th>Department</th>
                                <th>Branch</th>
                                <th>Shift</th>
                                <th class="text-center" style="min-width: 110px;">KYC Status</th>
                                <th class="text-center" style="min-width: 130px;">Geo Tracking</th>
                                <th class="text-center" style="width: 90px;">Status</th>
                                <th style="width: 140px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($employees as $index => $employee)
                                @php
                                    $isAadhar = ($employee->aadhar_verify == 'Yes');
                                    $isPan = ($employee->pan_verify == 'Yes');
                                    $isVoter = ($employee->voter_verify == 'Yes');
                                    $isDl = ($employee->dl_verify == 'Yes');
                                    $isVerified = ($isAadhar || $isPan || $isVoter || $isDl);
                                @endphp
                                <tr class="employee-row" data-search="{{ strtolower($employee->name . ' ' . $employee->email . ' ' . $employee->phone . ' ' . ($employee->emp_id ?? '') . ' ' . ($employee->branch->branch_name ?? '') . ' ' . ($employee->department->name ?? '')) }}">
                                    <td class="text-center text-muted fw-semibold">{{ $employees->firstItem() + $index }}</td>
                                    
                                    <!-- Employee Name & Avatar with KYC indicator -->
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-container">
                                                @if ($employee->image && file_exists(public_path('uploads/employees/' . $employee->image)))
                                                    <img src="{{ asset('uploads/employees/' . $employee->image) }}" alt="{{ $employee->name }}" 
                                                         class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; border: 1px solid #e2e8f0;">
                                                @else
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" 
                                                         style="width: 40px; height: 40px; font-size: 0.85rem; flex-shrink: 0;">
                                                        {{ strtoupper(substr($employee->name ?? 'E', 0, 2)) }}
                                                    </div>
                                                @endif

                                                <!-- KYC Indicator Dot on Avatar -->
                                                @if ($isVerified)
                                                    <a href="{{ route('documentVerification', $employee->id) }}" 
                                                       class="kyc-indicator-dot bg-success" 
                                                       title="KYC Verified - Click to View KYC">
                                                        <i class="fa-solid fa-check"></i>
                                                    </a>
                                                @else
                                                    <a href="{{ route('documentVerification', $employee->id) }}" 
                                                       class="kyc-indicator-dot bg-danger" 
                                                       title="KYC Not Verified - Click to Verify Documents">
                                                        <i class="fa-solid fa-exclamation"></i>
                                                    </a>
                                                @endif
                                            </div>

                                            <div>
                                                <a href="{{ route('user.employees.show', ['id' => $employee->id, 'company_id' => $employee->company_id]) }}" 
                                                   class="text-dark fw-bold text-decoration-none d-block lh-sm mb-1 hover-primary">
                                                    {{ $employee->name }}
                                                </a>
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                    @if($employee->emp_id)
                                                        <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.68rem; font-weight: 600;">
                                                            ID: {{ $employee->emp_id }}
                                                        </span>
                                                    @endif
                                                    @if($employee->companyRole)
                                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                            <i class="fa-solid fa-user-shield me-1"></i>{{ $employee->companyRole->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Contact Info -->
                                    <td>
                                        @if($employee->phone)
                                            <a href="tel:{{ $employee->phone }}" class="text-dark text-decoration-none small d-block mb-1 fw-medium">
                                                <i class="fa-solid fa-phone text-muted me-1" style="font-size: 0.75rem;"></i>{{ $employee->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted small d-block mb-1">-</span>
                                        @endif
                                        @if($employee->email)
                                            <a href="mailto:{{ $employee->email }}" class="text-muted text-decoration-none small d-block text-truncate" style="max-width: 170px;" title="{{ $employee->email }}">
                                                <i class="fa-solid fa-envelope text-muted me-1" style="font-size: 0.75rem;"></i>{{ $employee->email }}
                                            </a>
                                        @endif
                                    </td>

                                    <!-- Department -->
                                    <td>
                                        @if ($employee->department)
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.775rem;">
                                                <i class="fa-solid fa-sitemap text-primary me-1"></i> {{ $employee->department->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small"><em>Not Assigned</em></span>
                                        @endif
                                    </td>

                                    <!-- Branch -->
                                    <td>
                                        @if ($employee->branch)
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.775rem;">
                                                <i class="fa-solid fa-building text-success me-1"></i> {{ $employee->branch->branch_name }}
                                            </span>
                                        @else
                                            <span class="text-muted small"><em>Not Assigned</em></span>
                                        @endif
                                    </td>

                                    <!-- Shift Timings -->
                                    <td>
                                        @if ($employee->shifts && $employee->shifts->count() > 0)
                                            @foreach ($employee->shifts as $shift)
                                                <span class="badge bg-light text-dark border px-2 py-1 d-inline-block mb-1" style="font-size: 0.75rem;">
                                                    <i class="fa-solid fa-clock text-info me-1"></i> {{ $shift->shift_name }}
                                                </span>
                                            @endforeach
                                        @elseif ($employee->shift)
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-clock text-info me-1"></i> {{ $employee->shift->shift_name }}
                                            </span>
                                        @else
                                            <span class="text-muted small"><em>Not Assigned</em></span>
                                        @endif
                                    </td>

                                    <!-- KYC Status Column -->
                                    <td class="text-center">
                                        @if ($isVerified)
                                            <a href="{{ route('documentVerification', $employee->id) }}" 
                                               class="btn btn-sm btn-outline-success py-1 px-2 d-inline-flex align-items-center gap-1 text-decoration-none kyc-badge-btn shadow-xs" 
                                               title="KYC Verified - Click to View KYC Details">
                                                <i class="fa-solid fa-circle-check text-success"></i>
                                                <span>Verified</span>
                                            </a>
                                        @else
                                            <a href="{{ route('documentVerification', $employee->id) }}" 
                                               class="btn btn-sm btn-outline-danger py-1 px-2 d-inline-flex align-items-center gap-1 text-decoration-none kyc-badge-btn shadow-xs" 
                                               title="KYC Not Verified - Click to Verify Documents Now">
                                                <i class="fa-solid fa-shield-halved text-danger"></i>
                                                <span>Pending</span>
                                            </a>
                                        @endif
                                    </td>

                                    <!-- Geo Tracking Status & Request Toggle -->
                                    <td class="text-center" id="geo-cell-{{ $employee->id }}">
                                        <div class="d-inline-flex flex-column align-items-center">
                                            <div class="form-check form-switch d-inline-block mb-1">
                                                <input class="form-check-input geo-status-toggle" 
                                                       type="checkbox" 
                                                       role="switch" 
                                                       id="geoSwitch_{{ $employee->id }}" 
                                                       data-employee-id="{{ $employee->id }}"
                                                       data-employee-name="{{ $employee->name }}"
                                                       {{ in_array((string)$employee->geo_status, ['1', '2']) ? 'checked' : '' }}
                                                       title="{{ (string)$employee->geo_status === '2' ? 'Tracking is Active (Accepted). Click to turn OFF' : ((string)$employee->geo_status === '1' ? 'Tracking Requested (Pending Acceptance). Click to turn OFF' : 'Tracking is Off. Click to request tracking') }}"
                                                       onchange="toggleGeoStatus({{ $employee->id }}, this.checked, '{{ addslashes($employee->name) }}')">
                                            </div>
                                            @if((string)$employee->geo_status === '2')
                                                <span id="geoBadge_{{ $employee->id }}" class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" style="font-size: 0.68rem; transition: all 0.2s;" title="Employee accepted. Real-time tracking active.">
                                                    <i class="fa-solid fa-location-dot me-1" id="geoIcon_{{ $employee->id }}"></i>
                                                    <span id="geoText_{{ $employee->id }}">Tracking ON</span>
                                                </span>
                                            @elseif((string)$employee->geo_status === '1')
                                                <span id="geoBadge_{{ $employee->id }}" class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5" style="font-size: 0.68rem; transition: all 0.2s;" title="Request sent to employee. Awaiting acceptance.">
                                                    <i class="fa-solid fa-clock me-1" id="geoIcon_{{ $employee->id }}"></i>
                                                    <span id="geoText_{{ $employee->id }}">Request Sent</span>
                                                </span>
                                            @else
                                                <span id="geoBadge_{{ $employee->id }}" class="badge bg-light text-muted border px-2 py-0.5" style="font-size: 0.68rem; transition: all 0.2s;" title="Tracking is disabled.">
                                                    <i class="fa-solid fa-location-crosshairs me-1" id="geoIcon_{{ $employee->id }}"></i>
                                                    <span id="geoText_{{ $employee->id }}">Tracking OFF</span>
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Employee Account Status -->
                                    <td class="text-center">
                                        @if(($employee->status ?? 1) == 1)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1" style="font-size: 0.725rem;">
                                                <i class="fa-solid fa-circle me-1" style="font-size: 6px;"></i> Active
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.725rem;">
                                                <i class="fa-solid fa-circle me-1" style="font-size: 6px;"></i> Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <!-- Dedicated Attendance Calendar Button -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary action-btn" 
                                                    onclick="openCalendarForEmployee({{ $employee->id }}, '{{ addslashes($employee->name) }}')" 
                                                    title="View Current Month Attendance Calendar">
                                                <i class="fa-solid fa-calendar-days"></i>
                                            </button>

                                            <!-- View Profile -->
                                            <a href="{{ route('user.employees.show', ['id' => $employee->id, 'company_id' => $employee->company_id]) }}"
                                                class="btn btn-sm btn-outline-info action-btn" title="View Full Profile">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            <!-- Edit Profile -->
                                            <a href="{{ route('user.employees.edit', $employee->id) }}"
                                                class="btn btn-sm btn-outline-warning action-btn" title="Edit Employee">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>

                                            <!-- Dropdown Menu for More Operations -->
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary action-btn" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <a class="dropdown-item py-2 text-primary fw-semibold" href="javascript:void(0)" onclick="openCalendarForEmployee({{ $employee->id }}, '{{ addslashes($employee->name) }}')">
                                                            <i class="fa-solid fa-calendar-days me-2"></i> Attendance Calendar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2" href="{{ route('employee.location', $employee->id) }}">
                                                            <i class="fa-solid fa-map-location-dot text-info me-2"></i> View Location Map
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2" href="{{ $employee->bankAccount ? route('editBankAccount', $employee->bankAccount->id) : route('addBankAccount', $employee->id) }}">
                                                            <i class="fa-solid fa-building-columns text-primary me-2"></i> {{ $employee->bankAccount ? 'Edit Bank Account' : 'Add Bank Account' }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2" href="javascript:void(0);" id="geoDropdownAction_{{ $employee->id }}" 
                                                           onclick="triggerGeoToggleFromDropdown({{ $employee->id }}, '{{ addslashes($employee->name) }}')">
                                                            <i class="fa-solid fa-location-crosshairs {{ in_array((string)$employee->geo_status, ['1', '2']) ? 'text-danger' : 'text-success' }} me-2" id="geoDropdownIcon_{{ $employee->id }}"></i> 
                                                            <span id="geoDropdownText_{{ $employee->id }}">{{ in_array((string)$employee->geo_status, ['1', '2']) ? 'Stop Tracking' : 'Request Tracking' }}</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2" href="{{ $employee->document ? route('employee.documents.edit', ['employeeId' => $employee->id, 'documentId' => $employee->document->id]) : route('employee.documents.create', $employee->id) }}">
                                                            <i class="fa-solid fa-file-arrow-up text-info me-2"></i> Documents
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2" href="{{ route('documentVerification', $employee->id) }}">
                                                            <i class="fa-solid fa-certificate text-warning me-2"></i> KYC Verification
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-2" href="{{ route('employeePerformance', $employee->id) }}">
                                                            <i class="fa-solid fa-trophy text-warning me-2"></i> Performance
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider my-1"></li>
                                                    <li>
                                                        <form action="{{ route('user.employees.delete', $employee->id) }}"
                                                            method="POST" id="delete-form-{{ $employee->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="dropdown-item py-2 text-danger"
                                                                onclick="confirmDelete(event, {{ $employee->id }})">
                                                                <i class="fa-solid fa-trash-can me-2"></i> Delete Employee
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noResultsRow">
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-users-slash fs-2 mb-2 d-block opacity-50"></i>
                                        No employees found matching your criteria.
                                        <div class="mt-2">
                                            <a href="{{ route('employee.create') }}" class="btn btn-primary btn-sm px-3">
                                                <i class="fa-solid fa-user-plus me-1"></i> Add New Employee
                                            </a>
                                            @if(request()->hasAny(['name', 'email', 'phone', 'branch_id', 'department_id', 'kyc_status']))
                                                <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary btn-sm px-3 ms-1">
                                                    <i class="fa-solid fa-rotate-left me-1"></i> Clear Filters
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination & Per Page Selector -->
                @if ($employees->total() > 0)
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Show</span>
                            <select class="form-select form-select-sm" style="width: 75px;" onchange="changePerPage(this.value)">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-muted small">entries per page &bull; Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }} employees</span>
                        </div>

                        <div class="pagination-container">
                            {{ $employees->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
                </div> <!-- /#employeeTableWrapper -->
            </div>

            <!-- ============================================================ -->
            <!-- SECTION 2: ATTENDANCE CALENDAR VIEW                          -->
            <!-- ============================================================ -->
            <div id="attendanceCalendarSection" class="{{ request('view') === 'calendar' ? '' : 'd-none' }}">
                
                <!-- Calendar Controls Header Bar -->
                <div class="p-3 bg-light rounded-4 border mb-4">
                    <div class="row g-3 align-items-center">
                        <!-- Employee Selector -->
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label small fw-semibold text-muted mb-1">
                                <i class="fa-solid fa-user me-1 text-primary"></i>Select Employee:
                            </label>
                            <select id="calEmployeeSelect" class="form-select shadow-none" onchange="onCalendarEmployeeChange(this.value)">
                                @if(isset($allEmployeesList) && $allEmployeesList->isNotEmpty())
                                    @foreach($allEmployeesList as $emp)
                                        <option value="{{ $emp->id }}" {{ (isset($calendarEmployeeId) && $calendarEmployeeId == $emp->id) ? 'selected' : '' }}>
                                            {{ $emp->name }} ({{ $emp->emp_id ?? 'ID: ' . $emp->id }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Month & Year Navigation Controls -->
                        <div class="col-lg-5 col-md-6 text-center">
                            <label class="form-label small fw-semibold text-muted mb-1 d-none d-lg-block">Attendance Month:</label>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 py-1.5 rounded-pill shadow-xs" onclick="navigateCalendarMonth(-1)" title="Previous Month">
                                    <i class="fa-solid fa-chevron-left me-1"></i> Prev
                                </button>
                                
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-white border px-3 py-1.5 rounded-pill fw-bold shadow-xs dropdown-toggle" type="button" id="monthDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-regular fa-calendar text-primary me-1.5"></i><span id="calDisplayMonthYear">{{ date('F Y') }}</span>
                                    </button>
                                    <div class="dropdown-menu p-3 shadow-lg border-0 rounded-4" style="min-width: 250px;">
                                        <div class="row g-2 mb-2">
                                            <div class="col-7">
                                                <label class="small text-muted fw-bold">Month</label>
                                                <select id="calMonthDropdown" class="form-select form-select-sm" onchange="applyCustomMonthYear()">
                                                    @for($m = 1; $m <= 12; $m++)
                                                        @php $mStr = sprintf('%02d', $m); @endphp
                                                        <option value="{{ $mStr }}" {{ date('m') == $mStr ? 'selected' : '' }}>
                                                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-5">
                                                <label class="small text-muted fw-bold">Year</label>
                                                <select id="calYearDropdown" class="form-select form-select-sm" onchange="applyCustomMonthYear()">
                                                    @for($y = date('Y') + 1; $y >= date('Y') - 3; $y--)
                                                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 py-1.5 rounded-pill shadow-xs" onclick="navigateCalendarMonth(1)" title="Next Month">
                                    Next <i class="fa-solid fa-chevron-right ms-1"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-primary px-2.5 py-1.5 rounded-pill shadow-xs" onclick="resetCalendarToCurrentMonth()" title="Current Month">
                                    Current Month
                                </button>
                            </div>
                        </div>

                        <!-- Action Shortcuts -->
                        <div class="col-lg-3 col-md-12 d-flex justify-content-lg-end justify-content-center gap-2">
                            <a href="#" id="calViewLocationMapBtn" class="btn btn-sm btn-outline-success px-3 py-1.5 rounded-pill fw-semibold shadow-xs" target="_blank" title="View Today Location Route">
                                <i class="fa-solid fa-map-location-dot me-1"></i> Track Location
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1.5 rounded-pill shadow-xs" onclick="window.print()" title="Print Calendar">
                                <i class="fa-solid fa-print"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Selected Employee Mini Profile Banner -->
                <div class="card p-3 shadow-xs border rounded-4 mb-4 bg-white" id="calEmployeeBanner">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div id="calEmpAvatar" class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.1rem; border: 2px solid #e2e8f0;">
                                --
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" id="calEmpName">
                                    Loading Employee...
                                </h5>
                                <div class="d-flex align-items-center gap-2 mt-1 flex-wrap small text-muted">
                                    <span><i class="fa-solid fa-id-badge text-primary me-1"></i><span id="calEmpId">--</span></span>
                                    <span>&bull;</span>
                                    <span><i class="fa-solid fa-building text-success me-1"></i><span id="calEmpBranch">--</span></span>
                                    <span>&bull;</span>
                                    <span><i class="fa-solid fa-sitemap text-info me-1"></i><span id="calEmpDept">--</span></span>
                                    <span>&bull;</span>
                                    <span><i class="fa-solid fa-clock text-warning me-1"></i><span id="calEmpShift">--</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-muted border px-2.5 py-1.5 rounded-pill small" id="calTrackingStatusBadge">
                                <i class="fa-solid fa-location-dot me-1"></i>Checking...
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Monthly Attendance Telemetry Summary Cards -->
                <div class="row g-2 mb-4" id="calSummaryCardsRow">
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="p-2.5 stat-card text-center h-100">
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem;">Working Days</span>
                            <h4 class="fw-bold text-dark mb-0 mt-1" id="sumWorkingDays">--</h4>
                            <small class="text-muted fs-xs">Total Scheduled</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="p-2.5 stat-card text-center h-100 border-success border-opacity-25 bg-success bg-opacity-10">
                            <span class="text-success small fw-semibold text-uppercase" style="font-size: 0.68rem;">Present Days</span>
                            <h4 class="fw-bold text-success mb-0 mt-1" id="sumPresentDays">--</h4>
                            <small class="text-success fs-xs">Full Check-ins</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="p-2.5 stat-card text-center h-100 border-warning border-opacity-25 bg-warning bg-opacity-10">
                            <span class="text-warning-emphasis small fw-semibold text-uppercase" style="font-size: 0.68rem;">Half Days</span>
                            <h4 class="fw-bold text-warning-emphasis mb-0 mt-1" id="sumHalfDays">--</h4>
                            <small class="text-muted fs-xs">Partial Shift</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="p-2.5 stat-card text-center h-100 border-info border-opacity-25 bg-info bg-opacity-10">
                            <span class="text-primary small fw-semibold text-uppercase" style="font-size: 0.68rem;">Approved Leaves</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1" id="sumLeaveDays">--</h4>
                            <small class="text-muted fs-xs">Paid / Sanctioned</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="p-2.5 stat-card text-center h-100 border-purple border-opacity-25" style="background:#faf5ff;">
                            <span class="small fw-semibold text-uppercase" style="font-size: 0.68rem; color:#7e22ce;">Holidays &amp; Off</span>
                            <h4 class="fw-bold mb-0 mt-1" style="color:#7e22ce;" id="sumHolidayDays">--</h4>
                            <small class="text-muted fs-xs">Festival &amp; Sundays</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="p-2.5 stat-card text-center h-100 border-danger border-opacity-25 bg-danger bg-opacity-10">
                            <span class="text-danger small fw-semibold text-uppercase" style="font-size: 0.68rem;">Absent Days</span>
                            <h4 class="fw-bold text-danger mb-0 mt-1" id="sumAbsentDays">--</h4>
                            <small class="text-danger fs-xs">Unannounced</small>
                        </div>
                    </div>
                </div>

                <!-- Attendance Status Legend Bar -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-2 px-3 bg-white border rounded-3 mb-3 small">
                    <div class="fw-semibold text-dark"><i class="fa-solid fa-circle-info text-primary me-1.5"></i>Status Legend:</div>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <span class="d-inline-flex align-items-center gap-1.5"><span class="cal-legend-dot bg-success"></span> Present</span>
                        <span class="d-inline-flex align-items-center gap-1.5"><span class="cal-legend-dot bg-warning"></span> Half Day</span>
                        <span class="d-inline-flex align-items-center gap-1.5"><span class="cal-legend-dot bg-info"></span> Leave</span>
                        <span class="d-inline-flex align-items-center gap-1.5"><span class="cal-legend-dot" style="background:#a855f7;"></span> Holiday</span>
                        <span class="d-inline-flex align-items-center gap-1.5"><span class="cal-legend-dot bg-secondary"></span> Weekly Off</span>
                        <span class="d-inline-flex align-items-center gap-1.5"><span class="cal-legend-dot bg-danger"></span> Absent</span>
                    </div>
                </div>

                <!-- Full Calendar Container -->
                <div class="position-relative">
                    <!-- Loading Spinner Overlay -->
                    <div id="calendarLoadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex align-items-center justify-content-center d-none" style="z-index: 10; border-radius: 12px;">
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary mb-2" role="status"></div>
                            <div class="fw-semibold text-dark">Loading attendance records...</div>
                        </div>
                    </div>

                    <!-- 7-Day Header -->
                    <div class="calendar-week-header">
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                        <div class="weekend">Sun</div>
                    </div>

                    <!-- 7-Column Days Grid -->
                    <div class="calendar-days-grid" id="calendarDaysGridContainer">
                        <!-- Populated dynamically via JS -->
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal: Day Attendance Details Popup -->
    <div class="modal fade" id="dayAttendanceModal" tabindex="-1" aria-labelledby="dayAttendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark" id="dayAttendanceModalLabel">
                        <i class="fa-solid fa-calendar-day text-primary me-2"></i>Day Attendance Breakdown
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border mb-3">
                        <div>
                            <span class="text-muted small fw-semibold">DATE</span>
                            <h5 class="fw-bold text-dark mb-0" id="modalDayFullDate">--</h5>
                        </div>
                        <div>
                            <span class="badge fs-6 px-3 py-1.5 rounded-pill" id="modalDayStatusBadge">--</span>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 border bg-light text-center">
                                <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.7rem;">Punch In Time</span>
                                <h6 class="fw-bold text-success mb-0 mt-1" id="modalDayInTime">--:--</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2.5 rounded-3 border bg-light text-center">
                                <span class="text-muted small fw-semibold text-uppercase d-block" style="font-size: 0.7rem;">Punch Out Time</span>
                                <h6 class="fw-bold text-danger mb-0 mt-1" id="modalDayOutTime">--:--</h6>
                            </div>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-3 border bg-light d-flex align-items-center justify-content-between mb-3" id="modalDurationRow">
                        <span class="text-muted small fw-semibold"><i class="fa-solid fa-stopwatch me-1 text-primary"></i>Total Work Duration:</span>
                        <strong class="text-dark" id="modalDayWorkDuration">--</strong>
                    </div>

                    <!-- Selfie Punches Preview (if captured) -->
                    <div id="modalSelfieRow" class="mb-3 d-none">
                        <label class="small text-muted fw-bold mb-1"><i class="fa-solid fa-camera me-1 text-primary"></i>Punch Selfies:</label>
                        <div class="row g-2">
                            <div class="col-6 text-center" id="modalPunchinImgCol">
                                <small class="text-muted d-block mb-1">Check-in Photo</small>
                                <img id="modalPunchinImg" src="" class="img-fluid rounded-3 border shadow-xs" style="max-height: 120px; object-fit: cover;">
                            </div>
                            <div class="col-6 text-center" id="modalPunchoutImgCol">
                                <small class="text-muted d-block mb-1">Check-out Photo</small>
                                <img id="modalPunchoutImg" src="" class="img-fluid rounded-3 border shadow-xs" style="max-height: 120px; object-fit: cover;">
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="#" id="modalDayLocationLink" class="btn btn-success w-100 py-2 fw-semibold rounded-pill shadow-xs" target="_blank">
                            <i class="fa-solid fa-map-location-dot me-1.5"></i> View GPS Route on this Date
                        </a>
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark" id="importModalLabel">
                        <i class="fa-solid fa-file-import text-primary me-2"></i> Import Employees
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.employees.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark">Upload Excel / CSV File</label>
                            <input type="file" name="file" class="form-control shadow-none" required accept=".xlsx,.xls,.csv">
                            <small class="text-muted">Ensure your spreadsheet follows the standard format.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Upload &amp; Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global Calendar State
        let currentCalendarEmployeeId = {{ $calendarEmployeeId ?? ($allEmployeesList->first()->id ?? 0) }};
        let currentCalendarMonth = "{{ $currentMonth ?? date('m') }}";
        let currentCalendarYear = {{ $currentYear ?? date('Y') }};
        let currentMonthDaysCache = {};

        // Confirm Employee Deletion
        function confirmDelete(event, employeeId) {
            event.preventDefault();
            Swal.fire({
                title: 'Delete Employee & All Associated Data?',
                text: "This will permanently delete this employee and ALL related records (attendance, leaves, payroll, documents, tasks, expenses, etc.)!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete everything!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${employeeId}`).submit();
                }
            });
        }

        // Switch View (Directory Table vs Attendance Calendar)
        function switchDirectoryView(viewName) {
            const tableSection = document.getElementById('directoryTableSection');
            const calendarSection = document.getElementById('attendanceCalendarSection');
            const btnTable = document.getElementById('btnViewTable');
            const btnCalendar = document.getElementById('btnViewCalendar');
            const titleEl = document.getElementById('mainHeaderTitle');
            const subtitleEl = document.getElementById('mainHeaderSubtitle');

            if (viewName === 'calendar') {
                if (tableSection) tableSection.classList.add('d-none');
                if (calendarSection) calendarSection.classList.remove('d-none');
                if (btnTable) btnTable.classList.remove('active');
                if (btnCalendar) btnCalendar.classList.add('active');
                if (titleEl) titleEl.innerText = 'Employee Attendance Calendar';
                if (subtitleEl) subtitleEl.innerText = 'View monthly punch times, work durations, leaves, holidays, and live routes';

                // Update URL without reloading
                const url = new URL(window.location.href);
                url.searchParams.set('view', 'calendar');
                window.history.replaceState({}, '', url);

                // Load Calendar
                loadEmployeeCalendar(currentCalendarEmployeeId, currentCalendarMonth, currentCalendarYear);
            } else {
                if (tableSection) tableSection.classList.remove('d-none');
                if (calendarSection) calendarSection.classList.add('d-none');
                if (btnTable) btnTable.classList.add('active');
                if (btnCalendar) btnCalendar.classList.remove('active');
                if (titleEl) titleEl.innerText = 'Employees Directory';
                if (subtitleEl) subtitleEl.innerText = 'Manage employee records, shifts, branches, departments, and KYC document verification';

                // Update URL without reloading
                const url = new URL(window.location.href);
                url.searchParams.delete('view');
                window.history.replaceState({}, '', url);
            }
        }

        // Open calendar directly from table row
        function openCalendarForEmployee(employeeId, employeeName) {
            currentCalendarEmployeeId = employeeId;
            const selectEl = document.getElementById('calEmployeeSelect');
            if (selectEl) {
                selectEl.value = employeeId;
            }
            switchDirectoryView('calendar');
        }

        // When employee selection changes in calendar view
        function onCalendarEmployeeChange(empId) {
            currentCalendarEmployeeId = parseInt(empId);
            loadEmployeeCalendar(currentCalendarEmployeeId, currentCalendarMonth, currentCalendarYear);
        }

        // Navigate Month (-1 for previous, +1 for next)
        function navigateCalendarMonth(direction) {
            let m = parseInt(currentCalendarMonth) + direction;
            let y = currentCalendarYear;

            if (m < 1) {
                m = 12;
                y -= 1;
            } else if (m > 12) {
                m = 1;
                y += 1;
            }

            currentCalendarMonth = String(m).padStart(2, '0');
            currentCalendarYear = y;

            const mSelect = document.getElementById('calMonthDropdown');
            const ySelect = document.getElementById('calYearDropdown');
            if (mSelect) mSelect.value = currentCalendarMonth;
            if (ySelect) ySelect.value = currentCalendarYear;

            loadEmployeeCalendar(currentCalendarEmployeeId, currentCalendarMonth, currentCalendarYear);
        }

        // Reset to Current Month
        function resetCalendarToCurrentMonth() {
            const now = new Date();
            currentCalendarMonth = String(now.getMonth() + 1).padStart(2, '0');
            currentCalendarYear = now.getFullYear();

            const mSelect = document.getElementById('calMonthDropdown');
            const ySelect = document.getElementById('calYearDropdown');
            if (mSelect) mSelect.value = currentCalendarMonth;
            if (ySelect) ySelect.value = currentCalendarYear;

            loadEmployeeCalendar(currentCalendarEmployeeId, currentCalendarMonth, currentCalendarYear);
        }

        // Apply Custom Month/Year from Dropdown
        function applyCustomMonthYear() {
            const mSelect = document.getElementById('calMonthDropdown');
            const ySelect = document.getElementById('calYearDropdown');
            if (mSelect && ySelect) {
                currentCalendarMonth = mSelect.value;
                currentCalendarYear = parseInt(ySelect.value);
                loadEmployeeCalendar(currentCalendarEmployeeId, currentCalendarMonth, currentCalendarYear);
            }
        }

        // Fetch & Render Employee Attendance Calendar via AJAX
        function loadEmployeeCalendar(employeeId, month, year) {
            if (!employeeId) return;

            const overlay = document.getElementById('calendarLoadingOverlay');
            if (overlay) overlay.classList.remove('d-none');

            const url = `{{ url('company/employee/monthly-attendance') }}/${employeeId}?month=${month}&year=${year}`;

            fetch(url, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (overlay) overlay.classList.add('d-none');
                if (data.status) {
                    renderCalendarView(data);
                } else {
                    Swal.fire('Error', data.message || 'Failed to load attendance calendar.', 'error');
                }
            })
            .catch(err => {
                if (overlay) overlay.classList.add('d-none');
                console.error(err);
            });
        }

        // Render Calendar DOM from Data
        function renderCalendarView(data) {
            // 1. Update Month Display
            const displayTitle = `${data.month_name} ${data.year}`;
            const titleEl = document.getElementById('calDisplayMonthYear');
            if (titleEl) titleEl.innerText = displayTitle;

            // 2. Update Employee Banner
            if (data.employee) {
                const nameEl = document.getElementById('calEmpName');
                const idEl = document.getElementById('calEmpId');
                const branchEl = document.getElementById('calEmpBranch');
                const deptEl = document.getElementById('calEmpDept');
                const shiftEl = document.getElementById('calEmpShift');
                const avatarEl = document.getElementById('calEmpAvatar');
                const trackBadge = document.getElementById('calTrackingStatusBadge');
                const locBtn = document.getElementById('calViewLocationMapBtn');

                if (nameEl) nameEl.innerText = data.employee.name;
                if (idEl) idEl.innerText = data.employee.emp_id ? `ID: ${data.employee.emp_id}` : `ID: ${data.employee.id}`;
                if (branchEl) branchEl.innerText = data.employee.branch;
                if (deptEl) deptEl.innerText = data.employee.department;
                if (shiftEl) shiftEl.innerText = data.employee.shift;

                if (avatarEl) {
                    if (data.employee.image) {
                        avatarEl.innerHTML = `<img src="${data.employee.image}" class="rounded-circle w-100 h-100" style="object-fit: cover;">`;
                    } else {
                        avatarEl.innerText = data.employee.initials || 'EM';
                    }
                }

                if (trackBadge) {
                    const geoStatus = String(data.employee.geo_status || '0');
                    if (geoStatus === '2') {
                        trackBadge.className = 'badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill small';
                        trackBadge.innerHTML = '<i class="fa-solid fa-circle-dot text-success me-1"></i>Live Tracking ON';
                    } else if (geoStatus === '1') {
                        trackBadge.className = 'badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5 rounded-pill small';
                        trackBadge.innerHTML = '<i class="fa-solid fa-clock text-warning me-1"></i>Request Sent (Pending)';
                    } else {
                        trackBadge.className = 'badge bg-light text-muted border px-2.5 py-1.5 rounded-pill small';
                        trackBadge.innerHTML = '<i class="fa-solid fa-circle text-muted me-1"></i>Tracking OFF';
                    }
                }

                if (locBtn) {
                    locBtn.href = `{{ url('company/employee/location') }}/${data.employee.id}`;
                }
            }

            // 3. Update Summary Telemetry
            if (data.summary) {
                const sWork = document.getElementById('sumWorkingDays');
                const sPresent = document.getElementById('sumPresentDays');
                const sHalf = document.getElementById('sumHalfDays');
                const sLeave = document.getElementById('sumLeaveDays');
                const sHol = document.getElementById('sumHolidayDays');
                const sAbsent = document.getElementById('sumAbsentDays');

                if (sWork) sWork.innerText = data.summary.working_days;
                if (sPresent) sPresent.innerText = data.summary.present_count;
                if (sHalf) sHalf.innerText = data.summary.half_day_count;
                if (sLeave) sLeave.innerText = data.summary.leave_count;
                if (sHol) sHol.innerText = (data.summary.holiday_count + data.summary.weekend_count);
                if (sAbsent) sAbsent.innerText = data.summary.absent_count;
            }

            // 4. Render 7-Column Grid Cells
            const grid = document.getElementById('calendarDaysGridContainer');
            if (!grid) return;
            grid.innerHTML = '';

            currentMonthDaysCache = data.days || {};

            // Start Day of Week offset (1 = Mon ... 7 = Sun)
            const startDayOffset = (data.start_day_of_week - 1);
            for (let i = 0; i < startDayOffset; i++) {
                const blankCell = document.createElement('div');
                blankCell.className = 'calendar-day-cell other-month';
                grid.appendChild(blankCell);
            }

            // Actual Month Days
            Object.keys(currentMonthDaysCache).forEach(dateStr => {
                const dayData = currentMonthDaysCache[dateStr];
                const cell = document.createElement('div');
                cell.className = `calendar-day-cell status-${dayData.status} ${dayData.is_today ? 'is-today' : ''}`;
                cell.onclick = function () { openDayDetailsModal(dateStr); };

                let statusBadgeHtml = '';
                if (dayData.status === 'present') {
                    statusBadgeHtml = `<span class="cell-badge bg-success text-white"><i class="fa-solid fa-check fs-xs"></i> Present</span>`;
                } else if (dayData.status === 'halfday') {
                    statusBadgeHtml = `<span class="cell-badge bg-warning text-dark"><i class="fa-solid fa-hourglass-half fs-xs"></i> Half Day</span>`;
                } else if (dayData.status === 'leave') {
                    statusBadgeHtml = `<span class="cell-badge bg-info text-white"><i class="fa-solid fa-user-clock fs-xs"></i> ${dayData.status_text}</span>`;
                } else if (dayData.status === 'holiday') {
                    statusBadgeHtml = `<span class="cell-badge text-white" style="background:#a855f7;"><i class="fa-solid fa-star fs-xs"></i> ${dayData.status_text}</span>`;
                } else if (dayData.status === 'weekend') {
                    statusBadgeHtml = `<span class="cell-badge bg-secondary text-white">Weekly Off</span>`;
                } else if (dayData.status === 'absent') {
                    statusBadgeHtml = `<span class="cell-badge bg-danger text-white"><i class="fa-solid fa-xmark fs-xs"></i> Absent</span>`;
                } else if (dayData.status === 'pending') {
                    statusBadgeHtml = `<span class="cell-badge bg-warning-subtle text-warning border border-warning-subtle">Pending</span>`;
                }

                let inOutHtml = '';
                if (dayData.in_time) {
                    inOutHtml += `<div class="punch-time-chip text-success"><i class="fa-solid fa-arrow-right-to-bracket"></i> ${dayData.in_time}</div>`;
                }
                if (dayData.out_time) {
                    inOutHtml += `<div class="punch-time-chip text-danger"><i class="fa-solid fa-arrow-right-from-bracket"></i> ${dayData.out_time}</div>`;
                }
                if (dayData.work_duration) {
                    inOutHtml += `<span class="work-duration-pill">${dayData.work_duration}</span>`;
                }

                cell.innerHTML = `
                    <div class="day-cell-top">
                        <span class="day-cell-number">${dayData.day}</span>
                        ${dayData.is_today ? '<span class="badge bg-primary text-white" style="font-size: 0.6rem;">TODAY</span>' : ''}
                    </div>
                    <div class="mb-1">${statusBadgeHtml}</div>
                    ${inOutHtml}
                `;

                grid.appendChild(cell);
            });
        }

        // Open Individual Day Breakdown Modal
        function openDayDetailsModal(dateStr) {
            const dayData = currentMonthDaysCache[dateStr];
            if (!dayData) return;

            document.getElementById('modalDayFullDate').innerText = `${dayData.full_date} (${dayData.day_name})`;
            
            const badge = document.getElementById('modalDayStatusBadge');
            if (badge) {
                badge.className = `badge fs-6 px-3 py-1.5 rounded-pill ${dayData.badge_class}`;
                badge.innerText = dayData.status_text;
            }

            document.getElementById('modalDayInTime').innerText = dayData.in_time || '--:--';
            document.getElementById('modalDayOutTime').innerText = dayData.out_time || '--:--';
            document.getElementById('modalDayWorkDuration').innerText = dayData.work_duration || 'Not Recorded';

            // Punch Images
            const selfieRow = document.getElementById('modalSelfieRow');
            const inImg = document.getElementById('modalPunchinImg');
            const outImg = document.getElementById('modalPunchoutImg');
            const inCol = document.getElementById('modalPunchinImgCol');
            const outCol = document.getElementById('modalPunchoutImgCol');

            let hasSelfies = false;
            if (dayData.punchin_image) {
                inImg.src = dayData.punchin_image;
                inCol.classList.remove('d-none');
                hasSelfies = true;
            } else {
                inCol.classList.add('d-none');
            }

            if (dayData.punchout_image) {
                outImg.src = dayData.punchout_image;
                outCol.classList.remove('d-none');
                hasSelfies = true;
            } else {
                outCol.classList.add('d-none');
            }

            if (hasSelfies) {
                selfieRow.classList.remove('d-none');
            } else {
                selfieRow.classList.add('d-none');
            }

            // GPS Route Link
            const routeBtn = document.getElementById('modalDayLocationLink');
            if (routeBtn) {
                routeBtn.href = dayData.location_url;
            }

            const modal = new bootstrap.Modal(document.getElementById('dayAttendanceModal'));
            modal.show();
        }

        // Seamless Instant Local Filter + Server-Side AJAX Search (Name, Email, Mobile)
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('instantSearchInput');
            const searchIndicator = document.getElementById('searchIconIndicator');
            const clearBtn = document.getElementById('clearSearchBtn');
            const form = document.getElementById('employeeFilterForm');
            let debounceTimer = null;
            let currentAbortCtrl = null;

            function updateClearBtn() {
                if (clearBtn && searchInput) {
                    if (searchInput.value.trim().length > 0) {
                        clearBtn.classList.remove('d-none');
                    } else {
                        clearBtn.classList.add('d-none');
                    }
                }
            }

            function triggerServerSearch(queryVal) {
                if (currentAbortCtrl) {
                    currentAbortCtrl.abort();
                }
                currentAbortCtrl = new AbortController();

                if (searchIndicator) {
                    searchIndicator.className = 'fa-solid fa-spinner fa-spin position-absolute top-50 start-0 translate-middle-y ms-3 text-primary';
                }

                const url = new URL(window.location.href);
                const trimmed = (queryVal || '').trim();
                if (trimmed === '') {
                    url.searchParams.delete('name');
                    url.searchParams.delete('search');
                } else {
                    url.searchParams.set('name', trimmed);
                }
                url.searchParams.set('page', '1');

                if (form) {
                    const branchSel = form.querySelector('select[name="branch_id"]');
                    if (branchSel && branchSel.value) url.searchParams.set('branch_id', branchSel.value);
                    else url.searchParams.delete('branch_id');

                    const deptSel = form.querySelector('select[name="department_id"]');
                    if (deptSel && deptSel.value) url.searchParams.set('department_id', deptSel.value);
                    else url.searchParams.delete('department_id');

                    const kycSel = form.querySelector('select[name="kyc_status"]');
                    if (kycSel && kycSel.value) url.searchParams.set('kyc_status', kycSel.value);
                    else url.searchParams.delete('kyc_status');
                }

                fetch(url.toString(), {
                    signal: currentAbortCtrl.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newWrapper = doc.getElementById('employeeTableWrapper');
                    const oldWrapper = document.getElementById('employeeTableWrapper');

                    if (newWrapper && oldWrapper) {
                        oldWrapper.innerHTML = newWrapper.innerHTML;
                    }

                    window.history.replaceState(null, '', url.toString());

                    if (searchIndicator) {
                        searchIndicator.className = 'fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted';
                    }
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        if (searchIndicator) {
                            searchIndicator.className = 'fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted';
                        }
                    }
                });
            }

            if (searchInput) {
                // 1. Instant local filter on visible rows + debounced full-database search
                searchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();
                    updateClearBtn();

                    // Fast local filter on current rendered rows
                    const rows = document.querySelectorAll('#employeeDirectoryTable tbody tr.employee-row');
                    rows.forEach(function (row) {
                        const searchText = row.getAttribute('data-search') || '';
                        if (query === '' || searchText.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Debounced server search to search across all pages & database
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        triggerServerSearch(searchInput.value);
                    }, 400);
                });

                // Enter key press triggers immediately
                searchInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(debounceTimer);
                        triggerServerSearch(this.value);
                    }
                });

                if (clearBtn) {
                    clearBtn.addEventListener('click', function () {
                        searchInput.value = '';
                        updateClearBtn();
                        clearTimeout(debounceTimer);
                        triggerServerSearch('');
                        searchInput.focus();
                    });
                }
            }

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (searchInput) {
                        clearTimeout(debounceTimer);
                        triggerServerSearch(searchInput.value);
                    }
                });
            }

            // If initial load requested calendar view
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('view') === 'calendar') {
                const empParam = urlParams.get('calendar_employee_id');
                if (empParam) {
                    currentCalendarEmployeeId = parseInt(empParam);
                    const sel = document.getElementById('calEmployeeSelect');
                    if (sel) sel.value = currentCalendarEmployeeId;
                }
                switchDirectoryView('calendar');
            }
        });

        // Change entries per page
        function changePerPage(val) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', val);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }

        // Toggle Employee Real-Time Geo Tracking
        function toggleGeoStatus(employeeId, isChecked, employeeName) {
            const newStatus = isChecked ? '1' : '0';
            const switchEl = document.getElementById(`geoSwitch_${employeeId}`);
            const badgeEl = document.getElementById(`geoBadge_${employeeId}`);
            const textEl = document.getElementById(`geoText_${employeeId}`);
            const iconEl = document.getElementById(`geoIcon_${employeeId}`);
            const dropdownTextEl = document.getElementById(`geoDropdownText_${employeeId}`);
            const dropdownIconEl = document.getElementById(`geoDropdownIcon_${employeeId}`);

            if (switchEl) switchEl.disabled = true;
            if (badgeEl) {
                badgeEl.className = 'badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5';
                if (textEl) textEl.innerText = 'Updating...';
                if (iconEl) iconEl.className = 'fa-solid fa-spinner fa-spin me-1';
            }

            fetch("{{ route('employee.updateGeoStatus') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    employee_id: employeeId,
                    geo_status: newStatus
                })
            })
            .then(res => res.json())
            .then(data => {
                if (switchEl) switchEl.disabled = false;
                if (data.status) {
                    const statusStr = String(data.geo_status || '0');
                    if (statusStr === '2') {
                        if (switchEl) {
                            switchEl.checked = true;
                            switchEl.title = 'Tracking is Active (Accepted). Click to turn OFF';
                        }
                        if (badgeEl) badgeEl.className = 'badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5';
                        if (textEl) textEl.innerText = 'Tracking ON';
                        if (iconEl) iconEl.className = 'fa-solid fa-location-dot me-1';
                        if (dropdownTextEl) dropdownTextEl.innerText = 'Stop Tracking';
                        if (dropdownIconEl) dropdownIconEl.className = 'fa-solid fa-location-crosshairs text-danger me-2';
                    } else if (statusStr === '1') {
                        if (switchEl) {
                            switchEl.checked = true;
                            switchEl.title = 'Tracking Requested (Pending Acceptance). Click to turn OFF';
                        }
                        if (badgeEl) badgeEl.className = 'badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5';
                        if (textEl) textEl.innerText = 'Request Sent';
                        if (iconEl) iconEl.className = 'fa-solid fa-clock me-1';
                        if (dropdownTextEl) dropdownTextEl.innerText = 'Stop Tracking';
                        if (dropdownIconEl) dropdownIconEl.className = 'fa-solid fa-location-crosshairs text-danger me-2';
                    } else {
                        if (switchEl) {
                            switchEl.checked = false;
                            switchEl.title = 'Tracking is Off. Click to request tracking';
                        }
                        if (badgeEl) badgeEl.className = 'badge bg-light text-muted border px-2 py-0.5';
                        if (textEl) textEl.innerText = 'Tracking OFF';
                        if (iconEl) iconEl.className = 'fa-solid fa-location-crosshairs me-1';
                        if (dropdownTextEl) dropdownTextEl.innerText = 'Request Tracking';
                        if (dropdownIconEl) dropdownIconEl.className = 'fa-solid fa-location-crosshairs text-success me-2';
                    }

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: statusStr === '1' 
                            ? `Location tracking requested for ${employeeName}` 
                            : (statusStr === '2' ? `Location tracking active for ${employeeName}` : `Location tracking turned off for ${employeeName}`)
                    });
                } else {
                    if (switchEl) switchEl.checked = !isChecked;
                    resetBadgeState(employeeId, !isChecked);
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: data.message || 'Could not update location tracking status.'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                if (switchEl) {
                    switchEl.disabled = false;
                    switchEl.checked = !isChecked;
                }
                resetBadgeState(employeeId, !isChecked);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'An error occurred while updating status. Please try again.'
                });
            });
        }

        function resetBadgeState(employeeId, isChecked) {
            const badgeEl = document.getElementById(`geoBadge_${employeeId}`);
            const textEl = document.getElementById(`geoText_${employeeId}`);
            const iconEl = document.getElementById(`geoIcon_${employeeId}`);
            if (badgeEl) {
                badgeEl.className = isChecked 
                    ? 'badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5'
                    : 'badge bg-light text-muted border px-2 py-0.5';
            }
            if (textEl) {
                textEl.innerText = isChecked ? 'Request Sent' : 'Tracking OFF';
            }
            if (iconEl) {
                iconEl.className = isChecked ? 'fa-solid fa-clock me-1' : 'fa-solid fa-location-crosshairs me-1';
            }
        }

        function triggerGeoToggleFromDropdown(employeeId, employeeName) {
            const switchEl = document.getElementById(`geoSwitch_${employeeId}`);
            const willBeChecked = switchEl ? !switchEl.checked : true;
            if (switchEl) switchEl.checked = willBeChecked;
            toggleGeoStatus(employeeId, willBeChecked, employeeName);
        }
    </script>
@endsection
