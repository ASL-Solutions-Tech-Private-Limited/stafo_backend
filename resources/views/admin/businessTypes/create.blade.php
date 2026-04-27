@extends('admin.layouts.layout')

@section('title', 'Document Type List')
@section('content')

    <div class="container">
        <h3 class="mb-3">Create New Business Type</h3>
        <form action="{{ route('businessTypes.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="business_name">Business Name</label>
                <input type="text" name="business_name" id="business_name" class="form-control" required>
            </div>
            <div class="form-group ">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Create</button>
        </form>
    </div>

@endsection