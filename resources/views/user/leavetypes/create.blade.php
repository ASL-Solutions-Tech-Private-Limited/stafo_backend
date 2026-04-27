@extends('user.layouts.app')

@section('title', 'Add Leavetype')

@section('content')
    <div class="card mt-4 p-3">

        <div class="container">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="fw-bold">Add Leavetype</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('leavetypes.index') }}" class="btn btn-primary" style="float: right;">Leavetype
                        List</a>
                </div>
            </div>

            <form action="{{ route('leavetypes.store') }}" method="POST">
                @csrf
                <div class="row align-items-end">

                    <!-- Leavetype Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Leavetype Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="no_of_days" class="form-label">Max no of days leave taken per year</label>
                        <input type="text" name="no_of_days" class="form-control" value="{{ old('no_of_days') }}">
                        @error('no_of_days')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="is_paid" class="form-label">Is Paid</label>
                        <select name="is_paid" id="is_paid" class="form-control">
                            <option value="0" {{ old('is_paid', 0) == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_paid', 0) == 1 ? 'selected' : '' }}>Yes</option>                            
                        </select>

                        @error('is_paid')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>

                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-md-6 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>



                    <!-- Save Button -->
                    <div class="col-md-6 mb-3 text-end">
                        <button type="submit" class="btn btn-success ">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection