@extends('user.layouts.app')

@section('title', 'Create Salary Package | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Create Salary Package</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-box me-1"></i> New Package
                    </span>
                </div>
                <p class="text-muted small mb-0">Define a new compensation package tier for employee salary structures</p>
            </div>
            <a href="{{ route('salary-package-type.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Packages
            </a>
        </div>

        <form action="{{ route('salary-package-type.store') }}" method="POST">
            @csrf

            <!-- Package Details Card -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-sliders text-primary"></i> Package Configuration
                </h5>

                <div class="row g-3">
                    <!-- Package Name -->
                    <div class="col-12 col-md-6">
                        <label for="package_name" class="form-label fw-semibold text-dark">
                            Package Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="package_name" id="package_name" 
                               class="form-control @error('package_name') is-invalid @enderror" 
                               value="{{ old('package_name') }}" placeholder="e.g. Premium Package, Basic Package" required>
                        @error('package_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Package Type -->
                    <div class="col-12 col-md-6">
                        <label for="package_type" class="form-label fw-semibold text-dark">
                            Package Type <span class="text-danger">*</span>
                        </label>
                        <select name="package_type" id="package_type" class="form-select @error('package_type') is-invalid @enderror" required>
                            <option value="">-- Select Package Type --</option>
                            <option value="Basic" {{ old('package_type') == 'Basic' ? 'selected' : '' }}>Basic</option>
                            <option value="Standard" {{ old('package_type') == 'Standard' ? 'selected' : '' }}>Standard</option>
                            <option value="Premium" {{ old('package_type') == 'Premium' ? 'selected' : '' }}>Premium</option>
                            <option value="Custom" {{ old('package_type') == 'Custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                        @error('package_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label fw-semibold text-dark">
                            Status
                        </label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="package_description" class="form-label fw-semibold text-dark">
                            Description <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea name="package_description" id="package_description" 
                                  class="form-control @error('package_description') is-invalid @enderror" 
                                  rows="3" placeholder="Describe the package benefits, inclusions, and eligibility criteria">{{ old('package_description') }}</textarea>
                        @error('package_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('salary-package-type.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Create Package
                </button>
            </div>
        </form>

    </div>
</div>
@endsection