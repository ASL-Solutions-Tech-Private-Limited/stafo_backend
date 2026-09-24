@extends('employee.layouts.app')

@section('title', 'Edit Staff Attendance | Management Portal')

@section('content')
<div class="container-fluid p-0">
    <div class="card shadow-sm border-0 rounded-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Management Action
                        </span>
                        <h4 class="fw-bold text-dark mb-0">Edit Attendance Record</h4>
                    </div>
                    <p class="text-muted small mb-0">Modify employee attendance status and punch in/out timestamps for {{ $attendance->date }}</p>
                </div>
                <a href="{{ route('employee.management.attendance') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Attendance List
                </a>
            </div>

            <form action="{{ route('employee.management.attendance.update', $attendance->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Organization & Employee Information -->
                <div class="p-4 bg-light rounded-4 border mb-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-user-check text-primary"></i> Employee & Status Details
                    </h6>

                    <div class="row g-3">
                        <!-- Employee Dropdown -->
                        <div class="col-12 col-md-6">
                            <label for="employee_id" class="form-label fw-semibold text-dark small">
                                Employee <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" id="employee_id" required>
                                <option value="">-- Select Employee --</option>
                                @foreach ($employees as $empOpt)
                                    <option value="{{ $empOpt->id }}"
                                        {{ old('employee_id', $attendance->employee_id) == $empOpt->id ? 'selected' : '' }}>
                                        {{ $empOpt->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Attendance Status -->
                        <div class="col-12 col-md-6">
                            <label for="attendance" class="form-label fw-semibold text-dark small">
                                Attendance Status <span class="text-danger">*</span>
                            </label>
                            <select name="attendance" id="attendance" class="form-select @error('attendance') is-invalid @enderror" required>
                                <option value="Present" {{ old('attendance', $attendance->attendance) == 'Present' ? 'selected' : '' }}>Present</option>
                                <option value="Absent" {{ old('attendance', $attendance->attendance) == 'Absent' ? 'selected' : '' }}>Absent</option>
                                <option value="Leave" {{ old('attendance', $attendance->attendance) == 'Leave' ? 'selected' : '' }}>Leave</option>
                            </select>
                            @error('attendance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Branch Dropdown -->
                        <div class="col-12 col-md-6">
                            <label for="branch_id" class="form-label fw-semibold text-dark small">
                                Branch <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('branch_id') is-invalid @enderror" name="branch_id" id="branch_id" required>
                                <option value="">-- Select Branch --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id', $attendance->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Department Dropdown -->
                        <div class="col-12 col-md-6">
                            <label for="department_id" class="form-label fw-semibold text-dark small">
                                Department <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('department_id') is-invalid @enderror" name="department_id" id="department_id" required>
                                <option value="">-- Select Department --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ old('department_id', $attendance->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Timing & Schedule Information -->
                <div class="p-4 bg-light rounded-4 border mb-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-clock text-primary"></i> Date & Timing Information
                    </h6>

                    <div class="row g-3">
                        <!-- Date Field -->
                        <div class="col-12 col-md-4">
                            <label for="date" class="form-label fw-semibold text-dark small">
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror"
                                name="date" id="date" value="{{ old('date', $attendance->date) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- In Time -->
                        <div class="col-12 col-md-4">
                            <label for="in_time" class="form-label fw-semibold text-dark small">In Time</label>
                            <input type="text" class="form-control font-monospace @error('in_time') is-invalid @enderror"
                                name="in_time" id="in_time" placeholder="HH:MM:SS"
                                value="{{ old('in_time', $attendance->in_time) }}">
                            @error('in_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Out Time -->
                        <div class="col-12 col-md-4">
                            <label for="out_time" class="form-label fw-semibold text-dark small">Out Time</label>
                            <input type="text" class="form-control font-monospace @error('out_time') is-invalid @enderror"
                                name="out_time" id="out_time" placeholder="HH:MM:SS"
                                value="{{ old('out_time', $attendance->out_time) }}">
                            @error('out_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Half Day Checkbox -->
                        <div class="col-12 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="halfday" id="halfday" value="1"
                                    {{ old('halfday', $attendance->halfday) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-dark small" for="halfday">
                                    Mark as Half Day Attendance
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex align-items-center justify-content-end gap-2 pt-2">
                    <a href="{{ route('employee.management.attendance') }}" class="btn btn-light border px-4 py-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                        <i class="fa-solid fa-save me-1"></i> Update Attendance Record
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
