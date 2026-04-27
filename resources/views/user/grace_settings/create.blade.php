@extends('user.layouts.app')

@section('title', 'Add Salary Setting')

@section('content')
    <div class="container ">
        <h3 class="mb-3">Add Salary Setting</h3>
        <form action="{{ route('grace_settings.store') }}" method="POST">
            @csrf

            <!-- Key Field -->
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required>
                @error('name')
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