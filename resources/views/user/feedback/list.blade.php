@extends('user.layouts.app')

@section('title', 'Company-feedback-list')

@section('content')
    <div class="card mt-4 p-3">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-between user-access">
            <div class="user-welcome">
                <h5>Feedback List</h5>
            </div>
            <!-- Create Button with + Icon -->
            <div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createReplyModal">
                    <i class="bi bi-plus-circle"></i> Create
                </button>
            </div>
        </div>

        <div class="table-responsive table-same mt-3">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Reply</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $index => $record)
                        <tr>
                            <td width="5%">{{ $records->firstItem() + $index }}</td>
                            <td width="30%">{{ $record->message }}</td>

                            <td width="15%">{{ date('dS F, Y', strtotime($record->created_at)) }}</td>
                            <td width="15%">
                                {{-- Check if reply exists, else display "No reply yet" --}}
                                @if ($record->reply)
                                    {{ $record->reply }}
                                @else
                                    <span class="text-warning">No reply yet</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $records->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>

        <!-- Modal for Creating Reply (Create button) -->
        <div class="modal fade" id="createReplyModal" tabindex="-1" aria-labelledby="createReplyModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createReplyModalLabel">Create feedback</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('company.feedback.create') }}">
                        @csrf
                        <div class="modal-body">

                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
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
