@extends('admin.layouts.layout')

@section('title', 'Create FAQ')

@section('content')
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Create New FAQ</h3>
                <p class="text-muted mb-0">Add a new question, answer, and related image.</p>
            </div>
            <a href="{{ route('faq.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to FAQs
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <form action="{{ route('faq.store') }}" method="POST" enctype="multipart/form-data" id="faq-form">
                    @csrf
                    
                    <!-- Question / Category -->
                    <div class="mb-3">
                        <label for="question" class="form-label fw-semibold">Question / Category Title <span class="text-danger">*</span></label>
                        <input type="text" name="question" id="question" class="form-control" 
                            placeholder="e.g. 1. General Questions or What is STAFO?" 
                            value="{{ old('question') }}" required>
                        @error('question')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Related Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">Related Image</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <div class="form-text">Upload a relevant screenshot, banner, or illustration for this FAQ topic (PNG, JPG, WebP).</div>
                        @error('image')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Answer (HTML / Rich Text) -->
                    <div class="mb-4">
                        <label for="answer" class="form-label fw-semibold">Answer (HTML / Rich Text) <span class="text-danger">*</span></label>
                        <textarea id="answer" name="answer" class="form-control" rows="8">{{ old('answer') }}</textarea>
                        @error('answer')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('faq.index') }}" class="btn btn-outline-danger">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save FAQ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- TinyMCE with Code and Fullscreen Support -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#answer',
            height: 350,
            menubar: true,
            plugins: ['code', 'fullscreen', 'preview', 'table', 'lists', 'link', 'image', 'autolink'],
            toolbar: 'code fullscreen preview | undo redo | blocks | bold italic underline | forecolor backcolor | alignleft aligncenter alignright | bullist numlist | link table | removeformat',
            extended_valid_elements: '*[*]',
            valid_elements: '*[*]',
            cleanup: false,
            verify_html: false,
            content_style: 'body { font-family: "Plus Jakarta Sans", sans-serif; font-size: 14px; line-height: 1.6; }'
        });
    </script>
@endsection