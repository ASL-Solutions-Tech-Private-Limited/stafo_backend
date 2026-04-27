@extends('user.layouts.app')
@section('title', 'Create Trip')

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
            <h3 class="fw-bold text-primary"><i class="fas fa-route me-2"></i>Create New Trip</h3>
            <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-1"></i> Trip List
            </a>
        </div>

        <form action="{{ route('trips.store') }}" method="POST">
            @csrf

            <!-- Customer Info -->
            <div class="card bg-light p-4 mb-4 border-0 shadow-sm rounded">
                <h5 class="text-primary mb-3"><i class="fas fa-user-circle me-2"></i>Customer Information</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="customer_name" class="form-control rounded" required placeholder="Enter name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="customer_email" class="form-control rounded" placeholder="Enter email">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" name="customer_phone" class="form-control rounded" required placeholder="Enter mobile number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <textarea name="customer_address" class="form-control rounded" rows="2" placeholder="Enter full address..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trip Info -->
            <div class="card bg-white p-4 border-0 shadow-sm rounded">
                <h5 class="text-success mb-3"><i class="fas fa-truck-moving me-2"></i>Trip Details</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Trip Title</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-heading"></i></span>
                            <input type="text" name="title" class="form-control rounded" required placeholder="Enter trip title">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Start Time</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <input type="datetime-local" name="start_time" class="form-control rounded" required placeholder="Enter start time">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Time</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-hourglass-end"></i></span>
                            <input type="datetime-local" name="end_time" class="form-control rounded" placeholder="Enter end time">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tasks"></i></span>
                            <select name="status" class="form-select rounded" required>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
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
                                    <input type="text" id="start_latitude" name="start_latitude" class="form-control" readonly required>
                                </div>
                                <div class="col-md-6 d-none">
                                    <label class="form-label">Start Longitude</label>
                                    <input type="text" id="start_longitude" name="start_longitude" class="form-control" readonly required>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label">From Address</label>
                                    <input type="text" id="from_address" name="from_address" class="form-control">
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
                                    <input type="text" id="end_latitude" name="end_latitude" class="form-control" readonly required>
                                </div>
                                <div class="col-md-6 d-none">
                                    <label class="form-label">End Longitude</label>
                                    <input type="text" id="end_longitude" name="end_longitude" class="form-control" readonly required>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label">To Address</label>
                                    <input type="text" id="to_address" name="to_address" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                     <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                            <textarea name="notes" class="form-control rounded" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-check-circle me-1"></i> Create Trip
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
    const map = L.map(mapId).setView([defaultLat, defaultLng], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    document.getElementById(latInputId).value = defaultLat;
    document.getElementById(lngInputId).value = defaultLng;

    function updateAddress(lat, lng, targetInput) {
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
            .then(res => res.json())
            .then(data => {
                if (data && data.display_name) {
                    targetInput.value = data.display_name;
                }
            });
    }

    marker.on('dragend', function () {
        const pos = marker.getLatLng();
        document.getElementById(latInputId).value = pos.lat.toFixed(6);
        document.getElementById(lngInputId).value = pos.lng.toFixed(6);
        updateAddress(pos.lat, pos.lng, document.getElementById(addressInputId));
    });

    // Address Autocomplete using Nominatim
    const input = document.getElementById(addressInputId);
    let timeout = null;
    const suggestionsBox = document.createElement('div');
    suggestionsBox.className = 'list-group position-absolute w-100 shadow-sm z-3';
    suggestionsBox.style.maxHeight = '200px';
    suggestionsBox.style.overflowY = 'auto';
    suggestionsBox.style.display = 'none';
    input.parentNode.appendChild(suggestionsBox);

    input.addEventListener('input', function () {
        const query = input.value.trim();
        clearTimeout(timeout);
        suggestionsBox.innerHTML = '';
        if (query.length < 3) {
            suggestionsBox.style.display = 'none';
            return;
        }

        timeout = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(results => {
                    suggestionsBox.innerHTML = '';
                    results.forEach(item => {
                        const option = document.createElement('button');
                        option.className = 'list-group-item list-group-item-action';
                        option.textContent = item.display_name;
                        option.onclick = () => {
                            input.value = item.display_name;
                            const lat = parseFloat(item.lat);
                            const lon = parseFloat(item.lon);
                            document.getElementById(latInputId).value = lat.toFixed(6);
                            document.getElementById(lngInputId).value = lon.toFixed(6);
                            map.setView([lat, lon], 14);
                            marker.setLatLng([lat, lon]);
                            suggestionsBox.style.display = 'none';
                        };
                        suggestionsBox.appendChild(option);
                    });
                    suggestionsBox.style.display = 'block';
                });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!suggestionsBox.contains(e.target) && e.target !== input) {
            suggestionsBox.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initializeMap('startMap', 'start_latitude', 'start_longitude', 'from_address');
    initializeMap('endMap', 'end_latitude', 'end_longitude', 'to_address');
});
</script>

@endsection
