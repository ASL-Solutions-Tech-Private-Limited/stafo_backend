@extends('user.layouts.app')
@section('title', 'Employee Details') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 shadow-sm">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="fw-bold">Employee Details</h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('employee.index') }}" class="btn btn-primary float-right">
                        <i class="bi bi-person-lines-fill"></i> Employees List
                    </a>
                </div>
            </div>

            <div class="mt-2 border-top border-dark pt-4">
                <div class="container px-0">
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <h4 class="mb-4 fw-bold">Employee Information</h4>

                            <!-- Employee Details -->
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Employee ID:</strong> {{ $employee->emp_id }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> {{ $employee->name }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Email:</strong> {{ $employee->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Phone:</strong> {{ $employee->phone }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Position:</strong> {{ $employee->position }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Salary:</strong> {{ $employee->salary }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Branch:</strong> {{ $employee->branch->branch_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Department:</strong> {{ $employee->department->name ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Gender:</strong> {{ $employee->gender }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Marital Status:</strong> {{ $employee->marital_status }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Blood Group:</strong> {{ $employee->blood_group }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Guardian Name:</strong> {{ $employee->guardian_name }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Date of Birth:</strong> {{ $employee->date_of_birth }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Date of Joining:</strong> {{ $employee->date_of_joining }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Pin:</strong> {{ $employee->pin }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> {{ $employee->status == 1 ? 'Active' : 'Inactive' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Device Status:</strong>
                                        {{ $employee->device_status == 1 ? 'Online' : 'Offline' }}</p>
                                </div>
                            </div>

                            <!-- Display the employee image -->
                            @if ($employee->image_url)
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p><strong>Profile Image:</strong></p>
                                        <img src="{{ $employee->image_url }}" alt="Employee Image" class="img-fluid"
                                            style="max-width: 150px;">
                                    </div>


                                </div>
                            @endif

                            <!-- Display the selfie if it exists -->
                            @if ($employee->selfie_url)
                                <div class="row mb-2">

                                    <div class="col-md-6">
                                        <p><strong>Selfie Image:</strong></p>
                                        <img src="{{ $employee->selfie_url }}" alt="Employee selfie" class="img-fluid"
                                            style="max-width: 150px;">
                                    </div>

                                </div>
                            @endif

                            <!-- Display the resume if it exists -->
                            @if ($employee->resume_url)
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p><strong>Resume:</strong> <a href="{{ $employee->resume_url }}"
                                                target="_blank">Download Resume</a></p>
                                    </div>
                                </div>
                            @endif


                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
