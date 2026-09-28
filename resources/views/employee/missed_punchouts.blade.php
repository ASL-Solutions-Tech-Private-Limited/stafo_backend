@extends('employee.layouts.app')

@section('title', 'Missed Punch-Out Requests | STAFO HRMS')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-business-time text-primary me-2"></i>Missed Punch-Out / Regularization
            </h4>
            <span class="text-muted small">Forgot to punch out? Submit a regularization request with your actual out-time for company review.</span>
        </div>
        <button class="btn btn-primary px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#applyRegularizationModal">
            <i class="fa-solid fa-plus-circle"></i>
            <span class="fw-semibold">New Request</span>
        </button>
    </div>

    <!-- Alert banners -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Requests</span>
                    <div class="avatar-xs rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:34px;height:34px;">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ $totalCount ?? 0 }}</h3>
                <span class="text-muted small" style="font-size: 0.75rem;">Lifetime regularizations</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Pending Review</span>
                    <div class="avatar-xs rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width:34px;height:34px;">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-warning mb-0">{{ $pendingCount ?? 0 }}</h3>
                <span class="text-muted small" style="font-size: 0.75rem;">Awaiting company approval</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Approved</span>
                    <div class="avatar-xs rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:34px;height:34px;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-0">{{ $approvedCount ?? 0 }}</h3>
                <span class="text-muted small" style="font-size: 0.75rem;">Marked as Present</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Rejected</span>
                    <div class="avatar-xs rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width:34px;height:34px;">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-danger mb-0">{{ $rejectedCount ?? 0 }}</h3>
                <span class="text-muted small" style="font-size: 0.75rem;">Marked Half Day / Absent</span>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('employee.missedPunchouts') }}" class="row g-2 align-items-center">
                <div class="col-md-4 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-filter text-muted"></i></span>
                        <select name="status" class="form-select border-start-0" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-calendar-day text-muted"></i></span>
                        <input type="date" name="date" class="form-control border-start-0" value="{{ request('date') }}" onchange="this.form.submit()">
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary px-3 rounded-3">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['status', 'date']))
                        <a href="{{ route('employee.missedPunchouts') }}" class="btn btn-sm btn-outline-secondary px-3 rounded-3">
                            <i class="fa-solid fa-rotate-left me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                <h6 class="fw-bold mb-0 text-dark">My Request History</h6>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                {{ $requests->total() }} Total
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-3 py-3">Date</th>
                            <th>In Time</th>
                            <th>Requested Out Time</th>
                            <th>My Reason</th>
                            <th>Status</th>
                            <th>Company Decision / Remark</th>
                            <th class="pe-3 text-end">Submitted On</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($requests as $req)
                            <tr>
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-xs rounded-3 bg-light text-primary d-flex align-items-center justify-content-center fw-bold" style="width:36px;height:36px;font-size:0.8rem;">
                                            {{ date('d', strtotime($req->date)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ date('D, d M Y', strtotime($req->date)) }}</div>
                                            <span class="text-muted small" style="font-size: 0.72rem;">Target Work Date</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 font-monospace">
                                        <i class="fa-solid fa-arrow-right-to-bracket text-success me-1"></i>
                                        {{ !empty($req->in_time) ? date('h:i A', strtotime($req->in_time)) : '--:--' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 font-monospace fw-bold">
                                        <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>
                                        {{ !empty($req->out_time) ? date('h:i A', strtotime($req->out_time)) : '--:--' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 240px;" title="{{ $req->reason }}">
                                        {{ $req->reason ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    @if($req->status === 'Approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">
                                            <i class="fa-solid fa-circle-check me-1"></i> Approved
                                        </span>
                                    @elseif($req->status === 'Rejected')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> Rejected
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill">
                                            <i class="fa-solid fa-clock me-1"></i> Pending Review
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status === 'Approved')
                                        <div class="text-success small d-flex align-items-center gap-1">
                                            <i class="fa-solid fa-check"></i>
                                            <span>Approved & attendance updated to Full Day Present</span>
                                        </div>
                                    @elseif($req->status === 'Rejected')
                                        <div>
                                            <span class="badge bg-dark-subtle text-dark border px-2 py-0.5 rounded-pill mb-1" style="font-size: 0.7rem;">
                                                Marked as: {{ ($req->halfday == 1) ? 'Half Day' : 'Absent' }}
                                            </span>
                                            @if(!empty($req->reject_reason))
                                                <div class="text-danger small" style="font-size: 0.78rem;">
                                                    <i class="fa-solid fa-quote-left me-1 opacity-50"></i>{{ $req->reject_reason }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">Under review by HR / Manager</span>
                                    @endif
                                </td>
                                <td class="pe-3 text-end text-muted small" style="font-size: 0.75rem;">
                                    {{ $req->created_at ? $req->created_at->format('d M Y, h:i A') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <div class="avatar-lg rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                                            <i class="fa-solid fa-business-time fa-2x text-secondary opacity-50"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-secondary">No Missed Punch-Out Requests Found</h6>
                                    <p class="small text-muted mb-3">If you missed punching out on any day, you can submit a regularisation request.</p>
                                    <button class="btn btn-sm btn-primary rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#applyRegularizationModal">
                                        <i class="fa-solid fa-plus-circle me-1"></i> Submit Request
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($requests->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Apply for Missed Punch-Out / Regularization -->
<div class="modal fade" id="applyRegularizationModal" tabindex="-1" aria-labelledby="applyRegularizationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="applyRegularizationModalLabel">
                    <i class="fa-solid fa-business-time me-2"></i>Missed Punch-Out Request
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employee.missedPunchout.apply') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 small mb-3 py-2">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        Please select the date on which you forgot to punch out and specify your departure time along with a valid explanation.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Select Date <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-calendar-day text-primary"></i></span>
                            <input type="date" name="date" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-text small">Cannot select future dates.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Actual Punch-Out Time <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fa-solid fa-clock text-primary"></i></span>
                            <input type="time" name="out_time" class="form-control" value="18:30" required>
                        </div>
                        <div class="form-text small">The approximate time you departed from the workplace.</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-dark small">Reason for Missed Punch-Out <span class="text-danger">*</span></label>
                        <textarea name="reason" rows="3" class="form-control" placeholder="e.g., Forgot to punch out while leaving for client meeting / Biometric device was offline..." required minlength="5" maxlength="500"></textarea>
                        <div class="form-text small">Provide a clear explanation for your manager / company HR.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary px-3 rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                        <i class="fa-solid fa-paper-plane me-1"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
