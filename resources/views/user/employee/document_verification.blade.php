@extends('user.layouts.app')
@section('title', 'Employee Documents Verification') <!-- Set your custom title here -->

@section('css')
    <style>
        /* Apply Roboto font to the entire page */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #eef2f7;
        }

        .card {
            border-radius: 12px;
            padding: 25px;
            margin: auto;
            background: white;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .card:hover {
            box-shadow: 0px 6px 18px rgba(0, 0, 0, 0.15);
        }

        .form-floating label {
            color: #6c757d;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 12px;
            font-family: 'Roboto', sans-serif;
            transition: 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: none;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-radius: 10px 0 0 10px;
        }

        .btn-primary {
            background: #007bff;
            border-radius: 8px;
            font-weight: bold;
            font-family: 'Roboto', sans-serif;
            padding: 12px;
            transition: 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-dark {
            border-radius: 8px;
            font-weight: bold;
            background: linear-gradient(135deg, #218838, #1e7e34);
        }

        .custom-radio input {
            margin-right: 5px;
        }
    </style>
@endsection
@section('content')
    <div class="card mt-4 p-3">

        <div class="container my-5">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Employee Documents Verification</h4>

                    <a href="{{ route('employee.index') }}" class="btn btn-primary" style="float: right;"> <i
                            class="fas fa-list"></i> Employee
                        List</a>

                </div>

                
                    <!-- Upload Image -->
                    
                    <div class="row g-3">
                        <input type="hidden" name="user_id" id="user_id" value="{{ $employee->id }}">
                        <div class="col-md-12">
                            Verification of <b>{{ $employee->name }}</b>
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Aadhar Card</label>
                            </div>
                            <div class="input-group col-md-4">
                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                <input type="text" class="form-control" name="aadhar" id="aadhar"
                                    value="{{ old('aadhar', $employee->aadhar) }}" placeholder="Enter aadhar card number">
                            </div>
                            @if($employee->aadhar_verify == 'Yes')
                            <div class="col-md-3">
                                <span class="btn btn-success mt-3">Verified</span>
                            </div>
                            @elseif($employee->aadhar == '')
                            <div class="col-md-3">
                                <button class="btn btn-primary mt-3" type="button" onclick="updateData('aadhar')">Add</button>
                            </div>
                            @else
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="aadhar_otp" id="aadhar_otp" style="display:none" placeholder="Enter OTP">
                                <button class="btn btn-primary mt-3" id="aadhar_button" type="button" onclick="verifyData('aadhar')">Verify</button>
                                <button class="btn btn-primary mt-3" id="aadhar_otp_button" type="button" onclick="verifyData('aadhar-otp')" style="display:none">Verify OTP</button>
                            </div>
                            @endif
                        </div>                        
                        <div class="col-md-8 d-flex align-items-center">
                            
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Pan Card</label>
                            </div>
                            <div class="input-group col-md-4">
                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                <input type="text" class="form-control" name="pan" id="pan"
                                    value="{{ old('pan', $employee->pan) }}" placeholder="Enter pan card number">
                            </div>
                            @if($employee->pan_verify == 'Yes')
                            <div class="col-md-3">
                                <span class="btn btn-success mt-3">Verified</span>
                            </div>
                            @elseif($employee->pan == '')
                            <div class="col-md-3">
                                <button class="btn btn-primary mt-3" type="button" onclick="updateData('pan')">Add</button>
                            </div>
                            @else
                            <div class="col-md-3">
                                <button class="btn btn-primary mt-3" type="button" onclick="verifyData('pan')">Verify</button>
                            </div>
                            @endif
                        </div>
                        
                        <div class="col-md-8 d-flex align-items-center">
                            
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Votar Card</label>
                            </div>
                            <div class="input-group col-md-4">
                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                <input type="text" class="form-control" name="voter" id="voter"
                                    value="{{ old('voter', $employee->voter) }}" placeholder="Enter votar card number">
                            </div>
                            @if($employee->voter_verify == 'Yes')
                            <div class="col-md-3">
                                <span class="btn btn-success mt-3">Verified</span>
                            </div>
                            @elseif($employee->voter == '')
                            <div class="col-md-3">
                                <button class="btn btn-primary mt-3" type="button"  onclick="updateData('voter')">Add</button>
                            </div>
                            @else
                            <div class="col-md-3">
                                <button class="btn btn-primary mt-3" type="button" onclick="verifyData('voter')">Verify</button>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-8 d-flex align-items-center">
                            
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Driving License</label>
                            </div>
                            <div class="input-group col-md-4">
                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                <input type="text" class="form-control" name="driving_license" id="driving_license"
                                    value="{{ old('driving_license', $employee->driving_license) }}" placeholder="Enter driving license number">
                                <input type="text" class="form-control" name="dob" id="dob"
                                    value="" placeholder="Enter date of birth">
                            </div>
                            @if($employee->dl_verify == 'Yes')
                            <div class="col-md-3">
                                <span class="btn btn-success mt-3">Verified</span>
                            </div>
                            @elseif($employee->driving_license == '')
                            <div class="col-md-3">
                                <button class="btn btn-primary mt-3" type="button"  onclick="updateData('driving_license')">Add</button>
                            </div>
                            @else
                            <div class="col-md-3">
                                <button class="btn btn-primary mt-3" type="button" onclick="verifyData('driving_license')">Verify</button>
                            </div>
                            @endif
                        </div>

                    </div>
                
            </div>
        </div>





    </div>
@endsection
<script>
    function updateData(type) {
        var data = document.getElementById(type).value;
        var user_id = document.getElementById('user_id').value;
        var url = "{{ route('employeesDataUpdate', ':id') }}";
        url = url.replace(':id', user_id);
        $.ajax({
            url: url,
            type: 'PUT',
            data: {
                id: user_id,
                data: data,
                type: type,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                console.log(response);
            }
        });
    }
    function verifyData(type) {
        var number = document.getElementById(type).value;
        var user_id = document.getElementById('user_id').value;
        var url = "{{ url('api/document-verify') }}";
        url = url.replace(':id', user_id);
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                employee_id: user_id,
                number: number,
                type: type,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                res = JSON.parse(response);
                console.log(res);
                if(res.status == 'success') {
                    Swal.fire({
                        title: 'Verified',
                        text: "Document verified successfully",
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        //confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        
                    });
                } else{
                    Swal.fire({
                        title: 'Error',
                        text: "Document not verified",
                        icon: 'error',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    }).then((result) => {
                        
                    });
                }
            }
        });
    }
</script>
