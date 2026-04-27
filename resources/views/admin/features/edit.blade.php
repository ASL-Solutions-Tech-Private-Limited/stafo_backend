<!-- resources/views/admin/features/edit.blade.php -->
@extends('admin.layouts.layout')

@section('title', 'Edit Feature')

@section('content')
    <div class="container">
        <h3 class="mb-3">Edit Feature</h3>
        <form action="{{ route('features.update', $feature->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Feature Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $feature->name) }}" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" id="description"
                    class="form-control">{{ old('description', $feature->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label>Icon (optional)</label>
                @if ($feature->icon)
                    <img src="{{ asset('storage/icons/' . $feature->icon) }}" alt="{{ $feature->name }}" width="30">
                    <p>Current Icon: {{ $feature->icon }}</p>
                @endif
                <input type="file" name="icon" class="form-control">
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1" {{ old('status', $feature->status) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $feature->status) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('features.index') }}" class="btn btn-warning">Back</a>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection