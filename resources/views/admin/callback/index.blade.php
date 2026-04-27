@extends('admin.layouts.layout')

@section('title', 'Callback Request List')
@section('content')
    <div class="container mt-4">
        <h3 class="mb-4">Callback Requests</h3>

        <!-- Search and Reset Section -->
        <form method="GET" action="{{ route('request-callback') }}">
            <div class="row">
                <!-- Name Search Field -->
                <div class="col-md-3 mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Search by Name"
                        value="{{ request('name') }}">
                </div>

                <!-- Phone Search Field -->
                <div class="col-md-3 mb-3">
                    <input type="text" name="phone" class="form-control" placeholder="Search by Phone"
                        value="{{ request('phone') }}">
                </div>

                <!-- Search and Reset Buttons -->
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">Search</button>
                    <a href="{{ route('request-callback') }}" class="btn btn-danger w-100">Reset</a>
                </div>
            </div>
        </form>

        <!-- Callback Request Card -->
        <div class="card">
            <div class="card-body">
                @if ($submissions->isEmpty())
                    <p class="text-center">No callback requests available.</p>
                @else
                    <div class="table-responsive table-same">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($submissions as $index => $submission)
                                    <tr>
                                        <td>{{ $submissions->firstItem() + $index }}</td>
                                        <td>{{ $submission->name }}</td>
                                        <td>{{ $submission->phone }}</td>
                                        <td>
                                            <!-- Delete Button (Only Icon) -->
                                            <form action="{{ route('admin.callback_request.delete', $submission->id) }}"
                                                method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this request?')"
                                                    style="border: none; background: none;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $submissions->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection
