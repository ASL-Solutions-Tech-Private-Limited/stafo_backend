@extends('user.layouts.app')
@section('title', 'Biometric Device Authorization | STAFO HRMS')

@section('css')
<style>
    .stat-badge-card {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        padding: 1.15rem 1.35rem;
        transition: all 0.2s ease;
    }
    .stat-badge-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05);
    }
    .device-pill-code {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.76rem;
        background: #f1f5f9;
        color: #334155;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        display: inline-block;
    }
    [data-theme="dark"] .stat-badge-card {
        background: #0f172a !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .device-pill-code {
        background: #1e293b !important;
        color: #cbd5e1 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .pagination {
        margin-bottom: 0;
        gap: 4px;
    }
    .pagination .page-item .page-link {
        border-radius: 8px !important;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.4rem 0.75rem;
        transition: all 0.15s ease;
    }
    .pagination .page-item.active .page-link {
        background-color: #0284c7;
        border-color: #0284c7;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.35);
    }
    .pagination .page-item .page-link:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }
    .pagination .page-item.disabled .page-link {
        opacity: 0.5;
        background-color: #f8fafc;
    }
</style>
@endsection

@section('content')
@include('user.layouts.alert')
<div class="container-fluid p-0">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-mobile-screen-button text-primary me-2"></i> Employee Device Authorization
            </h4>
            <p class="text-muted small mb-0">Approve or reject employee mobile punch devices and prevent proxy attendance</p>
        </div>
    </div>

    <!-- Quick Stats Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('deviceList') }}" class="text-decoration-none">
                <div class="stat-badge-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">All Records</span>
                        <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary fs-5">
                            <i class="fa-solid fa-mobile-screen"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ ($pendingCount ?? 0) + ($approvedCount ?? 0) + ($rejectedCount ?? 0) }}</h3>
                    <small class="text-muted">Total devices registered</small>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('deviceList', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="stat-badge-card border-warning border-opacity-50">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-warning small fw-bold">Pending Requests</span>
                        <div class="rounded-3 p-2 bg-warning bg-opacity-15 text-warning fs-5">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ $pendingCount ?? 0 }}</h3>
                    <small class="text-warning fw-semibold">Requires your review &rarr;</small>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('deviceList', ['status' => 'approved']) }}" class="text-decoration-none">
                <div class="stat-badge-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-success small fw-semibold">Authorized Devices</span>
                        <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success fs-5">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ $approvedCount ?? 0 }}</h3>
                    <small class="text-muted">Active for mobile punch</small>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('deviceList', ['status' => 'rejected']) }}" class="text-decoration-none">
                <div class="stat-badge-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-danger small fw-semibold">Rejected Requests</span>
                        <div class="rounded-3 p-2 bg-danger bg-opacity-10 text-danger fs-5">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ $rejectedCount ?? 0 }}</h3>
                    <small class="text-muted">Denied change requests</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow-sm border-0 rounded-4">
        <!-- Filter Tabs Header -->
        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ empty($statusFilter) ? 'active' : '' }}" href="{{ route('deviceList') }}">
                        All Devices
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ $statusFilter === 'pending' ? 'active' : '' }}" href="{{ route('deviceList', ['status' => 'pending']) }}">
                        Pending Approval 
                        @if(($pendingCount ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ $statusFilter === 'approved' ? 'active' : '' }}" href="{{ route('deviceList', ['status' => 'approved']) }}">
                        Authorized
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ $statusFilter === 'rejected' ? 'active' : '' }}" href="{{ route('deviceList', ['status' => 'rejected']) }}">
                        Rejected
                    </a>
                </li>
            </ul>

            <span class="badge bg-light text-muted border small">{{ $deviceRequests->total() }} Employees</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th style="width: 60px;" class="text-center">S.No</th>
                            <th>Employee Details</th>
                            <th>Device Model</th>
                            <th>Bound Device ID</th>
                            <th>Requested Device ID</th>
                            <th>Status</th>
                            <th style="width: 180px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deviceRequests as $index => $request)
                            <tr>
                                <td class="text-center text-muted fw-semibold small">
                                    {{ $deviceRequests->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 38px; height: 38px; font-size: 0.85rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($request->name ?? 'E', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block mb-0">{{ $request->name }}</span>
                                            <small class="text-muted font-monospace">{{ $request->emp_id ?? 'EMP' }} • {{ $request->phone }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark small">
                                        {{ $request->device_name ?: 'Mobile App Device' }}
                                    </span>
                                    @if($request->android_version)
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">Android {{ $request->android_version }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($request->device_id)
                                        <span class="device-pill-code" title="{{ $request->device_id }}">
                                            {{ Str::limit($request->device_id, 16) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->latest_session && !empty($request->latest_session->employee_device_id))
                                        <span class="device-pill-code text-primary border-primary border-opacity-25" title="{{ $request->latest_session->employee_device_id }}">
                                            {{ Str::limit($request->latest_session->employee_device_id, 16) }}
                                        </span>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">{{ Carbon\Carbon::parse($request->latest_session->created_at)->diffForHumans() }}</small>
                                    @else
                                        <span class="text-muted small">--</span>
                                    @endif
                                </td>
                                <td>
                                    @php $st = strtolower($request->device_status ?? ''); @endphp
                                    @if($st === 'pending')
                                        <span class="badge bg-warning bg-opacity-20 text-dark border border-warning px-2 py-1">
                                            <i class="fa-solid fa-hourglass-half text-warning me-1"></i> Pending Review
                                        </span>
                                    @elseif(in_array($st, ['approve', 'approved']) || (!empty($request->device_id) && $st !== 'rejected'))
                                        <span class="badge bg-success bg-opacity-15 text-success px-2 py-1">
                                            <i class="fa-solid fa-circle-check me-1"></i> Authorized
                                        </span>
                                    @elseif($st === 'rejected')
                                        <span class="badge bg-danger bg-opacity-15 text-danger px-2 py-1">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> Rejected
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1">Unbound</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($st === 'pending')
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <button type="button" 
                                                    class="btn btn-sm btn-success px-2.5 py-1 fw-semibold shadow-xs" 
                                                    style="border-radius: 8px; font-size: 0.78rem;"
                                                    onclick="confirmApprove('{{ $request->id }}', '{{ addslashes($request->name) }}')">
                                                <i class="fa-solid fa-check me-1"></i> Approve
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger px-2 py-1 fw-semibold" 
                                                    style="border-radius: 8px; font-size: 0.78rem;"
                                                    onclick="confirmReject('{{ $request->id }}', '{{ addslashes($request->name) }}')">
                                                <i class="fa-solid fa-xmark me-1"></i> Reject
                                            </button>
                                        </div>
                                    @else
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-1 px-2.5" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.78rem; border-radius: 8px;">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 10px; font-size: 0.82rem;">
                                                @if($st !== 'pending')
                                                    <li>
                                                        <a class="dropdown-item text-success" href="javascript:void(0);" onclick="confirmApprove('{{ $request->id }}', '{{ addslashes($request->name) }}')">
                                                            <i class="fa-solid fa-check me-1.5"></i> Authorize / Re-approve
                                                        </a>
                                                    </li>
                                                @endif
                                                @if($request->device_id || $st)
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmReset('{{ $request->id }}', '{{ addslashes($request->name) }}')">
                                                            <i class="fa-solid fa-rotate-left me-1.5"></i> Reset Device Binding
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-2 text-muted" style="font-size: 2.5rem;">
                                        <i class="fa-solid fa-mobile-screen opacity-50"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">No device records found</h6>
                                    <p class="text-muted small mb-0">No employee device change requests match this filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($deviceRequests->hasPages())
                <div class="p-3.5 border-top d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                    <div class="text-muted small">
                        Showing <span class="fw-semibold text-dark">{{ $deviceRequests->firstItem() }}</span> to <span class="fw-semibold text-dark">{{ $deviceRequests->lastItem() }}</span> of <span class="fw-semibold text-dark">{{ $deviceRequests->total() }}</span> records
                    </div>
                    <div class="pagination-wrapper">
                        {{ $deviceRequests->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function confirmApprove(id, empName) {
        Swal.fire({
            title: 'Authorize Device?',
            text: 'Are you sure you want to approve the device binding for ' + empName + '? This will allow biometric punch-in from this device.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Yes, Authorize'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('approveDevice', '') }}/" + id;
            }
        });
    }

    function confirmReject(id, empName) {
        Swal.fire({
            title: 'Reject Request?',
            text: 'Are you sure you want to reject the device change request for ' + empName + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Reject'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('rejectDevice', '') }}/" + id;
            }
        });
    }

    function confirmReset(id, empName) {
        Swal.fire({
            title: 'Reset Device Binding?',
            text: 'This will unlink the current device for ' + empName + ', allowing them to register a fresh device.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-rotate-left me-1"></i> Yes, Reset Binding'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('resetDevice', '') }}/" + id;
            }
        });
    }
</script>
@endsection
