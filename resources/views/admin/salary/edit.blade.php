@extends('admin.layouts.layout')

@section('title', 'Edit Salary Type') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 edit-page">
        <h2 class="mb-3 fw-bold">Edit Salary Type</h2>

        <form action="{{ route('salarytype.update', $salarytype->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="payment_type ">Payment Type</label>
                        <select name="payment_type" class="form-control" required>
                            <option value="Earning" {{ old('payment_type', $salarytype->payment_type) == 'Earning' ? 'selected' : '' }}>
                            Earning</option>
                            <option value="Deduction" {{ old('payment_type', $salarytype->payment_type) == 'Deduction' ? 'selected' : '' }}>
                            Deduction</option>
                            
                        </select>                        
                        @error('payment_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="salary_type ">Salarytype</label>
                        <input type="text" name="salary_type" class="form-control"
                            value="{{ old('salary_type', $salarytype->salary_type) }}" required>
                        @error('salary_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="salary_type_description">Description</label>
                            <input type="text" name="salary_type_description" class="form-control"
                                value="{{ old('salary_type_description', $salarytype->salary_type_description) }}">
                            @error('salary_type_description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="amount">Amount</label>
                            <input type="text" name="amount" class="form-control" value="{{ old('amount', $salarytype->amount) }}"
                                required>
                            @error('amount')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="amount_type">Amount Type</label>
                            <select name="amount_type" class="form-control" required>
                                <option value="Flat" {{ $salarytype->amount_type == 'Flat' ? 'selected' : '' }}>Flat</option>
                                <option value="Percentage" {{ $salarytype->amount_type == 'Percentage' ? 'selected' : '' }}>Percentage</option>
                            </select>
                            @error('amount_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="1" {{ old('status', $salarytype->status) == '1' ? 'selected' : '' }}>Active
                            </option>
                            <option value="0" {{ old('status', $salarytype->status) == '0' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary ">Update</button>
                    <a href="{{ route('salarytype.index') }}" class="btn btn-danger ">Cancel</a>
                </div>
            </div>







        </form>
    </div>
@endsection