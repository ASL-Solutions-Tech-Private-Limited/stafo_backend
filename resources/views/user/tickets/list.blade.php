@extends('user.layouts.app')

@section('title', 'Ticket List')

@section('content')
    <div class="card mt-4 p-3">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-between user-access">
            <div class="user-welcome">
                <h3>Ticket List</h3>
            </div>
            <div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createReplyModal">
                        <i class="bi bi-plus-circle"></i> Create Ticket
                    </button>
                @if ($records->isEmpty())
                    {{-- <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createReplyModal">
                        <i class="bi bi-plus-circle"></i> Create Ticket
                    </button>--}}
                @else
                    {{-- <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createReplyModal">
                        <i class="bi bi-arrow-repeat"></i> Reply to Ticket
                    </button> --}}
                @endif
            </div>
        </div>

        <div class="table-responsive table-same mt-3">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Replies</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $ticket)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ticket->title }}</td>
                            <td>{{ $ticket->message }}</td>
                            <td>{{ date('dS F, Y', strtotime($ticket->created_at)) }}</td>
                            <td>
                                @if ($ticket->replies->isNotEmpty())
                                    @foreach ($ticket->replies->take(0) as $reply)
                                        <!-- Show only the first 3 replies -->
                                        <div class="reply">
                                            <strong>{{ $reply->message_by }}:</strong>
                                            <p>{{ $reply->message }}</p>
                                            <p>{{ $reply->reply }}</p>
                                            <small>{{ date('dS F, Y', strtotime($reply->created_at)) }}</small>
                                        </div>
                                    @endforeach

                                    <!-- Show More Replies Button -->


                                    <div id="replies{{ $ticket->id }}" class="collapse">
                                        @foreach ($ticket->replies->slice(3) as $reply)
                                            <!-- Show remaining replies -->
                                            <div class="reply">
                                                <strong>{{ $reply->message_by }}:</strong>
                                                <p>{{ $reply->message }}</p>
                                                <p>{{ $reply->reply }}</p>
                                                <small>{{ date('dS F, Y', strtotime($reply->created_at)) }}</small>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Button to View All Replies (Optional) -->
                                    <a href="{{ route('ticket.showReplies', $ticket->id) }}" class="btn btn-link">
                                        View Replies
                                    </a>
                                @else
                                    No replies yet.
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- <div class="mt-3">
                {{ $tickets->links() }}
            </div> --}}
        </div>

        <!-- Modal for Creating Reply -->
        <div class="modal fade" id="createReplyModal" tabindex="-1" aria-labelledby="createReplyModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createReplyModalLabel">
                           New Ticket
                            
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('company.tickets.storeReply') }}">

                        @csrf
                        <div class="modal-body">
                           
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                            

                            <div class="mb-3">
                                <label for="message" class="form-label">
                                    
                                        Message
                                   
                                </label>
                                <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection