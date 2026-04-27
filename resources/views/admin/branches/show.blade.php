@extends('admin.layouts.layout')

@section('title', 'View Branch')

@section('content')
    <div class="container">
        <h3>Branch Details</h3>
        <div class="mb-3">
            <strong>Company Name:</strong> {{ $branch->company->company_name }}
        </div>
        <div class="mb-3">
            <strong>Branch Name:</strong> {{ $branch->branch_name }}
        </div>
        <div class="mb-3">
            <strong>Branch Address:</strong> {{ $branch->branch_address }}
        </div>
        <a href="{{ route('branches.index') }}" class="btn btn-warning">Back to List</a>
    </div>
@endsection