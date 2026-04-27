@extends('user.layouts.app')
@section('title', 'Employee List') <!-- Set your custom title here -->
@section('css')
    <style>
        .modal-content {
            border-radius: 10px;
            /* Add rounded corners to modal */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Soft shadow around modal */
        }

        .modal-header {
            border-bottom: 2px solid #007bff;
            /* Subtle border under the header */
        }

        .modal-footer {
            border-top: 2px solid #007bff;
            /* Subtle border at the top of footer */
        }

        .modal-body .d-flex {
            margin-bottom: 15px;
        }

        .schedule-day select,
        .schedule-day input {
            margin-top: 10px;
            border-radius: 5px;
            /* Round the inputs */
        }

        .schedule-day .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
            /* Make checkboxes match the theme color */
        }

        .schedule-day .form-select {
            border-radius: 5px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Subtle inner shadow */
        }

        .schedule-day {
            background-color: #f0f8fd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .modal-body {
            max-height: 70vh;
            /* Max height for large screens */
            overflow-y: auto;
            /* Scroll if content overflows */
        }
    </style>
@endsection
@section('content')
    <script src="jquery-3.7.1.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <div class="card mt-4 p-3 shadow-sm border-0">


        <div class="content">
            <h3>My Team</h3>
            <ul class="nav nav-tabs">

                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#staff">Staff Details</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#attendance">Attendance Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#bank">Bank Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#salary">Salary Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#leave">Leave Policy</a>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <!-- Staff Tab -->
                <div id="staff" class="tab-pane fade show active">
                    <h5>Staff Details</h5>
                    <button class="btn btn-primary">Update Work Timings</button>
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Job Title</th>
                                <th>Schedule Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Abhishek Das</td>
                                <td>Manager</td>
                                <td>Fixed</td>
                            </tr>
                            <tr>
                                <td>Ashadul Molla</td>
                                <td>Assistant</td>
                                <td>Fixed</td>
                            </tr>
                            <tr>
                                <td>Fahim Gazi</td>
                                <td>Developer</td>
                                <td>Not Set</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Attendance Tab -->
                <div id="attendance" class="tab-pane fade">
                    <h5>Attendance Details</h5>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#updateWorkTimingsModal">Update Work
                        Timings</button>
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Job Title</th>
                                <th>Schedule Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Abhishek Das</td>
                                <td>Manager</td>
                                <td>Fixed</td>
                            </tr>
                            <tr>
                                <td>Ashadul Molla</td>
                                <td>Assistant</td>
                                <td>Fixed</td>
                            </tr>
                            <tr>
                                <td>Fahim Gazi</td>
                                <td>Developer</td>
                                <td>Not Set</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Bank Tab -->
                <div id="bank" class="tab-pane fade">
                    <h5>Bank Details</h5>
                    <button class="btn btn-primary">Update Work Timings</button>
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Job Title</th>
                                <th>Schedule Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Abhishek Das</td>
                                <td>Manager</td>
                                <td>Fixed</td>
                            </tr>
                            <tr>
                                <td>Ashadul Molla</td>
                                <td>Assistant</td>
                                <td>Fixed</td>
                            </tr>
                            <tr>
                                <td>Fahim Gazi</td>
                                <td>Developer</td>
                                <td>Not Set</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Salary Tab -->
                <div id="salary" class="tab-pane fade">
                    <h5>Salary Details</h5>
                    <button class="btn btn-primary">Update Work Timings</button>
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Job Title</th>
                                <th>Schedule Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Abhishek Das</td>
                                <td>Manager</td>
                                <td>Fixed</td>
                            </tr>
                            <tr>
                                <td>Ashadul Molla</td>
                                <td>Assistant</td>
                                <td>Fixed</td>
                            </tr>
                            <tr>
                                <td>Fahim Gazi</td>
                                <td>Developer</td>
                                <td>Not Set</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Leave Tab -->
                <div id="leave" class="tab-pane fade">
                    <h5>Leave Policy</h5>
                    <button class="btn btn-primary">Update Work Timings</button>
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Job Title</th>
                                <th>Schedule Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Abhishek Das</td>
                                <td>Manager</td>
                                <td>Fixed</td>
                            </tr>
                            <tr>
                                <td>Ashadul Molla</td>
                                <td>Assistant</td>
                                <td>Fixed</td>
                            </tr>
                            <tr>
                                <td>Fahim Gazi</td>
                                <td>Developer</td>
                                <td>Not Set</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>





    </div>

    <!-- Modal for updating work timings -->
    <div class="modal fade" id="updateWorkTimingsModal" tabindex="-1" role="dialog"
        aria-labelledby="updateWorkTimingsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"> <!-- Added 'modal-lg' for larger modal on wider screens -->
            <div class="modal-content">
                <div class="modal-header bg-primary text-white"> <!-- Added background color for the header -->
                    <h5 class="modal-title" id="updateWorkTimingsModalLabel">Bulk Update Work Timings for All Staff</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Work Type Selection (Fixed or Flexible) -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex align-items-center border rounded-3 p-3"
                                style="background-color: rgb(240, 248, 253);">
                                <div class="fw-bold" style="width: 20%;">Select Type</div>
                                <!-- Fixed Radio Button -->
                                <div class="form-check me-4"> <!-- 'me-4' adds right margin -->
                                    <input class="form-check-input" type="radio" name="workType" id="fixedWork"
                                        value="fixed" checked>
                                    <label class="form-check-label" for="fixedWork">Fixed</label>
                                </div>
                                <!-- Flexible Radio Button -->
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="workType" id="flexibleWork"
                                        value="flexible">
                                    <label class="form-check-label" for="flexibleWork">Flexible</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Weekdays Schedule -->
                    <div class="form-group">
                        <label class="fw-bold">Weekdays Schedule</label>
                        <div class="d-flex flex-column gap-3">
                            <!-- Iterate over each day -->
                            <div class="schedule-day" id="days-schedule">
                                <!-- Monday -->
                                <div class="d-flex justify-content-between align-items-center p-3"
                                    style="background-color: rgb(240, 248, 253); border-radius: 5px;">
                                    <div class="d-flex justify-content-between align-items-center"
                                        style="width: 37%; min-width: 37%; max-width: 37%;">
                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                            <div style="color: red; line-height: 16.8px;">*</div>
                                            <div class="fw-semibold">Mon</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <input type="checkbox" name="mon" checked class="form-check-input">
                                        <select name="monShift" class="form-select" style="width: 180px;">
                                            <option>Select Shift</option>
                                            <option>08:00 AM - 10:00 PM</option>
                                            <option>08:00 AM - 09:00 PM</option>
                                            <option>08:00 AM - 09:00 AM</option>
                                            <option>08:30 AM - 08:30 PM</option>
                                            <option>08:30 AM - 10:00 PM</option>
                                            <option>09:00 AM - 09:00 PM</option>
                                            <option>09:00 AM - 08:00 PM</option>
                                            <option>09:00 AM - 10:00 PM</option>
                                            <option>10:00 AM - 07:00 PM</option>
                                            <option>10:00 AM - 09:00 PM</option>
                                            <option>10:00 AM - 08:00 PM</option>
                                            <option>02:00 PM - 07:00 PM</option>
                                        </select>

                                    </div>
                                </div>

                                <!-- Tuesday -->
                                <div class="d-flex justify-content-between align-items-center p-3"
                                    style="background-color: rgb(240, 248, 253); border-radius: 5px;">
                                    <div class="d-flex justify-content-between align-items-center"
                                        style="width: 37%; min-width: 37%; max-width: 37%;">
                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                            <div style="color: red; line-height: 16.8px;">*</div>
                                            <div class="fw-semibold">Tue</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <input type="checkbox" name="tue" checked class="form-check-input">
                                        <select name="tueShift" class="form-select" style="width: 180px;">
                                            <option>Select Shift</option>
                                            <option>08:00 AM - 10:00 PM</option>
                                            <option>08:00 AM - 09:00 PM</option>
                                            <option>08:00 AM - 09:00 AM</option>
                                            <option>08:30 AM - 08:30 PM</option>
                                            <option>08:30 AM - 10:00 PM</option>
                                            <option>09:00 AM - 09:00 PM</option>
                                            <option>09:00 AM - 08:00 PM</option>
                                            <option>09:00 AM - 10:00 PM</option>
                                            <option>10:00 AM - 07:00 PM</option>
                                            <option>10:00 AM - 09:00 PM</option>
                                            <option>10:00 AM - 08:00 PM</option>
                                            <option>02:00 PM - 07:00 PM</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Wednesday -->
                                <div class="d-flex justify-content-between align-items-center p-3"
                                    style="background-color: rgb(240, 248, 253); border-radius: 5px;">
                                    <div class="d-flex justify-content-between align-items-center"
                                        style="width: 37%; min-width: 37%; max-width: 37%;">
                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                            <div style="color: red; line-height: 16.8px;">*</div>
                                            <div class="fw-semibold">Wed</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <input type="checkbox" name="wed" checked class="form-check-input">
                                        <select name="wedShift" class="form-select" style="width: 180px;">
                                            <option>Select Shift</option>
                                            <option>08:00 AM - 10:00 PM</option>
                                            <option>08:00 AM - 09:00 PM</option>
                                            <option>08:00 AM - 09:00 AM</option>
                                            <option>08:30 AM - 08:30 PM</option>
                                            <option>08:30 AM - 10:00 PM</option>
                                            <option>09:00 AM - 09:00 PM</option>
                                            <option>09:00 AM - 08:00 PM</option>
                                            <option>09:00 AM - 10:00 PM</option>
                                            <option>10:00 AM - 07:00 PM</option>
                                            <option>10:00 AM - 09:00 PM</option>
                                            <option>10:00 AM - 08:00 PM</option>
                                            <option>02:00 PM - 07:00 PM</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Thursday -->
                                <div class="d-flex justify-content-between align-items-center p-3"
                                    style="background-color: rgb(240, 248, 253); border-radius: 5px;">
                                    <div class="d-flex justify-content-between align-items-center"
                                        style="width: 37%; min-width: 37%; max-width: 37%;">
                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                            <div style="color: red; line-height: 16.8px;">*</div>
                                            <div class="fw-semibold">Thu</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <input type="checkbox" name="thu" checked class="form-check-input">
                                        <select name="thuShift" class="form-select" style="width: 180px;">
                                            <option>Select Shift</option>
                                            <option>08:00 AM - 10:00 PM</option>
                                            <option>08:00 AM - 09:00 PM</option>
                                            <option>08:00 AM - 09:00 AM</option>
                                            <option>08:30 AM - 08:30 PM</option>
                                            <option>08:30 AM - 10:00 PM</option>
                                            <option>09:00 AM - 09:00 PM</option>
                                            <option>09:00 AM - 08:00 PM</option>
                                            <option>09:00 AM - 10:00 PM</option>
                                            <option>10:00 AM - 07:00 PM</option>
                                            <option>10:00 AM - 09:00 PM</option>
                                            <option>10:00 AM - 08:00 PM</option>
                                            <option>02:00 PM - 07:00 PM</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Friday -->
                                <div class="d-flex justify-content-between align-items-center p-3"
                                    style="background-color: rgb(240, 248, 253); border-radius: 5px;">
                                    <div class="d-flex justify-content-between align-items-center"
                                        style="width: 37%; min-width: 37%; max-width: 37%;">
                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                            <div style="color: red; line-height: 16.8px;">*</div>
                                            <div class="fw-semibold">Fri</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <input type="checkbox" name="fri" checked class="form-check-input">
                                        <select name="friShift" class="form-select" style="width: 180px;">
                                            <option>Select Shift</option>
                                            <option>08:00 AM - 10:00 PM</option>
                                            <option>08:00 AM - 09:00 PM</option>
                                            <option>08:00 AM - 09:00 AM</option>
                                            <option>08:30 AM - 08:30 PM</option>
                                            <option>08:30 AM - 10:00 PM</option>
                                            <option>09:00 AM - 09:00 PM</option>
                                            <option>09:00 AM - 08:00 PM</option>
                                            <option>09:00 AM - 10:00 PM</option>
                                            <option>10:00 AM - 07:00 PM</option>
                                            <option>10:00 AM - 09:00 PM</option>
                                            <option>10:00 AM - 08:00 PM</option>
                                            <option>02:00 PM - 07:00 PM</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Saturday -->
                                <div class="d-flex justify-content-between align-items-center p-3"
                                    style="background-color: rgb(240, 248, 253); border-radius: 5px;">
                                    <div class="d-flex justify-content-between align-items-center"
                                        style="width: 37%; min-width: 37%; max-width: 37%;">
                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                            <div style="color: red; line-height: 16.8px;">*</div>
                                            <div class="fw-semibold">Sat</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <input type="checkbox" name="sat" checked class="form-check-input">
                                        <select name="satShift" class="form-select" style="width: 180px;">
                                            <option>Select Shift</option>
                                            <option>08:00 AM - 10:00 PM</option>
                                            <option>08:00 AM - 09:00 PM</option>
                                            <option>08:00 AM - 09:00 AM</option>
                                            <option>08:30 AM - 08:30 PM</option>
                                            <option>08:30 AM - 10:00 PM</option>
                                            <option>09:00 AM - 09:00 PM</option>
                                            <option>09:00 AM - 08:00 PM</option>
                                            <option>09:00 AM - 10:00 PM</option>
                                            <option>10:00 AM - 07:00 PM</option>
                                            <option>10:00 AM - 09:00 PM</option>
                                            <option>10:00 AM - 08:00 PM</option>
                                            <option>02:00 PM - 07:00 PM</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Sunday -->
                                <div class="d-flex justify-content-between align-items-center p-3"
                                    style="background-color: rgb(240, 248, 253); border-radius: 5px;">
                                    <div class="d-flex justify-content-between align-items-center"
                                        style="width: 37%; min-width: 37%; max-width: 37%;">
                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                            <div style="color: red; line-height: 16.8px;">*</div>
                                            <div class="fw-semibold">Sun</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-3">
                                        <input type="checkbox" name="sun" checked class="form-check-input">
                                        <select name="sunShift" class="form-select" style="width: 180px;">
                                            <option>Select Shift</option>
                                            <option>08:00 AM - 10:00 PM</option>
                                            <option>08:00 AM - 09:00 PM</option>
                                            <option>08:00 AM - 09:00 AM</option>
                                            <option>08:30 AM - 08:30 PM</option>
                                            <option>08:30 AM - 10:00 PM</option>
                                            <option>09:00 AM - 09:00 PM</option>
                                            <option>09:00 AM - 08:00 PM</option>
                                            <option>09:00 AM - 10:00 PM</option>
                                            <option>10:00 AM - 07:00 PM</option>
                                            <option>10:00 AM - 09:00 PM</option>
                                            <option>10:00 AM - 08:00 PM</option>
                                            <option>02:00 PM - 07:00 PM</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelBtn">Cancel</button>
                    <button type="button" class="btn btn-primary">Update Work Timings for All Staff</button>
                </div>
            </div>
        </div>
    </div>




    <script>
        $(document).ready(function() {
            // Open the modal on button click
            $('#attendance').on('click', function() {
                $('#updateWorkTimingsModal').modal('show');
            });

            // Close the modal when the Cancel button is clicked
            $('#cancelBtn').on('click', function() {
                $('#updateWorkTimingsModal').modal('hide');
            });
        });
    </script>


@endsection
