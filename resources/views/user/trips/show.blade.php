@extends('user.layouts.app')
@section('title', 'Trip Details')

@section('content')
<div class="container mt-4">
    <div class="card shadow border-0 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>Trip Details</h3>
            <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <!-- Trip Summary -->
        <div class="row g-4">
            <!-- Trip Info -->
            <div class="col-md-6">
                <div class="bg-light p-4 rounded shadow-sm h-100">
                    <h5 class="text-success"><i class="fas fa-truck-moving me-2"></i>Trip Info</h5>
                    <p><strong>Title:</strong> {{ $trip->title }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge bg-{{ $trip->status === 'completed' ? 'success' : ($trip->status === 'cancelled' ? 'danger' : 'warning') }}">
                            {{ ucfirst($trip->status) }}
                        </span>
                    </p>
                    <p><strong>Notes:</strong><br> {!! nl2br(e($trip->notes)) !!}</p>
                </div>
            </div>

            <!-- Locations -->
    <div class="col-md-6">
        <div class="bg-light p-4 rounded shadow-sm h-100">
        <h5 class="text-warning"><i class="fas fa-map-marked-alt me-2"></i>Locations</h5>
        
       
        <p><strong>From Address:</strong> {{ $trip->from_address ?? 'N/A' }}</p>
        <hr>

       
        <p><strong>To Address:</strong> {{ $trip->to_address ?? 'N/A' }}</p>
    </div>
      </div>



            <!-- Timing -->
            <div class="col-md-6">
                <div class="bg-light p-4 rounded shadow-sm h-100">
                    <h5 class="text-info"><i class="fas fa-clock me-2"></i>Timing</h5>
                    <p><strong>Start Time:</strong> {{ \Carbon\Carbon::parse($trip->start_time)->format('d M Y, h:i A') }}</p>
                    <p><strong>End Time:</strong> {{ \Carbon\Carbon::parse($trip->end_time)->format('d M Y, h:i A') }}</p>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="col-md-6">
                <div class="bg-light p-4 rounded shadow-sm h-100">
                    <h5 class="text-primary"><i class="fas fa-user-circle me-2"></i>Customer Info</h5>
                    @if($trip->customerInfo)
                        <p><strong>Name:</strong> {{ $trip->customerInfo->customer_name }}</p>
                        <p><strong>Email:</strong> {{ $trip->customerInfo->email }}</p>
                        <p><strong>Phone:</strong> {{ $trip->customerInfo->phone }}</p>
                        <p><strong>Address:</strong> {{ $trip->customerInfo->address }}</p>
                    @else
                        <p class="text-muted">No customer data available.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Vehicle & Driver Assignment -->
        <div class="mt-5">
            <h5 class="text-dark mb-3 border-bottom pb-2">
                <i class="fas fa-tools me-2 text-success"></i>Assign Vehicle & Driver
            </h5>

            <form action="{{ route('trips.assign', $trip->id) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')
            <!-- Vehicle Dropdown -->
            <div class="col-md-6">
                <label for="vehicle" class="form-label">Vehicle</label>
                <select id="vehicle" name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" onchange="checkVehicleAvailability()">
                    <option value="">-- Select Vehicle --</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $trip->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                            Seater {{ (int) $vehicle->load_capacity }} - {{ $vehicle->vehicle_type }} - [{{ $vehicle->vehicle_no }}]
                        </option>
                    @endforeach
                </select>
                <div id="vehicleAvailability" class="mt-1 text-sm"></div>
                @error('vehicle_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

<!-- Driver Dropdown -->
<div class="col-md-6">
    <label for="employee" class="form-label">Driver</label>
    <select id="employee" name="driver_id" class="form-select @error('driver_id') is-invalid @enderror" onchange="checkDriverAvailability()">
        <option value="">-- Select Driver --</option>
        @foreach($employees as $employee)
            <option value="{{ $employee->id }}" {{ old('driver_id', $trip->driver_id) == $employee->id ? 'selected' : '' }}>
                {{ $employee->name }} - {{ $employee->emp_id }}
            </option>
        @endforeach
    </select>
    <div id="driverAvailability" class="mt-1 text-sm"></div>
    @error('driver_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

             
                <!-- Submit -->
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="assignBtn" disabled>
                        <i class="fas fa-check-circle me-1"></i> Assign Now
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@section('js')

{{-- <script>
    function checkVehicleAvailability() {
        const vehicleId = document.getElementById('vehicle').value;
        const tripId = {{ $trip->id }};

        if (!vehicleId) {
            document.getElementById('vehicleAvailability').innerHTML = '';
            return;
        }

        const url = `{{ route('trips.checkVehicleAvailability', ['trip' => '__trip__', 'vehicle' => '__vehicle__']) }}`.replace('__trip__', tripId).replace('__vehicle__', vehicleId);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const msg = data.available
                    ? '<span class="text-success">✅ Vehicle is available.</span>'
                    : '<span class="text-danger">❌ Vehicle is NOT available.</span>';
                document.getElementById('vehicleAvailability').innerHTML = msg;
            })
            .catch(() => {
                document.getElementById('vehicleAvailability').innerHTML = '<span class="text-danger">Error checking vehicle.</span>';
            });
    }

    function checkDriverAvailability() {
        const driverId = document.getElementById('employee').value;
        const tripId = {{ $trip->id }};

        if (!driverId) {
            document.getElementById('driverAvailability').innerHTML = '';
            return;
        }

        const url = `{{ route('trips.checkDriverAvailability', ['trip' => '__trip__', 'driver' => '__driver__']) }}`.replace('__trip__', tripId).replace('__driver__', driverId);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const msg = data.available
                    ? '<span class="text-success">✅ Driver is available.</span>'
                    : '<span class="text-danger">❌ Driver is NOT available.</span>';
                document.getElementById('driverAvailability').innerHTML = msg;
            })
            .catch(() => {
                document.getElementById('driverAvailability').innerHTML = '<span class="text-danger">Error checking driver.</span>';
            });
    }
</script> --}}


