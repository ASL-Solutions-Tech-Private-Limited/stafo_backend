@extends('admin.layouts.layout')
@section('title', 'Company Add')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            
            <div class="d-flex justify-content-between user-access">
                <div class="user-welcome">
                    <h3>Add Company Details</h3>
                </div>
                <!-- <button class="btn primary-bg"><i class="fa-solid fa-plus"></i> Add New User</button> -->
            </div>
            <form action="{{ route('company.details.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="new-user-form  py-3">
                    <div class="row">

                         <!-- Company Type -->
                         <div class="col-md-4 mb-3">
                            <label for="company_type" class="text-dark">Company Type</label>
                            
                            <select name="company_type" id="company_type" class="form-control">
                                <option value="">Select Company Type</option>
                                @foreach ($companytypes as $companytype)
                                    <option value="{{ $companytype->id }}"
                                        {{ old('company_type') == $companytype->id ? 'selected' : '' }}>
                                        {{ $companytype->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="business_type_id" class="text-dark">Business Type</label>
                            
                            <select name="business_type_id" id="business_type_id" class="form-control">
                                <option value="">Select Business Type</option>
                                @foreach ($business_types as $business_type)
                                    <option value="{{ $business_type->id }}"
                                        {{ old('business_type_id') == $business_type->id ? 'selected' : '' }}>
                                        {{ $business_type->business_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('business_type_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Company Name -->
                        <div class="col-md-4 mb-3">
                            <label for="company_name" class="text-dark">Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control"
                                value="{{ old('company_name') }}" placeholder="Enter Company Name">
                            @error('company_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                       <div class="col-md-4 mb-3">
                            <label for="mobile_no" class="text-dark">Mobile Number</label>
                            <input type="text" name="mobile_no" id="mobile_no" class="form-control"
                                value="{{ old('mobile_no') }}" placeholder="Enter Company Mobile Number">
                            @error('mobile_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email" class="text-dark">Email</label>
                            <input type="text" name="email" id="email" class="form-control"
                                value="{{ old('email') }}" placeholder="Enter Company Email">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Registration Number -->
                        <div class="col-md-4 mb-3">
                            <label for="registration_number" class="text-dark">Registration Number</label>
                            <input type="text" name="registration_number" id="registration_number" class="form-control"
                                value="{{ old('registration_number') }}" placeholder="Enter Registration Number">
                            @error('registration_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- GST Number -->
                        <div class="col-md-4 mb-3">
                            <label for="gst_number" class="text-dark">GST Number</label>
                            <input type="text" name="gst_number" id="gst_number" class="form-control"
                                value="{{ old('gst_number') }}" placeholder="Enter GST Number">
                            @error('gst_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- PAN Number -->
                        <div class="col-md-4 mb-3">
                            <label for="pan_number" class="text-dark">PAN Number</label>
                            <input type="text" name="pan_number" id="pan_number" class="form-control"
                                value="{{ old('pan_number') }}" placeholder="Enter PAN Number">
                            @error('pan_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="col-md-12 mb-3">
                            <label for="address" class="text-dark">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="2" placeholder="Enter Address">{{ old('address') }}</textarea>
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div class="col-md-4 mb-3">
                            <label for="country" class="text-dark">Country</label>
                            <select name="country" id="country" class="form-control">
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ old('country') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- State -->
                        <div class="col-md-4 mb-3">
                            <label for="state" class="text-dark">State</label>
                            <select name="state" id="state" class="form-control" disabled>
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}"
                                        {{ old('state') == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('state')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="col-md-4 mb-3">
                            <label for="city" class="text-dark">City</label>
                            <select name="city" id="city" class="form-control" disabled>
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('city')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- PIN -->
                        <div class="col-md-4 mb-3">
                            <label for="pin" class="text-dark">PIN</label>
                            <input type="text" name="pin" id="pin" class="form-control"
                                value="{{ old('pin') }}" placeholder="Enter PIN">
                            @error('pin')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Bank Name -->
                        <div class="col-md-4 mb-3">
                            <label for="bank_name" class="text-dark">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control"
                                value="{{ old('bank_name') }}" placeholder="Enter Bank Name">
                            @error('bank_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Account Number -->
                        <div class="col-md-4 mb-3">
                            <label for="account_number" class="text-dark">Account Number</label>
                            <input type="text" name="account_number" id="account_number" class="form-control"
                                value="{{ old('account_number') }}" placeholder="Enter Account Number">
                            @error('account_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- IFSC Code -->
                        <div class="col-md-4 mb-3">
                            <label for="ifsc_code" class="text-dark">IFSC Code</label>
                            <input type="text" name="ifsc_code" id="ifsc_code" class="form-control"
                                value="{{ old('ifsc_code') }}" placeholder="Enter IFSC Code">
                            @error('ifsc_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- No of Employees -->
                        <div class="col-md-4 mb-3">
                            <label for="no_of_employee" class="text-dark">Number of Employees<span class="red">*</span></label>
                            <input type="number" name="no_of_employee" id="no_of_employee" class="form-control"
                                value="{{ old('no_of_employee') }}" placeholder="Enter Number of Employees">
                            @error('no_of_employee')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-4 mb-3">
                            <label for="status" class="text-dark">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('proprietor.list') }}">
                                <button type="button" class="btn bg-danger text-white">Cancel</button>
                            </a>
                            <button class="btn btn-primary text-white" type="submit">Save</button>
                        </div>
                    </div>
                </div>
            </form>
           
        </div>
    </div>
@endsection
@section('scripts')

    <script>
        $(document).ready(function() {
            $('#country').on('change', function() {
                const countryId = $(this).val();

                // Reset State and City Selects
                $('#state').html('<option value="">Select State</option>').prop('disabled', true);
                $('#city').html('<option value="">Select City</option>').prop('disabled', true);

                if (countryId) {
                    // Using Blade route() helper and JavaScript to replace countryId dynamically
                    const url = `{{ route('getStates', ':countryId') }}`.replace(':countryId', countryId);

                    $.ajax({
                        url: url, // Use the URL with the dynamic countryId
                        type: 'GET',
                        success: function(states) {
                            if (states.length > 0) {
                                $('#state').prop('disabled', false);
                                $.each(states, function(key, state) {
                                    $('#state').append(
                                        `<option value="${state.id}">${state.name}</option>`
                                    );
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching states:', error);
                        },
                    });
                }
            });

            $('#state').on('change', function() {
                const stateId = $(this).val();

                // Reset City Select
                $('#city').html('<option value="">Select City</option>').prop('disabled', true);

                if (stateId) {
                    // Using Blade route() helper and JavaScript to replace stateId dynamically
                    const url = `{{ route('getCities', ':stateId') }}`.replace(':stateId', stateId);

                    $.ajax({
                        url: url, // Use the URL with the dynamic stateId
                        type: 'GET',
                        success: function(cities) {
                            if (cities.length > 0) {
                                $('#city').prop('disabled', false);
                                $.each(cities, function(key, city) {
                                    $('#city').append(
                                        `<option value="${city.id}">${city.name}</option>`
                                    );
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching cities:', error);
                        },
                    });
                }
            });
        });
    </script>

@endsection
