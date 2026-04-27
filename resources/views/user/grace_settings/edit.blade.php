@extends('user.layouts.app')

@section('title', 'Edit Salary Setting')

@section('content')
    <div class="container ">
        <h3 class="mb-3">Edit Salary Setting</h3>
        <form action="{{ route('grace_settings.update', $graceSetting->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Key Field -->
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" readonly class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $graceSetting->name) }}" required>
                @error('kenamey')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Value Field -->
            <div class="mb-3">
                <label for="value" class="form-label">Value</label>
                <textarea id="value" name="value" class="form-control @error('value') is-invalid @enderror" rows="4"
                    required>{{ old('value', $graceSetting->value) }}</textarea>
                @error('value')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection