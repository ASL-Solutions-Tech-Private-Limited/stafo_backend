@extends('user.layouts.app')

@section('title', 'Shift Details | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Shift Details</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-clock me-1"></i> {{ $shift->shift_name }}
                    </span>
                </div>
                <p class="text-muted small mb-0">View shift schedule and timing configuration</p>
            </div>
            <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Shifts
            </a>
        </div>

        <!-- Information Card -->
        <div class="p-4 bg-light rounded-4 border mb-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-info text-primary"></i> Schedule Overview
            </h5>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Shift Name</small>
                        <span class="fw-bold text-dark fs-6">{{ $shift->shift_name }}</span>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-3 bg-white rounded-3 border">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-right-to-bracket text-success"></i>
                            <small class="text-muted">Start Time</small>
                        </div>
                        <span class="fw-bold text-dark fs-6">{{ $shift->start_time }}</span>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-3 bg-white rounded-3 border">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-right-from-bracket text-danger"></i>
                            <small class="text-muted">End Time</small>
                        </div>
                        <span class="fw-bold text-dark fs-6">{{ $shift->end_time }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
            <a href="{{ route('shifts.edit', $shift->id) }}" class="btn btn-warning px-4 fw-bold">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Shift
            </a>
        </div>

    </div>
</div>
@endsection
