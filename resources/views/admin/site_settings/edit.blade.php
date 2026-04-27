@extends('admin.layouts.layout')

@section('title', 'Edit Site Setting')

@section('content')
    <div class="container ">
        <h3 class="mb-3">Edit Site Setting</h3>
        <form action="{{ route('site_settings.update', $siteSetting->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Key Field -->
            <div class="mb-3">
                <label for="key" class="form-label">Key</label>
                <input type="text" readonly class="form-control @error('key') is-invalid @enderror"
                    value="{{ old('key', $siteSetting->key) }}" required>
                @error('key')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Value Field -->
            <div class="mb-3">
                <label for="value" class="form-label">Value</label>
                <textarea id="value" name="value" class="form-control @error('value') is-invalid @enderror" rows="4"
                    required>{{ old('value', $siteSetting->value) }}</textarea>
                @error('value')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection