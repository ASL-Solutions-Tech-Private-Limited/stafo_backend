@extends('admin.layouts.layout')

@section('title', 'Add New Feature')

@section('content')
    <div class="container">
        <h2>Add New Feature</h2>
        <form action="{{ route('features.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>Feature Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" id="description">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label>Icon (optional)</label>
                <input type="file" name="icon" class="form-control">
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('features.index') }}" class="btn btn-secondary">Back</a>
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
