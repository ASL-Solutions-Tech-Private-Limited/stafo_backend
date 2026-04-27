@extends('admin.layouts.layout')

@section('title', 'Create Blog ')

@section('content')
<div class="container mt-4">
    <h2>Create Blog</h2>
<form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="category_id" class="form-label">Category</label>
        <select name="category_id" class="form-control tomselect2" id="category_id" required>
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}" required>
    </div>

    <div class="mb-3">
        <label for="auther" class="form-label">Author</label>
        <input type="text" name="auther" class="form-control" id="auther" value="{{ old('auther') }}">
    </div>
    <div class="mb-3">
        <label for="date" class="form-label">Date</label>
        <input type="date" name="date" class="form-control" id="date" value="{{ old('date') }}">
    </div>
    <div class="mb-3">
        <label for="short_description" class="form-label">Short Description</label>
        <textarea name="short_description" class="form-control" id="short_description" rows="2">{{ old('short_description') }}</textarea>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" class="form-control" id="description" rows="5">{{ old('description') }}</textarea>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Image</label>
        <input type="file" name="image" class="form-control" id="image">
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" class="form-control" id="status">
            <option value="Draft" {{ old('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
            <option value="Published" {{ old('status') == 'Published' ? 'selected' : '' }}>Published</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="tags" class="form-label">Tags</label>
        <select name="tags[]" class="form-control tomselect" id="tags" multiple>
            <option value="">Select Tag</option>
            @foreach($tags as $tag)
                <option value="{{ $tag->id }}" {{ old('tags') == $tag->id ? 'selected' : '' }}>
                    {{ $tag->name }}
                </option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Create Blog</button>
</form>
</div>
@endsection