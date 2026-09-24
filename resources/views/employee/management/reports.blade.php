@extends('employee.layouts.app')

@section('title', 'Download Reports Hub | Management Portal')

@section('content')
<div class="container-fluid p-0">


    <!-- Main Download Reports Card (Matches Company Panel) -->
    <div class="card shadow-sm border-0 rounded-4 mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Download Reports Hub</h3>
                <p class="text-muted small mb-0">Export organizational data, attendance logs, and leave statistics to Excel or PDF</p>
            </div>
        </div>

            <!-- Modern Pill Tabs -->
            <ul class="nav nav-pills gap-2 mb-4 bg-light p-2 rounded-4 border" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4 py-2 rounded-3 fw-semibold" id="employee-tab" data-bs-toggle="pill" data-bs-target="#employee-report" type="button" role="tab" aria-controls="employee-report" aria-selected="true">
                        <i class="fa-solid fa-users me-2"></i> Employee Report
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2 rounded-3 fw-semibold" id="attendance-tab" data-bs-toggle="pill" data-bs-target="#attendance-report" type="button" role="tab" aria-controls="attendance-report" aria-selected="false">
                        <i class="fa-solid fa-calendar-check me-2"></i> Attendance Report
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-2 rounded-3 fw-semibold" id="leave-tab" data-bs-toggle="pill" data-bs-target="#leave-report" type="button" role="tab" aria-controls="leave-report" aria-selected="false">
                        <i class="fa-solid fa-plane-departure me-2"></i> Leave Report
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="reportTabsContent">
                
                <!-- 1. Employee Report Tab -->
                <div class="tab-pane fade show active" id="employee-report" role="tabpanel" aria-labelledby="employee-tab">
                    <div class="p-4 bg-light rounded-4 border">
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-file-export text-primary"></i> Export Employee Directory
                        </h5>
                        <form action="{{ route('employee.management.reports.exportEmployee') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="employee_dept" class="form-label fw-semibold text-dark small">Department</label>
                                    <select name="department" id="employee_dept" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="employee_branch" class="form-label fw-semibold text-dark small">Branch</label>
                                    <select name="branch" id="employee_branch" class="form-select">
                                        <option value="">All Branches</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="employee_start_date" class="form-label fw-semibold text-dark small">From Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="employee_start_date" class="form-control" value="{{ date('Y-01-01') }}" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="employee_end_date" class="form-label fw-semibold text-dark small">To Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="employee_end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-dark small">Export Format <span class="text-danger">*</span></label>
                                    <div class="dropdown">
                                        <button class="btn btn-white border dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center py-2"
                                                type="button" id="employeeDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span id="employee-format-text"><i class="fa-solid fa-file-excel text-success me-2"></i> Excel (.xlsx)</span>
                                        </button>
                                        <ul class="dropdown-menu w-100 shadow-sm" aria-labelledby="employeeDropdownBtn">
                                            <li>
                                                <a class="dropdown-item py-2" href="javascript:void(0)" data-value="excel" data-target="employee">
                                                    <i class="fa-solid fa-file-excel text-success me-2"></i> Excel (.xlsx)
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2" href="javascript:void(0)" data-value="pdf" data-target="employee">
                                                    <i class="fa-solid fa-file-pdf text-danger me-2"></i> PDF Document (.pdf)
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <input type="hidden" name="format" id="employee-format" value="excel">
                                </div>

                                <div class="col-12 col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                        <i class="fa-solid fa-download me-1"></i> Download Employee Report
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 2. Attendance Report Tab -->
                <div class="tab-pane fade" id="attendance-report" role="tabpanel" aria-labelledby="attendance-tab">
                    <div class="p-4 bg-light rounded-4 border">
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-calendar-check text-primary"></i> Export Attendance Logs
                        </h5>
                        <form action="{{ route('employee.management.reports.exportAttendance') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="att_dept" class="form-label fw-semibold text-dark small">Department</label>
                                    <select name="department" id="att_dept" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="att_branch" class="form-label fw-semibold text-dark small">Branch</label>
                                    <select name="branch" id="att_branch" class="form-select">
                                        <option value="">All Branches</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="att_start_date" class="form-label fw-semibold text-dark small">From Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="att_start_date" class="form-control" value="{{ date('Y-m-01') }}" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="att_end_date" class="form-label fw-semibold text-dark small">To Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="att_end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-dark small">Export Format <span class="text-danger">*</span></label>
                                    <div class="dropdown">
                                        <button class="btn btn-white border dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center py-2"
                                                type="button" id="attendanceDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span id="attendance-format-text"><i class="fa-solid fa-file-excel text-success me-2"></i> Excel (.xlsx)</span>
                                        </button>
                                        <ul class="dropdown-menu w-100 shadow-sm" aria-labelledby="attendanceDropdownBtn">
                                            <li>
                                                <a class="dropdown-item py-2" href="javascript:void(0)" data-value="excel" data-target="attendance">
                                                    <i class="fa-solid fa-file-excel text-success me-2"></i> Excel (.xlsx)
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2" href="javascript:void(0)" data-value="pdf" data-target="attendance">
                                                    <i class="fa-solid fa-file-pdf text-danger me-2"></i> PDF Document (.pdf)
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <input type="hidden" name="format" id="attendance-format" value="excel">
                                </div>

                                <div class="col-12 col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                        <i class="fa-solid fa-download me-1"></i> Download Attendance Report
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 3. Leave Report Tab -->
                <div class="tab-pane fade" id="leave-report" role="tabpanel" aria-labelledby="leave-tab">
                    <div class="p-4 bg-light rounded-4 border">
                        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-plane-departure text-primary"></i> Export Leave History
                        </h5>
                        <form action="{{ route('employee.management.reports.exportLeave') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="leave_dept" class="form-label fw-semibold text-dark small">Department</label>
                                    <select name="department" id="leave_dept" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="leave_branch" class="form-label fw-semibold text-dark small">Branch</label>
                                    <select name="branch" id="leave_branch" class="form-select">
                                        <option value="">All Branches</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="leave_start_date" class="form-label fw-semibold text-dark small">From Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="leave_start_date" class="form-control" value="{{ date('Y-01-01') }}" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="leave_end_date" class="form-label fw-semibold text-dark small">To Date <span class="text-danger">*</span></label>
                                    <input type="date" name="end_date" id="leave_end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-dark small">Export Format <span class="text-danger">*</span></label>
                                    <div class="dropdown">
                                        <button class="btn btn-white border dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center py-2"
                                                type="button" id="leaveDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span id="leave-format-text"><i class="fa-solid fa-file-excel text-success me-2"></i> Excel (.xlsx)</span>
                                        </button>
                                        <ul class="dropdown-menu w-100 shadow-sm" aria-labelledby="leaveDropdownBtn">
                                            <li>
                                                <a class="dropdown-item py-2" href="javascript:void(0)" data-value="excel" data-target="leave">
                                                    <i class="fa-solid fa-file-excel text-success me-2"></i> Excel (.xlsx)
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2" href="javascript:void(0)" data-value="pdf" data-target="leave">
                                                    <i class="fa-solid fa-file-pdf text-danger me-2"></i> PDF Document (.pdf)
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <input type="hidden" name="format" id="leave-format" value="excel">
                                </div>

                                <div class="col-12 col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                                        <i class="fa-solid fa-download me-1"></i> Download Leave Report
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

<script>
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const selectedValue = this.getAttribute('data-value');
            const target = this.getAttribute('data-target');
            const iconClass = selectedValue === 'excel' ? 'fa-file-excel text-success' : 'fa-file-pdf text-danger';
            const labelText = selectedValue === 'excel' ? 'Excel (.xlsx)' : 'PDF Document (.pdf)';
            
            if (target === 'employee') {
                document.getElementById('employee-format-text').innerHTML = `<i class="fa-solid ${iconClass} me-2"></i> ${labelText}`;
                document.getElementById('employee-format').value = selectedValue;
            } else if (target === 'attendance') {
                document.getElementById('attendance-format-text').innerHTML = `<i class="fa-solid ${iconClass} me-2"></i> ${labelText}`;
                document.getElementById('attendance-format').value = selectedValue;
            } else if (target === 'leave') {
                document.getElementById('leave-format-text').innerHTML = `<i class="fa-solid ${iconClass} me-2"></i> ${labelText}`;
                document.getElementById('leave-format').value = selectedValue;
            }
        });
    });
</script>
@endsection
