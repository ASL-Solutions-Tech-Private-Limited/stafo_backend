@extends('user.layouts.app')

@section('title', 'Help List')

@section('content')

    <!-- Section Title -->
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold">📚 Help & Support</h2>

        </div>

        <!-- Bootstrap Accordion -->
        <div class="accordion" id="hrHelpAccordion">
            @forelse ($helpContents as $index => $help)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $index }}">
                            🧾 {{ $help->title }}
                        </button>
                    </h2>
                    <div id="collapse{{ $index }}"
                        class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                        aria-labelledby="heading{{ $index }}" data-bs-parent="#hrHelpAccordion">
                        <div class="accordion-body">

                            {{-- Notes --}}
                            @if ($help->file_path)
                                <div class="mb-3">
                                    <h5>📓 Notes</h5>
                                    <a href="{{ asset('uploads/help_contents/' . $help->file_path) }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm">
                                        View Notes
                                    </a>
                                </div>
                            @endif

                            {{-- Video --}}
                            {{-- @if ($help->type == 'video' && $help->url) --}}
                            {{-- {{ dd($help->url) }} --}}
                            <div class="mb-3">
                                <h5>🎥 Video</h5>
                                <div class="ratio ratio-16x9">
                                    <iframe src="{{ $help->url }}" title="Help Video" allowfullscreen></iframe>
                                </div>
                            </div>
                            {{-- @endif --}}

                            {{-- Description --}}
                            @if ($help->description)
                                <div>
                                    <h5>📝 Description</h5>
                                    <p>{{ $help->description }}</p>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-warning text-center">
                    No help content found.
                </div>
            @endforelse
        </div>
    </div>

@endsection
