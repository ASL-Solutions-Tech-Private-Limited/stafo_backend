@extends('admin.layouts.layout')

@section('title', 'Employee edit')

@section('css')

@endsection
@section('content')


    <div class="card shadow-lg rounded p-2">
        <div class="container my-3">
            <div class="row mb-3">
                <div class="col-md-7">
                    <h3>Update Employee</h3>
                </div>
                <div class="col-md-5">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('employees.list') }}" class="btn btn-success">
                            Employee List
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-container">




                <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- Company & Image -->
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input class="form-control" type="file" name="image" accept="image/*">
                        @if ($employee->image)
                            <img src="{{ asset('uploads/employees/' . $employee->image) }}" alt="Profile Image"
                                class="img-thumbnail mt-2" width="100">
                        @endif
                        @error('image')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Personal Information -->
                    <div class="row">
                        <div class="col-md-4  mb-3">
                            <label class="form-label"><strong>Name</strong></label>
                            <input type="text" class="form-control" name="name" placeholder="Enter name"
                                value="{{ old('name', $employee->name) }}">
                        </div>
                        <div class="col-md-4  mb-3">
                            <label class="form-label"><strong>Email</strong></label>
                            <input type="email" class="form-control" name="email" placeholder="Enter email"
                                value="{{ old('email', $employee->email) }}">
                        </div>
                        <div class="col-md-4  mb-3">
                            <label class="form-label"><strong>Phone</strong></label>
                            <input type="text" class="form-control" name="phone" placeholder="Enter phone number"
                                value="{{ old('phone', $employee->phone) }}">
                        </div>
                    </div>

                    <div class="row ">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Date of Birth</strong></label>
                            <input type="date" class="form-control" name="date_of_birth"
                                value="{{ old('date_of_birth', $employee->date_of_birth) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Marital Status</strong></label>
                            <select class="form-select" name="marital_status">
                                <option value="" selected>Select Status</option>
                                <option value="Single"
                                    {{ old('marital_status', $employee->marital_status) == 'Single' ? 'selected' : '' }}>
                                    Single</option>
                                <option value="Married"
                                    {{ old('marital_status', $employee->marital_status) == 'Married' ? 'selected' : '' }}>
                                    Married</option>
                            </select>

                        </div>
                        {{-- {{ dd($employee) }} --}}
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Gender</strong></label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="male" value="Male"
                                    {{ old('gender', $employee->gender) == 'male' ? 'checked' : '' }}>
                                <label class="form-check-label" for="male">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="female" value="Female"
                                    {{ old('gender', $employee->gender) == 'female' ? 'checked' : '' }}>
                                <label class="form-check-label" for="female">Female</label>
                            </div>
                            <!-- Other option -->
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="other" value="Other"
                                    {{ old('gender', $employee->gender) == 'other' ? 'checked' : '' }}>
                                <label class="form-check-label" for="other">Other</label>
                            </div>

                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="row">
                        <div class="col-md-4  mb-3">
                            <label class="form-label"><strong>Position</strong></label>
                            <input type="text" class="form-control" name="position" placeholder="Enter position"
                                value="{{ old('position', $employee->position) }}">

                        </div>
                        <div class="col-md-4  mb-3">
                            <label class="form-label"><strong>Salary</strong></label>
                            <input type="text" class="form-control" name="salary" placeholder="Enter salary"
                                value="{{ old('salary', $employee->salary) }}">

                        </div>
                        <div class="col-md-4  mb-3">
                            <label class="form-label"><strong>Branch</strong></label>
                            <select class="form-select" name="branch_id">
                                <option value="" selected>Select Branch</option>
                                @foreach ($branches as $br)
                                    <option value="{{ $br->id }}"
                                        {{ old('branch_id', $employee->branch_id) == $br->id ? 'selected' : '' }}>
                                        {{ $br->branch_name }}</option>
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
                                        {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Shift</strong></label>
                            <select class="form-select" name="shift_id">
                                <option value="" selected>Select Shift</option>
                                @foreach ($shift as $s)
                                    <option value="{{ $s->id }}"
                                        {{ old('shift_id', $employee->shift_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->shift_name }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Job Role</strong></label>
                            <select class="form-select" name="job_title_id">
                                <option value="" selected>Select Job Role</option>
                                @foreach ($job_types as $job_type)
                                    <option value="{{ $job_type->id }}"
                                        {{ old('job_title_id', $employee->job_title_id) == $job_type->id ? 'selected' : '' }}>
                                        {{ $job_type->name }}</option>
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
                                        {{ old('employee_type_id', $employee->employee_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}</option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Date of Joining</strong></label>
                            <input type="date" class="form-control" name="date_of_joining"
                                value="{{ old('date_of_joining', $employee->date_of_joining) }}">

                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Country</strong></label>
                            <select class="form-select" id="country" name="country">
                                <option value="" selected>Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}"
                                        {{ old('country_id', $employee->country) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="row ">


                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>State</strong></label>
                            <select class="form-control" name="state" id="state">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}"
                                        {{ old('state', $employee->state) == $state->id ? 'selected' : '' }}
                                        class="state-option country-{{ $employee->country }}">
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>City</strong></label>
                            <select class="form-control" name="city" id="city">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}"
                                        {{ old('city', $employee->city) == $city->id ? 'selected' : '' }}
                                        class="city-option state-{{ $employee->state }}">
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label"><strong>Pin Code</strong></label>
                            <input type="text" class="form-control @error('pin') is-invalid @enderror" name="pin"
                                placeholder="Enter PIN" value="{{ old('pin', $employee->pin) }}">
                            @error('pin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <!-- Official Details -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><strong>Official Email</strong></label>
                            <input type="email" class="form-control" name="official_email_id"
                                placeholder="Enter official email"
                                value="{{ old('official_email_id', $employee->official_email_id) }}">

                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><strong>ESI Number</strong></label>
                            <input type="text" class="form-control" name="esi_number" placeholder="Enter ESI number"
                                value="{{ old('esi_number', $employee->esi_number) }}">

                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><strong>PF Number</strong></label>
                            <input type="text" class="form-control" name="pf_number" placeholder="Enter PF number"
                                value="{{ old('pf_number', $employee->pf_number) }}">

                        </div>
                    </div>

                    <!-- Leave Details -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><strong>Privileged Leave</strong></label>
                            <input type="text" class="form-control @error('privileged_leave') is-invalid @enderror"
                                name="privileged_leave"
                                value="{{ old('privileged_leave', $employee->privileged_leave) }}">
                            @error('privileged_leave')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><strong>Sick Leave</strong></label>
                            <input type="text" class="form-control  @error('sick_leave') is-invalid @enderror"
                                name="sick_leave" value="{{ old('sick_leave', $employee->sick_leave) }}">
                            @error('sick_leave')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><strong>Casual Leave</strong></label>
                            <input type="text" class="form-control @error('casual_leave') is-invalid @enderror"
                                name="casual_leave" value="{{ old('casual_leave', $employee->casual_leave) }}">
                            @error('casual_leave')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label"><strong>Address</strong></label>
                            <textarea class="form-control" rows="2" name="address" placeholder="Enter address">{{ old('address', $employee->address) }}</textarea>

                        </div>
                    </div>
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection


@section('scripts')
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
