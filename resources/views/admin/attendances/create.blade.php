@extends('admin.layouts.layout')

@section('title', 'Create Attendance')

@section('content')
    <div class="container">
        <h3 class="mb-3">Create Attendance</h3>
        <form action="{{ route('attendances.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Company</label>
                        <select name="company_id" class="form-control" required>
                            <option value="">Select Company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-control" required>
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Attendance</label>
                        <select name="attendance" class="form-control" required>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Leave">Leave</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">In Time</label>
                        <input type="time" name="in_time" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Out Time</label>
                        <input type="time" name="out_time" class="form-control">
                    </div>
                </div>
                <div class="col-md-3 add-flex-res">
                    <div>
                        <label class="form-label">Halfday</label>
                        <input type="checkbox" name="halfday" value="1">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Submit</button>
        </form>
    </div>
@endsection