@extends('user.layouts.app')

@section('title', 'Add Branch | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Create New Branch</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-code-branch me-1"></i> Location Setup
                    </span>
                </div>
                <p class="text-muted small mb-0">Register a new office branch location for your organization</p>
            </div>
            <a href="{{ route('branche.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Branches
            </a>
        </div>

        <form action="{{ route('branche.store') }}" method="POST">
            @csrf

            <!-- Form Card -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-location-dot text-primary"></i> Branch Information
                </h5>

                <div class="row g-3">
                    <!-- Branch Name -->
                    <div class="col-12 col-md-6">
                        <label for="branch_name" class="form-label fw-semibold text-dark">
                            Branch Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="branch_name" id="branch_name" class="form-control @error('branch_name') is-invalid @enderror"
                               value="{{ old('branch_name') }}" placeholder="e.g. Head Office, Mumbai Branch" required>
                        @error('branch_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label fw-semibold text-dark">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Branch Address -->
                    <div class="col-12">
                        <label for="branch_address" class="form-label fw-semibold text-dark">
                            Branch Address <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="branch_address" id="branch_address" class="form-control @error('branch_address') is-invalid @enderror"
                               value="{{ old('branch_address') }}" placeholder="Full street address, city, state, pincode" required>
                        @error('branch_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('branche.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Create Branch
                </button>
            </div>
        </form>

    </div>
</div>
@endsection