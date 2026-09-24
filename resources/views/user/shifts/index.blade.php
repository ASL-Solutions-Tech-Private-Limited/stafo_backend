@extends('user.layouts.app')
@section('title', 'Shift List | STAFO HRMS')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            
            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Work Shifts</h3>
                    <p class="text-muted small mb-0">Define and manage company shift schedules and working hours</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('shifts.create') }}" class="btn btn-primary px-3 py-2">
                        <i class="fa-solid fa-plus me-1"></i> Add Shift
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('shifts.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="position-relative">
                            <input type="text" name="shift_name" class="form-control ps-4" value="{{ request()->get('shift_name') }}"
                                placeholder="Search shift name...">
                            <i class="fa-solid fa-clock position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <input type="text" name="start_time" class="form-control" value="{{ request()->get('start_time') }}"
                            placeholder="Start Time (e.g. 09:00 AM)">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <input type="text" name="end_time" class="form-control" value="{{ request()->get('end_time') }}"
                            placeholder="End Time (e.g. 06:00 PM)">
                    </div>

                    <div class="col-12 col-sm-6 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->filled('shift_name') || request()->filled('start_time') || request()->filled('end_time'))
                            <a href="{{ route('shifts.index') }}" class="btn btn-light border text-muted px-3" title="Clear Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Shifts Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 70px;" class="text-center">S.No</th>
                            <th style="width: 250px;">Shift Name</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th style="width: 140px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shifts as $index => $shift)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                            <i class="fa-solid fa-business-time"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $shift->shift_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-stafo badge-stafo-info">
                                        <i class="fa-regular fa-clock"></i> {{ $shift->start_time }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-stafo badge-stafo-primary">
                                        <i class="fa-regular fa-clock"></i> {{ $shift->end_time }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('shifts.show', $shift->id) }}" class="btn btn-sm btn-outline-info p-0" title="View Shift" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('shifts.edit', $shift->id) }}" class="btn btn-sm btn-outline-warning p-0" title="Edit Shift" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST" style="display:inline;" id="delete-form-{{ $shift->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Shift"
                                                onclick="confirmDelete(event, {{ $shift->id }})" style="width: 32px; height: 32px; border-radius: 8px;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-clock-rotate-left fs-2 mb-2 d-block opacity-50"></i>
                                    No shifts found. Click <strong>Add Shift</strong> to define a schedule.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        function confirmDelete(event, shiftId) {
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
                    document.getElementById(`delete-form-${shiftId}`).submit();
                }
            });
        }
    </script>
@endsection