@extends('user.layouts.app')
@section('title', 'Shift Details')

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <div class="container">
            <h5 class="text-center text-primary mb-4">Shift Details</h5>
            <div class="row">
                <div class="col-md-6">
                    <h4>Shift Name</h4>
                    <p>{{ $shift->shift_name }}</p>
                </div>
                <div class="col-md-6">
                    <h4>Start Time</h4>
                    <p>{{ $shift->start_time }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h4>End Time</h4>
                    <p>{{ $shift->end_time }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Back to Shifts</a>
                <a href="{{ route('shifts.edit', $shift->id) }}" class="btn btn-warning">Edit Shift</a>
            </div>
        </div>
    </div>
@endsection
