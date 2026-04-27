@extends('admin.layouts.layout')
@section('title', 'Create Package')

@section('content')
    <div class="container">
        <h3 class="mb-3">Add New Package</h3>
        <form action="{{ route('packages.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Package Name</label>
                <input type="text" name="package_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" id="description"></textarea>
            </div>
            <div class="mb-3">
                <label>Price</label>
                <input type="number" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Discount Price</label>
                <input type="number" name="discount_price" class="form-control">
            </div>

            <div class="mb-3">
                <label>Days</label>
                <input type="number" name="days" class="form-control">
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- Features selection -->
            <div class="mb-3">
                <label class="mb-2">Features</label>
                <div class="form-check">
                    @foreach ($features as $feature)
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="features[{{ $feature->id }}]"
                                id="feature-{{ $feature->id }}" value="{{ $feature->id }}">
                            <label class="form-check-label" for="feature-{{ $feature->id }}">
                                {{ $feature->name }}
                            </label>
                            <input type="text" name="feature_value[{{ $feature->id }}]" class="form-control mt-2"
                                placeholder="Enter value for {{ $feature->name }}">
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('packages.index') }}" class="btn btn-warning">Back</a>
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