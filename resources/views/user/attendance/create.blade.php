@extends('user.layouts.app')

@section('title', 'Add Attendance Record | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Add Attendance Record</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-calendar-check me-1"></i> Manual Entry
                    </span>
                </div>
                <p class="text-muted small mb-0">Log individual employee attendance status and punch timing manually</p>
            </div>
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Attendance List
            </a>
        </div>

        <form action="{{ route('attendance.store') }}" method="POST">
            @csrf

            <!-- Organization & Employee Information -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-check text-primary"></i> Employee & Status Details
                </h5>

                <div class="row g-3">
                    <!-- Employee Dropdown -->
                    <div class="col-12 col-md-6">
                        <label for="employee_id" class="form-label fw-semibold text-dark">
                            Employee <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" id="employee_id" required>
                            <option value="">-- Select Employee --</option>
                            @if ($employees->isNotEmpty())
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No employees available</option>
                            @endif
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Attendance Status -->
                    <div class="col-12 col-md-6">
                        <label for="attendance" class="form-label fw-semibold text-dark">
                            Attendance Status <span class="text-danger">*</span>
                        </label>
                        <select name="attendance" id="attendance" class="form-select @error('attendance') is-invalid @enderror" required>
                            <option value="">-- Select Status --</option>
                            <option value="Present" {{ old('attendance', 'Present') == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Absent" {{ old('attendance') == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Leave" {{ old('attendance') == 'Leave' ? 'selected' : '' }}>Leave</option>
                        </select>
                        @error('attendance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Branch Dropdown -->
                    <div class="col-12 col-md-6">
                        <label for="branch_id" class="form-label fw-semibold text-dark">
                            Branch <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('branch_id') is-invalid @enderror" name="branch_id" id="branch_id" required>
                            <option value="">-- Select Branch --</option>
                            @if ($branches->isNotEmpty())
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No branches available</option>
                            @endif
                        </select>
                        @error('branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Department Dropdown -->
                    <div class="col-12 col-md-6">
                        <label for="department_id" class="form-label fw-semibold text-dark">
                            Department <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('department_id') is-invalid @enderror" name="department_id" id="department_id" required>
                            <option value="">-- Select Department --</option>
                            @if ($departments->isNotEmpty())
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No departments available</option>
                            @endif
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Timing & Schedule Information -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock text-primary"></i> Date & Timing Information
                </h5>

                <div class="row g-3">
                    <!-- Date Field -->
                    <div class="col-12 col-md-4">
                        <label for="date" class="form-label fw-semibold text-dark">
                            Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" 
                               value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- In Time Field -->
                    <div class="col-12 col-md-4">
                        <label for="in_time" class="form-label fw-semibold text-dark">In Time (Punch In)</label>
                        <input type="time" name="in_time" id="in_time" class="form-control @error('in_time') is-invalid @enderror" 
                               value="{{ old('in_time') }}">
                        @error('in_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Out Time Field -->
                    <div class="col-12 col-md-4">
                        <label for="out_time" class="form-label fw-semibold text-dark">Out Time (Punch Out)</label>
                        <input type="time" name="out_time" id="out_time" class="form-control @error('out_time') is-invalid @enderror" 
                               value="{{ old('out_time') }}">
                        @error('out_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Attendance
                </button>
            </div>
        </form>

    </div>
</div>
@endsection