@extends('admin.layouts.layout')

@section('title', 'Attendance Details')

@section('content')
<div class="container">
    <h1>Attendance Details</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $attendance->id }}</p>
            <p><strong>Company:</strong> {{ isset($attendance->company->company_name)?$attendance->company->company_name:'' }}</p>
            <p><strong>Branch:</strong> {{ isset($attendance->branch->branch_name)?$attendance->branch->branch_name:'' }}</p>
            <p><strong>Employee:</strong> {{ isset($attendance->employee->name)?$attendance->employee->name:'' }}</p>
            <p><strong>Attendance:</strong> {{ isset($attendance->attendance)?$attendance->attendance:'' }}</p>
            <p><strong>Halfday:</strong> {{ $attendance->halfday ? 'Yes' : 'No' }}</p>
            <p><strong>Date:</strong> {{ $attendance->date }}</p>
            <p><strong>In Time:</strong> {{ $attendance->in_time }}</p>
            <p><strong>Out Time:</strong> {{ $attendance->out_time }}</p>
            <a href="{{ route('attendances.index') }}" class="btn btn-secondary">Back</a>

        </div>
    </div>
</div>
@endsection