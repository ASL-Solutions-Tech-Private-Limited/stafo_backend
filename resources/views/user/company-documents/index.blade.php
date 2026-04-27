@extends('user.layouts.app')
@section('title', 'Company Documents list') <!-- Set your custom title here -->

@section('css')

@endsection

@section('content')
    <!-- Show success or error message -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mt-4 p-3 shadow-sm border-0">
        <div class="row">
            <div class="col-md-9 ">
                <h2 class="fw-bold">Company Documents</h2>
            </div>
            <div class="col-md-3 ">
                <div class="text-end">
                    <a href="{{ route('company-documents.create') }}" class="btn btn-success shadow-sm"><i
                            class="fas fa-plus me-1 "></i> Create</a>
                </div>
            </div>
        </div>
        <div class="container mt-3 px-0">
            <div class="table-responsive table-same">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>S.no</th>
                            <th>Document Type</th>
                            <th>Document</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $document)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $document->documentType->document_name }}</td>
                                <td><a href="{{ asset('uploads/company_documents/' . $document->document) }}"
                                        target="_blank">View</a>
                                </td>
                                <td>
                                    <a href="{{ route('company-documents.edit', $document->id) }}"
                                        class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>

                                    <!-- Delete button with Swal confirmation -->
                                    <button onclick="confirmDelete(event, {{ $document->id }})"
                                        class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>

                                    <!-- Form for delete action (hidden and triggered by Swal) -->
                                    <form id="delete-form-{{ $document->id }}"
                                        action="{{ route('company-documents.destroy', $document->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Swal confirmation script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(event, documentId) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the delete form
                    document.getElementById(`delete-form-${documentId}`).submit();
                }
            });
        }
    </script>
@endsection
