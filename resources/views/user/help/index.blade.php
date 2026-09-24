@extends('user.layouts.app')

@section('title', 'Help Center & Knowledge Base | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">📚 Help & Knowledge Base</h3>
                <p class="text-muted small mb-0">Tutorial guides, user manuals, and video walkthroughs to master STAFO HRMS</p>
            </div>
        </div>

        <!-- Help Topics Accordion -->
        <div class="accordion accordion-flush" id="hrHelpAccordion">
            @forelse ($helpContents as $index => $help)
                <div class="accordion-item border rounded-4 mb-3 overflow-hidden shadow-xs">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }} fw-bold text-dark p-3" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $index }}">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                    <i class="fa-solid fa-book-open"></i>
                                </div>
                                <span>{{ $help->title }}</span>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse{{ $index }}"
                        class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                        aria-labelledby="heading{{ $index }}" data-bs-parent="#hrHelpAccordion">
                        <div class="accordion-body p-4 bg-light bg-opacity-50">

                            <!-- Notes File Attachment -->
                            @if ($help->file_path)
                                <div class="mb-4 p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-file-pdf text-danger fs-4"></i>
                                        <div>
                                            <span class="fw-semibold text-dark d-block">Documentation Notes</span>
                                            <small class="text-muted">Downloadable manual / PDF guide</small>
                                        </div>
                                    </div>
                                    <a href="{{ asset('uploads/help_contents/' . $help->file_path) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary px-3 py-1" style="border-radius: 8px;">
                                        <i class="fa-solid fa-download me-1"></i> View Guide
                                    </a>
                                </div>
                            @endif

                            <!-- Video Walkthrough -->
                            @if ($help->url)
                                <div class="mb-4">
                                    <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-video text-danger"></i> Video Walkthrough
                                    </h6>
                                    <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-sm">
                                        <iframe src="{{ $help->url }}" title="{{ $help->title }}" allowfullscreen></iframe>
                                    </div>
                                </div>
                            @endif

                            <!-- Description / Text Guide -->
                            @if ($help->description)
                                <div class="p-3 bg-white rounded-3 border">
                                    <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-align-left text-primary"></i> Instructions
                                    </h6>
                                    <p class="text-dark mb-0 lh-base">{{ $help->description }}</p>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-light rounded-4 border text-muted">
                    <i class="fa-solid fa-circle-question fs-2 mb-2 d-block opacity-50"></i>
                    No help topics available at the moment.
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
