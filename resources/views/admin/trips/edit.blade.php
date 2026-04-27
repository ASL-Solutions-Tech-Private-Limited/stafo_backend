@extends('admin.layouts.layout')
@section('title', 'Edit Trip')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<style>
    .form-control,
    .form-select {
        border: 1px solid #ced4da !important;
        box-shadow: none !important;
    }
</style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="card shadow p-4 border-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-primary"><i class="fas fa-edit me-2"></i>Edit Trip</h3>
            <a href="{{ route('admin.trips.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-1"></i> Trip List
            </a>
        </div>

        <form action="{{ route('admin.trips.update', $trip->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Customer Info -->
            <div class="card bg-light p-4 mb-4 border-0 shadow-sm rounded">
                <h5 class="text-primary mb-3"><i class="fas fa-user-circle me-2"></i>Customer Information</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="customer_name" value="{{ $trip->customerInfo->customer_name ?? '' }}" class="form-control rounded" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="customer_email" value="{{ $trip->customerInfo->email ?? '' }}" class="form-control rounded">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" name="customer_phone" value="{{ $trip->customerInfo->phone ?? '' }}" class="form-control rounded" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <textarea name="customer_address" class="form-control rounded" rows="2">{{ $trip->customerInfo->address ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Trip Info -->
            <div class="card bg-white p-4 border-0 shadow-sm rounded">
                <h5 class="text-success mb-3"><i class="fas fa-truck-moving me-2"></i>Trip Details</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Trip Title</label>
                        <input type="text" name="title" value="{{ $trip->title }}" class="form-control rounded" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Start Time</label>
                        <input type="datetime-local" name="start_time" value="{{ \Carbon\Carbon::parse($trip->start_time)->format('Y-m-d\TH:i') }}" class="form-control rounded" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Time</label>
                        <input type="datetime-local" name="end_time" value="{{ \Carbon\Carbon::parse($trip->end_time)->format('Y-m-d\TH:i') }}" class="form-control rounded">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select rounded" required>
                            <option value="pending" {{ $trip->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ $trip->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $trip->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="row my-4">
                    <!-- Start Location Picker -->
                    <div class="col-lg-6 mb-4">
                        <div class="card bg-light p-4 border-0 shadow-sm rounded h-100">
                            <h5 class="text-info mb-3"><i class="fas fa-map-marker-alt me-2"></i>Source</h5>
                            <div id="startMap" style="height: 300px;" class="rounded shadow-sm mb-3"></div>
                            <div class="row">
                                <div class="col-md-6 d-none">
                                    <label class="form-label">Start Latitude</label>
                                    <input type="text" id="start_latitude" name="start_latitude" class="form-control" readonly required value="{{ $trip->start_latitude }}">
                                </div>
                                <div class="col-md-6 d-none">
                                    <label class="form-label">Start Longitude</label>
                                    <input type="text" id="start_longitude" name="start_longitude" class="form-control" readonly required value="{{ $trip->start_longitude }}">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label">From Address</label>
                                    <input type="text" id="from_address" name="from_address" class="form-control" value="{{ $trip->from_address }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- End Location Picker -->
                    <div class="col-lg-6 mb-4">
                        <div class="card bg-light p-4 border-0 shadow-sm rounded h-100">
                            <h5 class="text-warning mb-3"><i class="fas fa-map-marker me-2"></i>Destination</h5>
                            <div id="endMap" style="height: 300px;" class="rounded shadow-sm mb-3"></div>
                            <div class="row">
                                <div class="col-md-6 d-none">
                                    <label class="form-label">End Latitude</label>
                                    <input type="text" id="end_latitude" name="end_latitude" class="form-control" readonly required value="{{ $trip->end_latitude }}">
                                </div>
                                <div class="col-md-6 d-none">
                                    <label class="form-label">End Longitude</label>
                                    <input type="text" id="end_longitude" name="end_longitude" class="form-control" readonly required value="{{ $trip->end_longitude }}">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label">To Address</label>
                                    <input type="text" id="to_address" name="to_address" class="form-control" value="{{ $trip->to_address }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control rounded" rows="3">{{ $trip->notes }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-check-circle me-1"></i> Update Trip
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
function initializeMap(mapId, latInputId, lngInputId, addressInputId, defaultLat = 22.9734, defaultLng = 78.6569) {
    const latInput = document.getElementById(latInputId);
    const lngInput = document.getElementById(lngInputId);
    const addressInput = document.getElementById(addressInputId);

    // Parse values or fallback
    const initLat = latInput.value ? parseFloat(latInput.value) : defaultLat;
    const initLng = lngInput.value ? parseFloat(lngInput.value) : defaultLng;

    const map = L.map(mapId).setView([initLat, initLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);

    // Update inputs function
    function updateInputs(latlng) {
        latInput.value = latlng.lat.toFixed(6);
        lngInput.value = latlng.lng.toFixed(6);

        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latlng.lat}&lon=${latlng.lng}&format=json`)
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name) {
                    addressInput.value = data.display_name;
                }
            }).catch(() => {
                // fail silently
            });
    }

    // Update inputs initially
    updateInputs(marker.getLatLng());

    // On marker drag
    marker.on('dragend', function () {
        updateInputs(marker.getLatLng());
    });

    // On map click
    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        updateInputs(e.latlng);
    });

    // Geocoder control
    L.Control.geocoder({
        defaultMarkGeocode: false
    })
    .on('markgeocode', function (e) {
        const latlng = e.geocode.center;
        map.setView(latlng, 14);
        marker.setLatLng(latlng);
        updateInputs(latlng);
    })
    .addTo(map);
}

document.addEventListener('DOMContentLoaded', function () {
    initializeMap('startMap', 'start_latitude', 'start_longitude', 'from_address', {{ $trip->start_latitude ?? 22.9734 }}, {{ $trip->start_longitude ?? 78.6569 }});
    initializeMap('endMap', 'end_latitude', 'end_longitude', 'to_address', {{ $trip->end_latitude ?? 22.9734 }}, {{ $trip->end_longitude ?? 78.6569 }});
});
</script>
@endsection
