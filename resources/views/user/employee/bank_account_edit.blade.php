@extends('user.layouts.app')

@section('title', 'Edit Bank Account | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Edit Bank Account</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-user me-1"></i> {{ $bankAccount->employee->name ?? 'Employee' }}
                    </span>
                </div>
                <p class="text-muted small mb-0">Modify employee disbursement bank account and IFSC code credentials</p>
            </div>
            <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Employee List
            </a>
        </div>

        <form action="{{ route('updateBankAccount', $bankAccount->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Banking Credentials Card -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-building-columns text-primary"></i> Bank Account Information
                </h5>

                <div class="row g-3">
                    
                    <!-- Account Holder Name -->
                    <div class="col-12 col-md-6">
                        <label for="account_holder_name" class="form-label fw-semibold text-dark">
                            Account Holder Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-user text-muted"></i></span>
                            <input type="text" name="account_holder_name" id="account_holder_name" 
                                   class="form-control @error('account_holder_name') is-invalid @enderror"
                                   value="{{ old('account_holder_name', $bankAccount->account_holder_name) }}" 
                                   placeholder="Full name as printed in bank passbook" required>
                        </div>
                        @error('account_holder_name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Account Number -->
                    <div class="col-12 col-md-6">
                        <label for="account_number" class="form-label fw-semibold text-dark">
                            Bank Account Number <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-credit-card text-muted"></i></span>
                            <input type="text" name="account_number" id="account_number" 
                                   class="form-control @error('account_number') is-invalid @enderror"
                                   value="{{ old('account_number', $bankAccount->account_number) }}" 
                                   placeholder="e.g. 123456789012" required>
                        </div>
                        @error('account_number')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bank Name -->
                    <div class="col-12 col-md-4">
                        <label for="bank_name" class="form-label fw-semibold text-dark">
                            Bank Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-building text-muted"></i></span>
                            <input type="text" name="bank_name" id="bank_name" 
                                   class="form-control @error('bank_name') is-invalid @enderror" 
                                   value="{{ old('bank_name', $bankAccount->bank_name) }}" 
                                   placeholder="e.g. HDFC Bank, SBI, ICICI" required>
                        </div>
                        @error('bank_name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Branch Name -->
                    <div class="col-12 col-md-4">
                        <label for="branch_name" class="form-label fw-semibold text-dark">
                            Bank Branch <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-location-dot text-muted"></i></span>
                            <input type="text" name="branch_name" id="branch_name" 
                                   class="form-control @error('branch_name') is-invalid @enderror" 
                                   value="{{ old('branch_name', $bankAccount->branch_name) }}" 
                                   placeholder="e.g. Connaught Place, Mumbai Main" required>
                        </div>
                        @error('branch_name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- IFSC Code -->
                    <div class="col-12 col-md-4">
                        <label for="ifsc_code" class="form-label fw-semibold text-dark">
                            IFSC Code <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-code text-muted"></i></span>
                            <input type="text" name="ifsc_code" id="ifsc_code" 
                                   class="form-control text-uppercase @error('ifsc_code') is-invalid @enderror" 
                                   value="{{ old('ifsc_code', $bankAccount->ifsc_code) }}" 
                                   placeholder="e.g. HDFC0001234" required>
                        </div>
                        @error('ifsc_code')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Actions Bar -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Bank Account
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
