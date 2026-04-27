@extends('user.layouts.app')

@section('title', 'Add CompoffLeave')

@section('content')
    <div class="card mt-4 p-3">

        <div class="container">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="fw-bold">Add CompoffLeave</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('compoffleaves.index') }}" class="btn btn-primary" style="float: right;">CompoffLeave
                        List</a>
                </div>
            </div>

            <form action="{{ route('compoffleaves.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="employee_id">Employee</label>
                            <select class="form-control" name="employee_id" required id="employee_id">
                                <option value="">Select Employee</option>
                                @if (!$employees->isEmpty())
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <!-- CompoffLeave Name -->
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                        @error('title')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>                   

                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ old('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ old('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
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
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date') }}">
                        @error('date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <label for="filename">File</label>
                            <input type="file" name="filename" class="form-control">
                            @error('filename')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
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