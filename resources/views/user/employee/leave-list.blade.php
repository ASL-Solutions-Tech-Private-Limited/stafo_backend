@extends('user.layouts.app')

@section('title', 'Leave List')

@section('content')
<div class="card mt-4 p-3 shadow-sm border-0">
    <div class="container">

        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
        <div class="d-flex justify-content-between align-items-center">
            <div class="col-md-9 mb-9">
                <h2 class="fw-bold">Employee Leave</h2>
            </div>
        </div>

        <form method="GET" action="{{ route('leaveList') }}" class="mb-4">
            <div class="row justify-content-between align-items-center">

                <!-- Employee Filter -->
                <div class="col-md-3 mb-3">

                    <select name="employee_id" id="employee_id" class="form-control ">
                        <option value="">All Employees</option>
                        @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id')==$employee->id ? 'selected' : ''
                            }}>
                            {{ $employee->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Leave Type Filter -->
                <div class="col-md-3 mb-3">

                    <select name="leave_type" id="leave_type" class="form-control ">
                        <option value="">All Leave Types</option>
                        <option value="1" {{ request('leave_type')=='1' ? 'selected' : '' }}>Casual Leave
                        </option>
                        <option value="2" {{ request('leave_type')=='2' ? 'selected' : '' }}>Sick Leave</option>
                        <option value="3" {{ request('leave_type')=='3' ? 'selected' : '' }}>Privilege Leave
                        </option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-3 mb-3">

                    <select name="status" id="status" class="form-control ">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved
                        </option>
                        <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected
                        </option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="col-md-3 mb-3 d-flex align-items-center justify-content-center">
                    <button type="submit" class="btn btn-primary w-100 "><i class="fas fa-search "></i>
                        Search</button>
                    <!-- <a href="{{ route('leaveList') }}" class="btn btn-secondary w-100 ">Reset</a> -->
                </div>

                <!-- Reset Button -->

            </div>
        </form>


        <div class="table-responsive table-same">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Employee Name</th>
                        <th>Leave Type</th>
                        <th>Days</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Status</th>
                        <th>Actions</th>

                    </tr>
                </thead>
                <tbody>

                    @foreach ($leave as $index => $leaveRecord)
                    <tr>
                        <td>{{ $leave->firstItem() + $index }}</td>
                        <td>{{ $leaveRecord->employeeBasicInfo->name }}</td>
                        {{-- Map leave type ID to name --}}
                        <td>
                            {{ $leaveRecord->leaveType->name ?? ''    }}
                        </td>
                        <td>{{ $leaveRecord->days }}</td>
                        <td>{{ \Carbon\Carbon::parse($leaveRecord->from_date)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($leaveRecord->to_date)->format('M d, Y') }}</td>
                        <td>{{ $leaveRecord->status }}</td>

                        <td>
                            <div class="row" style="display: flex;">
                                <div class="col-md-8">
                                    <select class="form-control action-dropdown" data-id="{{ $leaveRecord->id }}"
                                        onchange="updateLeaveStatus(this)">
                                        <option value="">Select Action</option>
                                        @if ($leaveRecord->status == 'pending')
                                        <option value="approve">Approve</option>
                                        <option value="reject">Reject</option>
                                        @elseif ($leaveRecord->status == 'approved')
                                        <option value="reject">Reject</option>
                                        <option value="pending">Mark as Pending</option>
                                        @elseif ($leaveRecord->status == 'rejected')
                                        <option value="approve">Re-approve</option>
                                        <option value="pending">Mark as Pending</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <form action="{{ route('leave.delete', $leaveRecord->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm mt-2" title="Delete">
                                            <i class="fas fa-trash-alt"></i> <!-- Trash icon -->
                                        </button>
                                    </form>
                                    <div>
                                    </div>
                        </td>


                    </tr>
                    @endforeach


                </tbody>
            </table>
        </div>

        <!-- Add pagination links below the table -->
        <div class="d-flex justify-content-center mt-4">
            {{ $leave->links('pagination::bootstrap-4') }} <!-- You can use any pagination style -->
        </div>
    </div>
</div>
@endsection

@section('js')

<script>
    function updateLeaveStatus(selectElement) {
        const leaveId = selectElement.getAttribute('data-id');
        const action = selectElement.value;

        // If no action is selected, exit
        if (!action) return;

        // Show a loading spinner or some feedback
        const spinner = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        selectElement.disabled = true;
        selectElement.innerHTML = spinner;

        // Perform AJAX request to update the leave status
        fetch('{{ route('leave.updateStatus') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                leave_id: leaveId,
                action: action,
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // On success, update the row status and re-enable dropdown
                    selectElement.innerHTML =
                        '<option value="approve">Approve</option><option value="reject">Reject</option>';
                    alert('Leave status updated successfully!');
                    location.reload();
                } else {
                    alert('Failed to update leave status.');
                    selectElement.disabled = false;
                }
            })
            .catch(error => {
                alert('Error occurred while updating leave status.');
                selectElement.disabled = false;
            });
    }
</script>

@endsection