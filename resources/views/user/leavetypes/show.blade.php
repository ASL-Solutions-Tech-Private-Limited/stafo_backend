@extends('user.layouts.app')

@section('title', 'Leavetype Details')

@section('content')
    <div class="card mt-4 p-3 shadow-sm">
        <div class="container">

            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="mb-3 fw-bold">Leavetype Details</h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('leavetypes.index') }}" class="btn btn-primary float-right">
                        <i class="bi bi-building"></i> Leavetypes List
                    </a>
                </div>
            </div>

            <div class="mt-4">
                <div class="container px-0">
                    <div>
                        <div class="col-lg-12">
                            <h4 class="mb-4">Leavetype Information</h4>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Leavetype Name:</strong> {{ $leavetype->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> {{ $leavetype->status ? 'Active' : 'Inactive' }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Description:</strong>
                                        {{ $leavetype->description ?? 'No description available' }}</p>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <a href="{{ route('leavetypes.edit', $leavetype->id) }}"
                                        class="btn btn-success float-left mr-2">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('leavetypes.destroy', $leavetype->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this leavetype?')">
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