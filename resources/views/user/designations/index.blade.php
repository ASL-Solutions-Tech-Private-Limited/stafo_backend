@extends('user.layouts.app')
@section('title', 'Designations | STAFO HRMS')

@section('content')
<style>
    [data-bs-theme="dark"] .text-dark {
        color: #f1f5f9 !important;
    }
    [data-bs-theme="dark"] .bg-light {
        background-color: rgba(255, 255, 255, 0.04) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-bs-theme="dark"] .card {
        background-color: #1e293b;
        border-color: #334155 !important;
    }
    [data-bs-theme="dark"] .table {
        --bs-table-bg: transparent;
        --bs-table-color: #cbd5e1;
        --bs-table-hover-bg: rgba(255, 255, 255, 0.03);
        --bs-table-border-color: #334155;
    }
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select {
        background-color: #0f172a;
        border-color: #334155;
        color: #f8fafc;
    }
    [data-bs-theme="dark"] .border {
        border-color: #334155 !important;
    }
    .badge-soft-primary {
        background-color: rgba(59, 130, 246, 0.12);
        color: #3b82f6;
    }
</style>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-id-badge text-primary me-2"></i>Company Designations
                    </h3>
                    <p class="text-muted small mb-0">Manage job roles, professional titles, and hierarchy for your workforce</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('company-roles.index') }}" class="btn btn-outline-primary px-3 py-2 fw-semibold">
                        <i class="fa-solid fa-shield-halved me-1"></i> Configure Role Permissions
                    </a>
                    <a href="{{ route('designations.create') }}" class="btn btn-primary px-3 py-2 fw-semibold">
                        <i class="fa-solid fa-plus me-1"></i> Add Designation
                    </a>
                </div>
            </div>

            <!-- Informational Note: Designation is the Role -->
            <div class="alert alert-info border-0 shadow-xs rounded-3 mb-4 d-flex align-items-center gap-3 p-3" style="background: rgba(14, 165, 233, 0.08); border-left: 4px solid #0ea5e9 !important;">
                <i class="fa-solid fa-circle-info fs-5 text-info"></i>
                <div class="small">
                    <strong class="text-dark">Designation is the Employee's Role:</strong> <span class="text-body">Employees holding a designation automatically receive that designation's administrative permissions. You can configure and manage permissions for any designation in</span> <a href="{{ route('company-roles.index') }}" class="fw-bold text-primary text-decoration-none">Roles & Permissions</a>.
                </div>
            </div>

            <!-- Metric Summary Mini Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-md-4">
                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Designations</small>
                            <h4 class="fw-bold text-dark mb-0 mt-1">{{ $totalCount ?? $designations->total() }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2.5">
                            <i class="fa-solid fa-briefcase fs-5"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Active Designations</small>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $activeCount ?? 0 }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2.5">
                            <i class="fa-solid fa-circle-check fs-5"></i>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Assigned Staff</small>
                            <h4 class="fw-bold text-info mb-0 mt-1">
                                {{ $designations->sum('employees_count') }}
                            </h4>
                        </div>
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2.5">
                            <i class="fa-solid fa-users fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('designations.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="position-relative">
                            <input type="text" name="search" class="form-control ps-4" placeholder="Search designations by title..."
                                value="{{ request()->get('search') }}">
                        </div>
                    </div>

                    <div class="col-12 col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request()->get('status') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request()->get('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->filled('search') || request()->filled('status'))
                            <a href="{{ route('designations.index') }}" class="btn btn-light border text-muted px-3" title="Clear Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Designation Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 60px;" class="text-center">#</th>
                            <th style="min-width: 200px;">Designation / Role</th>
                            <th>Description</th>
                            <th style="width: 140px;" class="text-center">Assigned Employees</th>
                            <th style="width: 150px;" class="text-center">Role Permissions</th>
                            <th style="width: 120px;" class="text-center">Status</th>
                            <th style="width: 160px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($designations as $index => $designation)
                            <tr>
                                <td class="text-center text-muted fw-semibold">
                                    {{ $designations->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" 
                                             style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                            <i class="fa-solid fa-id-card"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block text-body">{{ $designation->name }}</strong>
                                            <small class="text-muted">Created {{ $designation->created_at->format('d M, Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $designation->description ?: 'No description specified.' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 font-monospace">
                                        <i class="fa-solid fa-users text-primary me-1"></i> {{ $designation->employees_count }} Staff
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('company-roles.index', ['designation_id' => $designation->id]) }}" 
                                       class="badge {{ $designation->permissions->count() > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary border' }} px-2.5 py-1.5 text-decoration-none"
                                       title="Manage permissions for this role">
                                        <i class="fa-solid fa-shield-halved me-1"></i>
                                        {{ $designation->permissions->count() }} Perms
                                    </a>
                                </td>
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-sm btn-status-toggle {{ $designation->status ? 'btn-outline-success' : 'btn-outline-secondary' }} px-2.5 py-1"
                                            onclick="toggleDesignationStatus({{ $designation->id }}, this)">
                                        <i class="fa-solid {{ $designation->status ? 'fa-circle-check text-success' : 'fa-circle-pause text-muted' }} me-1"></i>
                                        <span>{{ $designation->status ? 'Active' : 'Inactive' }}</span>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('company-roles.index', ['designation_id' => $designation->id]) }}" 
                                           class="btn btn-sm btn-outline-info px-2.5 py-1" 
                                           title="Configure Permissions">
                                            <i class="fa-solid fa-shield-halved"></i>
                                        </a>
                                        <a href="{{ route('designations.edit', $designation->id) }}" 
                                           class="btn btn-sm btn-outline-primary px-2.5 py-1" title="Edit Designation">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger px-2.5 py-1" 
                                                onclick="confirmDeleteDesignation({{ $designation->id }}, '{{ addslashes($designation->name) }}', {{ $designation->employees_count }})"
                                                title="Delete Designation">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        <form id="delete-designation-form-{{ $designation->id }}" 
                                              action="{{ route('designations.destroy', $designation->id) }}" 
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa-solid fa-id-badge fs-1 d-block mb-3 opacity-25"></i>
                                        <h6 class="fw-bold mb-1">No Designations Found</h6>
                                        <p class="small text-muted mb-3">Create designations to organize roles and assign them to your employees.</p>
                                        <a href="{{ route('designations.create') }}" class="btn btn-primary btn-sm px-3 py-1.5">
                                            <i class="fa-solid fa-plus me-1"></i> Add First Designation
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($designations->hasPages())
                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Showing {{ $designations->firstItem() }} to {{ $designations->lastItem() }} of {{ $designations->total() }} designations
                    </small>
                    <div>
                        {{ $designations->links() }}
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        function toggleDesignationStatus(id, btn) {
            btn.disabled = true;
            $.ajax({
                url: "{{ url('company/designations') }}/" + id + "/toggle-status",
                type: "PATCH",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    btn.disabled = false;
                    if (res.status) {
                        if (res.new_status == 1) {
                            btn.className = "btn btn-sm btn-status-toggle btn-outline-success px-2.5 py-1";
                            btn.innerHTML = '<i class="fa-solid fa-circle-check text-success me-1"></i><span>Active</span>';
                        } else {
                            btn.className = "btn btn-sm btn-status-toggle btn-outline-secondary px-2.5 py-1";
                            btn.innerHTML = '<i class="fa-solid fa-circle-pause text-muted me-1"></i><span>Inactive</span>';
                        }
                    }
                },
                error: function () {
                    btn.disabled = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to update status.'
                    });
                }
            });
        }

        function confirmDeleteDesignation(id, name, count) {
            let text = `Are you sure you want to delete the designation "${name}"?`;
            if (count > 0) {
                text += ` Note: ${count} employee(s) currently hold this designation and will be detached safely.`;
            }

            Swal.fire({
                title: 'Delete Designation?',
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-designation-form-' + id).submit();
                }
            });
        }
    </script>
@endsection
