@extends('admin.layouts.layout')

@section('title', 'Employee Details')

@section('content')

    <div class="container mt-4">
        <h3 class="mb-4">Employee Details</h3>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $employee->name }}</p>
                        <p><strong>Email:</strong> {{ $employee->email }}</p>
                        <p><strong>Phone:</strong> {{ $employee->phone }}</p>
                        <p><strong>Position:</strong> {{ $employee->position }}</p>
                        <p><strong>Salary:</strong> {{ $employee->salary }}</p>
                    </div>

                    <div class="col-md-6">
                        <p><strong>Company:</strong> {{ $employee->company ? $employee->company->company_name : 'N/A' }}</p>
                        <p><strong>Branch:</strong> {{ $employee->branch ? $employee->branch->branch_name : 'N/A' }}</p>
                        <p><strong>Department:</strong> {{ $employee->department ? $employee->department->name : 'N/A' }}
                        </p>
                        <p><strong>Gender:</strong> {{ $employee->gender }}</p>
                        <p><strong>Marital Status:</strong> {{ $employee->marital_status }}</p>
                    </div>
                </div>

                <!-- Optional Section for Date of Birth, Joining, Leaving -->
                <div class="row ">
                    <div class="col-md-6">
                        <p><strong>Date of Birth:</strong>
                            {{ $employee->date_of_birth ? $employee->date_of_birth : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Date of Joining:</strong>
                            {{ $employee->date_of_joining ? $employee->date_of_joining : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Date of Leaving:</strong>
                            {{ $employee->date_of_leaving ? $employee->date_of_leaving : 'N/A' }}</p>
                    </div>
                </div>

                <div class="row mt-">
                    <div class="col-md-6">
                        <p><strong>Country:</strong> {{ $country ? $country->name : 'N/A' }}</p>

                    </div>

                    <div class="col-md-6">
                        <p><strong>State:</strong> {{ $state ? $state->name : 'N/A' }}</p>
                    </div>

                    <div class="col-md-6">
                        <p><strong>City:</strong> {{ $city ? $city->name : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                    <p><strong>Address:</strong> {{ $employee->address }}</p>
                    </div>
                    <div class="col-md-6">
  <!-- Optional: Employee Document -->
  @if ($employee->document)
                    <p><strong>Document:</strong> <a href="{{ asset('storage/' . $employee->document->file_path) }}"
                            target="_blank">View Document</a></p>
                @else
                    <p><strong>Document:</strong> N/A</p>
                @endif

                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('employees.list') }}" class="btn btn-warning">Back to List</a>
                </div>
            </div>
        </div>
    </div>

@endsection