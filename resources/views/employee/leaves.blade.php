@extends('employee.layouts.app')

@section('title', 'My Leaves | STAFO HRMS')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">Leave Management</h4>
            <span class="text-muted small">Check leave balances, apply for leaves, and track application status</span>
        </div>
        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
            <i class="fa-solid fa-plus-circle me-1"></i> Apply for Leave
        </button>
    </div>

    <!-- Leave Balances Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Casual Leave</span>
                    <span class="badge bg-success bg-opacity-10 text-success">CL</span>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $employee_info->casual_leave ?? 0 }} <small class="text-muted fs-6 fw-normal">Days</small></h3>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Sick Leave</span>
                    <span class="badge bg-warning bg-opacity-10 text-warning">SL</span>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $employee_info->sick_leave ?? 0 }} <small class="text-muted fs-6 fw-normal">Days</small></h3>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Privileged Leave</span>
                    <span class="badge bg-info bg-opacity-10 text-info">PL</span>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $employee_info->privileged_leave ?? 0 }} <small class="text-muted fs-6 fw-normal">Days</small></h3>
            </div>
        </div>
    </div>

    <!-- Leaves Table -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i> Leave Application History
            </h6>
            <span class="badge bg-light text-muted border small">{{ $leaves->total() }} Applications</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Applied On</th>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $item)
                            <tr>
                                <td class="small text-muted">{{ Carbon\Carbon::parse($item->created_at)->format('d M, Y') }}</td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $item->leave_type ?: 'General Leave' }}</span>
                                </td>
                                <td class="small">
                                    {{ Carbon\Carbon::parse($item->from_date)->format('d M, Y') }} - {{ Carbon\Carbon::parse($item->to_date)->format('d M, Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $item->days }} day(s)</span>
                                </td>
                                <td class="small text-muted" style="max-width: 250px;">
                                    {{ Str::limit($item->reason, 50) }}
                                </td>
                                <td>
                                    @if($item->status === 'approved')
                                        <span class="badge bg-success bg-opacity-15 text-success">Approved</span>
                                    @elseif($item->status === 'rejected')
                                        <span class="badge bg-danger bg-opacity-15 text-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-15 text-warning">Pending Approval</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">No leave applications submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($leaves->hasPages())
                <div class="p-3 border-top">
                    {{ $leaves->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Apply Leave Modal -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1" aria-labelledby="applyLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('employee.leave.apply') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark" id="applyLeaveModalLabel">
                        <i class="fa-solid fa-paper-plane text-primary me-2"></i> Apply for Leave
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Leave Type <span class="text-danger">*</span></label>
                        <select name="leave_type" class="form-select" required>
                            <option value="">Select Leave Type</option>
                            <option value="Casual Leave">Casual Leave (CL)</option>
                            <option value="Sick Leave">Sick Leave (SL)</option>
                            <option value="Privileged Leave">Privileged Leave (PL)</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->name ?? $type->leave_type }}">{{ $type->name ?? $type->leave_type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-dark">From Date <span class="text-danger">*</span></label>
                            <input type="date" name="from_date" class="form-control" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-dark">To Date <span class="text-danger">*</span></label>
                            <input type="date" name="to_date" class="form-control" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Reason for Leave <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="State reason for your leave application..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
