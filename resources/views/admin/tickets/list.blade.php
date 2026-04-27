@extends('admin.layouts.layout')

@section('title', 'Ticket List')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="container mt-4">
        <h3 class="mb-3">Ticket List</h3>

        <div class="card border-0">
            <div class="card-body px-0 pt-0">
                @if ($records->isEmpty())
                    <p class="text-center">No records available.</p>
                @else
                    <div class="table-responsive table-same">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Reply</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $index => $record)
                                    <tr>
                                        <td>{{ $records->firstItem() + $index }}</td>
                                        <td>
                                            @if (isset($record->company->company_name))
                                                {{ $record->company->company_name }}
                                            @endif
                                            @if (isset($record->employee->name))
                                                {{ $record->employee->name }}
                                            @endif
                                            {{ $record->message_by }}
                                        </td>
                                        <td>{{ $record->title }}</td>
                                        <td>{{ $record->message }}</td>
                                        <td>{{ date('dS F, Y', strtotime($record->created_at)) }}</td>

                                        <!-- Show replies for each ticket -->
                                        <td>
                                            <div>
                                                <div>
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#replyModal{{ $record->id }}">Reply</a>

                                                </div>
                                                <div >
                                                    @foreach ($record->replies as $reply)
                                                        {{-- <p><strong>{{ $reply->replied_by }}:</strong> {{ $reply->reply }}
                                                        </p> --}}
                                                        {{-- {{ dd($reply) }} --}}
                                                        <div class="reply mt-3 border-b-last-none">
                                                            <strong>{{ $reply->message_by }}:</strong>
                                                            <p>{{ $reply->reply }}</p>
                                                            <p>{{ $reply->message }}</p>
                                                            <small>{{ date('dS F, Y', strtotime($reply->created_at)) }}</small>
                                                            @if (!empty($reply->admin_id || $reply->company_id))
                                                                <a href="#" data-bs-toggle="modal"
                                                                    data-bs-target="#replyModal{{ $record->id }}">Reply</a>
                                                            @endif

                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.tickets_delete', $record->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this ticket?')">
                                                    <i class="fa fa-trash" style="color: white"></i>
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

    <!-- Modal to Add Reply -->
    @foreach ($records as $record)
        <div class="modal fade" id="replyModal{{ $record->id }}" tabindex="-1"
            aria-labelledby="replyModalLabel{{ $record->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="replyModalLabel{{ $record->id }}">Reply to Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.tickets_reply', $record->id) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="reply" class="form-label">Your Reply</label>
                                <textarea class="form-control" id="reply" name="reply" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Send Reply</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection