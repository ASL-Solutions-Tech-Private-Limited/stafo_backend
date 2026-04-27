@extends('user.layouts.app')

@section('title', 'Edit Leavetype')

@section('content')
    <div class="card mt-4 p-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="mb-3 fw-bold">Edit Leavetype</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('leavetypes.index') }}" class="btn btn-primary" style="float: right;">Leavetype
                        List</a>
                </div>
            </div>

            <form action="{{ route('leavetypes.update', $leavetype->id) }}" method="POST">
                @csrf
                @method('PUT') <!-- This is important for PUT requests -->

                <div class="row">
                    <!-- Leavetype Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Leavetype Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $leavetype->name) }}"
                            required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="no_of_days" class="form-label">Max no of days leave taken per year</label>
                        <input type="text" name="no_of_days" class="form-control" value="{{ old('no_of_days', $leavetype->no_of_days) }}">
                        @error('no_of_days')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="is_paid" class="form-label">Is Paid</label>
                        <select name="is_paid" id="is_paid" class="form-control">
                            <option value="0" {{ old('is_paid', $leavetype->is_paid) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_paid', $leavetype->is_paid) == 1 ? 'selected' : '' }}>Yes</option>                            
                        </select>

                        @error('is_paid')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="1" {{ old('status', $leavetype->status) == 1 ? 'selected' : '' }}>Active
                            </option>
                            <option value="0" {{ old('status', $leavetype->status) == 0 ? 'selected' : '' }}>Inactive
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
                            rows="3">{{ old('description', $leavetype->description) }}</textarea>
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