@extends('user.layouts.app')

@section('title', 'Branch add') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 ">
        <div class="container">
            <h2 class="fw-bold mb-3">Create New Branch</h2>
            <form action="{{ route('branche.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="branch_name">Branch Name</label>
                            <input type="text" name="branch_name" class="form-control" value="{{ old('branch_name') }}"
                                required>
                            @error('branch_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="branch_address">Branch Address</label>
                            <input type="text" name="branch_address" class="form-control"
                                value="{{ old('branch_address') }}" required>
                            @error('branch_address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="status">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary ">Create Branch</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection