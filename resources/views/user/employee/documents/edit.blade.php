@extends('user.layouts.app')

@section('title', 'Edit Employee Document | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Edit Employee Document</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-user me-1"></i> {{ $employee->name }}
                    </span>
                </div>
                <p class="text-muted small mb-0">Update document classification or upload a replacement file</p>
            </div>
            <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Employee List
            </a>
        </div>

        <form action="{{ route('employee.documents.update', ['employeeId' => $employee->id, 'documentId' => $document->id]) }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Document Details Card -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-file-contract text-primary"></i> Document Details
                </h5>

                <div class="row g-3">
                    
                    <!-- Document Type Dropdown -->
                    <div class="col-12 col-md-6">
                        <label for="document_type_id" class="form-label fw-semibold text-dark">
                            Select Document Type <span class="text-danger">*</span>
                        </label>
                        <select name="document_type_id" id="document_type_id" class="form-select" required>
                            <option value="">-- Select Document Type --</option>
                            @foreach ($documentTypes as $documentType)
                                <option value="{{ $documentType->id }}"
                                    {{ $document->document_type_id == $documentType->id ? 'selected' : '' }}>
                                    {{ $documentType->document_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('document_type_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- File Input -->
                    <div class="col-12 col-md-6">
                        <label for="file" class="form-label fw-semibold text-dark">
                            Upload New Document <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <input type="file" name="file" id="file" class="form-control">
                        
                        @if ($document->file_path)
                            <div class="mt-2 p-2 bg-white rounded-3 border d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-file-lines text-primary fs-5"></i>
                                    <div>
                                        <small class="text-muted d-block">Current Attached File</small>
                                        <span class="small fw-semibold text-dark">{{ basename($document->file_path) }}</span>
                                    </div>
                                </div>
                                <a href="{{ asset('employee_documents/' . $document->file_path) }}" target="_blank" 
                                   class="btn btn-sm btn-outline-primary px-3" style="border-radius: 6px;">
                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View
                                </a>
                            </div>
                        @endif
                        @error('file')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Actions Bar -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Document
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