<script>
    let vehicleAvailable = false;
    let driverAvailable = false;

    function updateAssignButtonState() {
        const assignBtn = document.getElementById('assignBtn');
        if (vehicleAvailable && driverAvailable) {
            assignBtn.disabled = false;
        } else {
            assignBtn.disabled = true;
        }
    }

    function checkVehicleAvailability() {
        const vehicleId = document.getElementById('vehicle').value;
        const tripId = {{ $trip->id }};

        if (!vehicleId) {
            document.getElementById('vehicleAvailability').innerHTML = '';
            vehicleAvailable = false;
            updateAssignButtonState();
            return;
        }

        const url = `{{ route('trips.checkVehicleAvailability', ['trip' => '__trip__', 'vehicle' => '__vehicle__']) }}`
            .replace('__trip__', tripId)
            .replace('__vehicle__', vehicleId);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    vehicleAvailable = true;
                    document.getElementById('vehicleAvailability').innerHTML = '<span class="text-success">✅ Vehicle is available.</span>';
                } else {
                    vehicleAvailable = false;
                    document.getElementById('vehicleAvailability').innerHTML = '<span class="text-danger">❌ Vehicle is NOT available.</span>';
                }
                updateAssignButtonState();
            })
            .catch(() => {
                vehicleAvailable = false;
                document.getElementById('vehicleAvailability').innerHTML = '<span class="text-danger">Error checking vehicle.</span>';
                updateAssignButtonState();
            });
    }

    function checkDriverAvailability() {
        const driverId = document.getElementById('employee').value;
        const tripId = {{ $trip->id }};

        if (!driverId) {
            document.getElementById('driverAvailability').innerHTML = '';
            driverAvailable = false;
            updateAssignButtonState();
            return;
        }

        const url = `{{ route('trips.checkDriverAvailability', ['trip' => '__trip__', 'driver' => '__driver__']) }}`
            .replace('__trip__', tripId)
            .replace('__driver__', driverId);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    driverAvailable = true;
                    document.getElementById('driverAvailability').innerHTML = '<span class="text-success">✅ Driver is available.</span>';
                } else {
                    driverAvailable = false;
                    document.getElementById('driverAvailability').innerHTML = '<span class="text-danger">❌ Driver is NOT available.</span>';
                }
                updateAssignButtonState();
            })
            .catch(() => {
                driverAvailable = false;
                document.getElementById('driverAvailability').innerHTML = '<span class="text-danger">Error checking driver.</span>';
                updateAssignButtonState();
            });
    }
</script>



@endsection
