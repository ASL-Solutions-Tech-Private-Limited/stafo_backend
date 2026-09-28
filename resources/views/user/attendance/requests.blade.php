@extends('user.layouts.app')
@section('title', 'Missed Punch-Out Requests | STAFO HRMS')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Missed Punch-Out Requests</h3>
                    <p class="text-muted small mb-0">Review, verify and approve or reject employee punch-out regularization requests</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary px-3 py-2">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Attendance
                    </a>
                </div>
            </div>

            {{-- Alerts --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="fa-solid fa-circle-check fs-5 me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filter Section -->
            <form method="GET" action="{{ route('user.attendanceRequests.index') }}" class="card bg-light border-0 p-3 mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-secondary">Employee</label>
                        <select name="employee_id" class="form-select select2">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->emp_id ?? 'ID:'.$emp->id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-secondary">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-secondary">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('user.attendanceRequests.index') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle border mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Employee</th>
                            <th scope="col">Branch / Dept</th>
                            <th scope="col">Date</th>
                            <th scope="col">In Time</th>
                            <th scope="col">Requested Out Time</th>
                            <th scope="col">Reason</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $req->employee->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $req->employee->emp_id ?? '' }} | {{ $req->employee->phone ?? '' }}</small>
                                </td>
                                <td>
                                    <div><i class="fa-solid fa-building text-secondary me-1"></i> {{ $req->branch->branch_name ?? 'All Branches' }}</div>
                                    <small class="text-muted"><i class="fa-solid fa-sitemap me-1"></i> {{ $req->department->name ?? 'General' }}</small>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ date('d M Y', strtotime($req->date)) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa-regular fa-clock text-primary me-1"></i> {{ $req->in_time ? date('h:i A', strtotime($req->in_time)) : '--:--' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa-solid fa-clock text-danger me-1"></i> {{ $req->out_time ? date('h:i A', strtotime($req->out_time)) : '--:--' }}
                                    </span>
                                </td>
                                <td style="max-width: 250px;">
                                    <div class="text-truncate" title="{{ $req->reason }}">{{ $req->reason ?? '-' }}</div>
                                    @if($req->status == 'Rejected' && $req->reject_reason)
                                        <div class="text-danger small mt-1">
                                            <strong>Reject Remark:</strong> {{ $req->reject_reason }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status == 'Pending')
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-clock-rotate-left me-1"></i> Pending</span>
                                    @elseif($req->status == 'Approved')
                                        <span class="badge bg-success px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> Approved</span>
                                    @elseif($req->status == 'Rejected')
                                        <span class="badge bg-danger px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i> Rejected</span>
                                        @if($req->halfday)
                                            <span class="badge bg-info text-dark d-block mt-1">Half Day</span>
                                        @elseif($req->attendance == 'Absent')
                                            <span class="badge bg-secondary d-block mt-1">Absent</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($req->status == 'Pending')
                                        <!-- Approve Form -->
                                        <form method="POST" action="{{ route('user.attendanceRequests.action') }}" class="d-inline" onsubmit="return confirm('Are you sure you want to approve this punch-out request?');">
                                            @csrf
                                            <input type="hidden" name="request_id" value="{{ $req->id }}">
                                            <input type="hidden" name="status" value="Approved">
                                            <button type="submit" class="btn btn-sm btn-success px-3">
                                                <i class="fa-solid fa-check me-1"></i> Approve
                                            </button>
                                        </form>

                                        <!-- Reject Button triggering modal -->
                                        <button type="button" class="btn btn-sm btn-outline-danger px-3 ms-1" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                            <i class="fa-solid fa-xmark me-1"></i> Reject
                                        </button>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('user.attendanceRequests.action') }}">
                                                        @csrf
                                                        <input type="hidden" name="request_id" value="{{ $req->id }}">
                                                        <input type="hidden" name="status" value="Rejected">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Reject Punch-Out Request</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <p class="text-muted small">Reject request for <strong>{{ $req->employee->name ?? 'Employee' }}</strong> on <strong>{{ date('d M Y', strtotime($req->date)) }}</strong>.</p>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Mark Attendance As <span class="text-danger">*</span></label>
                                                                <select name="reject_attendance_type" class="form-select" required>
                                                                    <option value="Halfday">Half Day (4 hrs credit)</option>
                                                                    <option value="Absent" selected>Absent (0 credit)</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Rejection Reason <span class="text-danger">*</span></label>
                                                                <textarea name="reject_reason" class="form-control" rows="3" placeholder="Specify why this punch-out is rejected..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">No action needed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fa-regular fa-folder-open fs-1 d-block mb-2 text-secondary"></i>
                                    No punch-out regularization requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $requests->withQueryString()->links() }}
            </div>

        </div>
    </div>
@endsection
