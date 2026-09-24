@extends('user.layouts.app')
@section('title', 'Compoff Leave List | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Comp-Off Leaves</h3>
                    <p class="text-muted small mb-0">Manage compensatory off grant requests and approvals</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('compoffleaves.create') }}" class="btn btn-primary px-3 py-2">
                        <i class="fa-solid fa-plus me-1"></i> Add Comp-Off
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('compoffleaves.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="position-relative">
                            <input type="text" name="name" class="form-control ps-4" placeholder="Search by employee name..."
                                value="{{ request()->get('name') }}">
                            <i class="fa-solid fa-user position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request()->get('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ request()->get('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ request()->get('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->filled('name') || request()->filled('status'))
                            <a href="{{ route('compoffleaves.index') }}" class="btn btn-light border text-muted px-3" title="Clear Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- CompoffLeave Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 70px;" class="text-center">S.No</th>
                            <th style="width: 220px;">Employee</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th style="width: 130px;" class="text-center">Status</th>
                            <th style="width: 140px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($compoffleaves as $index => $compoffleave)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $compoffleaves->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 34px; height: 34px; font-size: 0.8rem;">
                                            {{ strtoupper(substr($compoffleave->employee->name ?? 'E', 0, 2)) }}
                                        </div>
                                        <span class="fw-bold text-dark">{{ $compoffleave->employee->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $compoffleave->title }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $compoffleave->description ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($compoffleave->status == 'Approved')
                                        <span class="badge-stafo badge-stafo-success">
                                            <i class="fa-solid fa-check-circle"></i> Approved
                                        </span>
                                    @elseif ($compoffleave->status == 'Pending')
                                        <span class="badge-stafo badge-stafo-warning">
                                            <i class="fa-solid fa-clock"></i> Pending
                                        </span>
                                    @else
                                        <span class="badge-stafo badge-stafo-danger">
                                            <i class="fa-solid fa-circle-xmark"></i> Rejected
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('compoffleaves.show', $compoffleave->id) }}" class="btn btn-sm btn-outline-info p-0" title="View Details" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('compoffleaves.edit', $compoffleave->id) }}" class="btn btn-sm btn-outline-warning p-0" title="Edit Request" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('compoffleaves.destroy', $compoffleave->id) }}" method="POST" style="display:inline;" id="delete-form-{{ $compoffleave->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Request"
                                                onclick="confirmDelete(event, {{ $compoffleave->id }})" style="width: 32px; height: 32px; border-radius: 8px;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-business-time fs-2 mb-2 d-block opacity-50"></i>
                                    No comp-off leave requests found. Click <strong>Add Comp-Off</strong> to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($compoffleaves->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <small class="text-muted">Showing {{ $compoffleaves->firstItem() }} to {{ $compoffleaves->lastItem() }} of {{ $compoffleaves->total() }} results</small>
                    <div>{{ $compoffleaves->links('pagination::bootstrap-4') }}</div>
                </div>
            @endif

        </div>
    </div>

    <script>
        function confirmDelete(event, compoffleaveId) {
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
                    document.getElementById(`delete-form-${compoffleaveId}`).submit();
                }
            });
        }
    </script>
@endsection