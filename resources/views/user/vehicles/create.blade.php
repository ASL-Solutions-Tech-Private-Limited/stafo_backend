@extends('user.layouts.app')

@section('title', 'Add Vehicle')

@section('content')
    <div class="card mt-4 p-3">
        <div class="container">

            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="fw-bold">Add Vehicle</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('vehicles.index') }}" class="btn btn-primary float-end">Vehicle List</a>
                </div>
            </div>

            <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-end">

                    <!-- Vehicle No -->
                    <div class="col-md-6 mb-3">
                        <label for="vehicle_no">
                            <i class="fa-solid fa-id-card"></i> Vehicle No
                        </label>
                        <input type="text" name="vehicle_no" class="form-control" placeholder="Enter vehicle number" value="{{ old('vehicle_no') }}">
                        @error('vehicle_no')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vehicle Type -->
                    <div class="col-md-6 mb-3">
                        <label for="vehicle_type">
                            <i class="fa-solid fa-truck"></i> Vehicle Type
                        </label>
                        <select name="vehicle_type" class="form-control">
                            <option value="">-- Select Vehicle Type --</option>
                            <option value="Bus" {{ old('vehicle_type') == 'Bus' ? 'selected' : '' }}>Bus</option>
                            <option value="Mini Bus" {{ old('vehicle_type') == 'Mini Bus' ? 'selected' : '' }}>Mini Bus</option>
                            <option value="Coach" {{ old('vehicle_type') == 'Coach' ? 'selected' : '' }}>Coach</option>
                            <option value="Van" {{ old('vehicle_type') == 'Van' ? 'selected' : '' }}>Van</option>
                            <option value="Car" {{ old('vehicle_type') == 'Car' ? 'selected' : '' }}>Car</option>
                            <option value="Jeep" {{ old('vehicle_type') == 'Jeep' ? 'selected' : '' }}>Jeep</option>
                            <option value="Taxi" {{ old('vehicle_type') == 'Taxi' ? 'selected' : '' }}>Taxi</option>
                        </select>
                    </div>

                    

                     <!-- Fuel Type -->
                    <div class="col-md-6 mb-3">
                        <label for="fuel">
                            <i class="fa-solid fa-gas-pump"></i> Fuel Type
                        </label>
                        <select name="fuel" class="form-control">
                            <option value="">-- Select Fuel Type --</option>
                            <option value="Petrol" {{ old('fuel') == 'Petrol' ? 'selected' : '' }}>Petrol</option>
                            <option value="Diesel" {{ old('fuel') == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="CNG" {{ old('fuel') == 'CNG' ? 'selected' : '' }}>CNG</option>
                            <option value="Electric" {{ old('fuel') == 'Electric' ? 'selected' : '' }}>Electric</option>
                        </select>
                        @error('fuel')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>


                    <!-- Load Capacity -->
                    <div class="col-md-6 mb-3">
                        <label for="load_capacity">
                            <i class="fa-solid fa-box"></i> Load Capacity
                        </label>
                        <input type="number" step="0.01" name="load_capacity" class="form-control" placeholder="Load capacity (tons/kg)" value="{{ old('load_capacity') }}">
                    </div>

                    <!-- Speedometer -->
                    <div class="col-md-6 mb-3">
                        <label for="speedometer">
                            <i class="fa-solid fa-tachometer-alt"></i> Speedometer (KM)
                        </label>
                        <input type="number" name="speedometer" class="form-control" placeholder="Speedometer reading in KM" value="{{ old('speedometer', 0) }}">
                    </div>

                    <!-- RC Upload File -->
                    <div class="col-md-6 mb-3">
                        <label for="rc_upload_path">
                            <i class="fa-solid fa-file-arrow-up"></i> Upload RC File
                        </label>
                        <input type="file" name="rc_upload_path" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        @error('rc_upload_path')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>


                    <!-- Status -->
                    <div class="col-md-6 mb-3">
                        <label for="status">
                            <i class="fa-solid fa-info-circle"></i> Status
                        </label>
                        <select name="status" class="form-control">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>

                    <!-- KM Travelled -->
                    <div class="col-md-6 mb-3">
                        <label for="km_travelled">
                            <i class="fa-solid fa-road"></i> KM Travelled
                        </label>
                        <input type="number" name="km_travelled" class="form-control" placeholder="Total kilometers travelled" value="{{ old('km_travelled', 0) }}">
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-6 mb-3 text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-check"></i> Save
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection
