@extends('admin.layouts.layout')

@section('title', 'Create Blog Category')

@section('content')
<div class="container mt-4">
    <h2>Create Blog Category</h2>
    <form action="{{ route('admin.blog_categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
            @error('name')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description (optional)</label>
            <textarea name="description" id="description" class="form-control"></textarea>
            @error('description')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Create Category</button>
        <a href="{{ route('admin.blog_categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection