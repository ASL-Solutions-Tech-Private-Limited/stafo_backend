@extends('user.layouts.app')
@section('title', 'Attendance List | STAFO HRMS')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Attendance Records</h3>
                    <p class="text-muted small mb-0">Track daily employee check-ins, check-outs, punch images, and work hours</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('attendance.create') }}" class="btn btn-primary px-3 py-2">
                        <i class="fa-solid fa-plus me-1"></i> Mark Attendance
                    </a>
                    <button type="button" class="btn btn-outline-primary px-3 py-2" data-bs-toggle="modal" data-bs-target="#importAttendanceModal">
                        <i class="fa-solid fa-file-import me-1"></i> Import
                    </button>
                    <a href="{{ route('user.attendances.export', request()->query()) }}" class="btn btn-outline-secondary px-3 py-2">
                        <i class="fa-solid fa-file-export me-1"></i> Export
                    </a>
                </div>
            </div>

            {{-- Display success message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="fa-solid fa-circle-check fs-5 me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Attendance & Biometric Operations Marquee Ticker -->
            <div class="stafo-marquee-bar mb-4">
                <div class="stafo-marquee-pill" style="background: linear-gradient(135deg, #10b981 0%, #059669 45%, #0284c7 100%);">
                    <span class="pulse-dot"></span>
                    <i class="fa-solid fa-clock"></i>
                    <span>Attendance Ticker</span>
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

            <!-- Filter Form -->
            <form method="GET" action="{{ route('attendance.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    
                    <!-- Employee Filter -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <select name="employee_id" id="employee_id" class="form-select">
                            <option value="">All Employees</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Attendance Filter -->
                    <div class="col-12 col-sm-6 col-lg-2">
                        <select name="attendance" id="attendance" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Present" {{ request('attendance') == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Absent" {{ request('attendance') == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Leave" {{ request('attendance') == 'Leave' ? 'selected' : '' }}>Leave</option>
                        </select>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="col-12 col-sm-6 col-lg-2">
                        <input type="date" name="from_date" id="from_date" class="form-control"
                            value="{{ request('from_date') }}" title="From Date">
                    </div>

                    <div class="col-12 col-sm-6 col-lg-2">
                        <input type="date" name="to_date" id="to_date" class="form-control"
                            value="{{ request('to_date') }}" title="To Date">
                    </div>

                    <!-- Submit and Reset Buttons -->
                    <div class="col-12 col-lg-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->filled('employee_id') || request()->filled('attendance') || request()->filled('from_date') || request()->filled('to_date'))
                            <a href="{{ route('attendance.index') }}" class="btn btn-light border text-muted px-3" title="Clear Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>

                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 60px;" class="text-center">S.No</th>
                            <th style="width: 220px;">Employee</th>
                            <th style="width: 130px;">Date</th>
                            <th style="width: 120px;" class="text-center">Status</th>
                            <th style="width: 100px;" class="text-center">Punch In</th>
                            <th style="width: 100px;" class="text-center">Punch Out</th>
                            <th style="width: 110px;" class="text-center">In Time</th>
                            <th style="width: 110px;" class="text-center">Out Time</th>
                            <th style="width: 110px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $index => $attendance)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $attendances->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" 
                                             style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($attendance->employee->name ?? 'E', 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $attendance->employee->name ?? '-' }}</span>
                                            <small class="text-muted">ID: {{ $attendance->employee->emp_id ?? $attendance->employee_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary">
                                        <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                        {{ $attendance->date ? \Carbon\Carbon::parse($attendance->date)->format('M d, Y') : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($attendance->attendance == 'Present')
                                        <span class="badge-stafo badge-stafo-success">
                                            <i class="fa-solid fa-circle-check"></i> Present
                                        </span>
                                    @elseif ($attendance->attendance == 'Absent')
                                        <span class="badge-stafo badge-stafo-danger">
                                            <i class="fa-solid fa-circle-xmark"></i> Absent
                                        </span>
                                    @elseif ($attendance->attendance == 'Leave')
                                        <span class="badge-stafo badge-stafo-warning">
                                            <i class="fa-solid fa-clock"></i> Leave
                                        </span>
                                    @else
                                        <span class="badge-stafo badge-stafo-info">{{ $attendance->attendance ?? 'Unknown' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($attendance->punchin_image)
                                        <div class="position-relative d-inline-block" style="cursor: pointer;" onclick="viewPunchInImage('{{ url('uploads/employees/punchin/' . $attendance->punchin_image) }}')">
                                            <img src="{{ url('uploads/employees/punchin/' . $attendance->punchin_image) }}"
                                                alt="Punch In" class="rounded-2 shadow-sm border" 
                                                style="width: 40px; height: 40px; object-fit: cover; transition: transform 0.2s ease;"
                                                onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'" />
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($attendance->punchout_image)
                                        <div class="position-relative d-inline-block" style="cursor: pointer;" onclick="viewPunchOutImage('{{ url('uploads/employees/punchout/' . $attendance->punchout_image) }}')">
                                            <img src="{{ url('uploads/employees/punchout/' . $attendance->punchout_image) }}"
                                                alt="Punch Out" class="rounded-2 shadow-sm border" 
                                                style="width: 40px; height: 40px; object-fit: cover; transition: transform 0.2s ease;"
                                                onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'" />
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($attendance->in_time)
                                        <span class="badge-stafo badge-stafo-info font-monospace">
                                            <i class="fa-regular fa-clock me-1"></i>{{ $attendance->in_time }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($attendance->out_time)
                                        <span class="badge-stafo badge-stafo-primary font-monospace">
                                            <i class="fa-regular fa-clock me-1"></i>{{ $attendance->out_time }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('attendance.edit', $attendance->id) }}"
                                            class="btn btn-sm btn-outline-warning p-0" title="Edit Record" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Record"
                                            onclick="confirmDelete(event, {{ $attendance->id }})" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        <form id="delete-form-{{ $attendance->id }}"
                                            action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
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

            <!-- Pagination -->
            @if($attendances->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                    <small class="text-muted">Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} entries</small>
                    <div>{{ $attendances->links('pagination::bootstrap-4') }}</div>
                </div>
            @endif

        </div>
    </div>

    <!-- Modal for Punch In Image Preview -->
    <div class="modal fade" id="punchinImageModal" tabindex="-1" aria-labelledby="punchinImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="punchinImageModalLabel"><i class="fa-solid fa-camera text-success me-2"></i>Punch In Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img id="punchin_image_modal" class="img-fluid rounded-3 shadow-sm border" src="" alt="Punch In Image" style="max-height: 420px;" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Punch Out Image Preview -->
    <div class="modal fade" id="punchoutImageModal" tabindex="-1" aria-labelledby="punchoutImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="punchoutImageModalLabel"><i class="fa-solid fa-camera text-primary me-2"></i>Punch Out Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img id="punchout_image_modal" class="img-fluid rounded-3 shadow-sm border" src="" alt="Punch Out Image" style="max-height: 420px;" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importAttendanceModal" tabindex="-1" aria-labelledby="importAttendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importAttendanceModalLabel"><i class="fa-solid fa-file-excel text-success me-2"></i>Import Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.attendances.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload Excel / CSV File</label>
                            <input type="file" name="attendance_file" class="form-control" accept=".xls,.xlsx" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-upload me-1"></i> Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
        }
    </script>
@endsection
