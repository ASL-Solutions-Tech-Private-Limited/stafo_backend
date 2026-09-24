@extends('user.layouts.app')
@section('title', 'Vehicle Fleet | STAFO HRMS')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Fleet Vehicles</h3>
                <p class="text-muted small mb-0">Manage corporate vehicles, fuel logs, capacities, and registration files</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('vehicles.create') }}" class="btn btn-primary px-3 py-2">
                    <i class="fa-solid fa-plus me-1"></i> Add Vehicle
                </a>
            </div>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('vehicles.index') }}" class="mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="position-relative">
                        <input type="text" name="vehicle_no" class="form-control ps-4" placeholder="Search by vehicle number..."
                            value="{{ request('vehicle_no') }}">
                        <i class="fa-solid fa-car position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.8rem;"></i>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <select name="vehicle_type" class="form-select">
                        <option value="">All Vehicle Types</option>
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

                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->filled('vehicle_no') || request()->filled('vehicle_type'))
                        <a href="{{ route('vehicles.index') }}" class="btn btn-light border text-muted px-3" title="Clear Filters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 60px;" class="text-center">S.No</th>
                        <th style="width: 180px;">Vehicle No</th>
                        <th>Type</th>
                        <th>Fuel</th>
                        <th>Capacity</th>
                        <th style="width: 120px;" class="text-center">Status</th>
                        <th>KM Travelled</th>
                        <th>RC File</th>
                        <th style="width: 120px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $index => $vehicle)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $vehicles->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-truck"></i>
                                    </div>
                                    <span class="fw-bold text-dark">{{ $vehicle->vehicle_no }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge-stafo badge-stafo-info">{{ $vehicle->vehicle_type ?? 'Standard' }}</span>
                            </td>
                            <td>
                                <span class="text-dark fw-medium"><i class="fa-solid fa-gas-pump text-muted me-1"></i>{{ $vehicle->fuel ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="text-muted">{{ round($vehicle->load_capacity) }} kg/person</span>
                            </td>
                            <td class="text-center">
                                @if($vehicle->status == 'active')
                                    <span class="badge-stafo badge-stafo-success">
                                        <i class="fa-solid fa-circle-check"></i> Active
                                    </span>
                                @elseif($vehicle->status == 'inactive')
                                    <span class="badge-stafo badge-stafo-danger">
                                        <i class="fa-solid fa-circle-xmark"></i> Inactive
                                    </span>
                                @else
                                    <span class="badge-stafo badge-stafo-warning">{{ ucfirst($vehicle->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ number_format((float)$vehicle->km_travelled) }} KM</span>
                            </td>
                            <td>
                                @if($vehicle->rc_upload_path)
                                    <a href="{{ asset('uploads/rc_files/' . $vehicle->rc_upload_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.775rem;">
                                        <i class="fa-solid fa-file-pdf me-1"></i> View RC
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-outline-warning p-0" title="Edit Vehicle" style="width: 32px; height: 32px; border-radius: 8px;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form id="delete-form-{{ $vehicle->id }}" action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Vehicle" onclick="confirmDelete(event, {{ $vehicle->id }})" style="width: 32px; height: 32px; border-radius: 8px;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-truck-ramp-box fs-2 mb-2 d-block opacity-50"></i>
                                No vehicles found. Click <strong>Add Vehicle</strong> to register fleet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($vehicles->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                <small class="text-muted">Showing {{ $vehicles->firstItem() }} to {{ $vehicles->lastItem() }} of {{ $vehicles->total() }} entries</small>
                <div>{{ $vehicles->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif

    </div>
</div>
@endsection

@section('js')
<script>
    function confirmDelete(event, id) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the vehicle record and its RC file!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
@endsection
