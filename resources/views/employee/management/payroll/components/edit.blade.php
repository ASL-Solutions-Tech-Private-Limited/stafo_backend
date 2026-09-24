@extends('employee.layouts.app')

@section('title', 'Edit Salary Component | Management Portal')

@section('content')
<div class="container-fluid p-0">

    <div class="card shadow-sm border-0 rounded-4 mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Edit Salary Component</h4>
                    <p class="text-muted small mb-0">Modify allowance, bonus, deduction, or statutory contribution rates</p>
                </div>
                <a href="{{ route('employee.management.payroll.components') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Salary Types
                </a>
            </div>
            
            <form action="{{ route('employee.management.payroll.components.update', $salarytype->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="payment_type" class="form-label fw-semibold text-dark small">Payment Type <span class="text-danger">*</span></label>
                        <select name="payment_type" id="payment_type" class="form-select" required>
                            <option value="Earning" {{ old('payment_type', $salarytype->payment_type) == 'Earning' ? 'selected' : '' }}>Earning (Addition)</option>
                            <option value="Deduction" {{ old('payment_type', $salarytype->payment_type) == 'Deduction' ? 'selected' : '' }}>Deduction (Subtraction)</option>
                        </select>
                        @error('payment_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="salary_type" class="form-label fw-semibold text-dark small">Salary Component Name <span class="text-danger">*</span></label>
                        <input type="text" name="salary_type" id="salary_type" class="form-control" value="{{ old('salary_type', $salarytype->salary_type) }}" placeholder="e.g. HRA, Medical, Travel Allowance" required>
                        @error('salary_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label for="salary_type_description" class="form-label fw-semibold text-dark small">Description</label>
                        <textarea name="salary_type_description" id="salary_type_description" class="form-control" rows="2" placeholder="Optional description for this salary component">{{ old('salary_type_description', $salarytype->salary_type_description) }}</textarea>
                        @error('salary_type_description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="amount_type" class="form-label fw-semibold text-dark small">Calculation Type <span class="text-danger">*</span></label>
                        <select name="amount_type" id="amount_type" class="form-select" required>
                            <option value="Flat" {{ old('amount_type', $salarytype->amount_type) == 'Flat' ? 'selected' : '' }}>Flat Amount (₹)</option>
                            <option value="Percentage" {{ old('amount_type', $salarytype->amount_type) == 'Percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        </select>
                        @error('amount_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="amount" class="form-label fw-semibold text-dark small">Value / Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light" id="amount-symbol">₹</span>
                            <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" value="{{ old('amount', $salarytype->amount) }}" required>
                        </div>
                        @error('amount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label for="department_id" class="form-label fw-semibold text-dark small">Target Department</label>
                        <select name="department_id" id="department_id" class="form-select">
                            <option value="">General (All Departments)</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $salarytype->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold text-dark small">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="1" {{ old('status', $salarytype->status) == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $salarytype->status) == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('employee.management.payroll.components') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 fw-semibold">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                        <i class="fa-solid fa-check me-1"></i> Update Component
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountTypeSelect = document.getElementById('amount_type');
    const amountSymbol = document.getElementById('amount-symbol');
    
    function updateSymbol() {
        if (amountTypeSelect.value === 'Percentage') {
            amountSymbol.textContent = '%';
        } else {
            amountSymbol.textContent = '₹';
        }
    }
    
    amountTypeSelect.addEventListener('change', updateSymbol);
    updateSymbol();
});
</script>
@endsection
