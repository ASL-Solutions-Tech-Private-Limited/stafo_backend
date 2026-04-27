@extends('user.layouts.app')

@section('title', 'Add Document') <!-- Set your custom title here -->

@section('css')
    <style>
        /* Custom Styling for Form */
        .card {
            border: none;
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .text-primary {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .form-control {
            border-radius: 5px;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn {
            font-size: 1rem;
            padding: 12px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .row {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .text-center {
            margin-bottom: 30px;
        }

        .form-control-file {
            padding: 10px;
        }

        /* Spacing adjustments for form layout */
        .col-md-4 {
            margin-bottom: 15px;
        }

        .col-md-4.mb-3 {
            margin-bottom: 20px;
        }

        .col-md-4.d-flex {
            display: flex;
            justify-content: center;
        }

        .col-md-4.text-right {
            display: flex;
            justify-content: flex-end;
        }
    </style>
@endsection

@section('content')
    <div class="card mt-4 p-3">
        <h5 class="text-center text-primary mb-4">Add Document for {{ $employee->name }}</h5>

        <form action="{{ route('employee.documents.store', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row mb-4">
                <!-- Document Type Dropdown -->
                <div class="col-md-4 mb-3">
                    <label for="document_type_id" class="form-label">Select Document Type</label>
                    <select name="document_type_id" class="form-control" required>
                        <option value="">Select Document Type</option>
                        @foreach ($documentTypes as $documentType)
                            <option value="{{ $documentType->id }}">{{ $documentType->document_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- File Input -->
                <div class="col-md-4 mb-3">
                    <label for="file" class="form-label">Upload Document</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <!-- Submit Button -->
                <div class="col-md-4"></div>
                <div class="col-md-4 mb-3 d-flex justify-content-center">
                    <button type="submit" class="btn btn-success w-100">Add Document</button>
                </div>
                <!-- Back Button (Aligned to the right) -->
                <div class="col-md-4 text-right">
                    <a href="{{ route('employee.index') }}" class="btn btn-secondary">Back to Employee List</a>
                </div>
            </div>
        </form>
    </div>
@endsection
