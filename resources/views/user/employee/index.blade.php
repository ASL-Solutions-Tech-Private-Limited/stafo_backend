@extends('user.layouts.app')
@section('title', 'Employee List') <!-- Set your custom title here -->

@section('css')
    <link rel="stylesheet" href="{{ asset('main/css/employees.css') }}">

@endsection
@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0 emp-data">

        <div class="container">
            <div class="row">
                <div class="col-md-6 col-xl-6 mb-9">
                    <h2 class="fw-bold">Employee List</h2>
                </div>
                <div class="col-md-6 col-xl-6 mb-3 text-end">
                    <a href="{{ route('employee.create') }}" class="btn btn-success btn-md float-right me-2"> <i
                            class="fas fa-plus me-1"></i>Add</a>
                        <button type="button" class="btn btn-primary float-right me-2" data-bs-toggle="modal"
                        data-bs-target="#importModal">
                        <i class="fas fa-upload"></i> Import
                    </button>
                    <a href="{{ route('employees.export', request()->all()) }}"
                        class="btn btn-primary btn-md float-right"><i class="fas fa-download me-1"></i> Export</a>
                </div>

            </div>
            <!-- Filter Form -->
            <form action="{{ route('employee.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4 col-lg-3 col-xl-2 mb-2">
                        <input type="text" name="name" class="form-control" placeholder="Search by Name"
                            value="{{ request()->input('name') }}">
                    </div>
                    <div class="col-md-4 col-lg-3 col-xl-2 mb-2">
                        <input type="email" name="email" class="form-control" placeholder="Search by Email"
                            value="{{ request()->input('email') }}">
                    </div>
                    <div class="col-md-4 col-lg-3 col-xl-2 mb-2">
                        <input type="text" name="phone" class="form-control" placeholder="Search by Phone"
                            value="{{ request()->input('phone') }}">
                    </div>


                    <div class="col-md-4 col-lg-3 col-xl-2 mb-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search "></i> Search</button>
                    </div>
                    <!-- <div class="col-md-3 mb-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <a href="{{ route('employee.index') }}" class="btn btn-secondary w-100">Reset</a>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div> -->
                </div>
            </form>

            <!-- Employee Table -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Designation</th>
                            <th>Basic Salary</th>
                            <th>Assign Branch</th>
                            <th>Assign Department</th>
                            <th>Assign Shift</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $index => $employee)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->phone }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>{{ $employee->salary }}</td>
                                <td>
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#assignBranchModal"
                                        onclick="openAssignBranchModal({{ $employee->id }})">
                                        Assign Branch
                                    </button>
                                </td>

                                <td> <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#assignDepartmentModal"
                                        onclick="openAssignDepartmentModal({{ $employee->id }})">
                                        Assign Department
                                    </button> </td>

                                <td>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#assignShiftModal"
                                        onclick="openAssignShiftModal({{ $employee->id }})">
                                        Assign Shifts
                                    </button>
                                </td>




                                <td class="employee-actions">
                                    <a href="{{ route('user.employees.show', ['id' => $employee->id, 'company_id' => $employee->company_id]) }}"
                                        class="btn btn-info btn-sm" title="View Employee">
                                        <i class="fas fa-eye"></i> <!-- Eye Icon for View -->
                                    </a>

                                    <!-- If employee has a bank account, show 'Edit Bank Account', otherwise 'Add Bank Account' -->
                                    <a href="{{ $employee->bankAccount ? route('editBankAccount', $employee->bankAccount->id) : route('addBankAccount', $employee->id) }}"
                                        class="btn btn-primary btn-sm"
                                        title="{{ $employee->bankAccount ? 'Edit Bank Account' : 'Add Bank Account' }}">
                                        <i class="fas {{ $employee->bankAccount ? 'fa-edit' : 'fa-plus' }}"></i>
                                        <!-- Edit or Add Icon -->
                                    </a>


                                    <a href="{{ route('user.employees.edit', $employee->id) }}"
                                        class="btn btn-warning btn-sm" title="Edit Employee">
                                        <i class="fas fa-pencil-alt"></i> <!-- Edit Icon -->
                                    </a>
                                    <a href="{{ route('employee.location', $employee->id) }}"
                                        class="btn btn-primary btn-sm  d-inline-block" title="View Location">
                                        <i class="fas fa-map"></i>
                                    </a>
                                    <div class="dropdown multi-drops">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-item">
                                                <a href="{{ $employee->document ? route('employee.documents.edit', ['employeeId' => $employee->id, 'documentId' => $employee->document->id]) : route('employee.documents.create', $employee->id) }}"
                                                    class="btn btn-primary btn-sm d-inline-block"
                                                    title="{{ $employee->document ? 'Edit Document' : 'Add Document' }}">
                                                    <i class="fas {{ $employee->document ? 'fa-edit' : 'fa-plus' }}"></i>
                                                </a>
                                            </li>
                                            <li class="dropdown-item"> <a
                                                    href="{{ route('documentVerification', $employee->id) }}"
                                                    class="btn btn-primary btn-sm  d-inline-block"
                                                    title="Document verification">
                                                    <i class="fas fa-list"></i>
                                                </a></li>
                                            <li class="dropdown-item"> <a
                                                    href="{{ route('employeePerformance', $employee->id) }}"
                                                    class="btn btn-primary btn-sm  d-inline-block"
                                                    title="Performance">
                                                    <i class="fas fa-trophy"></i>
                                                </a></li>
                                            <li class="dropdown-item">
                                                <form action="{{ route('user.employees.delete', $employee->id) }}"
                                                    method="POST" style="display:inline;"
                                                    id="delete-form-{{ $employee->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete(event, {{ $employee->id }})"
                                                        title="Delete">
                                                        <i class="fas fa-trash-alt"></i> <!-- Trash Icon for Delete -->
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<div class="modal fade" id="importModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('user.employees.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <input type="file" name="attendance_file" class="form-control" accept=".xls,.xlsx">
                            </div>
                            <div class="mb-4">
                            <a href="{{ asset('sample_import_files/employees_import.xlsx') }}" class="btn btn-secondary btn-sm" download>
                                Download Sample File</a>    
                            </div
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
</div>
    <!-- Modal for assigning shifts -->

    <!-- Modal for assigning shifts -->
    <div class="modal fade" id="assignShiftModal" tabindex="-1" aria-labelledby="assignShiftModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignShiftModalLabel">Assign Shifts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignShiftForm">
                        <input type="hidden" id="employeeId" name="employee_id"> <!-- This was missing -->
                        <div class="form-check">
                            @foreach ($shifts as $shift)
                                <div>
                                    <input class="form-check-input shift-checkbox" type="checkbox"
                                        value="{{ $shift->id }}" id="shift{{ $shift->id }}" name="shift_ids[]">
                                    <label class="form-check-label" for="shift{{ $shift->id }}">
                                        {{ $shift->shift_name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Assign Shifts</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>




    <!-- Modal for Assign Branch -->
    <div class="modal fade" id="assignBranchModal" tabindex="-1" aria-labelledby="assignBranchModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignBranchModalLabel">Assign Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignBranchForm">
                        <input type="hidden" id="branchEmployeeId" name="employee_id">
                        <div class="form-group">
                            <label for="branchSelect">Select Branch</label>
                            <select class="form-select" id="branchSelect" name="branch_id">
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Assign Branch</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal for Assign Department -->
    <div class="modal fade" id="assignDepartmentModal" tabindex="-1" aria-labelledby="assignDepartmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignDepartmentModalLabel">Assign Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="assignDepartmentForm">
                        <input type="hidden" id="departmentEmployeeId" name="employee_id">
                        <div class="form-group">
                            <label for="departmentSelect">Select Department</label>
                            <select class="form-select" id="departmentSelect" name="department_id">
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Assign Department</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('js')
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


    <script>
        // Open the modal and populate the employee_id input
        function openAssignShiftModal(employeeId) {
            $('#employeeId').val(employeeId);

            const assignedShifts = @json($assignedShifts);

            $('.shift-checkbox').prop('checked', false);

            if (assignedShifts[employeeId]) {
                assignedShifts[employeeId].forEach(function(shiftId) {
                    $('#shift' + shiftId).prop('checked', true);
                });
            }
        }

        // Handle the form submission
        $('#assignShiftForm').on('submit', function(e) {
            e.preventDefault();

            const employeeId = $('#employeeId').val();
            const shiftIds = $('input[name="shift_ids[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            if (shiftIds.length === 0) {
                Swal.fire('Error', 'Please select at least one shift.', 'error');
                return;
            }

            $.ajax({
                url: 'employees/' + employeeId + '/assign-shift',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    shift_ids: shiftIds
                },
                success: function(response) {
                    Swal.fire('Success', 'Shifts assigned successfully!', 'success').then(() => {
                        $('#assignShiftModal').modal('hide');
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire('Error', 'An error occurred. Please try again.', 'error');
                }
            });
        });
    </script>

    <script>
        function openAssignBranchModal(employeeId) {
            $('#branchEmployeeId').val(employeeId);

            const assignedBranchId = @json($employees->pluck('branch_id', 'id'));
            const employeeBranchId = assignedBranchId[employeeId];

            // Set the selected branch in the dropdown
            if (employeeBranchId) {
                $('#branchSelect').val(employeeBranchId);
            } else {
                $('#branchSelect').val(""); // Clear selection if no branch is assigned
            }
        }

        function openAssignDepartmentModal(employeeId) {
            $('#departmentEmployeeId').val(employeeId);

            const assignedDepartmentId = @json($employees->pluck('department_id', 'id'));
            const employeeDepartmentId = assignedDepartmentId[employeeId];

            // Set the selected department in the dropdown
            if (employeeDepartmentId) {
                $('#departmentSelect').val(employeeDepartmentId);
            } else {
                $('#departmentSelect').val(""); // Clear selection if no department is assigned
            }
        }


        // Handle the form submission for assigning the branch
        $('#assignBranchForm').on('submit', function(e) {
            e.preventDefault();

            const employeeId = $('#branchEmployeeId').val();
            const branchId = $('#branchSelect').val();



            // Send the AJAX request to assign the branch
            $.ajax({
                url: 'employees/' + employeeId + '/assign-branch', // Update the route as needed
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    branch_id: branchId,
                },
                success: function(response) {
                    Swal.fire('Success', 'Branch assigned successfully!', 'success');
                    $('#assignBranchModal').modal('hide');
                    location.reload();
                },
                error: function() {
                    Swal.fire('Error', 'An error occurred. Please try again.', 'error');
                }
            });
        });

        $('#assignDepartmentForm').on('submit', function(e) {
            e.preventDefault();

            const employeeId = $('#departmentEmployeeId').val();
            const departmentId = $('#departmentSelect').val();

            $.ajax({
                url: 'employees/' + employeeId + '/assign-department',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    department_id: departmentId,
                },
                success: function(response) {
                    Swal.fire('Success', 'Department assigned successfully!', 'success');
                    $('#assignDepartmentModal').modal('hide');
                    location.reload();
                },
                error: function() {
                    Swal.fire('Error', 'An error occurred. Please try again.', 'error');
                }
            });
        });
    </script>





@endsection
