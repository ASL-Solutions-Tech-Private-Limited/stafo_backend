@extends('user.layouts.app')

@section('title', 'Add Document') <!-- Custom title -->

@section('content')
    <div class="card mt-4 p-3">

        <div class="container">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="fw-bold">Add Document</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('company-documents.index') }}" class="btn btn-primary" style="float: right;">Document
                        List</a>
                </div>
            </div>

            <form action="{{ route('company-documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-end">

                    <!-- Document Type Dropdown -->
                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <label for="document_type_id">Document Type</label>
                            <select name="document_type_id" id="document_type_id" class="form-control" required>
                                <option value="">Select Document Type</option>
                                @foreach ($documentTypes as $documentType)
                                    <option value="{{ $documentType->id }}" {{ old('document_type_id') == $documentType->id ? 'selected' : '' }}>
                                        {{ $documentType->document_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Document Upload -->
                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <label for="document">Upload Document</label>
                            <input type="file" name="document" class="form-control" required>
                            @error('document')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="col-md-4 mb-3 text-end">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection