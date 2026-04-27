@extends('user.layouts.app')
@section('title', 'Package Details')

@section('content')
@include('user.layouts.alert')
<div class="card mt-4 p-4 shadow-sm border-0" style="border-radius: 1rem;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="color: #1a2c3e;">
                    <i class="fas fa-box me-2" style="color: #2c6e9e;"></i>Package Details
                </h2>
                <p class="text-muted small mb-0">View package information and salary components</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('salary-package-type.edit', $package->id) }}" class="btn btn-warning rounded-pill">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <a href="{{ route('salary-package-type.index') }}" class="btn btn-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <!-- Package Info Card -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">Package Name</label>
                                <h5 class="fw-bold">{{ $package->package_name }}</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">Package Type</label>
                                <div>
                                    {!! $package->package_type_icon !!}
                                    <span class="badge bg-primary rounded-pill px-3 py-2">{{ $package->package_type }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">Status</label>
                                <div>{!! $package->status_badge !!}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small mb-1">Created Date</label>
                                <div>{{ $package->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small mb-1">Description</label>
                                <p class="mb-0">{{ $package->package_description ?? 'No description provided.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
        </div>

       
    </div>
</div>
@endsection