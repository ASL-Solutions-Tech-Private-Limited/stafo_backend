@extends('user.layouts.app')
@section('title', 'Company Documents Verification') <!-- Set your custom title here -->

@section('css')
    <style>
        /* Apply Roboto font to the entire page */
    </style>
@endsection
@section('content')
    <div class="card mt-4 p-3">

        <div class="container">
            <div class="docs-data">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold">Company Documents Verification</h2>
                </div>


                <!-- Upload Image -->

                <div class="row g-3">
                    <input type="hidden" name="user_id" id="user_id" value="{{ $company->id }}">
                    <div class="col-md-12">
                        Verification of <b>{{ $company->company_name }}</b>
                    </div>
                    <div class="row mt-3 g-3 align-items-center">
                        <div class="col-md-3 mt-0">
                            <label class="form-label fw-semibold">Aadhar Number <span class="red">*</span> </label>
                        </div>

                        <div class="col-md-7 col-9">
                            <div class="input-group ">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control" name="aadhar" id="aadhar"
                                    value="{{ old('aadhar', $company->aadhar) }}" placeholder="Enter aadhar card number"
                                    @if ($company->aadhar_verify == 'Yes') disabled @endif>
                                <input type="text" class="form-control" name="aadhar_otp" id="aadhar_otp"
                                    style="display:none" placeholder="Enter OTP">
                            </div>
                        </div>
                        @if ($company->aadhar_verify == 'Yes')
                            <div class="col-md-2 text-end col-3">
                                <button class="btn btn-success">Verified</button>
                            </div>
                        @else
                            <div class="col-md-2 text-end col-3 mt-0">
                                <button class="btn btn-primary  verify_button" id="aadhar_button" type="button"
                                    onclick="verifyData('aadhar')">Verify</button>
                                <button class="btn btn-primary  verify_button" id="aadhar_otp_button" type="button"
                                    onclick="verifyData('aadhar-otp')" style="display:none">Verify OTP</button>
                            </div>
                        @endif
                    </div>

                    <div class="row mt-3 g-3 align-items-center">
                        <div class="col-md-3 mt-0">
                            <label class="form-label fw-semibold">Pan Number</label>
                        </div>
                        <div class="col-md-7 col-9">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                <input type="text" class="form-control" name="pan" id="pan"
                                    value="{{ old('pan', $company->pan_number) }}" placeholder="Enter pan card number"
                                    @if ($company->pan_verify == 'Yes') disabled @endif>
                            </div>
                        </div>
                        <div class="col-md-2 text-end col-3 mt-0">
                            @if ($company->pan_verify == 'Yes')
                                <div>
                                    <span class="btn btn-success ">Verified</span>
                                </div>
                            @else
                                <div>
                                    <button class="btn btn-primary  verify_button" id="pan_button" type="button"
                                        onclick="verifyData('pan')">Verify</button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-3 g-3 align-items-center">
                        <div class="col-md-3 mt-0">
                            <label class="form-label fw-semibold">Registration Number</label>
                        </div>
                        <div class="col-md-7 col-9">
                            <div class="input-group col-md-4">
                                <span class="input-group-text"><i class="fas fa-clipboard"></i></span>
                                <input type="text" class="form-control" name="registration_number"
                                    id="registration_number"
                                    value="{{ old('registration_number', $company->registration_number) }}"
                                    placeholder="Enter registration number"
                                    @if ($company->registration_verify == 'Yes') disabled @endif>
                            </div>
                        </div>
                        <div class="col-md-2 text-end col-3 mt-0">
                            @if ($company->registration_verify == 'Yes')
                                <div>
                                    <span class="btn btn-success ">Verified</span>
                                </div>
                            @else
                                <div>
                                    <button class="btn btn-primary  verify_button" id="registration_number_button"
                                        type="button" onclick="verifyData('registration_number')">Verify</button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-3 g-3 align-items-center">
                        <div class="col-md-3 mt-0">
                            <label class="form-label fw-semibold">GST Number</label>
                        </div>
                        <div class="col-md-7 col-9">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-archive"></i></span>
                                <input type="text" class="form-control" name="gstin" id="gstin"
                                    value="{{ old('gst_number', $company->gst_number) }}" placeholder="Enter GST number"
                                    @if ($company->gstn_verify == 'Yes') disabled @endif>
                            </div>
                        </div>
                        <div class="col-md-2 text-end col-3 mt-0">
                            @if ($company->gstn_verify == 'Yes')
                                <div>
                                    <span class="btn btn-success mt-3">Verified</span>
                                </div>
                            @else
                                <div>
                                    <button class="btn btn-primary  verify_button" id="gstin_button" type="button"
                                        onclick="verifyData('gstin')">Verify</button>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </div>





    </div>
@endsection
<script>
    function updateData(type) {
        var data = document.getElementById(type).value;
        if (data == '') {
            Swal.fire({
                title: 'Error',
                text: "Please enter the document number",
                icon: 'error',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {

            });
            return;
        }
        var url = "{{ route('companyDataUpdate') }}";
        $.ajax({
            url: url,
            type: 'PUT',
            data: {
                data: data,
                type: type,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                console.log(response);
                if (response.status == 'success') {
                    document.getElementById(type + '_add_button').style.display = 'none';
                    document.getElementById(type + '_button').style.display = 'block';
                    Swal.fire({
                        title: 'Success',
                        text: "Document number updated successfully",
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        //confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {

                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: "Document number not updated",
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
    var request_id = '';
    var otp = '';

    function verifyData(type) {

        if (type == 'aadhar-otp') {
            var number = document.getElementById('aadhar').value;
            otp = document.getElementById('aadhar_otp').value;
        } else {
            var number = document.getElementById(type).value;
        }
        var user_id = document.getElementById('user_id').value;
        var url = "{{ url('api/document-verify') }}";
        url = url.replace(':id', user_id);
        if (number == '') {
            Swal.fire({
                title: 'Error',
                text: "Please enter the number",
                icon: 'error',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {

            });
            return;
        }
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                company_id: user_id,
                number: number,
                type: type,
                request_id: request_id,
                otp: otp,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                res = JSON.parse(response);
                console.log(res);
                if (res.status == 'success') {
                    if (type == 'aadhar') {
                        request_id = res.request_id; //'1111';
                        //document.getElementById('aadhar').style.display = 'none';
                        document.getElementById('aadhar_button').style.display = 'none';
                        document.getElementById('aadhar_otp').style.display = 'block';
                        document.getElementById('aadhar_otp_button').style.display = 'block';
                        Swal.fire({
                            title: 'OTP',
                            text: "Please enter Otp sent to your registered mobile number",
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            //confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {

                        });
                    } else {
                        //document.getElementById(type).style.display = 'none';
                        if (type == 'aadhar-otp') {
                            document.getElementById('aadhar_button').style.display = 'none';
                            document.getElementById('aadhar_otp').style.display = 'none';
                            document.getElementById('aadhar_otp_button').style.display = 'none';
                        } else {
                            document.getElementById(type + '_button').style.display = 'none';
                        }
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
                    }

                } else {
                    Swal.fire({
                        title: 'Error',
                        text: res.message,
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
