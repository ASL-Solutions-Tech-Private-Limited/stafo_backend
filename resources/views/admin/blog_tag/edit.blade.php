@extends('admin.layouts.layout')

@section('title', 'Edit Blog Tag')

@section('content')
<div class="container mt-4">
    <h2>Edit Blog Tag</h2>
    <form action="{{ route('admin.blog_tag.update', $tag->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Tag Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $tag->name) }}" required>
            @error('name')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $tag->description) }}</textarea>
            @error('description')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Update Tag</button>
        <a href="{{ route('admin.blog_tag.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection