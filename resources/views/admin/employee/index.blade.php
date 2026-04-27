@extends('admin.layouts.layout')

@section('title', 'Employee List')

@section('content')

    <div class="container">
        <div class="row mb-3">
            <div class="col-md-7">
                <h3>Employee List</h3>
            </div>
            <div class="col-md-5">
                <a href="{{ route('employees.create') }}" class="btn btn-primary" style="float: right;">
                        Add</a>
            </div>
        </div>


        <!-- Search Form -->

        <!-- Search Form -->
        <form method="GET" action="{{ route('employees.list') }}" class="mb-3">
            <div class="row g-3">

                <!-- Company Filter Dropdown -->
                <div class="col-md-3">
                    <select name="company_id" class="tomselect">
                        <option value="">Select Company</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}"
                                {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Employee Name Filter -->
                <div class="col-md-3">
                    <input type="text" name="employee_name" class="form-control" placeholder="Search by Employee Name"
                        value="{{ request('employee_name') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="phone" class="form-control" placeholder="Search by Phone"
                        value="{{ request('phone') }}">
                </div>


                <!-- Search Button -->
                <div class="col-md-3 text-end">
                    <button type="submit" class="btn btn-info ">Search</button>
                    <a href="{{ route('employees.list') }}" class="btn btn-danger ">Reset</a>
                </div>

            </div>
        </form>

        <div class="table-responsive table-same">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Company Name</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Branch</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $employee->company->company_name }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->phone }}</td>
                            <td>{{ $employee->branch ? $employee->branch->branch_name : 'N/A' }}</td>
                            <td>{{ $employee->department ? $employee->department->name : 'N/A' }}</td>

                            <td>
                                <div class="actions">
                                    <a href="{{ route('employees.show', $employee->id) }}"><i class="fa fa-eye"></i></a>
                                    <a href="{{ route('employees.edit', $employee->id) }}"><i
                                            class="fa fa-solid fa-pen"></i></a>
                                            <button class="prop-none" onclick="confirmDelete(event, {{ $employee->id }})"><i
                                            class="fa fa-solid fa-trash"></i></button>

                                    <div class="modal fade" id="deleteModal-{{ $employee->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel-{{ $employee->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel-{{ $employee->id }}">Confirm Deletion</h5>
                                                    
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this employee? All related data will be permanently removed and cannot be recovered.
                                                </div>
                                                <div class="modal-body">
                                                
                                                    <form action="{{ route('employees.delete', $employee->id) }}" method="POST"
                                                style="display:inline;" id="delete-form-{{ $employee->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="password" name="pin" id="pin_{{ $employee->id }}" class="form-control" placeholder="Enter PIN" required>
                                                        <button type="button" class="btn btn-secondary" onclick="$('#deleteModal-{{ $employee->id }}').modal('hide');">Cancel</button>
                                                        <button type="button" class="btn btn-danger" onclick="ajaxConfirmDelete(event, {{ $employee->id }})">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No Employees Found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $employees->links('pagination::bootstrap-4') }}
        </div>
    </div>

@endsection

@section('scripts')
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
                    $('#deleteModal-' + employeeId).modal('show');
                    //document.getElementById(`delete-form-${employeeId}`).submit();
                }
            });
        }

        function ajaxConfirmDelete(event, itemId) {
            //const url = `{{ route('company.status.toggle', ':id') }}`.replace(':id', id);
            const url = `{{ route('pinchecking') }}`; 
            const pin = document.getElementById(`pin_${itemId}`).value;
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pin: pin,
                    },
                    success: function (response) {
                        if (response.status == 'success') {
                            document.getElementById(`delete-form-${itemId}`).submit();
                        } else {
                            alert('Invalid Pin. Please try again.');
                        }
                    },
                    error: function (xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('An error occurred while updating the status.');
                    }
                });
        }
    </script>
@endsection
