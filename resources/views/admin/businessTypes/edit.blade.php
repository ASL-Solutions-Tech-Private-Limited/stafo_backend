@extends('admin.layouts.layout')

@section('title', 'Edit Business Type')
@section('content')

    <div class="container">
        <h3 class="mb-3">Edit Business Type</h3>
        <form action="{{ route('businessTypes.update', $businessType->id) }}" method="POST">
            @csrf
            @method('PUT') <!-- This tells Laravel that it's a PUT request (update) -->

            <div class="form-group mb-3">
                <label for="business_name">Business Name</label>
                <input type="text" name="business_name" id="business_name" class="form-control"
                    value="{{ old('business_name', $businessType->business_name) }}" required>
            </div>

            <div class="form-group ">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="1" {{ $businessType->status == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ $businessType->status == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update</button>
        </form>
    </div>

@endsection