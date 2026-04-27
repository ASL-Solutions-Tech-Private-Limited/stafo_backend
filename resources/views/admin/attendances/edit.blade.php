@extends('admin.layouts.layout')

@section('title', 'Edit Attendance')

@section('content')
<div class="container">
    <h1>Edit Attendance</h1>
    <form action="{{ route('attendances.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="company_id" class="form-label">Company</label>
            <select name="company_id" class="form-control" required>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ $attendance->company_id == $company->id ? 'selected' : '' }}>
                        {{ $company->company_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="branch_id" class="form-label">Branch</label>
            <select name="branch_id" class="form-control" required>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $attendance->branch_id == $branch->id ? 'selected' : '' }}>
                        {{ $branch->branch_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="employee_id" class="form-label">Employee</label>
            <select name="employee_id" class="form-control" required>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $attendance->employee_id == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="attendance" class="form-label">Attendance</label>
            <select name="attendance" class="form-control">
                <option value="Present" {{ $attendance->attendance == 'Present' ? 'selected' : '' }}>Present</option>
                <option value="Absent" {{ $attendance->attendance == 'Absent' ? 'selected' : '' }}>Absent</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="halfday" class="form-label">Halfday</label>
            <select name="halfday" class="form-control">
                <option value="1" {{ $attendance->halfday ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !$attendance->halfday ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="{{ $attendance->date }}" required>
        </div>

        <div class="mb-3">
            <label for="in_time" class="form-label">In Time</label>
            <input type="time" name="in_time" class="form-control" value="{{ $attendance->in_time }}">
        </div>

        <div class="mb-3">
            <label for="out_time" class="form-label">Out Time</label>
            <input type="time" name="out_time" class="form-control" value="{{ $attendance->out_time }}">
        </div>

        <button type="submit" class="btn btn-primary">Update Attendance</button>
    </form>
</div>
@endsection