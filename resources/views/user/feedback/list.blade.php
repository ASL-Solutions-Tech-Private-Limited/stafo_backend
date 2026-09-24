@extends('user.layouts.app')

@section('title', 'Platform Feedback | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Company Feedback & Suggestions</h3>
                <p class="text-muted small mb-0">Submit product feedback, feature requests, and suggestions directly to the product team</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary px-3 py-2" data-bs-toggle="modal" data-bs-target="#createReplyModal">
                    <i class="fa-solid fa-plus me-1"></i> Give Feedback
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 70px;" class="text-center">S.No</th>
                        <th>Feedback Message</th>
                        <th style="width: 170px;">Date Submitted</th>
                        <th style="width: 250px;">Admin Response</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $index => $record)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $records->firstItem() + $index }}</td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $record->message }}</span>
                            </td>
                            <td>
                                <span class="badge-stafo badge-stafo-secondary">
                                    <i class="fa-regular fa-calendar me-1"></i>
                                    {{ date('d M, Y', strtotime($record->created_at)) }}
                                </span>
                            </td>
                            <td>
                                @if ($record->reply)
                                    <div class="p-2 bg-light rounded-3 text-dark small border-start border-success border-3 ps-2">
                                        <i class="fa-solid fa-comment-dots text-success me-1"></i> {{ $record->reply }}
                                    </div>
                                @else
                                    <span class="badge-stafo badge-stafo-warning">
                                        <i class="fa-solid fa-clock me-1"></i> Awaiting Review
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-comment-dots fs-2 mb-2 d-block opacity-50"></i>
                                No feedback submitted yet. We'd love to hear your thoughts!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($records, 'links') && $records->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                <small class="text-muted">Showing {{ $records->firstItem() }} to {{ $records->lastItem() }} of {{ $records->total() }} entries</small>
                <div>{{ $records->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif

    </div>
</div>

<!-- Modal for Submitting Feedback -->
<div class="modal fade" id="createReplyModal" tabindex="-1" aria-labelledby="createReplyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="createReplyModalLabel">
                    <i class="fa-solid fa-paper-plane text-primary me-2"></i> Submit Feedback
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('company.feedback.create') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="message" class="form-label fw-semibold text-dark">Your Feedback & Suggestions <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" placeholder="Share your experience, suggest a feature, or tell us how we can improve..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="fa-solid fa-paper-plane me-1"></i> Send Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
