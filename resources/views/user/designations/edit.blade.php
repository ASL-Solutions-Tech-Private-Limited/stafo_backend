@extends('user.layouts.app')
@section('title', 'Edit Designation | STAFO HRMS')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Designation
                    </h3>
                    <p class="text-muted small mb-0">Update title, responsibilities, or status for this designation</p>
                </div>
                <a href="{{ route('designations.index') }}" class="btn btn-outline-secondary px-3 py-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Designations
                </a>
            </div>

            <!-- Form -->
            <form action="{{ route('designations.update', $designation->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-12 col-md-7">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold text-dark">
                                Designation Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $designation->name) }}" 
                                   placeholder="e.g. Senior Software Engineer, HR Manager" 
                                   required 
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small text-muted">
                                Changing the title will automatically reflect across all employees holding this designation.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold text-dark">
                                Description & Key Responsibilities <span class="text-muted small">(Optional)</span>
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Brief overview of the responsibilities and scope of this designation...">{{ old('description', $designation->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark d-block">Status</label>
                            <div class="d-flex align-items-center gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_active" value="1" {{ old('status', $designation->status) == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark fw-semibold" for="status_active">
                                        <i class="fa-solid fa-circle-check text-success me-1"></i> Active
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_inactive" value="0" {{ old('status', $designation->status) == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted" for="status_inactive">
                                        <i class="fa-solid fa-circle-pause text-secondary me-1"></i> Inactive
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 pt-2 border-top">
                            <button type="submit" class="btn btn-primary px-4 py-2.5 fw-semibold">
                                <i class="fa-solid fa-circle-check me-1"></i> Update Designation
                            </button>
                            <a href="{{ route('designations.index') }}" class="btn btn-outline-secondary px-4 py-2.5">
                                Cancel
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-5">
                        <div class="p-4 bg-light rounded-4 border">
                            <h6 class="fw-bold text-dark mb-2">
                                <i class="fa-solid fa-circle-info text-info me-1"></i> Designation Information
                            </h6>
                            <p class="text-muted small mb-2">
                                Created on: <strong>{{ $designation->created_at->format('d M Y, h:i A') }}</strong>
                            </p>
                            <p class="text-muted small mb-3">
                                Last modified: <strong>{{ $designation->updated_at->format('d M Y, h:i A') }}</strong>
                            </p>
                            <hr>
                            <h6 class="fw-bold text-dark mb-2 small">
                                <i class="fa-solid fa-user-shield text-primary me-1"></i> Role Permissions
                            </h6>
                            <p class="text-muted small mb-0">
                                Designation acts as the role. You can configure module permissions for employees holding this designation under <strong>Roles & Permissions</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
