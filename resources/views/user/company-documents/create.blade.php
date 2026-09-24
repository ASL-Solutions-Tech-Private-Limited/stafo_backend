@extends('user.layouts.app')

@section('title', 'Upload Company Document | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Upload Company Document</h3>
                <p class="text-muted small mb-0">Select document type and upload official files (PDF, JPG, PNG)</p>
            </div>
            <a href="{{ route('company-documents.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Documents
            </a>
        </div>

        <form action="{{ route('company-documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="p-3 bg-light rounded-4 border mb-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="document_type_id" class="form-label fw-semibold text-dark">
                            Document Category <span class="text-danger">*</span>
                        </label>
                        <select name="document_type_id" id="document_type_id" class="form-select" required>
                            <option value="">-- Select Document Category --</option>
                            @foreach ($documentTypes as $documentType)
                                <option value="{{ $documentType->id }}" {{ old('document_type_id') == $documentType->id ? 'selected' : '' }}>
                                    {{ $documentType->document_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('document_type_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="document" class="form-label fw-semibold text-dark">
                            Choose File <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="document" id="document" class="form-control" required>
                        @error('document')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('company-documents.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 fw-bold">
                    <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Document
                </button>
            </div>
        </form>
    </div>
</div>
@endsection