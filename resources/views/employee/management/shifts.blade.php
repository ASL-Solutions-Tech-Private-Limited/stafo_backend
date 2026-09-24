@extends('employee.layouts.app')

@section('title', 'Shift | Management Portal')

@section('content')
<div class="container-fluid p-0">

    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-business-time me-1"></i> Management Portal
                </span>
                <span class="text-muted small">• Work Shifts & Timings</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Shift</h3>
            <p class="text-muted small mb-0">Define, monitor, and configure company shift timings, working windows, and grace periods.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(Auth::guard('employee')->user()->hasPermission('shifts.create'))
                <button type="button" class="btn btn-primary px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addShiftModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Shift
                </button>
            @endif
            <span class="badge bg-light text-dark border px-3 py-2 rounded-3 shadow-2xs font-sans">
                Role: <strong class="text-primary ms-1">{{ Auth::guard('employee')->user()->designation->name ?? 'Management' }}</strong>
            </span>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('employee.management.shifts') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="position-relative">
                            <input type="text" name="shift_name" class="form-control ps-4" value="{{ request()->get('shift_name') }}"
                                placeholder="Search shift name...">
                            <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->filled('shift_name'))
                            <a href="{{ route('employee.management.shifts') }}" class="btn btn-light border text-muted px-3" title="Clear Filter">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bs-card-bg, #ffffff);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark" style="background: #0f172a;">
                    <tr class="text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <th style="width: 70px;" class="text-center">S.No</th>
                        <th style="width: 250px;">Shift Name</th>
                        <th style="width: 180px;">Start Time</th>
                        <th style="width: 180px;">End Time</th>
                        <th style="width: 150px;">Grace Time</th>
                        <th style="width: 120px;" class="text-center">Status</th>
                        @if(Auth::guard('employee')->user()->hasPermission('shifts.edit') || Auth::guard('employee')->user()->hasPermission('shifts.delete'))
                            <th style="width: 120px;" class="text-center">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $canEdit = Auth::guard('employee')->user()->hasPermission('shifts.edit');
                        $canDelete = Auth::guard('employee')->user()->hasPermission('shifts.delete');
                    @endphp
                    @forelse ($shifts as $index => $shift)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $shifts->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 38px; height: 38px; font-size: 0.95rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-business-time"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block lh-sm">{{ $shift->shift_name }}</span>
                                        <small class="text-muted font-monospace" style="font-size: 10px;">ID: SH-{{ str_pad($shift->id, 4, '0', STR_PAD_LEFT) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-semibold font-monospace" style="font-size: 0.78rem;">
                                    <i class="fa-regular fa-clock me-1"></i> {{ $shift->start_time }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 fw-semibold font-monospace" style="font-size: 0.78rem;">
                                    <i class="fa-regular fa-clock me-1"></i> {{ $shift->end_time }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-hourglass-start me-1"></i> {{ $shift->grace_time ?? '0' }} mins
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($shift->status == 1 || $shift->status === 'active')
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
                                            <button type="button" class="btn btn-sm btn-outline-warning p-0" title="Edit Shift" data-bs-toggle="modal" data-bs-target="#editShiftModal_{{ $shift->id }}" style="width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        @endif
                                        @if($canDelete)
                                            <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Shift" onclick="confirmDelete(event, {{ $shift->id }})" style="width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                            <form id="delete-form-{{ $shift->id }}" action="{{ route('employee.management.shifts.destroy', $shift->id) }}" method="POST" style="display: none;">
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
                            <td colspan="{{ ($canEdit || $canDelete) ? 7 : 6 }}" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-clock-rotate-left fs-2 mb-2 d-block opacity-40"></i>
                                No shift records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shifts->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 p-3 border-top">
                <small class="text-muted">Showing {{ $shifts->firstItem() }} to {{ $shifts->lastItem() }} of {{ $shifts->total() }} entries</small>
                <div>{{ $shifts->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif
    </div>

</div>

<!-- Modal: Add Shift (guarded by shifts.create) -->
@if(Auth::guard('employee')->user()->hasPermission('shifts.create'))
<div class="modal fade" id="addShiftModal" tabindex="-1" aria-labelledby="addShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-business-time fs-5"></i>
                    <h5 class="modal-title fw-bold" id="addShiftModalLabel">Create New Shift</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('employee.management.shifts.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="add_shift_name" class="form-label fw-semibold text-dark">
                            Shift Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="shift_name" id="add_shift_name" class="form-control" placeholder="e.g. Morning Shift, General Shift" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="add_start_time" class="form-label fw-semibold text-dark">
                                Start Time <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="start_time" id="add_start_time" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label for="add_end_time" class="form-label fw-semibold text-dark">
                                End Time <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="end_time" id="add_end_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="add_grace_time" class="form-label fw-semibold text-dark">
                            Grace Time (Minutes)
                        </label>
                        <input type="number" name="grace_time" id="add_grace_time" class="form-control" placeholder="e.g. 15" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Shift
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modals: Edit Shift (guarded by shifts.edit) -->
@if(Auth::guard('employee')->user()->hasPermission('shifts.edit'))
    @foreach ($shifts as $shift)
    <div class="modal fade" id="editShiftModal_{{ $shift->id }}" tabindex="-1" aria-labelledby="editShiftModalLabel_{{ $shift->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-warning text-dark py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-pen-to-square fs-5"></i>
                        <h5 class="modal-title fw-bold" id="editShiftModalLabel_{{ $shift->id }}">Edit Shift</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('employee.management.shifts.update', $shift->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="edit_shift_name_{{ $shift->id }}" class="form-label fw-semibold text-dark">
                                Shift Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="shift_name" id="edit_shift_name_{{ $shift->id }}" class="form-control" value="{{ $shift->shift_name }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="edit_start_time_{{ $shift->id }}" class="form-label fw-semibold text-dark">
                                    Start Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="start_time" id="edit_start_time_{{ $shift->id }}" class="form-control" value="{{ !empty($shift->start_time) ? date('H:i', strtotime($shift->start_time)) : '' }}" required>
                            </div>
                            <div class="col-6">
                                <label for="edit_end_time_{{ $shift->id }}" class="form-label fw-semibold text-dark">
                                    End Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="end_time" id="edit_end_time_{{ $shift->id }}" class="form-control" value="{{ !empty($shift->end_time) ? date('H:i', strtotime($shift->end_time)) : '' }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_grace_time_{{ $shift->id }}" class="form-label fw-semibold text-dark">
                                Grace Time (Minutes)
                            </label>
                            <input type="number" name="grace_time" id="edit_grace_time_{{ $shift->id }}" class="form-control" value="{{ $shift->grace_time ?? 0 }}" min="0">
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Update Shift
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

@if(Auth::guard('employee')->user()->hasPermission('shifts.delete'))
<script>
    function confirmDelete(event, shiftId) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this shift!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${shiftId}`).submit();
            }
        });
    }
</script>
@endif
@endsection
