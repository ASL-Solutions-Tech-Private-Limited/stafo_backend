@extends('user.layouts.app')
@section('title', 'Edit Company Profile')
@section('css')

@endsection
@section('content')

    <div class="card mt-4 p-3 edit-page">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-between user-access">
            <div class="user-welcome">
                <h3>Edit Company Profile</h3>
            </div>
            <!-- <a href="{{ route('company.index') }}" class="btn btn-secondary btn-sm">Back to Company List</a> -->
        </div>
        <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-12 mb-3">


                    <div class="image-upload-container">
                        <!-- Display the current logo if exists -->
                        @if ($company->image_name)
                            <div class="logo-preview" id="logoPreviewContainer">
                                <img src="{{ asset('uploads/compnay_logo/' . $company->image_name) }}" alt="Current Logo"
                                    class="img-thumbnail" width="100px" height="100px">

                            </div>
                        @else
                            <div class="logo-preview" id="logoPreviewContainer">
                                {{-- <p class="text-muted">No logo uploaded</p> --}}
                                <img src="{{ asset('uploads/compnay_logo/no-image.png') }}" alt="Current Logo"
                                    class="img-thumbnail" width="100px" height="100px">
                            </div>
                        @endif

                        <!-- File input -->
                        <div class="input-group mt-3 d-flex gap-2 align-items-center">
                            <label class="form-label mb-0">Upload Company Logo</label>
                            <input type="file" class="form-control" id="companyLogo" name="companylogo" accept="image/*"
                                onchange="previewImage(event)">
                            <button class="input-group-text btn-md btn btn-success" for="companyLogo"><i
                                    class="fas fa-upload"></i></button>
                        </div>

                        <!-- Loader and Preview Image -->
                        <div id="loader" class="d-none text-center mt-3">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                        </div>
                        <img id="logoPreview" src="#" alt="Company Logo Preview" class="img-thumbnail mt-3 d-none"
                            width="100px" height="100px">
                    </div>
                </div>

            </div>

            <!-- company details -->
            <div class="row">
                <div class="col-md-12">
                    <h3>Company Details</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Company Name </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <input type="text" name="company_name" class="form-control"
                            value="{{ old('company_name', $company->company_name) }}" placeholder="Enter company name">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mobile No</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" name="mobile_no" class="form-control"
                            value="{{ old('mobile_no', $company->mobile_no) }}" placeholder="Enter mobile number" readonly>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $company->email) }}" placeholder="Enter email">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Company Type</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-industry"></i></span>
                        <select class="form-select" name="company_types">
                            <option value="">Select Company Type</option>
                            @foreach ($company_type as $company_types)
                                <option value="{{ $company_types->id }}"
                                    {{ old('company_types', $company->company_type) == $company_types->id ? 'selected' : '' }}>
                                    {{ $company_types->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Business Type</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                        <select class="form-select" name="business_name">
                            <option value="">Select Business Type</option>
                            @foreach ($business_type as $business_types)
                                <option value="{{ $business_types->id }}"
                                    {{ old('business_name', $company->business_type_id) == $business_types->id ? 'selected' : '' }}>
                                    {{ $business_types->business_name }}
                                </option>
                            @endforeach
                        </select>


                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">No. of Employees</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-users"></i></span>
                        <input type="text" name="no_of_employee" class="form-control"
                            value="{{ old('no_of_employee', $company->no_of_employee) }}"
                            placeholder="Enter number of employees">
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Bank Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-university"></i></span>
                        <input type="text" name="bank_name" class="form-control"
                            value="{{ old('bank_name', $company->bank_name) }}" placeholder="Enter bank name">
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Account Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                        <input type="text" name="account_number" class="form-control"
                            value="{{ old('account_number', $company->account_number) }}"
                            placeholder="Enter account number">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">IFSC Code</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-code"></i></span>
                        <input type="text" name="ifsc_code" class="form-control"
                            value="{{ old('ifsc_code', $company->ifsc_code) }}" placeholder="Enter IFSC code">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Country</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                        <select name="country" id="country" class="form-control">
                            <option value="">Select Country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ $company->country == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">State</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map"></i></span>
                        <select name="state" id="state" class="form-control">
                            <option value="">Select State</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}"
                                    {{ $company->state == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">City</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                        <select name="city" id="city" class="form-control">
                            <option value="">Select City</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ $company->city == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>


            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Pin Code</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                        <input type="text" class="form-control" name="pin"
                            value="{{ old('pin', $company->pin) }}" placeholder="Enter pin code">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Address</label>
                    <div class="input-group">
                        <span class="input-group-text d-flex align-items-start"><i
                                class="fas fa-map-marker-alt"></i></span>
                        <textarea class="form-control" name="address" rows="2" placeholder="Enter address">{{ old('address', $company->address) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <h3>Proprietor Details</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">First Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <input type="text" name="first_name" class="form-control"
                            value="{{ isset($proprietor->first_name) ? $proprietor->first_name : '' }}"
                            placeholder="Enter First name">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Last Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <input type="text" name="last_name" class="form-control"
                            value="{{ isset($proprietor->last_name) ? $proprietor->last_name : '' }}"
                            placeholder="Enter Last name">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mobile No</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" name="mobile" class="form-control"
                            value="{{ isset($proprietor->mobile) ? $proprietor->mobile : '' }}"
                            placeholder="Enter mobile number">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="owner_email" class="form-control"
                            value="{{ isset($proprietor->email) ? $proprietor->email : '' }}" placeholder="Enter email">
                    </div>
                </div>
            </div>


            <div class="row justify-content-between align-items-end">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Address></label>
                    <div class="input-group">
                        <span class="input-group-text d-flex align-items-start"><i
                                class="fas fa-map-marker-alt"></i></span>
                        <textarea class="form-control" name="current_address" rows="2" placeholder="Enter address">{{ isset($proprietor->current_address) ? $proprietor->current_address : '' }}</textarea>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                    </div>
                </div>
            </div>
            <!-- personal detaisl -->

        </form>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>




    <script>
        // When country is selected, load the states
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
                            stateOptions += '<option value="' + state.id + '">' + state.name +
                                '</option>';
                        });
                        $('#state').html(stateOptions); // Populate states dropdown
                    }
                });
            } else {
                $('#state').html('<option value="">Select State</option>'); // Reset states dropdown
            }
        });

        // When state is selected, load the cities
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
                            cityOptions += '<option value="' + city.id + '">' + city.name +
                                '</option>';
                        });
                        $('#city').html(cityOptions); // Populate cities dropdown
                    }
                });
            } else {
                $('#city').html('<option value="">Select City</option>'); // Reset cities dropdown
            }
        });
    </script>

    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById("logoPreview");
            const loader = document.getElementById("loader");

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                // Show loader, hide preview
                loader.classList.remove("d-none");
                preview.classList.add("d-none");

                reader.onload = function(e) {
                    setTimeout(() => { // Simulating loading time
                        preview.src = e.target.result;
                        loader.classList.add("d-none"); // Hide loader
                        preview.classList.remove("d-none"); // Show preview
                    }, 1000); // Adjust time as needed
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>




@endsection
