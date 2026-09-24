@extends('user.layouts.app')

@section('title', 'Leave Type Details | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Leave Type Details</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-plane-departure me-1"></i> {{ $leavetype->name }}
                    </span>
                </div>
                <p class="text-muted small mb-0">Review leave policy details, quota allocations, and compensation status</p>
            </div>
            <a href="{{ route('leavetypes.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Leave Types
            </a>
        </div>

        <!-- Information Card -->
        <div class="p-4 bg-light rounded-4 border mb-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-info text-primary"></i> Policy Overview
            </h5>

            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Leave Type Name</small>
                        <span class="fw-bold text-dark fs-6">{{ $leavetype->name }}</span>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Annual Allowance</small>
                        <span class="fw-bold text-dark fs-6">{{ $leavetype->no_of_days ?? '0' }} Days / Year</span>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Compensation</small>
                        @if($leavetype->is_paid)
                            <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-check me-1"></i> Paid Leave</span>
                        @else
                            <span class="badge-stafo badge-stafo-warning"><i class="fa-solid fa-ban me-1"></i> Unpaid (LWP)</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Status</small>
                        @if($leavetype->status)
                            <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
                        @else
                            <span class="badge-stafo badge-stafo-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="col-12">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Policy Description & Rules</small>
                        <p class="text-dark mb-0">{{ $leavetype->description ?? 'No specific policy description provided.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
            <a href="{{ route('leavetypes.edit', $leavetype->id) }}" class="btn btn-warning px-4 fw-bold">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Leave Type
            </a>
            <form action="{{ route('leavetypes.destroy', $leavetype->id) }}" method="POST" style="display:inline;" id="delete-leavetype-{{ $leavetype->id }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger px-4" onclick="confirmDelete(event, {{ $leavetype->id }})">
                    <i class="fa-solid fa-trash me-1"></i> Delete
                </button>
            </form>
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    function confirmDelete(event, id) {
        event.preventDefault();
        Swal.fire({
            title: 'Delete Leave Type?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-leavetype-${id}`).submit();
            }
        });
    }
</script>
@endsection