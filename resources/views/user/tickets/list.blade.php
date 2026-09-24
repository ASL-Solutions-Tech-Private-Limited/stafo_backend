@extends('user.layouts.app')

@section('title', 'Support Tickets | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Support Tickets</h3>
                <p class="text-muted small mb-0">Submit service requests, report issues, and converse with customer support</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary px-3 py-2" data-bs-toggle="modal" data-bs-target="#createReplyModal">
                    <i class="fa-solid fa-plus me-1"></i> Create Ticket
                </button>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 70px;" class="text-center">S.No</th>
                        <th style="width: 260px;">Ticket Subject</th>
                        <th>Initial Message</th>
                        <th style="width: 150px;">Created Date</th>
                        <th style="width: 140px;" class="text-center">Responses</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $index => $ticket)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-ticket-simple"></i>
                                    </div>
                                    <span class="fw-bold text-dark">{{ $ticket->title }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ Str::limit($ticket->message, 80) }}</span>
                            </td>
                            <td>
                                <span class="badge-stafo badge-stafo-secondary">
                                    <i class="fa-regular fa-calendar me-1"></i>
                                    {{ date('d M, Y', strtotime($ticket->created_at)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($ticket->replies->isNotEmpty())
                                    <a href="{{ route('ticket.showReplies', $ticket->id) }}" class="btn btn-sm btn-outline-primary px-3 py-1" style="border-radius: 8px;">
                                        <i class="fa-solid fa-comments me-1"></i> View ({{ $ticket->replies->count() }})
                                    </a>
                                @else
                                    <span class="badge-stafo badge-stafo-warning">
                                        <i class="fa-solid fa-clock me-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-ticket-simple fs-2 mb-2 d-block opacity-50"></i>
                                No support tickets submitted yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal for Creating Ticket -->
<div class="modal fade" id="createReplyModal" tabindex="-1" aria-labelledby="createReplyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="createReplyModalLabel">
                    <i class="fa-solid fa-ticket-simple text-primary me-2"></i> Create Support Ticket
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('company.tickets.storeReply') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold text-dark">Ticket Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Brief subject of your query" required>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label fw-semibold text-dark">Detailed Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="4" placeholder="Describe your query or issue in detail..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="fa-solid fa-paper-plane me-1"></i> Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection