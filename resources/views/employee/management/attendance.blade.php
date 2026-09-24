@extends('employee.layouts.app')

@section('title', 'Staff Attendance | Management Portal')

@section('css')
<style>
    .badge-stafo {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.76rem;
        font-weight: 600;
        padding: 5px 11px;
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
    .badge-stafo-warning {
        background-color: rgba(245, 158, 11, 0.12);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }
    .badge-stafo-info {
        background-color: rgba(14, 165, 233, 0.12);
        color: #0284c7;
        border: 1px solid rgba(14, 165, 233, 0.25);
    }
    .badge-stafo-primary {
        background-color: rgba(99, 102, 241, 0.12);
        color: #4f46e5;
        border: 1px solid rgba(99, 102, 241, 0.25);
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
</style>
@endsection

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Management Portal
                </span>
                <span class="text-muted small">• Staff Attendance</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Attendance Records</h3>
            <p class="text-muted small mb-0">Track daily employee check-ins, check-outs, punch images, and work hours</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            @if(Auth::guard('employee')->user()->hasPermission('attendance.create'))
                <button type="button" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#markAttendanceModal">
                    <i class="fa-solid fa-plus me-1"></i> Mark Attendance
                </button>
            @endif
            <span class="badge bg-light text-dark border px-3 py-2 rounded-3 shadow-2xs font-sans">
                Role: <strong class="text-success ms-1">{{ Auth::guard('employee')->user()->designation->name ?? 'Management Role' }}</strong>
            </span>
        </div>
    </div>

    <!-- Attendance & Biometric Operations Marquee Ticker -->
    <div class="stafo-marquee-bar mb-4">
        <div class="stafo-marquee-pill" style="background: linear-gradient(135deg, #10b981 0%, #059669 45%, #0284c7 100%);">
            <span class="pulse-dot"></span>
            <i class="fa-solid fa-clock"></i>
            <span>ATTENDANCE TICKER</span>
        </div>
        <div class="stafo-marquee-container">
            <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" class="stafo-marquee-content">
                <span class="marquee-chip chip-attendance">
                    <i class="fa-solid fa-business-time"></i>
                    <span>Daily Shift Timing: Please ensure employees punch in before the designated shift grace period expires.</span>
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-policy">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Geofenced & Selfie Punch: Remote and on-field employees must punch from registered client/office coordinates.</span>
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-payroll">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <span>Half-Day / Overtime Rule: Minimum 4 hours for half-day, 8 hours for full-day attendance credit.</span>
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-holiday">
                    <i class="fa-solid fa-fingerprint"></i>
                    <span>Biometric Hardware: Real-time sync captures check-in/out timestamps instantly for automated payroll calculation.</span>
                </span>
            </marquee>
        </div>
    </div>

    <!-- Filter Form (Matches Company Panel Exactly) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('employee.management.attendance') }}">
                <div class="row g-2 align-items-center">
                    
                    <!-- Employee Filter -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <select name="employee_id" id="employee_id" class="form-select bg-light">
                            <option value="">All Employees</option>
                            @foreach ($employees as $empOpt)
                                <option value="{{ $empOpt->id }}"
                                    {{ request('employee_id') == $empOpt->id ? 'selected' : '' }}>
                                    {{ $empOpt->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Attendance Status Filter -->
                    <div class="col-12 col-sm-6 col-lg-2">
                        <select name="attendance" id="attendance" class="form-select bg-light">
                            <option value="">All Statuses</option>
                            <option value="Present" {{ request('attendance') == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Absent" {{ request('attendance') == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Leave" {{ request('attendance') == 'Leave' ? 'selected' : '' }}>Leave</option>
                        </select>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="col-12 col-sm-6 col-lg-2">
                        <input type="date" name="from_date" id="from_date" class="form-control bg-light"
                            value="{{ request('from_date') }}" title="From Date">
                    </div>

                    <div class="col-12 col-sm-6 col-lg-2">
                        <input type="date" name="to_date" id="to_date" class="form-control bg-light"
                            value="{{ request('to_date') }}" title="To Date">
                    </div>

                    <!-- Submit and Reset Buttons -->
                    <div class="col-12 col-lg-3 d-flex gap-2">
                        <button type="submit" class="btn text-white flex-grow-1 fw-bold" style="background: #059669; border-color: #059669;">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->filled('employee_id') || request()->filled('attendance') || request()->filled('from_date') || request()->filled('to_date'))
                            <a href="{{ route('employee.management.attendance') }}" class="btn btn-light border text-muted px-3" title="Clear Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table (Matches Company Panel S.NO, EMPLOYEE, DATE, STATUS, PUNCH IN, PUNCH OUT, IN TIME, OUT TIME, ACTIONS) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark" style="background: #0f172a;">
                    <tr class="text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <th style="width: 60px;" class="text-center">S.NO</th>
                        <th style="width: 220px;">EMPLOYEE</th>
                        <th style="width: 130px;">DATE</th>
                        <th style="width: 120px;" class="text-center">STATUS</th>
                        <th style="width: 100px;" class="text-center">PUNCH IN</th>
                        <th style="width: 100px;" class="text-center">PUNCH OUT</th>
                        <th style="width: 110px;" class="text-center">IN TIME</th>
                        <th style="width: 110px;" class="text-center">OUT TIME</th>
                        <th style="width: 110px;" class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $index => $att)
                        @php
                            $attEmp = $att->employee;
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-semibold">
                                {{ $attendances->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold flex-shrink-0" 
                                         style="width: 38px; height: 38px; font-size: 0.85rem;">
                                        {{ strtoupper(substr($attEmp->name ?? 'E', 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block mb-0">{{ $attEmp->name ?? '-' }}</span>
                                        <small class="text-muted">ID: {{ $attEmp->emp_id ?? ('EMP-' . str_pad($att->employee_id, 5, '0', STR_PAD_LEFT)) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold text-secondary">
                                    <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                    {{ $att->date ? \Carbon\Carbon::parse($att->date)->format('M d, Y') : '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($att->attendance == 'Present')
                                    <span class="badge-stafo badge-stafo-success">
                                        <i class="fa-solid fa-circle-check"></i> Present
                                    </span>
                                @elseif ($att->attendance == 'Absent')
                                    <span class="badge-stafo badge-stafo-danger">
                                        <i class="fa-solid fa-circle-xmark"></i> Absent
                                    </span>
                                @elseif ($att->attendance == 'Leave')
                                    <span class="badge-stafo badge-stafo-warning">
                                        <i class="fa-solid fa-clock"></i> Leave
                                    </span>
                                @else
                                    <span class="badge-stafo badge-stafo-info">{{ $att->attendance ?? 'Unknown' }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($att->punchin_image)
                                    <div class="position-relative d-inline-block" style="cursor: pointer;" onclick="viewPunchInImage('{{ url('uploads/employees/punchin/' . $att->punchin_image) }}')">
                                        <img src="{{ url('uploads/employees/punchin/' . $att->punchin_image) }}"
                                            alt="Punch In" class="rounded-2 shadow-sm border" 
                                            style="width: 40px; height: 40px; object-fit: cover; transition: transform 0.2s ease;"
                                            onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'" />
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($att->punchout_image)
                                    <div class="position-relative d-inline-block" style="cursor: pointer;" onclick="viewPunchOutImage('{{ url('uploads/employees/punchout/' . $att->punchout_image) }}')">
                                        <img src="{{ url('uploads/employees/punchout/' . $att->punchout_image) }}"
                                            alt="Punch Out" class="rounded-2 shadow-sm border" 
                                            style="width: 40px; height: 40px; object-fit: cover; transition: transform 0.2s ease;"
                                            onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'" />
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($att->in_time)
                                    <span class="badge-stafo badge-stafo-info font-monospace">
                                        <i class="fa-regular fa-clock me-1"></i>{{ $att->in_time }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($att->out_time)
                                    <span class="badge-stafo badge-stafo-primary font-monospace">
                                        <i class="fa-regular fa-clock me-1"></i>{{ $att->out_time }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $canEditAtt = Auth::guard('employee')->user()->hasPermission('attendance.edit');
                                    $canDeleteAtt = Auth::guard('employee')->user()->hasPermission('attendance.delete');
                                @endphp
                                @if($canEditAtt || $canDeleteAtt)
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        @if($canEditAtt)
                                            <a href="{{ route('employee.management.attendance.edit', $att->id) }}"
                                                class="btn btn-sm btn-outline-warning p-0" title="Edit Record" style="width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        @endif
                                        @if($canDeleteAtt)
                                            <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Record"
                                                onclick="confirmDelete(event, {{ $att->id }})" style="width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                            <form id="delete-form-{{ $att->id }}"
                                                action="{{ route('employee.management.attendance.destroy', $att->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">View Only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-calendar-xmark fs-2 mb-2 d-block opacity-50"></i>
                                No attendance records found for the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (Matches Company Panel) -->
        @if($attendances->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 p-3 border-top">
                <small class="text-muted">Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} entries</small>
                <div>{{ $attendances->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif
    </div>

</div>

<!-- Modal for Punch In Image Preview -->
<div class="modal fade" id="punchinImageModal" tabindex="-1" aria-labelledby="punchinImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fs-6 fw-bold" id="punchinImageModalLabel"><i class="fa-solid fa-camera me-2"></i>Punch In Image</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="punchin_image_modal" class="img-fluid rounded-3 shadow-sm border" src="" alt="Punch In Image" style="max-height: 420px;" />
            </div>
            <div class="modal-footer p-2 bg-light">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Punch Out Image Preview -->
<div class="modal fade" id="punchoutImageModal" tabindex="-1" aria-labelledby="punchoutImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fs-6 fw-bold" id="punchoutImageModalLabel"><i class="fa-solid fa-camera me-2"></i>Punch Out Image</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="punchout_image_modal" class="img-fluid rounded-3 shadow-sm border" src="" alt="Punch Out Image" style="max-height: 420px;" />
            </div>
            <div class="modal-footer p-2 bg-light">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Mark Attendance (guarded by attendance.create) -->
@if(Auth::guard('employee')->user()->hasPermission('attendance.create'))
<div class="modal fade" id="markAttendanceModal" tabindex="-1" aria-labelledby="markAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left fs-5"></i>
                    <h5 class="modal-title fw-bold" id="markAttendanceModalLabel">Mark Employee Attendance</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employee.management.attendance.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="mark_employee_id" class="form-label fw-semibold text-dark">
                            Employee <span class="text-danger">*</span>
                        </label>
                        <select name="employee_id" id="mark_employee_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->name }} (ID: {{ $emp->emp_id ?? $emp->id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="mark_date" class="form-label fw-semibold text-dark">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date" id="mark_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label for="mark_status" class="form-label fw-semibold text-dark">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="attendance" id="mark_status" class="form-select" required>
                                <option value="Present" selected>Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Leave">Leave</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="mark_in_time" class="form-label fw-semibold text-dark">
                                In Time
                            </label>
                            <input type="time" name="in_time" id="mark_in_time" class="form-control" value="09:00">
                        </div>
                        <div class="col-6">
                            <label for="mark_out_time" class="form-label fw-semibold text-dark">
                                Out Time
                            </label>
                            <input type="time" name="out_time" id="mark_out_time" class="form-control" value="18:00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    function viewPunchInImage(punchin_image_url) {
        document.getElementById('punchin_image_modal').src = punchin_image_url;
        $('#punchinImageModal').modal('show');
    }

    function viewPunchOutImage(punchout_image_url) {
        document.getElementById('punchout_image_modal').src = punchout_image_url;
        $('#punchoutImageModal').modal('show');
    }

    function confirmDelete(event, attendanceId) {
        event.preventDefault();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: "This attendance record will be deleted permanently!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${attendanceId}`).submit();
                }
            });
        } else {
            if (confirm("Are you sure you want to delete this attendance record?")) {
                document.getElementById(`delete-form-${attendanceId}`).submit();
            }
        }
    }
</script>
@endsection
