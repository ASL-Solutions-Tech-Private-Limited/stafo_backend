@extends('user.layouts.app')
@section('title', 'Employee Details') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 shadow-sm">

        <div class="container">
            <h5 class="mb-3">Add Employee</h5>

            <div class="row mb-3">
                <div class="col-lg-12">
                    <a href="{{ route('employee.index') }}" class="btn btn-primary float-right">
                        <i class="bi bi-person-lines-fill"></i> Employees List
                    </a>
                </div>
            </div>



            <div class="card mt-4 p-3 shadow-sm">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <h4 class="mb-4">Employee Details</h4>

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

                            {{-- <div class="row mb-2">
                            <div class="col-md-6">
                                <p><strong>Position:</strong> {{ $employee->position }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Salary:</strong> {{ $employee->salary }}</p>
                            </div>
                        </div> --}}

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Branch:</strong>
                                        {{ $employee->branch->branch_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Department:</strong>
                                        {{ $employee->department->name ?? 'N/A' }}</p>
                                </div>
                            </div>





                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
