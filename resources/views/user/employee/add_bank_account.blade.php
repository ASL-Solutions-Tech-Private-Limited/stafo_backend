@extends('user.layouts.app')
@section('css')
    <style>
        /* Style for the mandatory asterisk (*) */
        .mandatory {
            color: red;
        }
    </style>
@endsection
@section('title', 'Add Bank Account')

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <h2 class="text-center mb-4 fw-bold">Add Bank Account for <span class="text-primary">{{ $employee->name }}</span>
        </h2>
        <div class="container">
            <form action="{{ route('storeBankAccount', $employee->id) }}" method="POST">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-6 mb-2">
                        <label for="account_number">Account Number <span class="mandatory"> *</span></label>
                        <input type="text" name="account_number" class="form-control"
                            value="{{ old('account_number') }}">
                        @error('account_number')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="bank_name">Bank Name <span class="mandatory"> *</span></label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
                        @error('bank_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="branch_name">Branch Name <span class="mandatory"> *</span></label>
                        <input type="text" name="branch_name" class="form-control" value="{{ old('branch_name') }}">
                        @error('branch_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="ifsc_code">IFSC Code <span class="mandatory"> *</span></label>
                        <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code') }}">
                        @error('ifsc_code')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="account_holder_name">Account Holder Name <span class="mandatory"> *</span></label>
                        <input type="text" name="account_holder_name" class="form-control"
                            value="{{ old('account_holder_name') }}">
                        @error('account_holder_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 text-end mb-2">
                        <button type="submit" class="btn btn-success">Save Bank Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
