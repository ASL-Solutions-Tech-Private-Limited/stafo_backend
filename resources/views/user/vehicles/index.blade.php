@extends('user.layouts.app')
@section('title', 'Vehicle List')

@section('content')
<div class="card mt-4 p-3">

    <div class="row mb-3">
        <div class="col-md-6">
            <h2 class="fw-bold">Vehicle List</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('vehicles.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Add Vehicle
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('vehicles.index') }}" class="row g-3 mb-3">
        <div class="col-md-4">
            <input
                type="text"
                name="vehicle_no"
                class="form-control"
                placeholder="Search by Vehicle No"
                value="{{ request('vehicle_no') }}"
            >
        </div>

        <div class="col-md-4">
            <select name="vehicle_type" class="form-control">
                <option value="">-- Select Vehicle Type --</option>
                @php
                    $types = ['Bus', 'Mini Bus', 'Coach', 'Van', 'Car', 'Jeep', 'Taxi'];
                @endphp
                @foreach ($types as $type)
                    <option value="{{ $type }}" {{ request('vehicle_type') == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <!-- table head and body here (same as before) -->
            <thead class="table-dark">
                <tr>
                    <th>S.no</th>
                    <th>Vehicle No</th>
                    <th>Type</th>
                    <th>Fuel</th>
                    <th>Load Capacity</th>
                    <th>Status</th>
                    <th>KM Travelled</th>
                    <th>Speedometer</th>
                    <th>RC File</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $index => $vehicle)
                    <tr>
                        <td>{{ $vehicles->firstItem() + $index }}</td>
                        <td>{{ $vehicle->vehicle_no }}</td>
                        <td>{{ $vehicle->vehicle_type }}</td>
                        <td>{{ $vehicle->fuel }}</td>
                        <td>{{ round($vehicle->load_capacity) }}</td>
                        <td>
                            <span class="badge bg-{{ $vehicle->status == 'active' ? 'success' : ($vehicle->status == 'inactive' ? 'secondary' : 'warning') }}">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        </td>
                        <td>{{ $vehicle->km_travelled }} KM</td>
                        <td>{{ $vehicle->speedometer }} KM</td>
                        <td>
                            @if($vehicle->rc_upload_path)
                                <a href="{{ asset('uploads/rc_files/' . $vehicle->rc_upload_path) }}" target="_blank" title="View RC File">View</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fa fa-edit"></i>
                            </a>
                            {{-- <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete"   onclick="confirmDelete(event, {{ $vehicle->id }})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form> --}}

                            <form id="delete-form-{{ $vehicle->id }}" action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="confirmDelete(event, {{ $vehicle->id }})">
                                <i class="fa fa-trash"></i>
                            </button>
                          </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No vehicles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-4">
            {{ $vehicles->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function confirmDelete(event, id) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the vehicle and its RC file.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
@endsection

