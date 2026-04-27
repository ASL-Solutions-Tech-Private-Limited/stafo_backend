@extends('user.layouts.app')

@section('title', 'Edit Branch') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 edit-page">
        <h2 class="mb-3 fw-bold">Edit Branch</h2>

        <form action="{{ route('branche.update', $branch->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="branch_name ">Branch Name</label>
                        <input type="text" name="branch_name" class="form-control"
                            value="{{ old('branch_name', $branch->branch_name) }}" required>
                        @error('branch_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="branch_address">Branch Address</label>
                        <input type="text" name="branch_address" class="form-control"
                            value="{{ old('branch_address', $branch->branch_address) }}" required>
                        @error('branch_address')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="active" {{ old('status', $branch->status) == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="inactive" {{ old('status', $branch->status) == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary ">Update Branch</button>
                    <a href="{{ route('branche.index') }}" class="btn btn-danger ">Cancel</a>
                </div>
            </div>







        </form>
    </div>
@endsection