@extends('user.layouts.app')

@section('title', 'Download Reports Hub | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
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
                <button class="nav-link active px-4 py-2 rounded-3 fw-semibold" id="employee-tab" data-bs-toggle="pill" data-bs-target="#employee" type="button" role="tab" aria-controls="employee" aria-selected="true">
                    <i class="fa-solid fa-users me-2"></i> Employee Report
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2 rounded-3 fw-semibold" id="attendance-tab" data-bs-toggle="pill" data-bs-target="#attendance" type="button" role="tab" aria-controls="attendance" aria-selected="false">
                    <i class="fa-solid fa-calendar-check me-2"></i> Attendance Report
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-4 py-2 rounded-3 fw-semibold" id="leave-tab" data-bs-toggle="pill" data-bs-target="#leave" type="button" role="tab" aria-controls="leave" aria-selected="false">
                    <i class="fa-solid fa-plane-departure me-2"></i> Leave Report
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="reportTabsContent">
            
            <!-- Employee Tab -->
            <div class="tab-pane fade show active" id="employee" role="tabpanel" aria-labelledby="employee-tab">
                <div class="p-4 bg-light rounded-4 border">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-export text-primary"></i> Export Employee Directory
                    </h5>
                    <form action="{{ route('report.exportEmployee') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="department" class="form-label fw-semibold text-dark">Department</label>
                                <select name="department" id="department" class="form-select">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="branch" class="form-label fw-semibold text-dark">Branch</label>
                                <select name="branch" id="branch" class="form-select">
                                    <option value="">All Branches</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="start_date" class="form-label fw-semibold text-dark">From Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="end_date" class="form-label fw-semibold text-dark">To Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Export Format <span class="text-danger">*</span></label>
                                <div class="dropdown">
                                    <button class="btn btn-white border dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center py-2"
                                            type="button" id="employee-dropdownMenuButton"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <span id="employee-format-text"><i class="fa-solid fa-file-excel text-success me-2"></i> Excel (.xlsx)</span>
                                    </button>
                                    <ul class="dropdown-menu w-100 shadow-sm" aria-labelledby="employee-dropdownMenuButton">
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
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="fa-solid fa-download me-1"></i> Download Employee Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Attendance Tab -->
            <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                <div class="p-4 bg-light rounded-4 border">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-primary"></i> Export Attendance Logs
                    </h5>
                    <form action="{{ route('report.exportAttendance') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="attendance_department" class="form-label fw-semibold text-dark">Department</label>
                                <select name="department" id="attendance_department" class="form-select">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="attendance_branch" class="form-label fw-semibold text-dark">Branch</label>
                                <select name="branch" id="attendance_branch" class="form-select">
                                    <option value="">All Branches</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="attendance_start_date" class="form-label fw-semibold text-dark">From Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="attendance_start_date" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="attendance_end_date" class="form-label fw-semibold text-dark">To Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="attendance_end_date" class="form-control" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Export Format <span class="text-danger">*</span></label>
                                <div class="dropdown">
                                    <button class="btn btn-white border dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center py-2"
                                            type="button" id="attendance-dropdownMenuButton"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <span id="attendance-format-text"><i class="fa-solid fa-file-excel text-success me-2"></i> Excel (.xlsx)</span>
                                    </button>
                                    <ul class="dropdown-menu w-100 shadow-sm" aria-labelledby="attendance-dropdownMenuButton">
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
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="fa-solid fa-download me-1"></i> Download Attendance Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Leave Tab -->
            <div class="tab-pane fade" id="leave" role="tabpanel" aria-labelledby="leave-tab">
                <div class="p-4 bg-light rounded-4 border">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-plane-departure text-primary"></i> Export Leave History
                    </h5>
                    <form action="{{ route('report.exportLeave') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="leave_department" class="form-label fw-semibold text-dark">Department</label>
                                <select name="department" id="leave_department" class="form-select">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="leave_branch" class="form-label fw-semibold text-dark">Branch</label>
                                <select name="branch" id="leave_branch" class="form-select">
                                    <option value="">All Branches</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="leave_start_date" class="form-label fw-semibold text-dark">From Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="leave_start_date" class="form-control" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="leave_end_date" class="form-label fw-semibold text-dark">To Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="leave_end_date" class="form-control" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark">Export Format <span class="text-danger">*</span></label>
                                <div class="dropdown">
                                    <button class="btn btn-white border dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center py-2"
                                            type="button" id="leave-dropdownMenuButton"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <span id="leave-format-text"><i class="fa-solid fa-file-excel text-success me-2"></i> Excel (.xlsx)</span>
                                    </button>
                                    <ul class="dropdown-menu w-100 shadow-sm" aria-labelledby="leave-dropdownMenuButton">
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
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
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
@endsection

@section('js')
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
