@extends('admin.layouts.layout')

@section('title', 'help List')

@section('content')

    <div class="container mt-4">
        <h3>Help Contents</h3>
        <!-- Button to open the modal -->
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#helpContentModal">
                <i class="bi bi-plus-circle"></i> Add
            </button>
        </div>

        <!-- Table to list help content -->

        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>S.No</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>File / URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($helpContents as $index => $content)
                    <tr>

                        <td>{{ $helpContents->firstItem() + $index }}</td>
                        <td>{{ $content->title }}</td>
                        <td>{{ ucfirst($content->type) }}</td>
                        <td>
                            @if ($content->type == 'video' && $content->url)
                                <a href="{{ $content->url }}" target="_blank" class="btn btn-info">Watch Video</a>
                            @elseif($content->type == 'note' && $content->file_path)
                                <a href="{{ asset('uploads/help_contents/' . $content->file_path) }}" target="_blank"
                                    class="btn btn-secondary">Download File</a>
                            @else
                                <span>No File/URL Available</span>
                            @endif
                        </td>
                        <td>
                            <!-- Edit Button -->
                            <a href="{{ route('admin.help.edit', $content->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> <!-- Edit icon -->
                            </a>
                            <!-- Delete Button (Form method POST with delete option) -->
                            <form action="{{ route('admin.help.destroy', $content->id) }}" method="POST"
                                style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this content?')">
                                    <i class="bi bi-trash"></i> <!-- Delete icon -->
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $helpContents->links('pagination::bootstrap-5') }}

        <!-- Modal for Adding Help Content -->
        <div class="modal fade" id="helpContentModal" tabindex="-1" aria-labelledby="helpContentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="helpContentModalLabel">Add Help Content</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.help.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" id="title"
                                    placeholder="Enter title" required>
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="note">Note</option>
                                    <option value="video">Video</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" class="form-control" id="description" rows="3" required
                                    placeholder="Enter Description"></textarea>
                            </div>

                            <div class="mb-3" id="fileUploadField">
                                <label for="file" class="form-label">File (PDF/Word for Note or Video File)</label>
                                <input type="file" name="file" class="form-control" id="file">
                            </div>

                            <div class="mb-3" id="urlField" style="display: none;">
                                <label for="url" class="form-label">Video URL</label>
                                <input type="url" name="url" class="form-control" id="url">
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Content</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



    </div>

@endsection

@section('scripts')
    <script>
        // Show file input or URL input based on selected type
        document.getElementById('type').addEventListener('change', function() {
            var type = this.value;
            if (type == 'video') {
                document.getElementById('fileUploadField').style.display = 'none';
                document.getElementById('urlField').style.display = 'block';
            } else {
                document.getElementById('fileUploadField').style.display = 'block';
                document.getElementById('urlField').style.display = 'none';
            }
        });
    </script>
@endsection
