@extends('admin.layouts.layout')

@section('title', 'Edit Help Content')

@section('content')

    <div class="container mt-4">
        <h3>Edit Help Content</h3>

        <form action="{{ route('admin.help.update', $helpContent->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Title Field -->
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" id="title"
                            value="{{ old('title', $helpContent->title) }}" required>
                    </div>
                </div>

                <!-- Type Field -->
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="note" {{ $helpContent->type === 'note' ? 'selected' : '' }}>Note</option>
                            <option value="video" {{ $helpContent->type === 'video' ? 'selected' : '' }}>Video</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Description Field -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" id="description" rows="4" required>{{ old('description', $helpContent->description) }}</textarea>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <!-- URL Field -->
                    <div class="mb-3">
                        <label for="url" class="form-label">URL (optional)</label>
                        <input type="url" name="url" class="form-control" id="url"
                            value="{{ old('url', $helpContent->url) }}" placeholder="Enter the URL here">
                    </div>
                </div>

                <div class="col-lg-6">
                    <!-- File input for 'note' type -->
                    @if ($helpContent->type == 'note')
                        <div class="mb-3">
                            <label for="file" class="form-label">File (PDF/Word for Note or Video File)</label>
                            <input type="file" name="file" class="form-control" id="file">
                            @if ($helpContent->file_path)
                                <small class="form-text text-muted">
                                    Current file: <a href="{{ asset('uploads/help_contents/' . $helpContent->file_path) }}"
                                        target="_blank">{{ $helpContent->file_path }}</a>
                                </small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>




            <!-- Submit Button -->
            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary btn-md">Update</button>
            </div>
        </form>

    </div>

@endsection
