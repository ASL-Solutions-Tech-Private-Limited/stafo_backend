@extends('user.layouts.app')

@section('title', 'Edit Reimbursement')

@section('content')
    <div class="card mt-4 p-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="mb-3 fw-bold">Edit Reimbursement</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('reimbursements.index') }}" class="btn btn-primary" style="float: right;">Reimbursement
                        List</a>
                </div>
            </div>

            <form action="{{ route('reimbursements.update', $reimbursement->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') <!-- This is important for PUT requests -->

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="employee_id">Employee</label>
                            <select class="form-control" name="employee_id" required id="employee_id">
                                <option value="">Select Employee</option>
                                @if (!$employees->isEmpty())
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ ($reimbursement->employee_id == $employee->id )?'selected':'' }}>{{ $employee->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <!-- Reimbursement Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Reimbursement Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $reimbursement->name) }}"
                            required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" name="amount" class="form-control" value="{{ old('amount', $reimbursement->amount) }}">
                        @error('amount')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="Pending" {{ old('status', $reimbursement->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ old('status', $reimbursement->status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ old('status', $reimbursement->status) == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>

                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    

                    <!-- Description -->
                    <div class="col-md-6 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" class="form-control"
                            rows="3">{{ old('description', $reimbursement->description) }}</textarea>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label">Submission Date</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', $reimbursement->date) }}">
                        @error('date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <label for="receipt_file">Receipt File</label>
                            <input type="file" name="receipt_file" class="form-control" >
                            @error('receipt_file')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
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