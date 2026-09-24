@extends('user.layouts.app')

@section('title', 'Department Details | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Department Details</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-building-columns me-1"></i> {{ $department->name }}
                    </span>
                </div>
                <p class="text-muted small mb-0">View department information, status, and manage settings</p>
            </div>
            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Departments
            </a>
        </div>

        <!-- Information Card -->
        <div class="p-4 bg-light rounded-4 border mb-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-info text-primary"></i> Department Overview
            </h5>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Department Name</small>
                        <span class="fw-bold text-dark fs-6">{{ $department->name }}</span>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Status</small>
                        @if($department->status)
                            <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check me-1"></i> Active</span>
                        @else
                            <span class="badge-stafo badge-stafo-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Created</small>
                        <span class="fw-bold text-dark">{{ $department->created_at ? $department->created_at->format('d M Y') : 'N/A' }}</span>
                    </div>
                </div>

                <div class="col-12">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Description</small>
                        <p class="text-dark mb-0">{{ $department->description ?? 'No description available.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
            <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-warning px-4 fw-bold">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Department
            </a>
            <form action="{{ route('departments.destroy', $department->id) }}" method="POST" style="display:inline;" id="delete-dept-{{ $department->id }}">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-danger px-4" onclick="confirmDelete({{ $department->id }})">
                    <i class="fa-solid fa-trash me-1"></i> Delete
                </button>
            </form>
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Delete Department?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-dept-${id}`).submit();
            }
        });
    }
</script>
@endsection