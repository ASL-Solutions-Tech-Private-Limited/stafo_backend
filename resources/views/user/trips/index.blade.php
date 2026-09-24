@extends('user.layouts.app')
@section('title', 'Trip Logs & Dispatches | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Fleet Trips & Dispatch Logs</h3>
                <p class="text-muted small mb-0">Track commercial travel logs, vehicle itineraries, odometer distances, and customer drops</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('trips.create') }}" class="btn btn-primary px-3 py-2">
                    <i class="fa-solid fa-plus me-1"></i> New Trip Entry
                </a>
            </div>
        </div>

        @if ($trips->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 60px;" class="text-center">S.No</th>
                            <th style="width: 240px;">Customer / Client</th>
                            <th style="width: 220px;">Trip Subject</th>
                            <th>Route (From &rarr; To)</th>
                            <th style="width: 130px;" class="text-center">Distance</th>
                            <th style="width: 200px;">Timing</th>
                            <th style="width: 120px;" class="text-center">Status</th>
                            <th style="width: 150px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trips as $index => $trip)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $index + $trips->firstItem() }}</td>

                                <!-- Customer Info -->
                                <td>
                                    @if($trip->customerInfo)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block">{{ $trip->customerInfo->customer_name }}</span>
                                                <small class="text-muted">{{ $trip->customerInfo->phone }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">No Client Details</span>
                                    @endif
                                </td>

                                <!-- Trip Info -->
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $trip->title }}</span>
                                    @if($trip->notes)
                                        <small class="text-muted">{{ Str::limit($trip->notes, 40) }}</small>
                                    @endif
                                </td>

                                <!-- Start & Destination -->
                                <td>
                                    <div class="small">
                                        <div class="text-dark d-flex align-items-center gap-1 mb-1">
                                            <i class="fa-solid fa-location-dot text-success"></i>
                                            <span>{{ $trip->from_address }}</span>
                                        </div>
                                        <div class="text-muted d-flex align-items-center gap-1">
                                            <i class="fa-solid fa-flag-checkered text-danger"></i>
                                            <span>{{ $trip->to_address }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Distance -->
                                <td class="text-center">
                                    <span class="badge-stafo badge-stafo-info font-monospace">
                                        {{ number_format((float)$trip->distance, 2) }} km
                                    </span>
                                </td>

                                <!-- Timing -->
                                <td>
                                    <small class="text-muted d-block">
                                        <i class="fa-solid fa-clock text-primary me-1"></i>
                                        {{ \Carbon\Carbon::parse($trip->start_time)->format('d M, h:i A') }}
                                    </small>
                                    @if($trip->end_time)
                                        <small class="text-muted d-block">
                                            <i class="fa-solid fa-flag text-secondary me-1"></i>
                                            {{ \Carbon\Carbon::parse($trip->end_time)->format('d M, h:i A') }}
                                        </small>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="text-center">
                                    @php
                                        $statusClass = 'badge-stafo-warning';
                                        if (strtolower($trip->status) == 'completed') $statusClass = 'badge-stafo-success';
                                        if (strtolower($trip->status) == 'cancelled') $statusClass = 'badge-stafo-danger';
                                    @endphp
                                    <span class="badge-stafo {{ $statusClass }}">
                                        {{ ucfirst($trip->status) }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('trips.show', $trip->id) }}" 
                                           class="btn btn-sm btn-outline-info p-0" 
                                           style="width: 32px; height: 32px; border-radius: 8px;"
                                           title="Trip Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <a href="{{ route('trips.location', $trip->id) }}" 
                                           class="btn btn-sm btn-outline-primary p-0" 
                                           style="width: 32px; height: 32px; border-radius: 8px;"
                                           title="GPS Map Location">
                                            <i class="fa-solid fa-map-location-dot"></i>
                                        </a>

                                        <a href="{{ route('trips.edit', $trip->id) }}" 
                                           class="btn btn-sm btn-outline-warning p-0" 
                                           style="width: 32px; height: 32px; border-radius: 8px;"
                                           title="Edit Trip">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger p-0" 
                                                style="width: 32px; height: 32px; border-radius: 8px;"
                                                title="Delete Trip"
                                                onclick="confirmDeleteTrip(event, {{ $trip->id }})">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>

                                        <form id="delete-form-{{ $trip->id }}" action="{{ route('trips.destroy', $trip->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($trips, 'links') && $trips->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                    <small class="text-muted">Showing {{ $trips->firstItem() }} to {{ $trips->lastItem() }} of {{ $trips->total() }} trips</small>
                    <div>{{ $trips->links('pagination::bootstrap-4') }}</div>
                </div>
            @endif
        @else
            <div class="text-center py-5 bg-light rounded-4 border text-muted">
                <i class="fa-solid fa-route fs-2 mb-2 d-block opacity-50"></i>
                No fleet trips logged yet.
            </div>
        @endif
    </div>
</div>
@endsection

@section('js')
<script>
    function confirmDeleteTrip(event, tripId) {
        event.preventDefault();

        Swal.fire({
            title: 'Delete Trip Record?',
            text: "You won't be able to revert this trip entry!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${tripId}`).submit();
            }
        });
    }
</script>
@endsection