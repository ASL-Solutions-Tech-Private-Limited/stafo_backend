@extends('user.layouts.app')

@section('title', 'Employee Leave Requests | STAFO HRMS')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Leave Requests</h3>
                <p class="text-muted small mb-0">Review employee leave applications, approve or reject time-off requests</p>
            </div>
            <div>
                <a href="{{ route('leavetypes.index') }}" class="btn btn-outline-primary px-3 py-2">
                    <i class="fa-solid fa-gear me-1"></i> Leave Settings
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="fa-solid fa-circle-check fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="fa-solid fa-triangle-exclamation fs-5 me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Leave Management Guidelines Marquee Ticker -->
        <div class="stafo-marquee-bar mb-4">
            <div class="stafo-marquee-pill" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 45%, #4f46e5 100%);">
                <span class="pulse-dot"></span>
                <i class="fa-solid fa-plane-departure"></i>
                <span>Leave Ticker</span>
            </div>
            <div class="stafo-marquee-container">
                <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" class="stafo-marquee-content">
                    <span class="marquee-chip chip-policy">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Approval SLA: Managers are requested to review and approve/reject leave requests within 24 to 48 hours.</span>
                    </span>
                    <span class="marquee-divider">•</span>

                    <span class="marquee-chip chip-holiday">
                        <i class="fa-solid fa-umbrella-beach"></i>
                        <span>Holiday Leaves: Employees applying for leaves adjoining public holidays are subject to company sandwich rules.</span>
                    </span>
                    <span class="marquee-divider">•</span>

                    <span class="marquee-chip chip-task">
                        <i class="fa-solid fa-file-medical"></i>
                        <span>Medical Leave Proof: Submitting a valid doctor certificate is mandatory for sick leaves exceeding 2 consecutive days.</span>
                    </span>
                    <span class="marquee-divider">•</span>

                    <span class="marquee-chip chip-attendance">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Leave Balance: Deductions and remaining balance auto-sync with the employee portal upon manager approval.</span>
                    </span>
                </marquee>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('leaveList') }}" class="mb-4">
            <div class="row g-2 align-items-center">

                <!-- Employee Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <select name="employee_id" id="employee_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Leave Type Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <select name="leave_type" id="leave_type" class="form-select">
                        <option value="">All Leave Types</option>
                        <option value="1" {{ request('leave_type') == '1' ? 'selected' : '' }}>Casual Leave</option>
                        <option value="2" {{ request('leave_type') == '2' ? 'selected' : '' }}>Sick Leave</option>
                        <option value="3" {{ request('leave_type') == '3' ? 'selected' : '' }}>Privilege Leave</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Submit and Clear -->
                <div class="col-12 col-sm-6 col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->filled('employee_id') || request()->filled('leave_type') || request()->filled('status'))
                        <a href="{{ route('leaveList') }}" class="btn btn-light border text-muted px-3" title="Clear Filters">
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
                        <th style="width: 150px;">Leave Type</th>
                        <th style="width: 90px;" class="text-center">Days</th>
                        <th style="width: 140px;">From Date</th>
                        <th style="width: 140px;">To Date</th>
                        <th style="width: 130px;" class="text-center">Status</th>
                        <th style="width: 180px;" class="text-center">Change Status</th>
                        <th style="width: 80px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leave as $index => $leaveRecord)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $leave->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" 
                                         style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($leaveRecord->employeeBasicInfo->name ?? 'E', 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $leaveRecord->employeeBasicInfo->name ?? '-' }}</span>
                                        <small class="text-muted">ID: {{ $leaveRecord->employeeBasicInfo->emp_id ?? $leaveRecord->employee_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-stafo badge-stafo-info">
                                    <i class="fa-solid fa-calendar-day me-1"></i>
                                    {{ $leaveRecord->leaveType->name ?? ($leaveRecord->leave_type == '1' ? 'Casual' : ($leaveRecord->leave_type == '2' ? 'Sick' : 'Privilege')) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border fw-bold px-2 py-1">{{ $leaveRecord->days }} {{ Str::plural('day', $leaveRecord->days) }}</span>
                            </td>
                            <td>
                                <span class="text-secondary small">
                                    <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                    {{ $leaveRecord->from_date ? \Carbon\Carbon::parse($leaveRecord->from_date)->format('M d, Y') : '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-secondary small">
                                    <i class="fa-regular fa-calendar-check me-1 text-muted"></i>
                                    {{ $leaveRecord->to_date ? \Carbon\Carbon::parse($leaveRecord->to_date)->format('M d, Y') : '-' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($leaveRecord->status == 'approved')
                                    <span class="badge-stafo badge-stafo-success">
                                        <i class="fa-solid fa-circle-check"></i> Approved
                                    </span>
                                @elseif ($leaveRecord->status == 'pending')
                                    <span class="badge-stafo badge-stafo-warning">
                                        <i class="fa-solid fa-clock"></i> Pending
                                    </span>
                                @elseif ($leaveRecord->status == 'rejected')
                                    <span class="badge-stafo badge-stafo-danger">
                                        <i class="fa-solid fa-circle-xmark"></i> Rejected
                                    </span>
                                @else
                                    <span class="badge-stafo badge-stafo-info">{{ ucfirst($leaveRecord->status) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <select class="form-select form-select-sm action-dropdown" data-id="{{ $leaveRecord->id }}"
                                    onchange="updateLeaveStatus(this)" style="font-size: 0.8125rem;">
                                    <option value="">Update Status...</option>
                                    @if ($leaveRecord->status == 'pending')
                                        <option value="approve">✔ Approve</option>
                                        <option value="reject">✖ Reject</option>
                                    @elseif ($leaveRecord->status == 'approved')
                                        <option value="reject">✖ Reject</option>
                                        <option value="pending">⏳ Mark as Pending</option>
                                    @elseif ($leaveRecord->status == 'rejected')
                                        <option value="approve">✔ Re-approve</option>
                                        <option value="pending">⏳ Mark as Pending</option>
                                    @endif
                                </select>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('leave.delete', $leaveRecord->id) }}" method="POST" id="delete-form-{{ $leaveRecord->id }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Leave Request"
                                        onclick="confirmDelete(event, {{ $leaveRecord->id }})" style="width: 32px; height: 32px; border-radius: 8px;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-calendar-xmark fs-2 mb-2 d-block opacity-50"></i>
                                No leave requests found matching your filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leave->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                <small class="text-muted">Showing {{ $leave->firstItem() }} to {{ $leave->lastItem() }} of {{ $leave->total() }} records</small>
                <div>{{ $leave->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif

    </div>
</div>
@endsection

@section('js')
<script>
    function updateLeaveStatus(selectElement) {
        const leaveId = selectElement.getAttribute('data-id');
        const action = selectElement.value;

        if (!action) return;

        selectElement.disabled = true;

        fetch('{{ route('leave.updateStatus') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                leave_id: leaveId,
                action: action,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Updated!',
                    text: data.message || 'Leave status updated successfully.',
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Failed',
                    text: data.message || 'Failed to update leave status.'
                });
                selectElement.disabled = false;
                selectElement.value = "";
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error occurred while communicating with the server.'
            });
            selectElement.disabled = false;
            selectElement.value = "";
        });
    }

    function confirmDelete(event, leaveId) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "This leave record will be deleted permanently!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${leaveId}`).submit();
            }
        });
    }
</script>
@endsection