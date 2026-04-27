@extends('user.layouts.app')

@section('title', 'Salary Type add') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 ">
        <div class="container">
            <h2 class="fw-bold mb-3">New Salary Type</h2>
            <form action="{{ route('salarytype.store') }}" method="POST">
                @csrf

                <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="payment_type ">Payment Type</label>
                        <select name="payment_type" class="form-control" required>
                            <option value="Earning" {{ old('payment_type') }}>
                            Earning</option>
                            <option value="Deduction" {{ old('payment_type') }}>
                            Deduction</option>
                            
                        </select>                        
                        @error('payment_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="salary_type">Salary Type</label>
                            <input type="text" name="salary_type" class="form-control" value="{{ old('salary_type') }}"
                                required>
                            @error('salary_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="salary_type_description">Description</label>
                            <input type="text" name="salary_type_description" class="form-control"
                                value="{{ old('salary_type_description') }}">
                            @error('salary_type_description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="amount">Amount</label>
                            <input type="text" name="amount" class="form-control" value="{{ old('amount') }}"
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
                                <option value="Flat" {{ old('amount_type') == 'Flat' ? 'selected' : '' }}>Flat</option>
                                <option value="Percentage" {{ old('amount_type') == 'Percentage' ? 'selected' : '' }}>Percentage</option>
                            </select>
                            @error('amount_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary ">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection