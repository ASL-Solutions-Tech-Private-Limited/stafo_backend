@extends('user.layouts.app')

@section('title', 'Department Details')

@section('content')
    <div class="card mt-4 p-3 shadow-sm">
        <div class="container">

            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="mb-3 fw-bold">Department Details</h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('departments.index') }}" class="btn btn-primary float-right">
                        <i class="bi bi-building"></i> Departments List
                    </a>
                </div>
            </div>

            <div class="mt-4">
                <div class="container px-0">
                    <div>
                        <div class="col-lg-12">
                            <h4 class="mb-4">Department Information</h4>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Department Name:</strong> {{ $department->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> {{ $department->status ? 'Active' : 'Inactive' }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Description:</strong>
                                        {{ $department->description ?? 'No description available' }}</p>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <a href="{{ route('departments.edit', $department->id) }}"
                                        class="btn btn-success float-left mr-2">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('departments.destroy', $department->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this department?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection