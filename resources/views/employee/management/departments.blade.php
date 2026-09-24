@extends('employee.layouts.app')

@section('title', 'Department | Management Portal')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-layer-group me-1"></i> Management Portal
                </span>
                <span class="text-muted small">• Organization Units</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Department</h3>
            <p class="text-muted small mb-0">Overview and management of operational departments and active team assignments.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(Auth::guard('employee')->user()->hasPermission('departments.create'))
                <button type="button" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addDeptModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Department
                </button>
            @endif
            <span class="badge bg-light text-dark border px-3 py-2 rounded-3 shadow-2xs font-sans">
                Role: <strong class="text-info ms-1">{{ Auth::guard('employee')->user()->designation->name ?? 'Management' }}</strong>
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
                        <th style="width: 260px;">Department Name</th>
                        <th>Description</th>
                        <th style="width: 140px;" class="text-center">Staff Count</th>
                        <th style="width: 120px;" class="text-center">Status</th>
                        @if(Auth::guard('employee')->user()->hasPermission('departments.edit') || Auth::guard('employee')->user()->hasPermission('departments.delete'))
                            <th style="width: 120px;" class="text-center">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $canEdit = Auth::guard('employee')->user()->hasPermission('departments.edit');
                        $canDelete = Auth::guard('employee')->user()->hasPermission('departments.delete');
                    @endphp
                    @forelse ($departments as $index => $dept)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $departments->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info fw-bold" style="width: 38px; height: 38px; font-size: 0.95rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block lh-sm">{{ $dept->name }}</span>
                                        <small class="text-muted font-monospace" style="font-size: 10px;">CODE: DEPT-{{ str_pad($dept->id, 3, '0', STR_PAD_LEFT) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary small">
                                    {{ $dept->description ?? 'Operational division' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-users me-1"></i> {{ $dept->employees_count ?? 0 }} Staff
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($dept->status == 1)
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
                                            <button type="button" class="btn btn-sm btn-outline-warning p-0" title="Edit Department" data-bs-toggle="modal" data-bs-target="#editDeptModal_{{ $dept->id }}" style="width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        @endif
                                        @if($canDelete)
                                            <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Department" onclick="confirmDelete(event, {{ $dept->id }})" style="width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                            <form id="delete-form-{{ $dept->id }}" action="{{ route('employee.management.departments.destroy', $dept->id) }}" method="POST" style="display: none;">
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
                                <i class="fa-solid fa-layer-group fs-2 mb-2 d-block opacity-40"></i>
                                No department records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($departments->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 p-3 border-top">
                <small class="text-muted">Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }} of {{ $departments->total() }} entries</small>
                <div>{{ $departments->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif
    </div>

</div>

<!-- Modal: Add Department (guarded by departments.create) -->
@if(Auth::guard('employee')->user()->hasPermission('departments.create'))
<div class="modal fade" id="addDeptModal" tabindex="-1" aria-labelledby="addDeptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-layer-group fs-5"></i>
                    <h5 class="modal-title fw-bold" id="addDeptModalLabel">Create New Department</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employee.management.departments.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add_dept_name" class="form-label fw-semibold text-dark">
                            Department Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="add_dept_name" class="form-control" placeholder="e.g. Human Resources, Engineering" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_dept_status" class="form-label fw-semibold text-dark">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" id="add_dept_status" class="form-select" required>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="add_dept_description" class="form-label fw-semibold text-dark">
                            Description <span class="text-muted small">(Optional)</span>
                        </label>
                        <textarea name="description" id="add_dept_description" rows="3" class="form-control" placeholder="Briefly describe department functions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Department
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modals: Edit Department (guarded by departments.edit) -->
@if(Auth::guard('employee')->user()->hasPermission('departments.edit'))
    @foreach ($departments as $dept)
    <div class="modal fade" id="editDeptModal_{{ $dept->id }}" tabindex="-1" aria-labelledby="editDeptModalLabel_{{ $dept->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-warning text-dark py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-pen-to-square fs-5"></i>
                        <h5 class="modal-title fw-bold" id="editDeptModalLabel_{{ $dept->id }}">Edit Department</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('employee.management.departments.update', $dept->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="edit_dept_name_{{ $dept->id }}" class="form-label fw-semibold text-dark">
                                Department Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="edit_dept_name_{{ $dept->id }}" class="form-control" value="{{ $dept->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_dept_status_{{ $dept->id }}" class="form-label fw-semibold text-dark">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="edit_dept_status_{{ $dept->id }}" class="form-select" required>
                                <option value="1" {{ $dept->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $dept->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edit_dept_description_{{ $dept->id }}" class="form-label fw-semibold text-dark">
                                Description <span class="text-muted small">(Optional)</span>
                            </label>
                            <textarea name="description" id="edit_dept_description_{{ $dept->id }}" rows="3" class="form-control">{{ $dept->description }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

@if(Auth::guard('employee')->user()->hasPermission('departments.delete'))
<script>
    function confirmDelete(event, deptId) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this department!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${deptId}`).submit();
            }
        });
    }
</script>
@endif
@endsection
