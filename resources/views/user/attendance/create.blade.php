@extends('user.layouts.app')

@section('title', 'Employee add') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3">

        <h2 class="mb-3 fw-bold">Add Attendance</h2>

        <!-- Display success message if available -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('attendance.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Employee Dropdown -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <select class="form-control" name="employee_id">
                            <option value="">Select Employee</option>
                            @if ($employees->isNotEmpty())
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No employees available</option>
                            @endif
                        </select>
                        @error('employee_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Branch Dropdown -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Branch</label>
                        <select class="form-control" name="branch_id">
                            <option value="">Select Branch</option>
                            @if ($branches->isNotEmpty())
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No branches available</option>
                            @endif
                        </select>
                        @error('branch_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Department Dropdown -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-control" name="department_id">
                            <option value="">Select Department</option>
                            @if ($departments->isNotEmpty())
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : ''
                                        }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No departments available</option>
                            @endif
                        </select>
                        @error('department_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Attendance</label>
                        <select name="attendance" class="form-control">
                            <option value="">Select Attendance</option>
                            <option value="Present" {{ old('attendance') == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Absent" {{ old('attendance') == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Leave" {{ old('attendance') == 'Leave' ? 'selected' : '' }}>Leave</option>
                        </select>
                        @error('attendance')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date') }}">
                        @error('date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- In Time Field -->
                    <div class="mb-3">
                        <label class="form-label">In Time</label>
                        <input type="time" name="in_time" class="form-control" value="{{ old('in_time') }}">
                        @error('in_time')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
            <!-- Date Field -->

            <div class="row align-items-end">
                <div class="col-md-6">
                    <!-- Out Time Field -->
                    <div class="mb-3">
                        <label class="form-label">Out Time</label>
                        <input type="time" name="out_time" class="form-control" value="{{ old('out_time') }}">
                        @error('out_time')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="mb-3">
                        <!-- Submit and Cancel Buttons -->
                        <button type="submit" class="btn btn-success">Submit</button>
                        <a href="{{ route('attendance.index') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection