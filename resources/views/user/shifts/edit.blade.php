@extends('user.layouts.app')

@section('title', 'Edit Shift | STAFO HRMS')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.0/jquery.timepicker.min.css">
@endsection

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Edit Shift</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-clock me-1"></i> {{ $shift->shift_name }}
                    </span>
                </div>
                <p class="text-muted small mb-0">Modify shift name, start time, and end time</p>
            </div>
            <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Shifts
            </a>
        </div>

        <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Form Card -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-business-time text-primary"></i> Shift Configuration
                </h5>

                <div class="row g-3">
                    <!-- Shift Name -->
                    <div class="col-12 col-md-4">
                        <label for="shift_name" class="form-label fw-semibold text-dark">
                            Shift Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="shift_name" id="shift_name" class="form-control @error('shift_name') is-invalid @enderror"
                               value="{{ old('shift_name', $shift->shift_name) }}" placeholder="e.g. Morning Shift, Night Shift" required>
                        @error('shift_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Start Time -->
                    <div class="col-12 col-md-4">
                        <label for="start_time" class="form-label fw-semibold text-dark">
                            Start Time <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-regular fa-clock text-muted"></i></span>
                            <input type="text" name="start_time" id="start_time" class="form-control timepicker @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time', $shift->start_time) }}" placeholder="e.g. 09:00 AM" required>
                        </div>
                        @error('start_time')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End Time -->
                    <div class="col-12 col-md-4">
                        <label for="end_time" class="form-label fw-semibold text-dark">
                            End Time <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-regular fa-clock text-muted"></i></span>
                            <input type="text" name="end_time" id="end_time" class="form-control timepicker @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time', $shift->end_time) }}" placeholder="e.g. 06:00 PM" required>
                        </div>
                        @error('end_time')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Shift
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.0/jquery.timepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#start_time, #end_time').timepicker({
                timeFormat: 'h:i A',
                dynamic: true,
                dropdown: true,
                scrollbar: true,
                defaultTime: false,
                show2400: false,
            });
        });
    </script>
@endsection
