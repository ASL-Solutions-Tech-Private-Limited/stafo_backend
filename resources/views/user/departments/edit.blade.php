@extends('user.layouts.app')

@section('title', 'Edit Department')

@section('content')
    <div class="card mt-4 p-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="mb-3 fw-bold">Edit Department</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('departments.index') }}" class="btn btn-primary" style="float: right;">Department
                        List</a>
                </div>
            </div>

            <form action="{{ route('departments.update', $department->id) }}" method="POST">
                @csrf
                @method('PUT') <!-- This is important for PUT requests -->

                <div class="row">
                    <!-- Department Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Department Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $department->name) }}"
                            required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="1" {{ old('status', $department->status) == 1 ? 'selected' : '' }}>Active
                            </option>
                            <option value="0" {{ old('status', $department->status) == 0 ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-md-6 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control"
                            rows="3">{{ old('description', $department->description) }}</textarea>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Save Button -->
                    <div class="col-md-6 mb-3 d-flex align-items-end justify-content-end">
                        <button type="submit" class="btn d-inline-block btn-success ">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection