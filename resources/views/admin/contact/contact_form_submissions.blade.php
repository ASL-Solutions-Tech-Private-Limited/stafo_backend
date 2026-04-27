@extends('admin.layouts.layout')

@section('title', 'Contact Form List')
@section('content')
    <div class="container mt-4">
        <h3 class="mb-3">Contact Form</h3>

        <!-- Search and Reset Section -->
        <form method="GET" action="{{ route('admin.contact_form_submissions') }}">
            <div class="row ">
                <!-- Name Search Field -->
                <div class="col-md-3 mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Search by Name"
                        value="{{ request('name') }}">
                </div>

                <!-- Email Search Field -->
                <div class="col-md-3 mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Search by Email"
                        value="{{ request('email') }}">
                </div>

                <!-- Phone Search Field -->
                <div class="col-md-3 mb-3">
                    <input type="text" name="phone" class="form-control" placeholder="Search by Phone"
                        value="{{ request('phone') }}">
                </div>

                <!-- Search and Reset Buttons -->
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">Search</button>
                    <a href="{{ route('admin.contact_form_submissions') }}" class="btn btn-danger w-100">Reset</a>
                </div>
            </div>
        </form>


        <!-- Contact Form Submissions Card -->
        <div class="card">
            <div class="card-body">
                @if ($submissions->isEmpty())
                    <p class="text-center">No contact form submissions available.</p>
                @else
                    <div class="table-responsive table-same">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Message</th>
                                    <th>Submitted At</th>
                                    <th>Actions</th> <!-- Added Actions Column -->

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($submissions as $index => $submission)
                                    <tr>
                                        <td>{{ $submissions->firstItem() + $index }}</td>
                                        <td>{{ $submission->full_name }}</td>
                                        <td>{{ $submission->email }}</td>
                                        <td>{{ $submission->phone }}</td>
                                        <td>{{ $submission->message }}</td>
                                        <td>{{ $submission->created_at->format('d M Y, H:i') }}</td>
                                        <td>
                                            <div class="actions">
                                            <!-- Delete Button (Only Icon) -->
                                            <form action="{{ route('admin.contact_form_submissions.delete', $submission->id) }}"
                                                method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this submission?')"
                                                    style="border: none; background: none;">
                                                    <i class="fas fa-trash"></i> <!-- Font Awesome Trash Icon -->
                                                </button>
                                            </form>
                                            </div>
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