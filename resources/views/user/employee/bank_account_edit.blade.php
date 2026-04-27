@extends('user.layouts.app')
@section('css')
    <style>
        /* Style for the mandatory asterisk (*) */
        .mandatory {
            color: red;
        }
    </style>
@endsection
@section('title', 'Edit Bank Account')

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <h5 class="text-center text-primary mb-4">Edit Bank Account for {{ $bankAccount->employee->name }}</h5>
        <div class="container">
            <form action="{{ route('updateBankAccount', $bankAccount->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="account_number">Account Number <span class="mandatory"> *</span></label>
                        <input type="text" name="account_number" class="form-control"
                            value="{{ old('account_number', $bankAccount->account_number) }}" required>
                        @error('account_number')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="bank_name">Bank Name <span class="mandatory"> *</span></label>
                        <input type="text" name="bank_name" class="form-control"
                            value="{{ old('bank_name', $bankAccount->bank_name) }}" required>
                        @error('bank_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="branch_name">Branch Name <span class="mandatory"> *</span></label>
                        <input type="text" name="branch_name" class="form-control"
                            value="{{ old('branch_name', $bankAccount->branch_name) }}" required>
                        @error('branch_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="ifsc_code">IFSC Code <span class="mandatory"> *</span></label>
                        <input type="text" name="ifsc_code" class="form-control"
                            value="{{ old('ifsc_code', $bankAccount->ifsc_code) }}" required>
                        @error('ifsc_code')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="account_holder_name">Account Holder Name <span class="mandatory"> *</span></label>
                        <input type="text" name="account_holder_name" class="form-control"
                            value="{{ old('account_holder_name', $bankAccount->account_holder_name) }}" required>
                        @error('account_holder_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-2">
                        <button type="submit" class="btn btn-success w-100">Save Bank Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
