@extends('user.layouts.app')
@section('title', 'Create Salary Package')

@section('content')
@include('user.layouts.alert')
<div class="card mt-4 p-4 shadow-sm border-0" style="border-radius: 1rem;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="color: #1a2c3e;">
                    <i class="fas fa-plus-circle me-2" style="color: #2c6e9e;"></i>Create Salary Package
                </h2>
                
            </div>
            <a href="{{ route('salary-package-type.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <form action="{{ route('salary-package-type.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="package_name" class="form-label fw-semibold">
                        <i class="fas fa-tag me-1 text-danger"></i> Package Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="package_name" 
                           id="package_name" 
                           class="form-control rounded-pill @error('package_name') is-invalid @enderror" 
                           value="{{ old('package_name') }}"
                           placeholder="e.g., Premium Package, Basic Package"
                           required>
                    @error('package_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="package_type" class="form-label fw-semibold">
                        <i class="fas fa-layer-group me-1 text-danger"></i> Package Type <span class="text-danger">*</span>
                    </label>
                    <select name="package_type" id="package_type" class="form-select rounded-pill @error('package_type') is-invalid @enderror" required>
                        <option value="">Select Package Type</option>
                        <option value="Basic" {{ old('package_type') == 'Basic' ? 'selected' : '' }}>
                            <i class="fas fa-star"></i> Basic
                        </option>
                        <option value="Standard" {{ old('package_type') == 'Standard' ? 'selected' : '' }}>
                            <i class="fas fa-gem"></i> Standard
                        </option>
                        <option value="Premium" {{ old('package_type') == 'Premium' ? 'selected' : '' }}>
                            <i class="fas fa-crown"></i> Premium
                        </option>
                        <option value="Custom" {{ old('package_type') == 'Custom' ? 'selected' : '' }}>
                            <i class="fas fa-cogs"></i> Custom
                        </option>
                    </select>
                    @error('package_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="package_description" class="form-label fw-semibold">
                        <i class="fas fa-align-left me-1"></i> Description
                    </label>
                    <textarea name="package_description" 
                              id="package_description" 
                              class="form-control @error('package_description') is-invalid @enderror" 
                              rows="3"
                              placeholder="Enter package description...">{{ old('package_description') }}</textarea>
                    @error('package_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label fw-semibold">
                        <i class="fas fa-circle me-1"></i> Status
                    </label>
                    <select name="status" id="status" class="form-select rounded-pill @error('status') is-invalid @enderror">
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('salary-package-type.index') }}" class="btn btn-secondary rounded-pill px-4">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-save me-1"></i> Create Package
                </button>
            </div>
        </form>
    </div>
</div>
@endsection