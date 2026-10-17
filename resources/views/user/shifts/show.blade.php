@extends('user.layouts.app')

@section('title', 'Shift Details | STAFO HRMS')

@section('css')
<style>
    .day-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        font-size: 0.85rem;
        font-weight: 700;
        margin: 0 3px;
    }
    .day-pill.active {
        background-color: #4f46e5;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
    }
    .day-pill.inactive {
        background-color: #f1f5f9;
        color: #cbd5e1;
        border: 1px solid #e2e8f0;
    }
</style>
@endsection

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Shift Details</h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small">
                        <i class="fa-solid fa-clock me-1"></i> {{ $shift->shift_name }}
                    </span>
                </div>
                <p class="text-muted small mb-0">View shift schedule and working days configuration</p>
            </div>
            <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-pill">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Shifts
            </a>
        </div>

        <!-- Information Card -->
        <div class="p-4 bg-light rounded-4 border mb-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-info text-primary"></i> Schedule Overview
            </h5>

            <div class="row g-3 mb-4">
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

            <!-- Working Days -->
            <div class="p-3 bg-white rounded-3 border">
                <small class="text-muted fw-semibold d-block mb-2">Active Working Days:</small>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-1">
                        <span class="day-pill {{ (bool)$shift->monday ? 'active' : 'inactive' }}">Mon</span>
                        <span class="day-pill {{ (bool)$shift->tuesday ? 'active' : 'inactive' }}">Tue</span>
                        <span class="day-pill {{ (bool)$shift->wednesday ? 'active' : 'inactive' }}">Wed</span>
                        <span class="day-pill {{ (bool)$shift->thursday ? 'active' : 'inactive' }}">Thu</span>
                        <span class="day-pill {{ (bool)$shift->friday ? 'active' : 'inactive' }}">Fri</span>
                        <span class="day-pill {{ (bool)$shift->saturday ? 'active' : 'inactive' }}">Sat</span>
                        <span class="day-pill {{ (bool)$shift->sunday ? 'active' : 'inactive' }}">Sun</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
            <a href="{{ route('shifts.index', ['action' => 'edit', 'id' => $shift->id]) }}" class="btn btn-primary px-4 fw-bold rounded-pill">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Shift
            </a>
        </div>

    </div>
</div>
@endsection
