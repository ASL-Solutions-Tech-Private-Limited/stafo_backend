@extends('user.layouts.app')

@section('title', 'Edit Shift')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.0/jquery.timepicker.min.css">
    <style>
        /* Custom styling for timepicker */
        .timepicker {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }

        .form-control {
            border-radius: 5px;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #333;
        }

        .text-danger {
            font-size: 0.875rem;
            color: #dc3545;
        }

        /* Adding some padding and margin to form */
        .form-group {
            margin-bottom: 20px;
        }

        .card {
            border: none;
            background-color: transparent;
        }
    </style>
@endsection

@section('content')
    <div class="card mt-4 p-3">
        <div class="container">
            <h1>Edit Shift</h1>

            <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Shift Name -->
                <div class="form-group">
                    <label for="shift_name">Shift Name</label>
                    <input type="text" name="shift_name" id="shift_name" class="form-control"
                        value="{{ old('shift_name', $shift->shift_name) }}" required>
                    @error('shift_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Start Time -->
                <div class="form-group">
                    <label for="start_time">Start Time</label>
                    <input type="text" name="start_time" id="start_time" class="form-control timepicker"
                        value="{{ old('start_time', $shift->start_time) }}" required>
                    @error('start_time')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- End Time -->
                <div class="form-group">
                    <label for="end_time">End Time</label>
                    <input type="text" name="end_time" id="end_time" class="form-control timepicker"
                        value="{{ old('end_time', $shift->end_time) }}" required>
                    @error('end_time')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Shift</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.0/jquery.timepicker.min.js"></script>

    <script>
        $(document).ready(function() {
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
