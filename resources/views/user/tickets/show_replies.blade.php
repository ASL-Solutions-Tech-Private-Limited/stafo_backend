@extends('user.layouts.app')

@section('title', 'Ticket Discussion Thread | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <a href="{{ route('company.tickets') }}" class="btn btn-sm btn-outline-secondary p-0" style="width: 28px; height: 28px; border-radius: 6px;">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <h3 class="fw-bold text-dark mb-0">{{ $ticket->title }}</h3>
                </div>
                <p class="text-muted small mb-0">Conversation thread and replies for ticket #{{ $ticket->id }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary px-3 py-2" data-bs-toggle="modal" data-bs-target="#createReplyModal">
                    <i class="fa-solid fa-reply me-1"></i> Post Reply
                </button>
            </div>
        </div>

        <!-- Initial Message Card -->
        <div class="p-4 rounded-4 bg-light border mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge-stafo badge-stafo-primary"><i class="fa-solid fa-user me-1"></i> Original Query</span>
                <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i> {{ date('d M, Y h:i A', strtotime($ticket->created_at)) }}</small>
            </div>
            <p class="text-dark mb-0">{{ $ticket->message }}</p>
        </div>

        <!-- Replies Section -->
        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i class="fa-solid fa-comments text-primary"></i> Replies ({{ $ticket->replies->count() }})
        </h5>

        @if ($ticket->replies->isEmpty())
            <div class="text-center py-4 bg-light rounded-4 border text-muted">
                <i class="fa-solid fa-clock fs-3 mb-2 d-block opacity-50"></i>
                No responses from support yet. We will get back to you shortly.
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach ($ticket->replies as $reply)
                    <div class="p-3 rounded-4 border bg-white shadow-xs">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="fw-bold text-primary">
                                <i class="fa-solid fa-user-tie me-1"></i> {{ $reply->message_by ?: 'Support Team' }}
                            </span>
                            <small class="text-muted">{{ date('d M, Y h:i A', strtotime($reply->created_at)) }}</small>
                        </div>
                        @if($reply->message)
                            <p class="text-dark mb-1">{{ $reply->message }}</p>
                        @endif
                        @if($reply->reply)
                            <div class="p-2 bg-light rounded-3 mt-2 text-dark small border-start border-primary border-3 ps-3">
                                {{ $reply->reply }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

<!-- Modal for Creating Reply -->
<div class="modal fade" id="createReplyModal" tabindex="-1" aria-labelledby="createReplyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="createReplyModalLabel">
                    <i class="fa-solid fa-reply text-primary me-2"></i> Post Reply
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('company.tickets.storeReply', $ticket->id ?? '') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="message" class="form-label fw-semibold text-dark">Your Reply Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="4" placeholder="Type your follow-up reply here..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection