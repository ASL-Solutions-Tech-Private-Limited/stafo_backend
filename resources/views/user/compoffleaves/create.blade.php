@extends('user.layouts.app')

@section('title', 'Add Comp-Off Leave | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Add Comp-Off Leave</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-business-time me-1"></i> Compensatory Off
                    </span>
                </div>
                <p class="text-muted small mb-0">Grant a compensatory off to an employee for extra hours worked</p>
            </div>
            <a href="{{ route('compoffleaves.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Comp-Off List
            </a>
        </div>

        <form action="{{ route('compoffleaves.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Request Details Card -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-primary"></i> Request Details
                </h5>

                <div class="row g-3">
                    <!-- Employee Dropdown -->
                    <div class="col-12 col-md-6">
                        <label for="employee_id" class="form-label fw-semibold text-dark">
                            Employee <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" id="employee_id" required>
                            <option value="">-- Select Employee --</option>
                            @if (!$employees->isEmpty())
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="col-12 col-md-6">
                        <label for="title" class="form-label fw-semibold text-dark">
                            Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}" placeholder="e.g. Weekend work, Holiday shift coverage" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div class="col-12 col-md-6">
                        <label for="date" class="form-label fw-semibold text-dark">
                            Comp-Off Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" 
                               value="{{ old('date') }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label fw-semibold text-dark">
                            Approval Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="Pending" {{ old('status', 'Pending') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ old('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ old('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12 col-md-6">
                        <label for="description" class="form-label fw-semibold text-dark">
                            Description <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" placeholder="Reason for granting compensatory leave">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- File Upload -->
                    <div class="col-12 col-md-6">
                        <label for="filename" class="form-label fw-semibold text-dark">
                            Supporting Document <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <input type="file" name="filename" id="filename" class="form-control @error('filename') is-invalid @enderror">
                        <small class="text-muted d-block mt-1">Upload any relevant proof or authorization document</small>
                        @error('filename')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('compoffleaves.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Comp-Off Leave
                </button>
            </div>
        </form>

    </div>
</div>
@endsection