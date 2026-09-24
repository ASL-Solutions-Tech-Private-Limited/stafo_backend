@extends('user.layouts.app')

@section('title', 'Edit Department | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Edit Department</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-building-columns me-1"></i> {{ $department->name }}
                    </span>
                </div>
                <p class="text-muted small mb-0">Modify department name, status, and description</p>
            </div>
            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Departments
            </a>
        </div>

        <form action="{{ route('departments.update', $department->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Form Card -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-sliders text-primary"></i> Department Configuration
                </h5>

                <div class="row g-3">
                    <!-- Department Name -->
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label fw-semibold text-dark">
                            Department Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $department->name) }}" placeholder="e.g. Human Resources, Engineering" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <label for="status" class="form-label fw-semibold text-dark">
                            Status <span class="text-danger">*</span>
                        </label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="1" {{ old('status', $department->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $department->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold text-dark">
                            Description <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Briefly describe the department's function and responsibilities">{{ old('description', $department->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Department
                </button>
            </div>
        </form>

    </div>
</div>
@endsection