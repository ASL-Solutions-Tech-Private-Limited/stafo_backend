@extends('admin.layouts.layout')

@section('title', 'Add Site Setting')

@section('content')
    <div class="container ">
        <h3 class="mb-3">Add Site Setting</h3>
        <form action="{{ route('site_settings.store') }}" method="POST">
            @csrf

            <!-- Key Field -->
            <div class="mb-3">
                <label for="key" class="form-label">Key</label>
                <input type="text" id="key" name="key" class="form-control @error('key') is-invalid @enderror"
                    value="{{ old('key') }}" required>
                @error('key')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Value Field -->
            <div class="mb-3">
                <label for="value" class="form-label">Value</label>
                <textarea id="value" name="value" class="form-control @error('value') is-invalid @enderror" rows="4"
                    required>{{ old('value') }}</textarea>
                @error('value')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-success">Save</button>
        </form>
    </div>
@endsection