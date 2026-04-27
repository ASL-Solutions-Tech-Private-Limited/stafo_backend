@extends('user.layouts.app')

@section('title', 'All Replies for Ticket')

@section('content')
    <div class="card mt-4 p-3">
        <h5>All Replies for Ticket: <span class="highlight-name">{{ $ticket->title }}</span></h5>
        <div class="d-flex justify-content-between user-access">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createReplyModal">
                <i class="bi bi-arrow-repeat"></i> Reply to Ticket
            </button>
        </div>


        @if ($ticket->replies->isEmpty())
            <p>No replies for this ticket yet.</p>
        @else
            <div class="table-responsive table-same mt-3">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Reply By</th>
                            <th>Message</th>
                            <th>Reply</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ticket->replies as $reply)
                            <tr>
                                <td>{{ $reply->message_by }}</td>
                                <td>{{ $reply->message }}</td>
                                <td>{{ $reply->reply }}</td>
                                <td>{{ date('dS F, Y', strtotime($reply->created_at)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Modal for Creating Reply -->
        <div class="modal fade" id="createReplyModal" tabindex="-1" aria-labelledby="createReplyModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="createReplyModalLabel">
                            <!-- Adjust this logic based on the ticket properties -->
                            @if ($ticket->title)
                                Reply to Ticket
                            @else
                                Create Ticket
                            @endif
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('company.tickets.storeReply', $ticket->id ?? '') }}">

                        @csrf
                        <div class="modal-body">
                            <!-- This check should be based on the ticket data -->
                            @if (!$ticket->title)
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="message" class="form-label">
                                    @if ($ticket->title)
                                        Reply
                                    @else
                                        Message
                                    @endif
                                </label>
                                <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                            </div>

                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Centering and making the button smaller -->
        <div class="d-flex justify-content-start mt-3">
            <a href="{{ route('company.tickets') }}" class="btn btn-warning ">Back to Ticket List</a>
        </div>
    </div>
@endsection