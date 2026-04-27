@extends('admin.layouts.layout')
@section('title', 'Proprietors Edit')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            <div class="d-flex justify-content-between user-access">
                <div class="user-welcome">
                    <h3>Edit Proprietor</h3>
                </div>
                <!-- <button class="btn primary-bg"><i class="fa-solid fa-plus"></i> Add New User</button> -->
            </div>
            <form action="{{ route('proprietor.update', $proprietor->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="new-user-form mt-4 p-4">
                    <div class="row">
                        <!-- First Name -->
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="text-dark">First Name<span>*</span></label>
                            <input type="text" name="first_name" id="first_name" class="form-control"
                                value="{{ old('first_name', $proprietor->first_name) }}" placeholder="Enter First Name">
                            @error('first_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="text-dark">Last Name<span>*</span></label>
                            <input type="text" name="last_name" id="last_name" class="form-control"
                                value="{{ old('last_name', $proprietor->last_name) }}" placeholder="Enter Last Name">
                            @error('last_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Mobile -->
                        <div class="col-md-6 mb-3">
                            <label for="mobile" class="text-dark">Mobile<span>*</span></label>
                            <input type="text" name="mobile_no" id="mobile" class="form-control"
                                value="{{ old('mobile', $proprietor->mobile_no) }}" placeholder="Enter Mobile">
                            @error('mobile_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="text-dark">Email<span>*</span></label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="{{ old('email', $proprietor->email) }}" placeholder="Enter Email">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Aadhar -->
                        <div class="col-md-6 mb-3">
                            <label for="aadhar" class="text-dark">Aadhar<strong>*</strong></label>
                            <input type="text" name="aadhar" id="aadhar" class="form-control"
                                value="{{ old('aadhar', $proprietor->aadhar) }}" placeholder="Enter Aadhar">
                            @error('aadhar')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Pan -->
                        <div class="col-md-6 mb-3">
                            <label for="pan" class="text-dark">PAN<strong>*</strong></label>
                            <input type="text" name="pan" id="pan" class="form-control"
                                value="{{ old('pan', $proprietor->pan) }}" placeholder="Enter PAN">
                            @error('pan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Current Address -->
                        <div class="col-md-12 mb-3">
                            <label for="current_address" class="text-dark">Current Address<strong>*</strong></label>
                            <textarea name="current_address" id="current_address" class="form-control" rows="2"
                                placeholder="Enter Current Address">{{ old('current_address', $proprietor->current_address) }}</textarea>
                            @error('current_address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Current Country -->
                        <div class="col-md-3 mb-3">
                            <label for="current_country" class="text-dark">Current Country<strong>*</strong></label>
                            <select name="current_country" id="current_country" class="form-control">
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ old('current_country', $proprietor->current_country) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('current_country')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Current State -->
                        <div class="col-md-3 mb-3">
                            <label for="current_state" class="text-dark">Current State<strong>*</strong></label>
                            <select name="current_state" id="current_state" class="form-control">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}"
                                        {{ old('current_state', $proprietor->current_state) == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('current_state')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Current City -->
                        <div class="col-md-3 mb-3">
                            <label for="current_city" class="text-dark">Current City<strong>*</strong></label>
                            <select name="current_city" id="current_city" class="form-control">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}"
                                        {{ old('current_city', $proprietor->current_city) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('current_city')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Current Pin -->
                        <div class="col-md-3 mb-3">
                            <label for="current_pin" class="text-dark">Current Pin<strong>*</strong></label>
                            <input type="text" name="current_pin" id="current_pin" class="form-control"
                                value="{{ old('current_pin', $proprietor->current_pin) }}"
                                placeholder="Enter Current Pin">
                            @error('current_pin')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Permanent Address -->
                        <div class="col-md-12 mb-3">
                            <label for="p_address" class="text-dark">Permanent Address<strong>*</strong></label>
                            <textarea name="p_address" id="p_address" class="form-control" rows="2"
                                placeholder="Enter Permanent Address">{{ old('p_address', $proprietor->p_address) }}</textarea>
                            @error('p_address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Permanent Country -->
                        <div class="col-md-4 mb-3">
                            <label for="p_country" class="text-dark">Permanent Country<strong>*</strong></label>
                            <select name="p_country" id="p_country" class="form-control">
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ old('p_country', $proprietor->p_country) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('p_country')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Permanent State -->
                        <div class="col-md-4 mb-3">
                            <label for="p_state" class="text-dark">Permanent State<strong>*</strong></label>
                            <select name="p_state" id="p_state" class="form-control">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}"
                                        {{ old('p_state', $proprietor->p_state) == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('p_state')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Permanent City -->
                        <div class="col-md-4 mb-3">
                            <label for="p_city" class="text-dark">Permanent City<strong>*</strong></label>
                            <select name="p_city" id="p_city" class="form-control">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}"
                                        {{ old('p_city', $proprietor->p_city) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('p_city')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Permanent Pin -->
                        <div class="col-md-6 mb-3">
                            <label for="p_pin" class="text-dark">Permanent Pin<strong>*</strong></label>
                            <input type="text" name="p_pin" id="p_pin" class="form-control"
                                value="{{ old('p_pin', $proprietor->p_pin) }}" placeholder="Enter Permanent Pin">
                            @error('p_pin')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="text-dark">Status<span>*</span></label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" {{ old('status', $proprietor->status) == '1' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="0" {{ old('status', $proprietor->status) == '0' ? 'selected' : '' }}>
                                    Inactive</option>
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('proprietor.list') }}">
                                <button type="button" class="btn bg-secondary text-white">Cancel</button>
                            </a>
                            <button class="btn btn-primary text-white" type="submit">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


    </div>





@endsection
@section('scripts')
    <script>
        function copyAddress() {
            if (document.getElementById("same_as_current").checked) {
                document.getElementById("p_address").value = document.getElementById("current_address").value;
                document.getElementById("p_city").value = document.getElementById("current_city").value;
                document.getElementById("p_state").value = document.getElementById("current_state").value;
                document.getElementById("p_country").value = document.getElementById("current_country").value;
                document.getElementById("p_pin").value = document.getElementById("current_pin").value;
            } else {
                document.getElementById("p_address").value = '';
                document.getElementById("p_city").value = '';
                document.getElementById("p_state").value = '';
                document.getElementById("p_country").value = '';
                document.getElementById("p_pin").value = '';
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#current_country').on('change', function() {
                const countryId = $(this).val();

                // Reset State and City Selects
                $('#current_state').html('<option value="">Select State</option>').prop('disabled', true);
                $('#current_city').html('<option value="">Select City</option>').prop('disabled', true);

                if (countryId) {
                    const url = `{{ route('getStates', ':countryId') }}`.replace(':countryId', countryId);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(states) {
                            if (states.length > 0) {
                                $('#current_state').prop('disabled', false);
                                $.each(states, function(key, state) {
                                    $('#current_state').append(
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

            $('#current_state').on('change', function() {
                const stateId = $(this).val();

                // Reset City Select
                $('#current_city').html('<option value="">Select City</option>').prop('disabled', true);

                if (stateId) {
                    const url = `{{ route('getCities', ':stateId') }}`.replace(':stateId', stateId);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(cities) {
                            if (cities.length > 0) {
                                $('#current_city').prop('disabled', false);
                                $.each(cities, function(key, city) {
                                    $('#current_city').append(
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
    <script>
        $(document).ready(function() {
            $('#p_country').on('change', function() {
                const pcountryId = $(this).val();
                console.log(pcountryId)
                // Reset State and City Selects
                $('#p_state').html('<option value="">Select State</option>').prop('disabled', true);
                $('#p_city').html('<option value="">Select City</option>').prop('disabled', true);

                if (pcountryId) {
                    $.ajax({
                        url: `/admin/states/${pcountryId}`,
                        type: 'GET',
                        success: function(states) {
                            if (states.length > 0) {
                                $('#p_state').prop('disabled', false);
                                $.each(states, function(key, state) {
                                    $('#p_state').append(
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

            $('#p_state').on('change', function() {
                const pstateId = $(this).val();

                // Reset City Select
                $('#p_city').html('<option value="">Select City</option>').prop('disabled', true);

                if (pstateId) {
                    $.ajax({
                        url: `/admin/cities/${pstateId}`,
                        type: 'GET',
                        success: function(cities) {
                            if (cities.length > 0) {
                                $('#p_city').prop('disabled', false);
                                $.each(cities, function(key, city) {
                                    $('#p_city').append(
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
