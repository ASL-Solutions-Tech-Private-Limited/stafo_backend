@extends('user.layouts.app')
@section('title', 'Employee List') <!-- Set your custom title here -->

@section('css')
    <link rel="stylesheet" href="{{ asset('css/employee.css') }}">
    <style>
        .employee-actions {
            min-height: 38px;
            /* Adjust as needed */
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
    </style>
@endsection
@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <h5 class="text-center text-primary mb-4">Employee List</h5>
        <div class="container">
            <!-- Filter Form -->
            <form action="{{ route('employee.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <input type="text" name="name" class="form-control" placeholder="Search by Name"
                            value="{{ request()->input('name') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="email" name="email" class="form-control" placeholder="Search by Email"
                            value="{{ request()->input('email') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <input type="text" name="phone" class="form-control" placeholder="Search by Phone"
                            value="{{ request()->input('phone') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="branch_id" class="form-control">
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request()->input('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="department_id" class="form-control">
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ request()->input('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('employee.index') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Add Employee Button -->
            <div class="mb-4">
                <a href="{{ route('employee.create') }}" class="btn btn-success btn-lg float-right">Add Employee</a>
            </div>

            <!-- Employee Table -->
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Branch</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr>
                            <td>{{ $employee->id }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->phone }}</td>
                            <td>{{ $employee->branch ? $employee->branch->branch_name : 'N/A' }}</td>

                            <td>{{ $employee->department->name ?? 'N/A' }}</td>


                            <td class="employee-actions">
                                <a href="{{ route('user.employees.show', ['id' => $employee->id, 'company_id' => $employee->company_id]) }}"
                                    class="btn btn-info btn-sm">View</a>

                                <!-- If employee has a bank account, show 'Edit Bank Account', otherwise 'Add Bank Account' -->
                                <a href="{{ $employee->bankAccount ? route('editBankAccount', $employee->bankAccount->id) : route('addBankAccount', $employee->id) }}"
                                    class="btn btn-primary btn-sm">
                                    {{ $employee->bankAccount ? 'Edit Bank Account' : 'Add Bank Account' }}
                                </a>

                                <a href="{{ route('user.employees.edit', $employee->id) }}"
                                    class="btn btn-warning btn-sm">Edit</a>

                                <form action="{{ route('user.employees.delete', $employee->id) }}" method="POST"
                                    style="display:inline;" id="delete-form-{{ $employee->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="confirmDelete(event, {{ $employee->id }})">Delete</button>
                                </form>
                            </td>



                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDelete(event, employeeId) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${employeeId}`).submit();
                }
            });
        }
    </script>
@endsection
