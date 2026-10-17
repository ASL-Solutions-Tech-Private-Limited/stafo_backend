@extends('user.layouts.app')

@section('title', 'Add Shift | STAFO HRMS')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.0/jquery.timepicker.min.css">
    <style>
        .day-toggle-card {
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease-in-out;
            border: 2px solid #e5e7eb;
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 10px 8px;
            text-align: center;
        }
        .day-toggle-card:hover {
            border-color: #cbd5e1;
            background-color: #f1f5f9;
        }
        .day-toggle-card.active {
            border-color: #4f46e5 !important;
            background-color: #eef2ff !important;
            color: #4338ca !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.12);
        }
        .day-toggle-card .day-abbr {
            font-weight: 700;
            font-size: 0.95rem;
            display: block;
        }
        .day-toggle-card .day-status-text {
            font-size: 0.7rem;
            font-weight: 600;
            display: block;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .day-toggle-card.active .day-status-text {
            color: #4f46e5;
        }
        .day-toggle-card:not(.active) .day-status-text {
            color: #94a3b8;
        }
        .preset-btn {
            font-size: 0.78rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Create New Shift</h3>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small">
                        <i class="fa-solid fa-clock me-1"></i> Work Schedule
                    </span>
                </div>
                <p class="text-muted small mb-0">Define a new shift roster with start/end timings and working days</p>
            </div>
            <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-pill">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Shifts
            </a>
        </div>

        <form action="{{ route('shifts.store') }}" method="POST">
            @csrf

            <!-- Form Card -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-business-time text-primary"></i> Shift Configuration
                </h5>

                <div class="row g-3 mb-4">
                    <!-- Shift Name -->
                    <div class="col-12 col-md-4">
                        <label for="shift_name" class="form-label fw-semibold text-dark small">
                            Shift Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="shift_name" id="shift_name" class="form-control @error('shift_name') is-invalid @enderror"
                               value="{{ old('shift_name') }}" placeholder="e.g. Morning Shift, General Shift" required>
                        @error('shift_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Start Time -->
                    <div class="col-12 col-md-4">
                        <label for="start_time" class="form-label fw-semibold text-dark small">
                            Start Time <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-success"><i class="fa-regular fa-clock"></i></span>
                            <input type="text" name="start_time" id="start_time" class="form-control timepicker @error('start_time') is-invalid @enderror"
                                   value="{{ old('start_time') }}" placeholder="e.g. 09:00 AM" required>
                        </div>
                        @error('start_time')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- End Time -->
                    <div class="col-12 col-md-4">
                        <label for="end_time" class="form-label fw-semibold text-dark small">
                            End Time <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-danger"><i class="fa-regular fa-clock"></i></span>
                            <input type="text" name="end_time" id="end_time" class="form-control timepicker @error('end_time') is-invalid @enderror"
                                   value="{{ old('end_time') }}" placeholder="e.g. 06:00 PM" required>
                        </div>
                        @error('end_time')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Working Days Section -->
                <div class="p-3.5 bg-white rounded-3 border">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3">
                        <div>
                            <label class="fw-bold text-dark small mb-0 d-flex align-items-center gap-1.5">
                                <i class="fa-solid fa-calendar-week text-primary"></i> Working Days / Shift Days
                            </label>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Select all active working days for this shift (Checked = True, Unchecked = False)</small>
                        </div>

                        <!-- Quick Presets -->
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary preset-btn" onclick="applyDayPreset('mon-sat')">Mon - Sat</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary preset-btn" onclick="applyDayPreset('mon-fri')">Mon - Fri</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary preset-btn" onclick="applyDayPreset('all')">All 7 Days</button>
                            <button type="button" class="btn btn-sm btn-outline-danger preset-btn" onclick="applyDayPreset('none')">Clear</button>
                        </div>
                    </div>

                    <!-- 7 Days Grid -->
                    <div class="row g-2 text-center">
                        @php
                            $days = [
                                'monday' => ['label' => 'Mon', 'default' => true],
                                'tuesday' => ['label' => 'Tue', 'default' => true],
                                'wednesday' => ['label' => 'Wed', 'default' => true],
                                'thursday' => ['label' => 'Thu', 'default' => true],
                                'friday' => ['label' => 'Fri', 'default' => true],
                                'saturday' => ['label' => 'Sat', 'default' => true],
                                'sunday' => ['label' => 'Sun', 'default' => false, 'danger' => true],
                            ];
                        @endphp

                        @foreach($days as $key => $d)
                            @php
                                $isChecked = old($key, $d['default'] ? 'true' : 'false') == 'true' || old($key, $d['default'] ? 1 : 0) == 1;
                            @endphp
                            <div class="col">
                                <div class="day-toggle-card {{ $isChecked ? 'active' : '' }}" id="card_day_{{ $key }}" onclick="toggleDayCheckbox('{{ $key }}')">
                                    <input type="checkbox" id="modal_day_{{ $key }}" name="{{ $key }}" class="d-none" value="1" {{ $isChecked ? 'checked' : '' }}>
                                    <span class="day-abbr {{ !empty($d['danger']) ? 'text-danger' : '' }}">{{ $d['label'] }}</span>
                                    <span class="day-status-text" id="label_day_{{ $key }}">{{ $isChecked ? 'ON' : 'OFF' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-pill shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Shift
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.0/jquery.timepicker.min.js"></script>
    <script>
        const daysList = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        $(document).ready(function () {
            $('#start_time, #end_time').timepicker({
                timeFormat: 'h:i A',
                dynamic: true,
                dropdown: true,
                scrollbar: true,
                defaultTime: false,
                show2400: false,
            });
        });

        function toggleDayCheckbox(dayName) {
            const cb = document.getElementById(`modal_day_${dayName}`);
            const card = document.getElementById(`card_day_${dayName}`);
            const label = document.getElementById(`label_day_${dayName}`);

            if (!cb || !card) return;

            cb.checked = !cb.checked;
            updateDayCardUI(dayName, cb.checked);
        }

        function updateDayCardUI(dayName, isChecked) {
            const card = document.getElementById(`card_day_${dayName}`);
            const label = document.getElementById(`label_day_${dayName}`);
            const cb = document.getElementById(`modal_day_${dayName}`);

            if (cb) cb.checked = isChecked;

            if (card) {
                if (isChecked) {
                    card.classList.add('active');
                } else {
                    card.classList.remove('active');
                }
            }

            if (label) {
                label.innerText = isChecked ? 'ON' : 'OFF';
            }
        }

        function applyDayPreset(preset) {
            daysList.forEach(d => {
                let shouldCheck = false;
                if (preset === 'all') {
                    shouldCheck = true;
                } else if (preset === 'mon-sat') {
                    shouldCheck = (d !== 'sunday');
                } else if (preset === 'mon-fri') {
                    shouldCheck = (d !== 'saturday' && d !== 'sunday');
                } else if (preset === 'none') {
                    shouldCheck = false;
                }
                updateDayCardUI(d, shouldCheck);
            });
        }
    </script>
@endsection