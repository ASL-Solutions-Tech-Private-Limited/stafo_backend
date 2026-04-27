@extends('admin.layouts.layout')

@section('title', 'Document Type List')
@section('content')

<div class="container">
    <h1>Employee Details</h1>
    <p><strong>Name:</strong> {{ $employee->name }}</p>
    <p><strong>Email:</strong> {{ $employee->email }}</p>
    <p><strong>Phone:</strong> {{ $employee->phone }}</p>
    <p><strong>Position:</strong> {{ $employee->position }}</p>
    <p><strong>Salary:</strong> {{ $employee->salary }}</p>
    <a href="{{ route('employees.list') }}" class="btn btn-secondary">Back to List</a>
</div>

@endsection