@extends('employee.layouts.app')

@section('title', 'Branch | Management Portal')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-building me-1"></i> Management Portal
                </span>
                <span class="text-muted small">• Company Locations</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Branch</h3>
            <p class="text-muted small mb-0">Overview and management of company branch offices and operational locations.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(Auth::guard('employee')->user()->hasPermission('branches.create'))
                <button type="button" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Branch
                </button>
            @endif
            <span class="badge bg-light text-dark border px-3 py-2 rounded-3 shadow-2xs font-sans">
                Role: <strong class="text-success ms-1">{{ Auth::guard('employee')->user()->designation->name ?? 'Management' }}</strong>
            </span>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bs-card-bg, #ffffff);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark" style="background: #0f172a;">
                    <tr class="text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <th style="width: 70px;" class="text-center">S.No</th>
                        <th style="width: 260px;">Branch Name</th>
                        <th>Address</th>
                        <th style="width: 140px;" class="text-center">Staff Count</th>
                        <th style="width: 120px;" class="text-center">Status</th>
                        @if(Auth::guard('employee')->user()->hasPermission('branches.edit') || Auth::guard('employee')->user()->hasPermission('branches.delete'))
                            <th style="width: 120px;" class="text-center">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $canEdit = Auth::guard('employee')->user()->hasPermission('branches.edit');
                        $canDelete = Auth::guard('employee')->user()->hasPermission('branches.delete');
                    @endphp
                    @forelse ($branches as $index => $branch)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $branches->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success fw-bold" style="width: 38px; height: 38px; font-size: 0.95rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-building"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block lh-sm">{{ $branch->branch_name }}</span>
                                        <small class="text-muted font-monospace" style="font-size: 10px;">ID: BR-{{ str_pad($branch->id, 4, '0', STR_PAD_LEFT) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary small">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i>
                                    {{ $branch->branch_address ?? 'Not specified' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-users me-1"></i> {{ $branch->employees_count ?? 0 }} Staff
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($branch->status == 1)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            @if($canEdit || $canDelete)
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        @if($canEdit)
                                            <button type="button" class="btn btn-sm btn-outline-warning p-0" title="Edit Branch" data-bs-toggle="modal" data-bs-target="#editBranchModal_{{ $branch->id }}" style="width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        @endif
                                        @if($canDelete)
                                            <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Branch" onclick="confirmDelete(event, {{ $branch->id }})" style="width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                            <form id="delete-form-{{ $branch->id }}" action="{{ route('employee.management.branches.destroy', $branch->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ ($canEdit || $canDelete) ? 6 : 5 }}" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-building-circle-xmark fs-2 mb-2 d-block opacity-40"></i>
                                No branch locations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($branches->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 p-3 border-top">
                <small class="text-muted">Showing {{ $branches->firstItem() }} to {{ $branches->lastItem() }} of {{ $branches->total() }} entries</small>
                <div>{{ $branches->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif
    </div>

</div>

<!-- Modal: Add Branch (guarded by branches.create) -->
@if(Auth::guard('employee')->user()->hasPermission('branches.create'))
<div class="modal fade" id="addBranchModal" tabindex="-1" aria-labelledby="addBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-building fs-5"></i>
                    <h5 class="modal-title fw-bold" id="addBranchModalLabel">Create New Branch</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employee.management.branches.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add_branch_name" class="form-label fw-semibold text-dark">
                            Branch Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="branch_name" id="add_branch_name" class="form-control" placeholder="e.g. Head Office, Mumbai Branch" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_status" class="form-label fw-semibold text-dark">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" id="add_status" class="form-select" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_branch_address" class="form-label fw-semibold text-dark">
                            Branch Address <span class="text-danger">*</span>
                        </label>
                        <textarea name="branch_address" id="add_branch_address" rows="3" class="form-control" placeholder="Full street address, city, state, pincode" required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Branch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modals: Edit Branch (guarded by branches.edit) -->
@if(Auth::guard('employee')->user()->hasPermission('branches.edit'))
    @foreach ($branches as $branch)
    <div class="modal fade" id="editBranchModal_{{ $branch->id }}" tabindex="-1" aria-labelledby="editBranchModalLabel_{{ $branch->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-warning text-dark py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-pen-to-square fs-5"></i>
                        <h5 class="modal-title fw-bold" id="editBranchModalLabel_{{ $branch->id }}">Edit Branch</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('employee.management.branches.update', $branch->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="edit_branch_name_{{ $branch->id }}" class="form-label fw-semibold text-dark">
                                Branch Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="branch_name" id="edit_branch_name_{{ $branch->id }}" class="form-control" value="{{ $branch->branch_name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_status_{{ $branch->id }}" class="form-label fw-semibold text-dark">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="edit_status_{{ $branch->id }}" class="form-select" required>
                                <option value="active" {{ $branch->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $branch->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_branch_address_{{ $branch->id }}" class="form-label fw-semibold text-dark">
                                Branch Address <span class="text-danger">*</span>
                            </label>
                            <textarea name="branch_address" id="edit_branch_address_{{ $branch->id }}" rows="3" class="form-control" required>{{ $branch->branch_address }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Branch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

@if(Auth::guard('employee')->user()->hasPermission('branches.delete'))
<script>
    function confirmDelete(event, branchId) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this branch!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${branchId}`).submit();
            }
        });
    }
</script>
@endif
@endsection
