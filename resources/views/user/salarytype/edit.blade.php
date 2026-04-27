@extends('user.layouts.app')

@section('title', 'Edit Salary Type')

@section('content')
<div class="card mt-4 p-3">
    <div class="container">
        <h2 class="fw-bold mb-3">Edit Salary Type</h2>
        
        <form action="{{ route('salarytype.update', $salarytype->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="payment_type">Payment Type <span class="text-danger">*</span></label>
                        <select name="payment_type" class="form-control" required>
                            <option value="Earning" {{ old('payment_type', $salarytype->payment_type) == 'Earning' ? 'selected' : '' }}>Earning</option>
                            <option value="Deduction" {{ old('payment_type', $salarytype->payment_type) == 'Deduction' ? 'selected' : '' }}>Deduction</option>
                        </select>
                        @error('payment_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="salary_type">Salary Type <span class="text-danger">*</span></label>
                        <input type="text" name="salary_type" class="form-control" value="{{ old('salary_type', $salarytype->salary_type) }}" required>
                        @error('salary_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="salary_type_description">Description</label>
                        <textarea name="salary_type_description" class="form-control" rows="3">{{ old('salary_type_description', $salarytype->salary_type_description) }}</textarea>
                        @error('salary_type_description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="amount">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $salarytype->amount) }}" required>
                        @error('amount')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="amount_type">Amount Type <span class="text-danger">*</span></label>
                        <select name="amount_type" class="form-control" required>
                            <option value="Flat" {{ old('amount_type', $salarytype->amount_type) == 'Flat' ? 'selected' : '' }}>Flat</option>
                            <option value="Percentage" {{ old('amount_type', $salarytype->amount_type) == 'Percentage' ? 'selected' : '' }}>Percentage</option>
                        </select>
                        @error('amount_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ old('status', $salarytype->status) == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $salarytype->status) == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-12 text-end">
                    <a href="{{ route('salarytype.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection