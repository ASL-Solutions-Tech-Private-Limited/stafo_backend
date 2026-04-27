@extends('user.layouts.app')

@section('title', 'Edit Document') <!-- Custom title -->

@section('content')
    <div class="card mt-4 p-3">
        <h5>Edit Document</h5>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <a href="{{ route('company-documents.index') }}" class="btn btn-primary" style="float: right;">Document
                        List</a>
                </div>
            </div>

            <form action="{{ route('company-documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">

                    <!-- Document Type Dropdown -->
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="document_type_id">Document Type</label>
                            <select name="document_type_id" id="document_type_id" class="form-control" required>
                                <option value="">Select Document Type</option>
                                @foreach ($documentTypes as $documentType)
                                    <option value="{{ $documentType->id }}"
                                        {{ old('document_type_id', $document->document_type_id) == $documentType->id ? 'selected' : '' }}>
                                        {{ $documentType->document_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Document Upload (existing document shown) -->
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="document">Upload New Document</label>
                            <input type="file" name="document" class="form-control">
                            @if ($document->document)
                                <p>Current Document: <a href="{{ asset('company_documents/' . $document->document) }}"
                                        target="_blank">View</a></p>
                            @endif
                            @error('document')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-success w-100">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
