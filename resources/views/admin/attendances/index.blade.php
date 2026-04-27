@extends('admin.layouts.layout')

@section('title', 'Attendance List')

@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-6">
                <h3>Attendance List</h3>
            </div>

            <div class="col-md-6">
                <div class="d-md-flex align-items-center justify-content-end">
                    <div class="d-flex mb-2 mb-md-0">
                        <button type="button" class="btn btn-primary me-2 w-100" data-bs-toggle="modal"
                            data-bs-target="#importModal">
                            Import
                        </button>
                        <a href="{{ route('attendances.export', request()->query()) }}"
                            class="btn btn-secondary me-2 w-100">
                            Export
                        </a>
                    </div>
                    <a href="{{ route('attendances.create') }}" class="btn btn-success">Create Attendance</a>
                </div>
            </div>
        </div>

        <!-- Import Modal -->
        <div class="modal fade" id="importModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Attendance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('attendances.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <input type="file" name="attendance_file" class="form-control" accept=".xls,.xlsx">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <form method="GET" action="{{ route('attendances.index') }}" class="mb-3">
            <div class="row g-3 justify-content-between">
                <div class="col-md-3">
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}"
                        placeholder="Date">
                    <i>Select From Date</i>
                </div>
                <div class="col-md-3">
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}"
                        placeholder="Date">
                    <i>Select To Date</i>
                </div>
                <div class="col-md-3">
                    <select name="company_id" class="tomselect">
                        <option value="">All Companies</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}"
                                {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="branch_id" class="tomselect2">
                        <option value="">All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="attendance" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="present" {{ request('attendance') == 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ request('attendance') == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="leave" {{ request('attendance') == 'leave' ? 'selected' : '' }}>Leave</option>
                        <option value="halfday" {{ request('attendance') == 'halfday' ? 'selected' : '' }}>Half Day
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="employee_search" class="form-control"
                        value="{{ request('employee_search') }}" placeholder="Search Employee">
                </div>
                <div class="col-md-3 text-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('attendances.index') }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
        </form>
        <div class="table-responsive table-same">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>EmpId</th>
                        <th>Company</th>
                        <th>Branch</th>
                        <th>Employee</th>
                        <th>Attendance</th>
                        <th>Date</th>
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendances as $index => $attendance)
                        <tr>
                            <td>{{ $attendances->firstItem() + $index }}</td>
                            <td>{{ isset($attendance->company->company_name)?$attendance->company->company_name:'' }}</td>
                            <td>{{ isset($attendance->branch->branch_name)?$attendance->branch->branch_name:'' }}</td>
                            <td>{{ isset($attendance->employee->name)?$attendance->employee->name:'' }}</td>
                            <td>{{ isset($attendance->attendance)?$attendance->attendance:'' }}</td>
                            <td>{{ isset($attendance->date)?$attendance->date:'' }}</td>
                            <td>{{ isset($attendance->in_time)?$attendance->in_time:'' }}</td>
                            <td>{{ isset($attendance->out_time)?$attendance->out_time:'' }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('attendances.show', $attendance->id) }}"><i
                                            class="fa fa-eye"></i></a>
                                    <a href="{{ route('attendances.edit', $attendance->id) }}"><i
                                            class="fa fa-solid fa-pen"></i></a>
                                    <form method="POST" action="{{ route('attendances.destroy', $attendance->id) }}"
                                        class="d-inline" id="delete-form-{{ $attendance->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="prop-none" onclick="confirmDelete(event, {{ $attendance->id }})"><i
                                                class="fa fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Add pagination links -->
        <div class="mt-3">
            {{ $attendances->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
        </div>

    </div>
@endsection
@section('scripts')
    <script>
        function confirmDelete(event, attendanceId) {
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
                    document.getElementById(`delete-form-${attendanceId}`).submit();
                }
            });
        }
    </script>
@endsection
