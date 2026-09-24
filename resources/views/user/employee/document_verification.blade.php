@extends('user.layouts.app')

@section('title', 'Employee Document KYC Verification | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Document KYC Verification</h3>
                    <span class="badge-stafo badge-stafo-primary">
                        <i class="fa-solid fa-user me-1"></i> {{ $employee->name }}
                    </span>
                    @if($employee->employee_id)
                        <span class="badge-stafo badge-stafo-info">ID: {{ $employee->employee_id }}</span>
                    @endif
                </div>
                <p class="text-muted small mb-0">Verify government identity credentials and official documents for {{ $employee->name }}</p>
            </div>
            <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Employee List
            </a>
        </div>

        <input type="hidden" name="user_id" id="user_id" value="{{ $employee->id }}">

        <!-- Verification Cards Grid -->
        <div class="row g-4">

            <!-- 1. Aadhar Card -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-white rounded-3 p-2 border shadow-xs">
                                    <i class="fa-solid fa-id-card text-primary fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">Aadhar Card</h5>
                                    <small class="text-muted">12-digit Unique Identification Authority of India</small>
                                </div>
                            </div>
                            @if($employee->aadhar_verify == 'Yes')
                                <span class="badge-stafo badge-stafo-success">
                                    <i class="fa-solid fa-circle-check me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge-stafo badge-stafo-warning">
                                    <i class="fa-solid fa-clock me-1"></i> Unverified
                                </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="aadhar" class="form-label fw-semibold text-dark small">Aadhar Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-fingerprint text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 {{ $employee->aadhar_verify == 'Yes' ? 'bg-white' : '' }}" name="aadhar" id="aadhar" maxlength="12"
                                    value="{{ old('aadhar', $employee->aadhar) }}" placeholder="Enter 12-digit Aadhar number" {{ $employee->aadhar_verify == 'Yes' ? 'readonly' : '' }}>
                            </div>
                        </div>

                        <!-- OTP Section for Aadhar -->
                        <div class="mb-3" id="aadhar_otp_group" style="display:none">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label for="aadhar_otp" class="form-label fw-semibold text-dark small mb-0">Enter Aadhar OTP</label>
                                <a href="javascript:void(0)" onclick="resendAadharOtp()" class="small text-primary text-decoration-none fw-semibold" style="font-size: 0.78rem;">
                                    <i class="fa-solid fa-arrows-rotate me-1"></i> Resend OTP
                                </a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-key text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" name="aadhar_otp" id="aadhar_otp" maxlength="6" placeholder="Enter 6-digit OTP received on mobile">
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">An OTP has been sent to the mobile number registered with your UIDAI Aadhaar.</small>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex gap-2">
                        @if($employee->aadhar_verify == 'Yes')
                            <button type="button" class="btn btn-success w-100 disabled" style="opacity: 0.9;">
                                <i class="fa-solid fa-check-double me-1"></i> Identity Verified
                            </button>
                        @else
                            <button class="btn btn-outline-primary px-3" id="aadhar_update_btn" type="button" onclick="updateData('aadhar')" title="Save without verifying">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save
                            </button>
                            <button class="btn btn-primary flex-grow-1 shadow-sm" id="aadhar_button" type="button" onclick="verifyData('aadhar')">
                                <i class="fa-solid fa-shield-halved me-1"></i> Send OTP for Verification
                            </button>
                            <button class="btn btn-success flex-grow-1 shadow-sm" id="aadhar_otp_button" type="button" onclick="verifyData('aadhar-otp')" style="display:none">
                                <i class="fa-solid fa-check-circle me-1"></i> Submit & Verify OTP
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. PAN Card -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-white rounded-3 p-2 border shadow-xs">
                                    <i class="fa-solid fa-credit-card text-success fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">PAN Card</h5>
                                    <small class="text-muted">10-character Permanent Account Number</small>
                                </div>
                            </div>
                            @if($employee->pan_verify == 'Yes')
                                <span class="badge-stafo badge-stafo-success">
                                    <i class="fa-solid fa-circle-check me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge-stafo badge-stafo-warning">
                                    <i class="fa-solid fa-clock me-1"></i> Unverified
                                </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="pan" class="form-label fw-semibold text-dark small">PAN Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-id-badge text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 text-uppercase {{ $employee->pan_verify == 'Yes' ? 'bg-white' : '' }}" name="pan" id="pan"
                                    value="{{ old('pan', $employee->pan) }}" placeholder="e.g. ABCDE1234F" {{ $employee->pan_verify == 'Yes' ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex gap-2">
                        @if($employee->pan_verify == 'Yes')
                            <button type="button" class="btn btn-success w-100 disabled" style="opacity: 0.9;">
                                <i class="fa-solid fa-check-double me-1"></i> Identity Verified
                            </button>
                        @elseif($employee->pan == '')
                            <button class="btn btn-primary w-100 shadow-sm" type="button" onclick="updateData('pan')">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save PAN Number
                            </button>
                        @else
                            <button class="btn btn-outline-primary px-3" type="button" onclick="updateData('pan')">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update
                            </button>
                            <button class="btn btn-primary flex-grow-1 shadow-sm" type="button" onclick="verifyData('pan')">
                                <i class="fa-solid fa-shield-halved me-1"></i> Verify PAN
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 3. Voter ID Card -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-white rounded-3 p-2 border shadow-xs">
                                    <i class="fa-solid fa-address-card text-warning fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">Voter ID (EPIC)</h5>
                                    <small class="text-muted">Election Commission of India Identity</small>
                                </div>
                            </div>
                            @if($employee->voter_verify == 'Yes')
                                <span class="badge-stafo badge-stafo-success">
                                    <i class="fa-solid fa-circle-check me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge-stafo badge-stafo-warning">
                                    <i class="fa-solid fa-clock me-1"></i> Unverified
                                </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="voter" class="form-label fw-semibold text-dark small">Voter ID Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-check-to-slot text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 {{ $employee->voter_verify == 'Yes' ? 'bg-white' : '' }}" name="voter" id="voter"
                                    value="{{ old('voter', $employee->voter) }}" placeholder="Enter Voter ID / EPIC number" {{ $employee->voter_verify == 'Yes' ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex gap-2">
                        @if($employee->voter_verify == 'Yes')
                            <button type="button" class="btn btn-success w-100 disabled" style="opacity: 0.9;">
                                <i class="fa-solid fa-check-double me-1"></i> Identity Verified
                            </button>
                        @elseif($employee->voter == '')
                            <button class="btn btn-primary w-100 shadow-sm" type="button" onclick="updateData('voter')">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Voter ID
                            </button>
                        @else
                            <button class="btn btn-outline-primary px-3" type="button" onclick="updateData('voter')">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update
                            </button>
                            <button class="btn btn-primary flex-grow-1 shadow-sm" type="button" onclick="verifyData('voter')">
                                <i class="fa-solid fa-shield-halved me-1"></i> Verify Voter ID
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 4. Driving License -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-white rounded-3 p-2 border shadow-xs">
                                    <i class="fa-solid fa-car text-info fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">Driving License</h5>
                                    <small class="text-muted">Transport Authority Motor Driving License</small>
                                </div>
                            </div>
                            @if($employee->dl_verify == 'Yes')
                                <span class="badge-stafo badge-stafo-success">
                                    <i class="fa-solid fa-circle-check me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge-stafo badge-stafo-warning">
                                    <i class="fa-solid fa-clock me-1"></i> Unverified
                                </span>
                            @endif
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-sm-7">
                                <label for="driving_license" class="form-label fw-semibold text-dark small">License Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-id-card-clip text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0 {{ $employee->dl_verify == 'Yes' ? 'bg-white' : '' }}" name="driving_license" id="driving_license"
                                        value="{{ old('driving_license', $employee->driving_license) }}" placeholder="License number" {{ $employee->dl_verify == 'Yes' ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-12 col-sm-5">
                                <label for="dob" class="form-label fw-semibold text-dark small">DOB (as in DL)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-calendar-day text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0 {{ $employee->dl_verify == 'Yes' ? 'bg-white' : '' }}" name="dob" id="dob"
                                        value="" placeholder="YYYY-MM-DD" {{ $employee->dl_verify == 'Yes' ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex gap-2">
                        @if($employee->dl_verify == 'Yes')
                            <button type="button" class="btn btn-success w-100 disabled" style="opacity: 0.9;">
                                <i class="fa-solid fa-check-double me-1"></i> Identity Verified
                            </button>
                        @elseif($employee->driving_license == '')
                            <button class="btn btn-primary w-100 shadow-sm" type="button" onclick="updateData('driving_license')">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Driving License
                            </button>
                        @else
                            <button class="btn btn-outline-primary px-3" type="button" onclick="updateData('driving_license')">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update
                            </button>
                            <button class="btn btn-primary flex-grow-1 shadow-sm" type="button" onclick="verifyData('driving_license')">
                                <i class="fa-solid fa-shield-halved me-1"></i> Verify DL
                            </button>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    function updateData(type) {
        var data = document.getElementById(type).value;
        var user_id = document.getElementById('user_id').value;
        var url = "{{ route('employeesDataUpdate', ':id') }}";
        url = url.replace(':id', user_id);

        if(!data) {
            Swal.fire({
                title: 'Empty Field',
                text: 'Please enter a valid document number first',
                icon: 'warning',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

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
                Swal.fire({
                    title: 'Saved',
                    text: 'Document details saved successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to update document details.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    }

    var aadhar_request_id = '';

    function resendAadharOtp() {
        verifyData('aadhar');
    }

    function verifyData(type) {
        var user_id = document.getElementById('user_id').value;
        var url = "{{ url('api/document-verify') }}";
        var number = '';
        var otp = '';
        var dob = '';

        if (type === 'aadhar') {
            number = $('#aadhar').val().trim();
            if (!number || number.length < 12) {
                Swal.fire({
                    title: 'Invalid Aadhaar',
                    text: 'Please enter a valid 12-digit Aadhaar number.',
                    icon: 'warning',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }
        } else if (type === 'aadhar-otp') {
            number = $('#aadhar').val().trim();
            otp = $('#aadhar_otp').val().trim();
            if (!otp) {
                Swal.fire({
                    title: 'Enter OTP',
                    text: 'Please enter the 6-digit OTP received on your mobile.',
                    icon: 'warning',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }
            if (!aadhar_request_id) {
                Swal.fire({
                    title: 'Session Expired',
                    text: 'Please click "Send OTP for Verification" to generate an OTP first.',
                    icon: 'error',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }
        } else if (type === 'driving_license') {
            number = $('#driving_license').val().trim();
            dob = $('#dob').val().trim();
            if (!number) {
                Swal.fire('Required', 'Please enter Driving License number', 'warning');
                return;
            }
        } else {
            var el = document.getElementById(type);
            number = el ? el.value.trim() : '';
            if (!number) {
                Swal.fire('Required', 'Please enter document number first', 'warning');
                return;
            }
        }

        // Show loading modal
        Swal.fire({
            title: type === 'aadhar' ? 'Requesting OTP...' : (type === 'aadhar-otp' ? 'Verifying OTP...' : 'Verifying Document...'),
            text: 'Please wait while we communicate with the verification gateway.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                employee_id: user_id,
                number: number,
                type: type,
                request_id: aadhar_request_id,
                client_id: aadhar_request_id,
                otp: otp,
                dob: dob,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                var res;
                try {
                    res = typeof response === 'object' ? response : JSON.parse(response);
                } catch(e) {
                    res = response;
                }

                var isSuccess = false;
                if (res) {
                    var status = res.status;
                    var code = res.code || res.status_code;
                    if (status === 'success' || status === true || (typeof status === 'string' && status.toLowerCase() === 'success') || code == 200) {
                        isSuccess = true;
                    }
                }

                if (isSuccess) {
                    if (type === 'aadhar') {
                        // Capture request_id or client_id from response
                        aadhar_request_id = res.request_id || (res.data && (res.data.client_id || res.data.request_id)) || '';
                        $('#aadhar_otp_group').slideDown();
                        $('#aadhar_button').hide();
                        $('#aadhar_update_btn').hide();
                        $('#aadhar_otp_button').show();
                        $('#aadhar_otp').focus();

                        Swal.fire({
                            title: 'OTP Sent!',
                            text: res.message || 'OTP has been sent to the mobile number registered with your UIDAI Aadhaar.',
                            icon: 'info',
                            confirmButtonColor: '#4f46e5'
                        });
                    } else if (type === 'aadhar-otp') {
                        Swal.fire({
                            title: 'Aadhaar Verified!',
                            text: 'Aadhaar identity has been verified successfully.',
                            icon: 'success',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Verified!',
                            text: "Document verified successfully",
                            icon: 'success',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            location.reload();
                        });
                    }
                } else {
                    var errMsg = (res && (res.message || res.error || (res.data && res.data.message))) || 'Verification could not be completed.';
                    Swal.fire({
                        title: 'Verification Failed',
                        text: errMsg,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            },
            error: function(xhr) {
                var errText = 'An error occurred while contacting the verification service.';
                try {
                    var errRes = JSON.parse(xhr.responseText);
                    if (errRes && (errRes.message || errRes.error)) {
                        errText = errRes.message || errRes.error;
                    }
                } catch(e) {}

                Swal.fire({
                    title: 'Verification Error',
                    text: errText,
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    }
</script>
@endsection
