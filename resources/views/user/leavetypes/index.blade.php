@extends('user.layouts.app')
@section('title', 'Leave Types | STAFO HRMS')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Leave Types</h3>
                    <p class="text-muted small mb-0">Manage leave categories, entitlements, and paid/unpaid policies</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('leavetypes.create') }}" class="btn btn-primary px-3 py-2">
                        <i class="fa-solid fa-plus me-1"></i> Add Leave Type
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('leavetypes.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="position-relative">
                            <input type="text" name="name" class="form-control ps-4" placeholder="Search by leave name..."
                                value="{{ request()->get('name') }}">
                            <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
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
                        @if(request()->filled('name') || request()->filled('status'))
                            <a href="{{ route('leavetypes.index') }}" class="btn btn-light border text-muted px-3" title="Clear Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Leavetype Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 70px;" class="text-center">S.No</th>
                            <th style="width: 220px;">Leave Type</th>
                            <th>Description</th>
                            <th style="width: 120px;" class="text-center">Type</th>
                            <th style="width: 130px;" class="text-center">Status</th>
                            <th style="width: 140px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leavetypes as $index => $leavetype)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $leavetypes->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-purple bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0; background-color: rgba(139, 92, 246, 0.1);">
                                            <i class="fa-solid fa-calendar-days text-primary"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $leavetype->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $leavetype->description ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($leavetype->is_paid)
                                        <span class="badge-stafo badge-stafo-success">Paid</span>
                                    @else
                                        <span class="badge-stafo badge-stafo-warning">Unpaid</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($leavetype->status == 1)
                                        <span class="badge-stafo badge-stafo-success">
                                            <i class="fa-solid fa-circle-check"></i> Active
                                        </span>
                                    @else
                                        <span class="badge-stafo badge-stafo-danger">
                                            <i class="fa-solid fa-circle-xmark"></i> Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('leavetypes.show', $leavetype->id) }}" class="btn btn-sm btn-outline-info p-0" title="View Details" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('leavetypes.edit', $leavetype->id) }}" class="btn btn-sm btn-outline-warning p-0" title="Edit Leave Type" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('leavetypes.destroy', $leavetype->id) }}" method="POST" style="display:inline;" id="delete-form-{{ $leavetype->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Leave Type"
                                                onclick="confirmDelete(event, {{ $leavetype->id }})" style="width: 32px; height: 32px; border-radius: 8px;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-calendar-xmark fs-2 mb-2 d-block opacity-50"></i>
                                    No leave types found. Click <strong>Add Leave Type</strong> to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leavetypes->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <small class="text-muted">Showing {{ $leavetypes->firstItem() }} to {{ $leavetypes->lastItem() }} of {{ $leavetypes->total() }} results</small>
                    <div>{{ $leavetypes->links('pagination::bootstrap-4') }}</div>
                </div>
            @endif

        </div>
    </div>

    <script>
        function confirmDelete(event, leaveTypeId) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${leaveTypeId}`).submit();
                }
            });
        }
    </script>
@endsection