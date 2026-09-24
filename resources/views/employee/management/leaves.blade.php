@extends('employee.layouts.app')

@section('title', 'Staff Leave Requests | Management Portal')

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

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-purple-subtle text-purple border border-purple-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-calendar-check me-1"></i> Management Portal
                </span>
                <span class="text-muted small">• Staff Leave Requests</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Leave Applications & Approvals</h3>
            <p class="text-muted small mb-0">Review company employee leave applications, grant approvals or rejections.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="badge bg-light text-dark border px-3 py-2 rounded-3 shadow-2xs font-sans">
                Role: <strong class="text-primary ms-1">{{ Auth::guard('employee')->user()->designation->name ?? 'Management Role' }}</strong>
            </span>
        </div>
    </div>

    <!-- Leave Operations Marquee Ticker -->
    <div class="stafo-marquee-bar mb-4">
        <div class="stafo-marquee-pill" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #4f46e5 100%);">
            <span class="pulse-dot"></span>
            <i class="fa-solid fa-calendar-check"></i>
            <span>LEAVE TICKER</span>
        </div>
        <div class="stafo-marquee-container">
            <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" class="stafo-marquee-content">
                <span class="marquee-chip chip-policy">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Advance Notice: Employees must submit planned leave requests at least 3 days in advance.</span>
                </span>
                <span class="marquee-divider">•</span>

                <span class="marquee-chip chip-holiday">
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

    <!-- Filter Form (Matches Company Panel) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('employee.management.leaves') }}">
                <div class="row g-2 align-items-center">

                    <!-- Employee Filter -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <select name="employee_id" id="employee_id" class="form-select bg-light">
                            <option value="">All Employees</option>
                            @foreach ($employees as $empOpt)
                                <option value="{{ $empOpt->id }}" {{ request('employee_id') == $empOpt->id ? 'selected' : '' }}>
                                    {{ $empOpt->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Leave Type Filter -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <select name="leave_type" id="leave_type" class="form-select bg-light">
                            <option value="">All Leave Types</option>
                            <option value="1" {{ request('leave_type') == '1' ? 'selected' : '' }}>Casual Leave</option>
                            <option value="2" {{ request('leave_type') == '2' ? 'selected' : '' }}>Sick Leave</option>
                            <option value="3" {{ request('leave_type') == '3' ? 'selected' : '' }}>Privilege Leave</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <select name="status" id="status" class="form-select bg-light">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <!-- Submit and Clear -->
                    <div class="col-12 col-sm-6 col-lg-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 fw-bold">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->filled('employee_id') || request()->filled('leave_type') || request()->filled('status'))
                            <a href="{{ route('employee.management.leaves') }}" class="btn btn-light border text-muted px-3" title="Clear Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Table (Matches Company Panel S.NO, EMPLOYEE, LEAVE TYPE, DAYS, FROM DATE, TO DATE, STATUS, CHANGE STATUS, ACTIONS) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark" style="background: #0f172a;">
                    <tr class="text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <th style="width: 60px;" class="text-center">S.NO</th>
                        <th style="width: 220px;">EMPLOYEE</th>
                        <th style="width: 150px;">LEAVE TYPE</th>
                        <th style="width: 90px;" class="text-center">DAYS</th>
                        <th style="width: 140px;">FROM DATE</th>
                        <th style="width: 140px;">TO DATE</th>
                        <th style="width: 130px;" class="text-center">STATUS</th>
                        <th style="width: 180px;" class="text-center">CHANGE STATUS</th>
                        <th style="width: 80px;" class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaves as $index => $leaveRecord)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $leaves->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" 
                                         style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($leaveRecord->employeeBasicInfo->name ?? 'E', 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $leaveRecord->employeeBasicInfo->name ?? '-' }}</span>
                                        <small class="text-muted">ID: {{ $leaveRecord->employeeBasicInfo->emp_id ?? ('EMP-' . str_pad($leaveRecord->employee_id, 5, '0', STR_PAD_LEFT)) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-stafo badge-stafo-info">
                                    <i class="fa-solid fa-calendar-day me-1"></i>
                                    {{ $leaveRecord->leavetype->name ?? ($leaveRecord->leave_type == '1' ? 'Casual' : ($leaveRecord->leave_type == '2' ? 'Sick' : 'Privilege')) }}
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
                                @if(Auth::guard('employee')->user()->hasPermission('leaves.edit') || Auth::guard('employee')->user()->hasPermission('leaves.approve'))
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
                                @else
                                    <span class="text-muted small">View Only</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(Auth::guard('employee')->user()->hasPermission('leaves.delete'))
                                    <form action="{{ route('employee.management.leave.delete', $leaveRecord->id) }}" method="POST" id="delete-form-{{ $leaveRecord->id }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Leave Request"
                                            onclick="confirmDelete(event, {{ $leaveRecord->id }})" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
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
        @if($leaves->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 p-3 border-top">
                <small class="text-muted">Showing {{ $leaves->firstItem() }} to {{ $leaves->lastItem() }} of {{ $leaves->total() }} records</small>
                <div>{{ $leaves->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif
    </div>

</div>

<script>
    function updateLeaveStatus(selectElement) {
        const leaveId = selectElement.getAttribute('data-id');
        const action = selectElement.value;

        if (!action) return;

        const confirmMsg = `Are you sure you want to change status to ${action.toUpperCase()}?`;
        if (!confirm(confirmMsg)) {
            selectElement.value = '';
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('leave_id', leaveId);
        formData.append('action', action);

        fetch('{{ route("employee.management.leave.updateStatus") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Error updating status');
                selectElement.value = '';
            }
        })
        .catch(err => {
            console.error(err);
            window.location.reload();
        });
    }

    function confirmDelete(event, leaveId) {
        event.preventDefault();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: "This leave application will be permanently deleted!",
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
        } else {
            if (confirm("Are you sure you want to delete this leave application?")) {
                document.getElementById(`delete-form-${leaveId}`).submit();
            }
        }
    }
</script>
@endsection
