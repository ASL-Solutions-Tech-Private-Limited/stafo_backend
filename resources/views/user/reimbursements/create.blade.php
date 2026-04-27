@extends('user.layouts.app')

@section('title', 'Add Reimbursement')

@section('content')
    <div class="card mt-4 p-3">

        <div class="container">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="fw-bold">Add Reimbursement</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('reimbursements.index') }}" class="btn btn-primary" style="float: right;">Reimbursement
                        List</a>
                </div>
            </div>

            <form action="{{ route('reimbursements.store') }}" method="POST" enctype="multipart/form-data">
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
                    <!-- Reimbursement Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>                   

                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" name="amount" class="form-control" value="{{ old('amount') }}">
                        @error('amount')
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
                        <label for="date" class="form-label">Submission Date</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date') }}">
                        @error('date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <label for="receipt_file">Receipt File</label>
                            <input type="file" name="receipt_file" class="form-control">
                            @error('receipt_file')
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