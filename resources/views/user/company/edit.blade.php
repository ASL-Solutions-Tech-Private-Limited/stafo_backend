@extends('user.layouts.app')
@section('title', 'Company Profile Settings | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Company Profile & Branding</h3>
                <p class="text-muted small mb-0">Manage corporate identity, business registration, banking credentials, and contact details</p>
            </div>
        </div>

        <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Section 1: Logo & Brand Identity -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-image text-primary"></i> Company Logo & Branding
                </h5>
                
                <div class="row align-items-center g-4">
                    <div class="col-auto">
                        <div class="position-relative">
                            @if ($company->image_name)
                                <img src="{{ asset('uploads/compnay_logo/' . $company->image_name) }}" alt="Company Logo"
                                     id="logoPreview" class="rounded-4 border shadow-sm object-fit-cover" width="100" height="100">
                            @else
                                <img src="{{ asset('uploads/compnay_logo/no-image.png') }}" alt="Default Logo"
                                     id="logoPreview" class="rounded-4 border shadow-sm object-fit-cover" width="100" height="100">
                            @endif
                            <div id="loader" class="d-none position-absolute top-50 start-50 translate-middle">
                                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <label for="companyLogo" class="form-label fw-semibold text-dark mb-1">Upload New Logo</label>
                        <p class="text-muted small mb-2">Recommended dimensions: 300x300px. Formats: PNG, JPG, WEBP (Max 2MB)</p>
                        <input type="file" class="form-control" id="companyLogo" name="companylogo" accept="image/*" onchange="previewImage(event)">
                    </div>
                </div>
            </div>

            <!-- Section 2: General Information -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-building text-primary"></i> Company Information
                </h5>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Company Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-building text-muted"></i></span>
                            <input type="text" name="company_name" class="form-control"
                                value="{{ old('company_name', $company->company_name) }}" placeholder="Enter company name" required>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Mobile Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-phone text-muted"></i></span>
                            <input type="text" name="mobile_no" class="form-control bg-light"
                                value="{{ old('mobile_no', $company->mobile_no) }}" readonly>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Official Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $company->email) }}" placeholder="Enter email address">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Company Type</label>
                        <select class="form-select" name="company_types">
                            <option value="">Select Company Type</option>
                            @foreach ($company_type as $c_type)
                                <option value="{{ $c_type->id }}"
                                    {{ old('company_types', $company->company_type) == $c_type->id ? 'selected' : '' }}>
                                    {{ $c_type->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Industry / Business Sector</label>
                        <select class="form-select" name="business_name">
                            <option value="">Select Business Sector</option>
                            @foreach ($business_type as $b_type)
                                <option value="{{ $b_type->id }}"
                                    {{ old('business_name', $company->business_type_id) == $b_type->id ? 'selected' : '' }}>
                                    {{ $b_type->business_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Number of Employees</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fa-solid fa-users text-muted"></i></span>
                            <input type="text" name="no_of_employee" class="form-control"
                                value="{{ old('no_of_employee', $company->no_of_employee) }}" placeholder="e.g. 25-50">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Banking Credentials -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-building-columns text-primary"></i> Banking Details
                </h5>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control"
                            value="{{ old('bank_name', $company->bank_name) }}" placeholder="e.g. HDFC Bank, SBI">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">Account Number</label>
                        <input type="text" name="account_number" class="form-control"
                            value="{{ old('account_number', $company->account_number) }}" placeholder="Enter bank account number">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold text-dark">IFSC Code</label>
                        <input type="text" name="ifsc_code" class="form-control"
                            value="{{ old('ifsc_code', $company->ifsc_code) }}" placeholder="e.g. HDFC0001234">
                    </div>
                </div>
            </div>

            <!-- Section 4: Location & Address -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-location-dot text-primary"></i> Address & Location
                </h5>
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold text-dark">Country</label>
                        <select name="country" id="country" class="form-select">
                            <option value="">Select Country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ $company->country == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold text-dark">State / Province</label>
                        <select name="state" id="state" class="form-select">
                            <option value="">Select State</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}" {{ $company->state == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold text-dark">City</label>
                        <select name="city" id="city" class="form-select">
                            <option value="">Select City</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ $company->city == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold text-dark">Pin / ZIP Code</label>
                        <input type="text" class="form-control" name="pin"
                            value="{{ old('pin', $company->pin) }}" placeholder="Enter PIN code">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Registered Office Address</label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Enter complete building, street, and landmark details...">{{ old('address', $company->address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 5: Proprietor / Director Details -->
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-user-tie text-primary"></i> Proprietor / Authorized Signatory
                </h5>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">First Name</label>
                        <input type="text" name="first_name" class="form-control"
                            value="{{ isset($proprietor->first_name) ? $proprietor->first_name : '' }}" placeholder="Enter first name">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Last Name</label>
                        <input type="text" name="last_name" class="form-control"
                            value="{{ isset($proprietor->last_name) ? $proprietor->last_name : '' }}" placeholder="Enter last name">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Contact Mobile</label>
                        <input type="text" name="mobile" class="form-control"
                            value="{{ isset($proprietor->mobile) ? $proprietor->mobile : '' }}" placeholder="Enter mobile number">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-dark">Contact Email</label>
                        <input type="email" name="owner_email" class="form-control"
                            value="{{ isset($proprietor->email) ? $proprietor->email : '' }}" placeholder="Enter email address">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Residential Address</label>
                        <textarea class="form-control" name="current_address" rows="2" placeholder="Enter residential address">{{ isset($proprietor->current_address) ? $proprietor->current_address : '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    $('#country').change(function() {
        var countryId = $(this).val();
        if (countryId) {
            const stateurl = `{{ route('user.getStates', ':countryId') }}`.replace(':countryId', countryId);
            $.ajax({
                url: stateurl,
                type: 'GET',
                success: function(response) {
                    var stateOptions = '<option value="">Select State</option>';
                    $.each(response, function(index, state) {
                        stateOptions += '<option value="' + state.id + '">' + state.name + '</option>';
                    });
                    $('#state').html(stateOptions);
                }
            });
        } else {
            $('#state').html('<option value="">Select State</option>');
        }
    });

    $('#state').change(function() {
        var stateId = $(this).val();
        if (stateId) {
            const cityurl = `{{ route('user.getCities', ':stateId') }}`.replace(':stateId', stateId);
            $.ajax({
                url: cityurl,
                type: 'GET',
                success: function(response) {
                    var cityOptions = '<option value="">Select City</option>';
                    $.each(response, function(index, city) {
                        cityOptions += '<option value="' + city.id + '">' + city.name + '</option>';
                    });
                    $('#city').html(cityOptions);
                }
            });
        } else {
            $('#city').html('<option value="">Select City</option>');
        }
    });

    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById("logoPreview");
        const loader = document.getElementById("loader");

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            if (loader) loader.classList.remove("d-none");

            reader.onload = function(e) {
                setTimeout(() => {
                    preview.src = e.target.result;
                    if (loader) loader.classList.add("d-none");
                }, 500);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
