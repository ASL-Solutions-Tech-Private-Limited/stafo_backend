@extends('user.layouts.app')
@section('title', 'Company Document KYC Verification | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">KYC & Document Verification</h3>
                <p class="text-muted small mb-0">Verify statutory company documents for {{ $company->company_name }} via government portals</p>
            </div>
        </div>

        <input type="hidden" name="user_id" id="user_id" value="{{ $company->id }}">

        <div class="row g-4">
            
            <!-- Aadhar Card Verification -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0">Aadhar Card Verification</h6>
                        </div>
                        @if ($company->aadhar_verify == 'Yes')
                            <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check"></i> Verified</span>
                        @else
                            <span class="badge-stafo badge-stafo-warning"><i class="fa-solid fa-clock"></i> Pending</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Aadhar Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="aadhar" id="aadhar"
                            value="{{ old('aadhar', $company->aadhar) }}" placeholder="Enter 12-digit Aadhar number"
                            @if ($company->aadhar_verify == 'Yes') disabled @endif>
                        <input type="text" class="form-control mt-2" name="aadhar_otp" id="aadhar_otp"
                            style="display:none" placeholder="Enter OTP received on registered mobile">
                    </div>

                    @if ($company->aadhar_verify != 'Yes')
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary px-3 verify_button" id="aadhar_button" type="button" onclick="verifyData('aadhar')">
                                <i class="fa-solid fa-shield-check me-1"></i> Send OTP & Verify
                            </button>
                            <button class="btn btn-success px-3 verify_button" id="aadhar_otp_button" type="button" onclick="verifyData('aadhar-otp')" style="display:none">
                                <i class="fa-solid fa-check me-1"></i> Confirm OTP
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- PAN Verification -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info fw-bold" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-credit-card"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0">PAN Card Verification</h6>
                        </div>
                        @if ($company->pan_verify == 'Yes')
                            <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check"></i> Verified</span>
                        @else
                            <span class="badge-stafo badge-stafo-warning"><i class="fa-solid fa-clock"></i> Pending</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">PAN Number</label>
                        <input type="text" class="form-control" name="pan" id="pan"
                            value="{{ old('pan', $company->pan_number) }}" placeholder="Enter 10-digit PAN (e.g. ABCDE1234F)"
                            @if ($company->pan_verify == 'Yes') disabled @endif>
                    </div>

                    @if ($company->pan_verify != 'Yes')
                        <button class="btn btn-primary px-3 verify_button" id="pan_button" type="button" onclick="verifyData('pan')">
                            <i class="fa-solid fa-shield-check me-1"></i> Verify PAN
                        </button>
                    @endif
                </div>
            </div>

            <!-- Registration Number -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning fw-bold" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-building-circle-check"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0">Company Registration (CIN / LLPIN)</h6>
                        </div>
                        @if ($company->registration_verify == 'Yes')
                            <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check"></i> Verified</span>
                        @else
                            <span class="badge-stafo badge-stafo-warning"><i class="fa-solid fa-clock"></i> Pending</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Registration Number</label>
                        <input type="text" class="form-control" name="registration_number" id="registration_number"
                            value="{{ old('registration_number', $company->registration_number) }}"
                            placeholder="Enter incorporation / registration number"
                            @if ($company->registration_verify == 'Yes') disabled @endif>
                    </div>

                    @if ($company->registration_verify != 'Yes')
                        <button class="btn btn-primary px-3 verify_button" id="registration_number_button" type="button" onclick="verifyData('registration_number')">
                            <i class="fa-solid fa-shield-check me-1"></i> Verify CIN
                        </button>
                    @endif
                </div>
            </div>

            <!-- GSTIN Verification -->
            <div class="col-12 col-lg-6">
                <div class="p-4 bg-light rounded-4 border h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success fw-bold" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0">GSTIN Tax Registration</h6>
                        </div>
                        @if ($company->gstn_verify == 'Yes')
                            <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-circle-check"></i> Verified</span>
                        @else
                            <span class="badge-stafo badge-stafo-warning"><i class="fa-solid fa-clock"></i> Pending</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">GST Number</label>
                        <input type="text" class="form-control" name="gstin" id="gstin"
                            value="{{ old('gst_number', $company->gst_number) }}" placeholder="Enter 15-digit GSTIN number"
                            @if ($company->gstn_verify == 'Yes') disabled @endif>
                    </div>

                    @if ($company->gstn_verify != 'Yes')
                        <button class="btn btn-primary px-3 verify_button" id="gstin_button" type="button" onclick="verifyData('gstin')">
                            <i class="fa-solid fa-shield-check me-1"></i> Verify GSTIN
                        </button>
                    @endif
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
        if (data == '') {
            Swal.fire({
                title: 'Error',
                text: "Please enter the document number",
                icon: 'error',
                showCancelButton: false,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
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
                if (response.status == 'success') {
                    if (document.getElementById(type + '_add_button')) document.getElementById(type + '_add_button').style.display = 'none';
                    if (document.getElementById(type + '_button')) document.getElementById(type + '_button').style.display = 'block';
                    Swal.fire({
                        title: 'Success',
                        text: "Document number updated successfully",
                        icon: 'success',
                        confirmButtonColor: '#4f46e5'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: "Document number not updated",
                        icon: 'error',
                        confirmButtonColor: '#4f46e5'
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
                confirmButtonColor: '#4f46e5'
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
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.status == 'success') {
                    if (type == 'aadhar') {
                        request_id = res.request_id;
                        document.getElementById('aadhar_button').style.display = 'none';
                        document.getElementById('aadhar_otp').style.display = 'block';
                        document.getElementById('aadhar_otp_button').style.display = 'block';
                        Swal.fire({
                            title: 'OTP Sent',
                            text: "Please enter OTP sent to your registered mobile number",
                            icon: 'info',
                            confirmButtonColor: '#4f46e5'
                        });
                    } else {
                        if (type == 'aadhar-otp') {
                            document.getElementById('aadhar_button').style.display = 'none';
                            document.getElementById('aadhar_otp').style.display = 'none';
                            document.getElementById('aadhar_otp_button').style.display = 'none';
                        } else {
                            if (document.getElementById(type + '_button')) document.getElementById(type + '_button').style.display = 'none';
                        }
                        Swal.fire({
                            title: 'Verified!',
                            text: "Document verified successfully",
                            icon: 'success',
                            confirmButtonColor: '#4f46e5'
                        }).then(() => {
                            location.reload();
                        });
                    }
                } else {
                    Swal.fire({
                        title: 'Verification Failed',
                        text: res.message || 'Verification could not be completed',
                        icon: 'error',
                        confirmButtonColor: '#4f46e5'
                    });
                }
            },
            error: function() {
                Swal.fire('Error', 'An error occurred during verification. Please try again.', 'error');
            }
        });
    }
</script>
@endsection
