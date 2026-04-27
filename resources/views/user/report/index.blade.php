@extends('user.layouts.app')

@section('title', 'Download Monthly Report')

@section('css')

    <style>
        .card-header {
            background-color: #007bff;
            color: white;
        }

        .nav-tabs .nav-link {
            border-radius: 0;
        }

        .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
        }

        .btn-icon {
            margin-right: 8px;
        }

        .tab-content {
            margin-top: 1rem;
        }

        .form-group {
            margin-bottom: .8rem;
        }

        .form-group label {
            font-weight: bold;
        }

        .footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <h2 class="text-center mb-4">Download Monthly Reports</h2>


        <div class="card">
            <div class="card-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="employee-tab" data-toggle="tab" href="#employee" role="tab"
                            aria-controls="employee" aria-selected="true">Employee</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="attendance-tab" data-toggle="tab" href="#attendance" role="tab"
                            aria-controls="attendance" aria-selected="false">Attendance</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="leave-tab" data-toggle="tab" href="#leave" role="tab"
                            aria-controls="leave" aria-selected="false">Leave</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="reportTabsContent">
                    <!-- Employee Tab -->
                    <div class="tab-pane fade show active" id="employee" role="tabpanel" aria-labelledby="employee-tab">
                        <div class=" mt-1">
                            <h3 class="card-header mb-3">Employee Report</h3>
                            <div class="card-body">
                                <form action="{{ route('report.exportEmployee') }}" method="GET">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="department">Department:</label>
                                                <select name="department" id="department" class="form-control">
                                                    <option value="">Select Department</option>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}">{{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="branch">Branch:</label>
                                                <select name="branch" id="branch" class="form-control">
                                                    <option value="">Select Branch</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="month">Month: <span class="text-danger">*</span></label>
                                        <select name="month" id="month" class="form-control" required>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">
                                                    {{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="year">Year: <span class="text-danger">*</span></label>
                                        <input type="number" name="year" id="year" value="{{ date('Y') }}"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div> --}}

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="start_date">Start Date: <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" name="start_date" id="start_date" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="end_date">End Date: <span class="text-danger">*</span></label>
                                                <input type="date" name="end_date" id="end_date" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Custom dropdown for selecting format -->
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="format">Select Format: <span
                                                        class="text-danger">*</span></label>
                                                <!-- For the Employee Tab -->
                                                <div class="dropdown">
                                                    <button class="btn btn-warning dropdown-toggle form-control"
                                                        type="button" id="employee-dropdownMenuButton"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Select Format
                                                    </button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="employee-dropdownMenuButton">
                                                        <a class="dropdown-item" href="#" data-value="excel">
                                                            <i class="fas fa-file-excel btn-icon"></i> Excel
                                                        </a>
                                                        <a class="dropdown-item" href="#" data-value="pdf">
                                                            <i class="fas fa-file-pdf btn-icon"></i> PDF
                                                        </a>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="format" id="employee-format">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group" style="margin-top: 24px">
                                                <button type="submit" class="btn btn-primary">Download Employee
                                                    Report</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Tab -->
                    <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                        <div class="mt-1">
                            <h3 class="card-header mb-3">Attendance Report</h3>
                            <div class="card-body">
                                <form action="{{ route('report.exportAttendance') }}" method="GET">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="department">Department:</label>
                                                <select name="department" id="department" class="form-control">
                                                    <option value="">Select Department</option>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}">{{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="branch">Branch:</label>
                                                <select name="branch" id="branch" class="form-control">
                                                    <option value="">Select Branch</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="month">Month: <span class="text-danger">*</span></label>
                                                <select name="month" id="attendance-month" class="form-control"
                                                    required>
                                                    @for ($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}">
                                                            {{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="year">Year: <span class="text-danger">*</span></label>
                                                <input type="number" name="year" id="attendance-year"
                                                    value="{{ date('Y') }}" class="form-control" required>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="start_date">Start Date: <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" name="start_date" id="start_date"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="end_date">End Date: <span class="text-danger">*</span></label>
                                                <input type="date" name="end_date" id="end_date"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Custom dropdown for selecting format -->
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="format">Select Format: <span
                                                        class="text-danger">*</span></label>
                                                <div class="dropdown">
                                                    <button class="btn btn-warning dropdown-toggle form-control"
                                                        type="button" id="attendance-dropdownMenuButton"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        Select Format
                                                    </button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="attendance-dropdownMenuButton">
                                                        <a class="dropdown-item" href="#" data-value="excel">
                                                            <i class="fas fa-file-excel btn-icon"></i> Excel
                                                        </a>
                                                        <a class="dropdown-item" href="#" data-value="pdf">
                                                            <i class="fas fa-file-pdf btn-icon"></i> PDF
                                                        </a>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="format" id="attendance-format">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group" style="margin-top: 24px">
                                                <button type="submit" class="btn btn-primary">Download Attendance
                                                    Report</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Tab -->
                    <div class="tab-pane fade" id="leave" role="tabpanel" aria-labelledby="leave-tab">
                        <div class="mt-1">
                            <h3 class="card-header mb-3">Leave Report</h3>
                            <div class="card-body">
                                <form action="{{ route('report.exportLeave') }}" method="GET">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="department">Department:</label>
                                                <select name="department" id="department" class="form-control">
                                                    <option value="">Select Department</option>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}">{{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="branch">Branch:</label>
                                                <select name="branch" id="branch" class="form-control">
                                                    <option value="">Select Branch</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="month">Month: <span class="text-danger">*</span></label>
                                                <select name="month" id="leave-month" class="form-control" required>
                                                    @for ($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}">
                                                            {{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="year">Year: <span class="text-danger">*</span></label>
                                                <input type="number" name="year" id="leave-year"
                                                    value="{{ date('Y') }}" class="form-control" required>
                                            </div>
                                        </div>
                                    </div> --}}

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="start_date">Start Date: <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" name="start_date" id="start_date"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="end_date">End Date: <span class="text-danger">*</span></label>
                                                <input type="date" name="end_date" id="end_date"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="format">Select Format: <span
                                                        class="text-danger">*</span></label>
                                                <div class="dropdown">
                                                    <button class="btn btn-warning dropdown-toggle form-control"
                                                        type="button" id="leave-dropdownMenuButton"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        Select Format
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="leave-dropdownMenuButton">
                                                        <a class="dropdown-item" href="#" data-value="excel">
                                                            <i class="fas fa-file-excel btn-icon"></i> Excel
                                                        </a>
                                                        <a class="dropdown-item" href="#" data-value="pdf">
                                                            <i class="fas fa-file-pdf btn-icon"></i> PDF
                                                        </a>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="format" id="leave-format">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group" style="margin-top: 24px">
                                                <button type="submit" class="btn btn-primary">Download Leave
                                                    Report</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <div class="footer">
        <p>© {{ date('Y') }} All rights reserved.</p>
    </div>
@endsection

@section('js')
    <!-- Include jQuery and Bootstrap JS (optional if not already included) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                var selectedValue = this.getAttribute('data-value');
                var formatText = this.innerText.trim();
                if (this.closest('#employee')) {
                    document.getElementById('employee-dropdownMenuButton').innerHTML =
                        `<i class="fas ${this.querySelector('i').classList[1]}"></i> ${formatText}`;
                    document.getElementById('employee-format').value = selectedValue;
                } else if (this.closest('#attendance')) {
                    document.getElementById('attendance-dropdownMenuButton').innerHTML =
                        `<i class="fas ${this.querySelector('i').classList[1]}"></i> ${formatText}`;
                    document.getElementById('attendance-format').value = selectedValue;
                } else if (this.closest('#leave')) {
                    document.getElementById('leave-dropdownMenuButton').innerHTML =
                        `<i class="fas ${this.querySelector('i').classList[1]}"></i> ${formatText}`;
                    document.getElementById('leave-format').value = selectedValue;
                }
            });
        });
    </script>

@endsection
