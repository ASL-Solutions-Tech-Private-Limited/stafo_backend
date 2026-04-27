@extends('admin.layouts.layout')

@section('title', 'Create Branch')

@section('content')
    <div class="container mt-4">
        <h3>Create Branch</h3>

        <form action="{{ route('branches.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Company -->
                <div class="col-md-6 mb-3">
                    <label for="company_id" class="form-label">Company</label>
                    <select name="company_id" class="form-control @error('company_id') is-invalid @enderror">
                        <option value="">Select a Company</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Branch Name -->
                <div class="col-md-6 mb-3">
                    <label for="branch_name" class="form-label">Branch Name</label>
                    <input type="text" name="branch_name" class="form-control @error('branch_name') is-invalid @enderror"
                        value="{{ old('branch_name') }}" placeholder="Enter Branch Name">
                    @error('branch_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Branch Address -->
                <div class="col-md-12 mb-3">
                    <label for="branch_address" class="form-label">Branch Address</label>
                    <textarea name="branch_address" class="form-control @error('branch_address') is-invalid @enderror" rows="4"
                        placeholder="Enter Branch Address">{{ old('branch_address') }}</textarea>
                    @error('branch_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <!-- Latitude -->
                <div class="col-md-4 mb-3">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror"
                        value="{{ old('latitude') }}" placeholder="Enter Latitude">
                    @error('latitude')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Longitude -->
                <div class="col-md-4 mb-3">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror"
                        value="{{ old('longitude') }}" placeholder="Enter Longitude">
                    @error('longitude')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Radar -->
                <div class="col-md-4 mb-3">
                    <label for="radar" class="form-label">Radar</label>
                    <input type="text" name="radar" class="form-control @error('radar') is-invalid @enderror"
                        value="{{ old('radar') }}" placeholder="Enter Radar">
                    @error('radar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Create Branch</button>
        </form>
    </div>
@endsection
