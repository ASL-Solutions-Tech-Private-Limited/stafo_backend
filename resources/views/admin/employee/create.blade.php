@extends('admin.layouts.layout')

@section('title', 'Eployeee add')

@section('css')

@endsection
@section('content')


    <div class="card shadow-lg rounded p-2">
        <div class="container my-3">
            <div class="row mb-2">
                <div class="col-md-7">
                    <h3>Add Employee</h3>
                </div>
                <div class="col-md-5">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('employees.list') }}" class="btn btn-primary">
                            Employee List
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-container">




                <form action="{{ route('employees.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <!-- Company & Image -->
                    <div class="row ">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label"><strong>Company</strong></label>
                            <select class="form-select @error('company_id') is-invalid @enderror" name="company_id">
                                <option value="" selected>Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6  mb-md-0">
                            <label class="form-label"><strong>Profile Image</strong></label>
                            <input type="file" name="image" class="form-control" id="profileImageInput"
                                accept="image/*" onchange="uploadImage(event)">

                            <div class="mt-2 d-flex align-items-center">
                                <div id="uploadLoader" class="spinner-border text-primary d-none" role="status"></div>
                                <i id="uploadSuccess" class="fas fa-check-circle text-success d-none fs-3 ms-2"
                                    onclick="toggleImagePreview()" title="Click to Show/Hide Preview"></i>
                                <span id="clickToPreviewText" class="text-success d-none ms-2" style="cursor: pointer;"
                                    onclick="toggleImagePreview()">Click to Preview</span>
                            </div>
                            <div class="mt-2">
                                <img id="profileImagePreview" src="" class="img-thumbnail d-none" width="100">
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="row ">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Name</strong></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                placeholder="Enter name" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Email</strong></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                placeholder="Enter email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Phone</strong></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                                placeholder="Enter phone number" value="{{ old('phone') }}"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                maxlength="10">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Date of Birth</strong></label>
                            <input type="date" class="form-control" name="date_of_birth"
                                value="{{ old('date_of_birth') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Marital Status</strong></label>
                            <select class="form-select" name="marital_status">
                                <option value="" selected>Select Status</option>
                                <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single
                                </option>
                                <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Gender</strong></label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="male" value="Male"
                                    {{ old('gender') == 'Male' ? 'checked' : '' }}>
                                <label class="form-check-label" for="male">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="female"
                                    value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }}>
                                <label class="form-check-label" for="female">Female</label>
                            </div>
                            <!-- Other option -->
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="other"
                                    value="Other" {{ old('gender') == 'Other' ? 'checked' : '' }}>
                                <label class="form-check-label" for="other">Other</label>
                            </div>

                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="row ">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Position</strong></label>
                            <input type="text" class="form-control" name="position" placeholder="Enter position"
                                value="{{ old('position') }}">

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Salary</strong></label>
                            <input type="text" class="form-control" name="salary" placeholder="Enter salary"
                                value="{{ old('salary') }}">

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Branch</strong></label>
                            <select class="form-select" name="branch_id">
                                <option value="" selected>Select Branch</option>
                                @foreach ($branch as $br)
                                    <option value="{{ $br->id }}"
                                        {{ old('branch_id') == $br->id ? 'selected' : '' }}>{{ $br->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Department</strong></label>
                            <select class="form-select" name="department_id">
                                <option value="" selected>Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Shift</strong></label>
                            <select class="form-select @error('shift_id') is-invalid @enderror" name="shift_id">
                                <option value="" selected>Select Shift</option>
                                @foreach ($shift as $s)
                                    <option value="{{ $s->id }}"
                                        {{ old('shift_id') == $s->id ? 'selected' : '' }}>{{ $s->shift_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Job Role</strong></label>
                            <select class="form-select" name="job_title_id">
                                <option value="" selected>Select Job Role</option>
                                @foreach ($job_types as $job_type)
                                    <option value="{{ $job_type->id }}"
                                        {{ old('job_title_id') == $job_type->id ? 'selected' : '' }}>{{ $job_type->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Employee Type</strong></label>
                            <select class="form-select" name="employee_type_id">
                                <option value="" selected>Select Employee Type</option>
                                @foreach ($employee_types as $type)
                                    <option value="{{ $type->id }}"
                                        {{ old('employee_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Date of Joining</strong></label>
                            <input type="date" class="form-control" name="date_of_joining"
                                value="{{ old('date_of_joining') }}">

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Country</strong></label>
                            <select class="form-select" id="country" name="country_id">
                                <option value="" selected>Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="row ">


                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>State</strong></label>
                            <select class="form-select" id="state" name="state_id">
                                <option value="" selected>Select State</option>
                            </select>

                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>City</strong></label>
                            <select class="form-select" id="city" name="city_id">
                                <option value="" selected>Select City</option>
                            </select>

                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Pin Code</strong></label>
                            <input type="text" class="form-control @error('pin') is-invalid @enderror" name="pin"
                                placeholder="Enter PIN" value="{{ old('pin') }}">
                            @error('pin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>




                    <div class="mb-3">
                        <label class="form-label"><strong>Address</strong></label>
                        <textarea class="form-control" rows="2" name="address" placeholder="Enter address">{{ old('address') }}</textarea>

                    </div>

                    <!-- Official Details -->
                    <div class="row ">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Official Email</strong></label>
                            <input type="email" class="form-control" name="official_email_id"
                                placeholder="Enter official email" value="{{ old('official_email_id') }}">

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>ESI Number</strong></label>
                            <input type="text" class="form-control" name="esi_number" placeholder="Enter ESI number"
                                value="{{ old('esi_number') }}">

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>PF Number</strong></label>
                            <input type="text" class="form-control @error('pf_number') is-invalid @enderror"
                                name="pf_number" placeholder="Enter PF number" value="{{ old('pf_number') }}">

                        </div>
                    </div>

                    <!-- Leave Details -->
                    <div class="row ">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Privileged Leave</strong></label>
                            <input type="text" class="form-control @error('privileged_leave') is-invalid @enderror"
                                name="privileged_leave" value="{{ old('privileged_leave') }}">
                            @error('privileged_leave')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Sick Leave</strong></label>
                            <input type="text" class="form-control @error('sick_leave') is-invalid @enderror"
                                name="sick_leave" value="{{ old('sick_leave') }}">
                            @error('sick_leave')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Casual Leave</strong></label>
                            <input type="text" class="form-control @error('casual_leave') is-invalid @enderror"
                                name="casual_leave" value="{{ old('casual_leave') }}">
                            @error('casual_leave')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-success"> Save</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>

@endsection


@section('scripts')
    <script>
        function uploadImage(event) {
            const fileInput = event.target;
            const uploadLoader = document.getElementById('uploadLoader');
            const uploadSuccess = document.getElementById('uploadSuccess');
            const clickToPreviewText = document.getElementById('clickToPreviewText');
            const imagePreview = document.getElementById('profileImagePreview');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                // Show loader, hide success icon, preview image & text
                uploadLoader.classList.remove("d-none");
                uploadSuccess.classList.add("d-none");
                clickToPreviewText.classList.add("d-none");
                imagePreview.classList.add("d-none");

                reader.onload = function(e) {
                    setTimeout(() => { // Simulate upload process
                        uploadLoader.classList.add("d-none"); // Hide loader
                        uploadSuccess.classList.remove("d-none"); // Show success icon
                        clickToPreviewText.classList.remove("d-none"); // Show "Click to Preview"
                        imagePreview.src = e.target.result; // Set image source (but still hidden)
                    }, 2000);
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }

        // Function to toggle the image preview when clicking the checkmark or text
        function toggleImagePreview() {
            const imagePreview = document.getElementById('profileImagePreview');

            if (imagePreview.classList.contains("d-none")) {
                imagePreview.classList.remove("d-none"); // Show image preview
            } else {
                imagePreview.classList.add("d-none"); // Hide image preview
            }
        }
    </script>


    <script>
        $(document).ready(function() {
            // On change of country, load the states
            $('#country').on('change', function() {
                const countryId = $(this).val();

                // Reset State and City Selects
                $('#state').html('<option value="">Select State</option>').prop('disabled', true);
                $('#city').html('<option value="">Select City</option>').prop('disabled', true);

                if (countryId) {
                    // Use Blade to generate the URL for fetching states
                    const url = `{{ route('getStates', ':countryId') }}`.replace(':countryId', countryId);

                    $.ajax({
                        url: url,
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

            // On change of state, load the cities
            $('#state').on('change', function() {
                const stateId = $(this).val();

                // Reset City Select
                $('#city').html('<option value="">Select City</option>').prop('disabled', true);

                if (stateId) {
                    // Use Blade to generate the URL for fetching cities
                    const url = `{{ route('getCities', ':stateId') }}`.replace(':stateId', stateId);

                    $.ajax({
                        url: url,
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
