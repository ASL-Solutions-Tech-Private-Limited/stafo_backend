@extends('user.layouts.app')

@section('title', 'Employee Add') <!-- Set your custom title here -->
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.0/jquery.timepicker.min.css">

@endsection

@section('content')
    <div class="card mt-4 p-3">
        <div class="container">
            <h2 class="mb-3 fw-bold">Create New Shift</h2>

            <form action="{{ route('shifts.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="shift_name">Shift Name</label>
                            <input type="text" name="shift_name" id="shift_name" class="form-control"
                                value="{{ old('shift_name') }}" placeholder="Enter shift name">
                            @error('shift_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="start_time">Start Time</label>
                            <input type="text" name="start_time" id="start_time" class="form-control timepicker"
                                value="{{ old('start_time') }}" placeholder="Enter start time (e.g., 08:00 AM)">
                            @error('start_time')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="end_time">End Time</label>
                            <input type="text" name="end_time" id="end_time" class="form-control timepicker"
                                value="{{ old('end_time') }}" placeholder="Enter end time (e.g., 05:00 PM)">
                            @error('end_time')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary">Save Shift</button>
                    </div>
                </div>







            </form>

        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.0/jquery.timepicker.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize timepicker for the start_time and end_time fields
            $('#start_time, #end_time').timepicker({
                timeFormat: 'h:i A', // 12-hour format with AM/PM
                dynamic: true, // Dynamic behavior (auto adjust for time range)
                dropdown: true, // Show dropdown to select time
                scrollbar: true, // Enable scrollbar for the dropdown
                defaultTime: false, // Don't set a default time, it'll show the dropdown first
                show2400: false, // Disable 24-hour format (only AM/PM format)
            });
        });
    </script>
@endsection