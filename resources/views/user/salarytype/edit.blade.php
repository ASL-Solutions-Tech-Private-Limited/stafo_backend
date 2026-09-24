@extends('user.layouts.app')

@section('title', 'Edit Salary Component | STAFO HRMS')

@section('content')
@include('user.layouts.alert')

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Edit Salary Component</h3>
                <p class="text-muted small mb-0">Modify allowance, bonus, deduction, or statutory contribution rates</p>
            </div>
            <a href="{{ route('salarytype.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Salary Types
            </a>
        </div>
        
        <form action="{{ route('salarytype.update', $salarytype->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="payment_type" class="form-label fw-semibold text-dark">Payment Type <span class="text-danger">*</span></label>
                        <select name="payment_type" id="payment_type" class="form-select" required>
                            <option value="Earning" {{ old('payment_type', $salarytype->payment_type) == 'Earning' ? 'selected' : '' }}>Earning (Addition)</option>
                            <option value="Deduction" {{ old('payment_type', $salarytype->payment_type) == 'Deduction' ? 'selected' : '' }}>Deduction (Subtraction)</option>
                        </select>
                        @error('payment_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="salary_type" class="form-label fw-semibold text-dark">Salary Component Name <span class="text-danger">*</span></label>
                        <input type="text" name="salary_type" id="salary_type" class="form-control" value="{{ old('salary_type', $salarytype->salary_type) }}" placeholder="e.g. HRA, Medical, Travel Allowance" required>
                        @error('salary_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label for="salary_type_description" class="form-label fw-semibold text-dark">Description</label>
                        <textarea name="salary_type_description" id="salary_type_description" class="form-control" rows="3" placeholder="Optional description for this salary component">{{ old('salary_type_description', $salarytype->salary_type_description) }}</textarea>
                        @error('salary_type_description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="amount" class="form-label fw-semibold text-dark">Amount / Value <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount', $salarytype->amount) }}" required>
                        @error('amount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="amount_type" class="form-label fw-semibold text-dark">Amount Type <span class="text-danger">*</span></label>
                        <select name="amount_type" id="amount_type" class="form-select" required>
                            <option value="Flat" {{ old('amount_type', $salarytype->amount_type) == 'Flat' ? 'selected' : '' }}>Flat Amount (₹)</option>
                            <option value="Percentage" {{ old('amount_type', $salarytype->amount_type) == 'Percentage' ? 'selected' : '' }}>Percentage of Basic (%)</option>
                        </select>
                        @error('amount_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="status" class="form-label fw-semibold text-dark">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1" {{ old('status', $salarytype->status) == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $salarytype->status) == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-12 text-end pt-3 border-top">
                    <a href="{{ route('salarytype.index') }}" class="btn btn-outline-secondary px-4 me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Salary Component
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection