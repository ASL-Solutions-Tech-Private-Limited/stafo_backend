@extends('user.layouts.app')

@section('title', 'Attendance Edit') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3">

        <h5 class="mb-3">Edit Attendance</h5>

        <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Employee Dropdown -->
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <select class="form-control" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ old('employee_id', $attendance->employee_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Company Dropdown -->
                {{-- <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Company</label>
                        <select class="form-control" name="company_id" required>
                            <option value="">Select Company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}"
                                    {{ old('company_id', $attendance->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div> --}}
            </div>

            <div class="row">
                <!-- Branch Dropdown -->
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Branch</label>
                        <select class="form-control" name="branch_id" required>
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('branch_id', $attendance->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Department Dropdown -->
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-control" name="department_id" required>
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ old('department_id', $attendance->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Date Field -->
            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', $attendance->date) }}"
                    required>
                @error('date')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Attendance Dropdown -->
            <div class="mb-3">
                <label class="form-label">Attendance</label>
                <select name="attendance" class="form-control" required>
                    <option value="Present"
                        {{ old('attendance', $attendance->attendance) == 'Present' ? 'selected' : '' }}>Present</option>
                    <option value="Absent" {{ old('attendance', $attendance->attendance) == 'Absent' ? 'selected' : '' }}>
                        Absent</option>
                    <option value="Leave" {{ old('attendance', $attendance->attendance) == 'Leave' ? 'selected' : '' }}>
                        Leave</option>
                </select>
                @error('attendance')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- In Time Field -->
            <div class="mb-3">
                <label class="form-label">In Time</label>
                <input type="time" name="in_time" class="form-control"
                    value="{{ old('in_time', $attendance->in_time) }}">
                @error('in_time')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Out Time Field -->
            <div class="mb-3">
                <label class="form-label">Out Time</label>
                <input type="time" name="out_time" class="form-control"
                    value="{{ old('out_time', $attendance->out_time) }}">
                @error('out_time')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit and Cancel Buttons -->
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
