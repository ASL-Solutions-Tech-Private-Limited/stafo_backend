@extends('user.layouts.app')
@section('title', 'Edit Company Profile')

@section('content')
    <div class="card mt-4 p-3">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-between user-access">
            <div class="user-welcome">
                <h3>Edit Company Profile</h3>
            </div>
            <!-- <button class="btn primary-bg"><i class="fa-solid fa-plus"></i> Add New User</button> -->
        </div>
        <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="new-user-form mt-4 p-4">

                <div class="row">
                    <!-- Company Name -->
                    <div class="col-md-3 mb-3">
                        <label for="company_name" class="text-dark">Company Name</label>
                        <input type="text" name="company_name" id="company_name" class="form-control"
                            value="{{ old('company_name', $company->company_name) }}" placeholder="Enter Company Name">

                    </div>

                    <!-- Company phone -->
                    <div class="col-md-3 mb-3">
                        <label for="company_name" class="text-dark">Company Phone <span style="color: red">*</span> </label>
                        <input type="text" name="mobile_no" id="mobile_no" class="form-control"
                            value="{{ old('mobile_no', $company->mobile_no) }}" placeholder="Enter Phone">

                        @error('mobile_no')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Company email -->
                    <div class="col-md-3 mb-3">
                        <label for="company_type" class="text-dark">Company Email<span style="color: red">*</span></label>
                        <input type="text" name="email" id="email" class="form-control"
                            value="{{ old('email', $company->email) }}" placeholder="Enter Company Type">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- Company Type -->
                    <div class="col-md-3 mb-3">
                        <label for="company_type" class="text-dark">Company Type</label>
                        <input type="text" name="company_type" id="company_type" class="form-control"
                            value="{{ old('company_type', $company->company_type) }}" placeholder="Enter Company Type">

                    </div>

                    <!-- Registration Number -->
                    <div class="col-md-6 mb-3">
                        <label for="registration_number" class="text-dark">Registration Number</label>
                        <input type="text" name="registration_number" id="registration_number" class="form-control"
                            value="{{ old('registration_number', $company->registration_number) }}"
                            placeholder="Enter Registration Number">

                    </div>

                    <!-- GST Number -->
                    <div class="col-md-6 mb-3">
                        <label for="gst_number" class="text-dark">GST Number</label>
                        <input type="text" name="gst_number" id="gst_number" class="form-control"
                            value="{{ old('gst_number', $company->gst_number) }}" placeholder="Enter GST Number">

                    </div>

                    <!-- PAN Number -->
                    <div class="col-md-6 mb-3">
                        <label for="pan_number" class="text-dark">PAN Number</label>
                        <input type="text" name="pan_number" id="pan_number" class="form-control"
                            value="{{ old('pan_number', $company->pan_number) }}" placeholder="Enter PAN Number">

                    </div>

                    <!-- Address -->
                    <div class="col-md-6 mb-3">
                        <label for="address" class="text-dark">Address</label>
                        <textarea name="address" id="address" class="form-control" rows="2" placeholder="Enter Address">{{ old('address', $company->address) }}</textarea>

                    </div>
                </div>
                <div class="row">

                    <!-- Country -->
                    <div class="col-md-3 mb-3">
                        <label for="country" class="text-dark">Country</label>
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

                    <!-- State -->
                    <div class="col-md-3 mb-3">
                        <label for="state" class="text-dark">State</label>
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

                    <!-- City -->
                    <div class="col-md-3 mb-3">
                        <label for="city" class="text-dark">City</label>
                        <select name="city" id="city" class="form-control">
                            <option value="">Select City</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ $company->city == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <!-- Other form fields -->
                    <!-- PIN -->
                    <div class="col-md-3 mb-3">
                        <label for="pin" class="text-dark">PIN</label>
                        <input type="text" name="pin" id="pin" class="form-control"
                            value="{{ old('pin', $company->pin) }}" placeholder="Enter PIN">
                        @error('pin')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Bank Name -->
                    <div class="col-md-6 mb-3">
                        <label for="bank_name" class="text-dark">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control"
                            value="{{ old('bank_name', $company->bank_name) }}" placeholder="Enter Bank Name">
                        @error('bank_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Account Number -->
                    <div class="col-md-6 mb-3">
                        <label for="account_number" class="text-dark">Account Number</label>
                        <input type="text" name="account_number" id="account_number" class="form-control"
                            value="{{ old('account_number', $company->account_number) }}"
                            placeholder="Enter Account Number">
                        @error('account_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- IFSC Code -->
                    <div class="col-md-3 mb-3">
                        <label for="ifsc_code" class="text-dark">IFSC Code</label>
                        <input type="text" name="ifsc_code" id="ifsc_code" class="form-control"
                            value="{{ old('ifsc_code', $company->ifsc_code) }}" placeholder="Enter IFSC Code">
                        @error('ifsc_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- No of Employees -->
                    <div class="col-md-3 mb-3">
                        <label for="no_of_employee" class="text-dark">Number of Employees</label>
                        <input type="number" name="no_of_employee" id="no_of_employee" class="form-control"
                            value="{{ old('no_of_employee', $company->no_of_employee) }}"
                            placeholder="Enter Number of Employees">

                    </div>

                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="text-dark">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ old('status', $company->status) == '1' ? 'selected' : '' }}>
                                Active</option>
                            <option value="0" {{ old('status', $company->status) == '0' ? 'selected' : '' }}>
                                Inactive</option>
                        </select>

                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        {{-- <a href="{{ route('company.details.list') }}">
                            <button type="button" class="btn bg-secondary text-white">Cancel</button>
                        </a> --}}
                        <button class="btn btn-primary text-white" type="submit">Update Profile</button>
                    </div>
                </div>
            </div>
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
                const stateurl = `{{ route('getStates', ':countryId') }}`.replace(':countryId', countryId);
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
                const cityurl = `{{ route('getCities', ':stateId') }}`.replace(':stateId', stateId);
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
@endsection
