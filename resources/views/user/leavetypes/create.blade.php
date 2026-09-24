@extends('user.layouts.app')

@section('title', 'Add Leave Type | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Add Leave Type</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-plane-departure me-1"></i> Policy Setup
                    </span>
                </div>
                <p class="text-muted small mb-0">Configure a new category for company leave allowances and quotas</p>
            </div>
            <a href="{{ route('leavetypes.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Leave Types
            </a>
        </div>

        <form action="{{ route('leavetypes.store') }}" method="POST">
            @csrf

            <!-- Form Card -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-sliders text-primary"></i> Leave Type Configuration
                </h5>

                <div class="row g-3">
                    <!-- Leave Type Name -->
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label fw-semibold text-dark">
                            Leave Type Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" placeholder="e.g. Annual Leave, Sick Leave, Casual Leave" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Max No. of Days Per Year -->
                    <div class="col-12 col-md-6">
                        <label for="no_of_days" class="form-label fw-semibold text-dark">
                            Annual Quota (Days / Year) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.5" name="no_of_days" id="no_of_days" class="form-control @error('no_of_days') is-invalid @enderror" 
                               value="{{ old('no_of_days') }}" placeholder="e.g. 12" required>
                        @error('no_of_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Is Paid -->
                    <div class="col-12 col-md-6">
                        <label for="is_paid" class="form-label fw-semibold text-dark">
                            Leave Compensation Type <span class="text-danger">*</span>
                        </label>
                        <select name="is_paid" id="is_paid" class="form-select @error('is_paid') is-invalid @enderror">
                            <option value="1" {{ old('is_paid', 1) == 1 ? 'selected' : '' }}>Paid Leave (Encashable/Covered)</option>
                            <option value="0" {{ old('is_paid', 1) == 0 ? 'selected' : '' }}>Unpaid Leave (LWP)</option>                            
                        </select>
                        @error('is_paid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label fw-semibold text-dark">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold text-dark">
                            Description & Policy Rules <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" placeholder="Provide details on eligibility, probation rules, or notice requirements">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('leavetypes.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Leave Type
                </button>
            </div>
        </form>

    </div>
</div>
@endsection