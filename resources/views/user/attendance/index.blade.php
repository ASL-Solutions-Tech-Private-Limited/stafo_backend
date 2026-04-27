@extends('user.layouts.app')
@section('title', 'Attendance List')

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">

        <!-- Modal for Punch In Image -->
        <div class="modal fade" id="punchinImageModal" tabindex="-1" aria-labelledby="punchinImageModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="punchinImageModalLabel">Punch In Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img id="punchin_image_modal" class="img-fluid" src="" alt="Punch In Image" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Punch Out Image -->
        <div class="modal fade" id="punchoutImageModal" tabindex="-1" aria-labelledby="punchoutImageModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="punchoutImageModalLabel">Punch Out Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img id="punchout_image_modal" class="img-fluid" src="" alt="Punch Out Image" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Display success message --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="container">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="fw-bold">Attendance List</h2>
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-center justify-content-end">
                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                        data-bs-target="#importModal">
                        <i class="fas fa-upload"></i> Import
                    </button>
                    <a href="{{ route('user.attendances.export', request()->query()) }}" class="btn btn-success me-2">
                        <i class="fas fa-download"></i> Export
                    </a>
                    <a href="{{ route('attendance.create') }}" class="btn btn-success ">
                        <i class="fas fa-plus"></i> Add
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('attendance.index') }}" class="mb-3">
                <div class="row">
                    <!-- Employee Filter -->
                    <div class="col-md-3 mb-3">
                        <select name="employee_id" id="employee_id" class="form-control">
                            <option value="">All Employees</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Attendance Filter -->
                    <div class="col-md-3 mb-3">
                        <select name="attendance" id="attendance" class="form-control">
                            <option value="">All Status</option>
                            <option value="Present" {{ request('attendance') == 'Present' ? 'selected' : '' }}>Present
                            </option>
                            <option value="Absent" {{ request('attendance') == 'Absent' ? 'selected' : '' }}>Absent</option>
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div class="col-md-2 mb-2">
                        <input type="date" name="from_date" id="date" class="form-control"
                            value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-2 mb-2">
                        <input type="date" name="to_date" id="date" class="form-control"
                            value="{{ request('to_date') }}">
                    </div>

                    <!-- Submit and Reset Buttons -->
                    <div class="col-md-2 mb-2 d-flex align-items-center justify-content-center">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="fas fa-search"></i>
                            Search</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive table-same">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Employee Name</th>
                            <th>Date</th>
                            <th>Attendance</th>
                            <th>Punch In Image</th>
                            <th>Punch Out Image</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $index => $attendance)
                            <tr>
                                <td>{{ $attendances->firstItem() + $index }}</td>
                                <td>{{ $attendance->employee->name }}</td>
                                <td>{{ $attendance->date }}</td>
                                <td>
                                    @if ($attendance->attendance == 'Present')
                                        <span class="badge bg-success">Present</span>
                                    @elseif ($attendance->attendance == 'Absent')
                                        <span class="badge bg-danger">Absent</span>
                                    @elseif ($attendance->attendance == 'Leave')
                                        <span class="badge bg-warning">Leave</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($attendance->punchin_image)
                                        <img src="{{ url('uploads/employees/punchin/' . $attendance->punchin_image) }}"
                                            alt="Punch In Image" style="width: 50px; height: 50px; cursor: pointer;"
                                            onclick="viewPunchInImage('{{ url('uploads/employees/punchin/' . $attendance->punchin_image) }}')" />
                                    @else
                                        <span>No Image</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($attendance->punchout_image)
                                        <img src="{{ url('uploads/employees/punchout/' . $attendance->punchout_image) }}"
                                            alt="Punch Out Image" style="width: 50px; height: 50px; cursor: pointer;"
                                            onclick="viewPunchOutImage('{{ url('uploads/employees/punchout/' . $attendance->punchout_image) }}')" />
                                    @else
                                        <span>No Image</span>
                                    @endif
                                </td>
                                <td>{{ $attendance->in_time }}</td>
                                <td>{{ $attendance->out_time }}</td>
                                <td>
                                    <a href="{{ route('attendance.edit', $attendance->id) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit" title="Edit"></i>
                                    </a>
                                    <form id="delete-form-{{ $attendance->id }}"
                                        action="{{ route('attendance.destroy', $attendance->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete(event, {{ $attendance->id }})">
                                            <i class="fas fa-trash" title="Delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center mt-4">
                {{ $attendances->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <script>
        function viewPunchInImage(punchin_image_url) {
            // Set the Punch In image in the modal
            document.getElementById('punchin_image_modal').src = punchin_image_url;
            // Show the Punch In Image modal
            $('#punchinImageModal').modal('show');
        }

        function viewPunchOutImage(punchout_image_url) {
            // Set the Punch Out image in the modal
            document.getElementById('punchout_image_modal').src = punchout_image_url;
            // Show the Punch Out Image modal
            $('#punchoutImageModal').modal('show');
        }

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
