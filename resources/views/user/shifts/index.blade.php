@extends('user.layouts.app')
@section('title', 'Work Shifts | STAFO HRMS')

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

        /* Day indicator pills in table */
        .day-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: 700;
            margin: 0 1px;
        }
        .day-pill.active {
            background-color: #4f46e5;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
        }
        .day-pill.inactive {
            background-color: #f1f5f9;
            color: #cbd5e1;
            border: 1px solid #e2e8f0;
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
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            
            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h3 class="fw-bold text-dark mb-0">Work Shifts</h3>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small">
                            <i class="fa-solid fa-clock me-1"></i> Shift Schedules
                        </span>
                    </div>
                    <p class="text-muted small mb-0">Define shift hours, start/end timings, and assigned working days</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary px-3.5 py-2 fw-semibold rounded-pill shadow-xs" onclick="openCreateShiftModal()">
                        <i class="fa-solid fa-plus me-1.5"></i> Add New Shift
                    </button>
                </div>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('shifts.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="position-relative">
                            <input type="text" name="shift_name" class="form-control ps-4" value="{{ request()->get('shift_name') }}"
                                placeholder="Search by shift name...">
                            <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.8rem;"></i>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <input type="text" name="start_time" class="form-control" value="{{ request()->get('start_time') }}"
                            placeholder="Start Time (e.g. 09:00 AM)">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <input type="text" name="end_time" class="form-control" value="{{ request()->get('end_time') }}"
                            placeholder="End Time (e.g. 06:00 PM)">
                    </div>

                    <div class="col-12 col-sm-6 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 rounded-3">
                            <i class="fa-solid fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->filled('shift_name') || request()->filled('start_time') || request()->filled('end_time'))
                            <a href="{{ route('shifts.index') }}" class="btn btn-light border text-muted px-3 rounded-3" title="Clear Filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Shifts Table -->
            <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0" id="shiftsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;" class="text-center">#</th>
                            <th style="min-width: 200px;">Shift Name</th>
                            <th style="min-width: 140px;">Timings</th>
                            <th style="min-width: 240px;">Working Days (Mon - Sun)</th>
                            <th style="width: 130px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shifts as $index => $shift)
                            <tr id="shift-row-{{ $shift->id }}">
                                <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 38px; height: 38px; font-size: 0.9rem; flex-shrink: 0;">
                                            <i class="fa-solid fa-business-time"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $shift->shift_name }}</span>
                                            <small class="text-muted">ID: #{{ $shift->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small w-fit">
                                            <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> {{ $shift->start_time }}
                                        </span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill small w-fit">
                                            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> {{ $shift->end_time }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        <span class="day-pill {{ (bool)$shift->monday ? 'active' : 'inactive' }}" title="Monday: {{ (bool)$shift->monday ? 'Working' : 'Off' }}">M</span>
                                        <span class="day-pill {{ (bool)$shift->tuesday ? 'active' : 'inactive' }}" title="Tuesday: {{ (bool)$shift->tuesday ? 'Working' : 'Off' }}">T</span>
                                        <span class="day-pill {{ (bool)$shift->wednesday ? 'active' : 'inactive' }}" title="Wednesday: {{ (bool)$shift->wednesday ? 'Working' : 'Off' }}">W</span>
                                        <span class="day-pill {{ (bool)$shift->thursday ? 'active' : 'inactive' }}" title="Thursday: {{ (bool)$shift->thursday ? 'Working' : 'Off' }}">T</span>
                                        <span class="day-pill {{ (bool)$shift->friday ? 'active' : 'inactive' }}" title="Friday: {{ (bool)$shift->friday ? 'Working' : 'Off' }}">F</span>
                                        <span class="day-pill {{ (bool)$shift->saturday ? 'active' : 'inactive' }}" title="Saturday: {{ (bool)$shift->saturday ? 'Working' : 'Off' }}">S</span>
                                        <span class="day-pill {{ (bool)$shift->sunday ? 'active' : 'inactive' }}" title="Sunday: {{ (bool)$shift->sunday ? 'Working' : 'Off' }}">S</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1.5">
                                        <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1 rounded-3" title="Edit Shift"
                                                onclick="openEditShiftModal({{ $shift->id }})">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1 rounded-3" title="Delete Shift"
                                                onclick="deleteShift({{ $shift->id }}, '{{ addslashes($shift->shift_name) }}')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="no-shifts-row">
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-clock-rotate-left fs-1 mb-2 d-block opacity-40 text-primary"></i>
                                    <h6 class="fw-bold text-dark mt-2">No Work Shifts Found</h6>
                                    <p class="small text-muted mb-3">Get started by adding your first company shift schedule.</p>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="openCreateShiftModal()">
                                        <i class="fa-solid fa-plus me-1"></i> Add Shift Now
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal: Create / Update Shift -->
    <div class="modal fade" id="shiftModal" tabindex="-1" aria-labelledby="shiftModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-light border-bottom py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fa-solid fa-business-time" id="modalHeaderIcon"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="shiftModalLabel">Create New Shift</h5>
                            <small class="text-muted" id="shiftModalSubtitle">Configure shift timings and working days</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="shiftForm" onsubmit="handleShiftFormSubmit(event)">
                    @csrf
                    <input type="hidden" id="shift_id" name="shift_id" value="">

                    <div class="modal-body p-4">
                        
                        <!-- General Info Row -->
                        <div class="row g-3 mb-4">
                            <!-- Shift Name -->
                            <div class="col-12 col-md-4">
                                <label for="modal_shift_name" class="form-label fw-semibold text-dark small mb-1">
                                    Shift Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-tag"></i></span>
                                    <input type="text" id="modal_shift_name" name="shift_name" class="form-control"
                                           placeholder="e.g. General Shift" required>
                                </div>
                                <div class="invalid-feedback" id="err_shift_name"></div>
                            </div>

                            <!-- Start Time -->
                            <div class="col-12 col-md-4">
                                <label for="modal_start_time" class="form-label fw-semibold text-dark small mb-1">
                                    Start Time <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-success"><i class="fa-regular fa-clock"></i></span>
                                    <input type="text" id="modal_start_time" name="start_time" class="form-control timepicker"
                                           placeholder="09:00 AM" required>
                                </div>
                                <div class="invalid-feedback" id="err_start_time"></div>
                            </div>

                            <!-- End Time -->
                            <div class="col-12 col-md-4">
                                <label for="modal_end_time" class="form-label fw-semibold text-dark small mb-1">
                                    End Time <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-danger"><i class="fa-regular fa-clock"></i></span>
                                    <input type="text" id="modal_end_time" name="end_time" class="form-control timepicker"
                                           placeholder="06:00 PM" required>
                                </div>
                                <div class="invalid-feedback" id="err_end_time"></div>
                            </div>
                        </div>

                        <!-- Working Days Section -->
                        <div class="p-3.5 bg-light rounded-4 border">
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
                            <div class="row g-2 text-center" id="workingDaysGrid">
                                
                                <!-- Monday -->
                                <div class="col">
                                    <div class="day-toggle-card active" id="card_day_monday" onclick="toggleDayCheckbox('monday')">
                                        <input type="checkbox" id="modal_day_monday" name="monday" class="d-none" checked>
                                        <span class="day-abbr">Mon</span>
                                        <span class="day-status-text" id="label_day_monday">ON</span>
                                    </div>
                                </div>

                                <!-- Tuesday -->
                                <div class="col">
                                    <div class="day-toggle-card active" id="card_day_tuesday" onclick="toggleDayCheckbox('tuesday')">
                                        <input type="checkbox" id="modal_day_tuesday" name="tuesday" class="d-none" checked>
                                        <span class="day-abbr">Tue</span>
                                        <span class="day-status-text" id="label_day_tuesday">ON</span>
                                    </div>
                                </div>

                                <!-- Wednesday -->
                                <div class="col">
                                    <div class="day-toggle-card active" id="card_day_wednesday" onclick="toggleDayCheckbox('wednesday')">
                                        <input type="checkbox" id="modal_day_wednesday" name="wednesday" class="d-none" checked>
                                        <span class="day-abbr">Wed</span>
                                        <span class="day-status-text" id="label_day_wednesday">ON</span>
                                    </div>
                                </div>

                                <!-- Thursday -->
                                <div class="col">
                                    <div class="day-toggle-card active" id="card_day_thursday" onclick="toggleDayCheckbox('thursday')">
                                        <input type="checkbox" id="modal_day_thursday" name="thursday" class="d-none" checked>
                                        <span class="day-abbr">Thu</span>
                                        <span class="day-status-text" id="label_day_thursday">ON</span>
                                    </div>
                                </div>

                                <!-- Friday -->
                                <div class="col">
                                    <div class="day-toggle-card active" id="card_day_friday" onclick="toggleDayCheckbox('friday')">
                                        <input type="checkbox" id="modal_day_friday" name="friday" class="d-none" checked>
                                        <span class="day-abbr">Fri</span>
                                        <span class="day-status-text" id="label_day_friday">ON</span>
                                    </div>
                                </div>

                                <!-- Saturday -->
                                <div class="col">
                                    <div class="day-toggle-card active" id="card_day_saturday" onclick="toggleDayCheckbox('saturday')">
                                        <input type="checkbox" id="modal_day_saturday" name="saturday" class="d-none" checked>
                                        <span class="day-abbr">Sat</span>
                                        <span class="day-status-text" id="label_day_saturday">ON</span>
                                    </div>
                                </div>

                                <!-- Sunday -->
                                <div class="col">
                                    <div class="day-toggle-card" id="card_day_sunday" onclick="toggleDayCheckbox('sunday')">
                                        <input type="checkbox" id="modal_day_sunday" name="sunday" class="d-none">
                                        <span class="day-abbr text-danger">Sun</span>
                                        <span class="day-status-text" id="label_day_sunday">OFF</span>
                                    </div>
                                </div>

                            </div>
                            <div class="text-danger small mt-2 d-none" id="err_working_days">Please select at least one working day for this shift.</div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" id="btnSubmitShift">
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="submitSpinner"></span>
                            <i class="fa-solid fa-floppy-disk me-1" id="submitIcon"></i>
                            <span id="submitBtnText">Save Shift</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.0/jquery.timepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const daysList = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        let shiftBootstrapModal = null;

        $(document).ready(function () {
            // Initialize Timepicker
            $('#modal_start_time, #modal_end_time').timepicker({
                timeFormat: 'h:i A',
                dynamic: true,
                dropdown: true,
                scrollbar: true,
                defaultTime: false,
                show2400: false,
            });

            // Initialize Bootstrap Modal instance
            const modalEl = document.getElementById('shiftModal');
            if (modalEl) {
                shiftBootstrapModal = new bootstrap.Modal(modalEl);
            }

            // Check if URL has ?action=create or ?action=edit&id=...
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'create') {
                openCreateShiftModal();
            } else if (urlParams.get('action') === 'edit' && urlParams.get('id')) {
                openEditShiftModal(urlParams.get('id'));
            }
        });

        // Toggle Single Day Checkbox
        function toggleDayCheckbox(dayName) {
            const cb = document.getElementById(`modal_day_${dayName}`);
            const card = document.getElementById(`card_day_${dayName}`);
            const label = document.getElementById(`label_day_${dayName}`);

            if (!cb || !card) return;

            cb.checked = !cb.checked;
            updateDayCardUI(dayName, cb.checked);
        }

        // Update single day card visual UI
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

        // Apply Quick Preset (Mon-Sat, Mon-Fri, All, None)
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

        // Open Modal in Create Mode
        function openCreateShiftModal() {
            document.getElementById('shiftForm').reset();
            document.getElementById('shift_id').value = '';
            document.getElementById('shiftModalLabel').innerText = 'Create New Shift';
            document.getElementById('shiftModalSubtitle').innerText = 'Define a new shift schedule and working days';
            document.getElementById('submitBtnText').innerText = 'Save Shift';
            document.getElementById('modalHeaderIcon').className = 'fa-solid fa-business-time';

            // Clear validation classes
            clearValidationErrors();

            // Default days: Mon - Sat ON, Sunday OFF
            applyDayPreset('mon-sat');

            if (shiftBootstrapModal) {
                shiftBootstrapModal.show();
            }
        }

        // Open Modal in Edit Mode (fetches shift details via API/AJAX)
        function openEditShiftModal(shiftId) {
            clearValidationErrors();
            
            // Set loading state
            document.getElementById('shift_id').value = shiftId;
            document.getElementById('shiftModalLabel').innerText = 'Edit Work Shift';
            document.getElementById('shiftModalSubtitle').innerText = 'Loading shift details...';
            document.getElementById('submitBtnText').innerText = 'Update Shift';
            document.getElementById('modalHeaderIcon').className = 'fa-solid fa-pen-to-square';

            if (shiftBootstrapModal) {
                shiftBootstrapModal.show();
            }

            // Fetch shift by ID
            fetch(`{{ url('company/shifts') }}/${shiftId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (res.status || res.success) {
                    const shift = res.data;
                    document.getElementById('modal_shift_name').value = shift.shift_name || '';
                    document.getElementById('modal_start_time').value = shift.start_time || '';
                    document.getElementById('modal_end_time').value = shift.end_time || '';
                    document.getElementById('shiftModalSubtitle').innerText = `Editing: ${shift.shift_name}`;

                    // Set Days Checkboxes
                    daysList.forEach(d => {
                        const isDayActive = Boolean(Number(shift[d]) === 1 || shift[d] === true || shift[d] === '1');
                        updateDayCardUI(d, isDayActive);
                    });
                } else {
                    Swal.fire('Error', res.message || 'Unable to fetch shift details.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Failed to communicate with server.', 'error');
            });
        }

        // Handle AJAX Form Submit for Create and Update
        function handleShiftFormSubmit(event) {
            event.preventDefault();
            clearValidationErrors();

            const shiftId = document.getElementById('shift_id').value;
            const isEdit = Boolean(shiftId);

            const shiftName = document.getElementById('modal_shift_name').value.trim();
            const startTime = document.getElementById('modal_start_time').value.trim();
            const endTime = document.getElementById('modal_end_time').value.trim();

            // Client Validation
            let hasError = false;
            if (!shiftName) {
                showFieldError('shift_name', 'Shift name is required.');
                hasError = true;
            }
            if (!startTime) {
                showFieldError('start_time', 'Start time is required.');
                hasError = true;
            }
            if (!endTime) {
                showFieldError('end_time', 'End time is required.');
                hasError = true;
            }

            // Check at least one day selected
            let anyDaySelected = false;
            daysList.forEach(d => {
                const cb = document.getElementById(`modal_day_${d}`);
                if (cb && cb.checked) anyDaySelected = true;
            });

            if (!anyDaySelected) {
                const errDiv = document.getElementById('err_working_days');
                if (errDiv) errDiv.classList.remove('d-none');
                hasError = true;
            }

            if (hasError) return;

            // Build Payload (strictly sending true/false as requested)
            const payload = {
                _token: '{{ csrf_token() }}',
                shift_name: shiftName,
                start_time: startTime,
                end_time: endTime,
                monday: document.getElementById('modal_day_monday').checked ? true : false,
                tuesday: document.getElementById('modal_day_tuesday').checked ? true : false,
                wednesday: document.getElementById('modal_day_wednesday').checked ? true : false,
                thursday: document.getElementById('modal_day_thursday').checked ? true : false,
                friday: document.getElementById('modal_day_friday').checked ? true : false,
                saturday: document.getElementById('modal_day_saturday').checked ? true : false,
                sunday: document.getElementById('modal_day_sunday').checked ? true : false,
            };

            const url = isEdit ? `{{ url('company/shifts') }}/${shiftId}` : `{{ url('company/shifts') }}`;
            const method = isEdit ? 'PUT' : 'POST';

            if (isEdit) {
                payload._method = 'PUT';
            }

            // Button loading UI
            const btnSubmit = document.getElementById('btnSubmitShift');
            const spinner = document.getElementById('submitSpinner');
            const icon = document.getElementById('submitIcon');
            btnSubmit.disabled = true;
            spinner.classList.remove('d-none');
            icon.classList.add('d-none');

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data: data })))
            .then(res => {
                btnSubmit.disabled = false;
                spinner.classList.add('d-none');
                icon.classList.remove('d-none');

                if (res.ok && (res.data.success || res.data.status)) {
                    if (shiftBootstrapModal) {
                        shiftBootstrapModal.hide();
                    }

                    Swal.fire({
                        title: 'Success!',
                        text: res.data.message || (isEdit ? 'Shift updated successfully!' : 'Shift created successfully!'),
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "{{ route('shifts.index') }}";
                    });
                } else {
                    if (res.data.errors) {
                        Object.keys(res.data.errors).forEach(key => {
                            showFieldError(key, res.data.errors[key][0]);
                        });
                    } else {
                        Swal.fire('Error', res.data.message || 'Failed to save shift.', 'error');
                    }
                }
            })
            .catch(err => {
                btnSubmit.disabled = false;
                spinner.classList.add('d-none');
                icon.classList.remove('d-none');
                console.error(err);
                Swal.fire('Error', 'An unexpected network error occurred.', 'error');
            });
        }

        // Delete Shift via AJAX
        function deleteShift(shiftId, shiftName) {
            Swal.fire({
                title: 'Delete Shift?',
                text: `Are you sure you want to delete "${shiftName}"? Employees assigned to this shift will lose their shift timings.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete shift'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('company/shifts') }}/${shiftId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success || res.status) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: res.message || 'Shift deleted successfully.',
                                icon: 'success',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            const row = document.getElementById(`shift-row-${shiftId}`);
                            if (row) {
                                row.style.transition = 'all 0.3s ease';
                                row.style.opacity = '0';
                                setTimeout(() => row.remove(), 300);
                            }
                        } else {
                            Swal.fire('Error', res.message || 'Failed to delete shift.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'An unexpected error occurred.', 'error');
                    });
                }
            });
        }

        function showFieldError(field, msg) {
            const input = document.getElementById(`modal_${field}`);
            const errDiv = document.getElementById(`err_${field}`);
            if (input) input.classList.add('is-invalid');
            if (errDiv) {
                errDiv.innerText = msg;
                errDiv.style.display = 'block';
            }
        }

        function clearValidationErrors() {
            ['shift_name', 'start_time', 'end_time'].forEach(f => {
                const input = document.getElementById(`modal_${f}`);
                const errDiv = document.getElementById(`err_${f}`);
                if (input) input.classList.remove('is-invalid');
                if (errDiv) {
                    errDiv.innerText = '';
                    errDiv.style.display = 'none';
                }
            });
            const daysErr = document.getElementById('err_working_days');
            if (daysErr) daysErr.classList.add('d-none');
        }
    </script>
@endsection