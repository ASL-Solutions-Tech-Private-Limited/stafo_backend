@extends('user.layouts.app')

@section('title', 'Create Performance KPI Type | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Create Performance Type</h3>
                <p class="text-muted small mb-0">Define a new KPI parameter to assess employee performance monthly</p>
            </div>
            <a href="{{ route('performancetypeList') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to KPI List
            </a>
        </div>

        <form action="{{ route('performancetypeStore') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label fw-semibold text-dark">KPI / Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Sales Target, Attendance Reliability, Code Quality" required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-md-8">
                    <div class="form-group mb-3">
                        <label for="description" class="form-label fw-semibold text-dark">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Optional description or scoring criteria...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>                 

                <div class="col-12 col-md-8 d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                    <a href="{{ route('performancetypeList') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5 fw-bold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save KPI
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection