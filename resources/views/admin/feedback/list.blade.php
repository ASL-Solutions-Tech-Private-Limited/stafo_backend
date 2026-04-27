@extends('admin.layouts.layout')

@section('title', 'Feedback List')
@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="container mt-4">
        <h3 class="mb-3">Feedback List</h3>

        <!-- Search and Reset Section -->
        <!-- <form method="GET" action="{{ route('admin.feedback_list') }}">
                            <div class="row ">
                                <div class="col-md-3 mb-3">
                                    <input type="text" name="name" class="form-control" placeholder="Search by Name"
                                        value="{{ request('name') }}">
                                </div>
                            </div>
                        </form> -->

        <div class="card border-0">
            <div class="card-body ps-0 pt-0">
                @if ($records->isEmpty())
                    <p class="text-center">No records available.</p>
                @else
                    <div class="table-responsive table-same">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Reply</th>
                                    <th>Action</th> <!-- Add Action Column -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $index => $record)
                                    <tr>
                                        <td width="5%">{{ $records->firstItem() + $index }}</td>

                                        <td width="20%">
                                            @if (isset($record->company->company_name))
                                                {{ $record->company->company_name }}
                                            @endif
                                            @if (isset($record->employee->name))
                                                {{ $record->employee->name }}
                                            @endif
                                            {{ $record->message_by }}
                                        </td>
                                        <td width="30%">{{ $record->message }}</td>
                                        <td width="15%">{{ date('dS F, Y', strtotime($record->created_at)) }}</td>
                                        <td width="30%">
                                            @if ($record->reply == null)
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#replyModal{{ $record->id }}">Reply</a>

                                                <!-- Modal -->
                                                <div class="modal fade" id="replyModal{{ $record->id }}" tabindex="-1"
                                                    aria-labelledby="replyModalLabel{{ $record->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="replyModalLabel{{ $record->id }}">Reply to
                                                                    Feedback</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST"
                                                                action="{{ route('admin.feedback_reply', $record->id) }}">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="reply" class="form-label">Your
                                                                            Reply</label>
                                                                        <textarea class="form-control" id="reply" name="reply" rows="3" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary">Send
                                                                        Reply</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                {{ $record->reply }}
                                            @endif
                                        </td>
                                        <td width="10%">
                                            <!-- Delete Icon and Form -->
                                            <form action="{{ route('admin.feedback_delete', $record->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE') <!-- Specify that this is a DELETE request -->
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this feedback?')">
                                                    <i class="fa fa-trash" style="color: white"></i>
                                                    <!-- Font Awesome Trash Icon -->
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $records->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection
