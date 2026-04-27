
@extends('user.layouts.app')
@section('title', 'Trip List')

@section('content')
<div class="container mt-4">
    <div class="card shadow p-4 border-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-primary"><i class="fas fa-route me-2"></i>All Trips</h3>
            <a href="{{ route('trips.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle me-1"></i> New Trip
            </a>
        </div>

        @if ($trips->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-striped">
                <thead class="table-primary text-center">
                    <tr>
                    <th>S.No</th>
                    <th><i class="fas fa-user-circle"></i> Customer</th>
                    <th><i class="fas fa-truck-moving"></i> Trip Details</th>
                    <th><i class="fas fa-map-marker-alt text-success"></i> From</th>
                    <th><i class="fas fa-flag-checkered text-danger"></i> To</th>
                    <th><i class="fas fa-road"></i> Distance</th>
                    <th><i class="fas fa-clock"></i> Timing</th>
                    <th><i class="fas fa-tasks"></i> Status</th>
                    <th><i class="fas fa-ellipsis-v"></i> Actions</th>
                    </tr>

                </thead>
                <tbody>
    @foreach ($trips as $index => $trip)
        <tr>
            <td class="text-center">{{ $index + $trips->firstItem() }}</td>

            <!-- Customer Info -->
            <td>
                @if($trip->customerInfo)
                    <strong><i class="fas fa-user text-primary me-1"></i>{{ $trip->customerInfo->customer_name }}</strong><br>
                    <i class="fas fa-envelope text-muted me-1"></i>{{ $trip->customerInfo->email }}<br>
                    <i class="fas fa-phone text-muted me-1"></i>{{ $trip->customerInfo->phone }}<br>
                    <i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $trip->customerInfo->address }}
                @else
                    <span class="text-muted">No Customer Info</span>
                @endif
            </td>

            <!-- Trip Info -->
            <td>
                <strong><i class="fas fa-heading text-primary me-1"></i>{{ $trip->title }}</strong><br>
                <i class="fas fa-sticky-note text-muted me-1"></i> {!! nl2br(e($trip->notes)) !!}
            </td>

            <!-- Start Destination -->
            <td>
                {{-- <strong>Start:</strong><br> --}}
                <small>
                     {{ $trip->from_address }}<br>
                </small>
            </td>


           <td>
   
    <small>
        {{ $trip->to_address }}<br>
        
    </small>
</td>


            <!-- Distance -->
            <td class="text-center">
                {{ number_format($trip->distance, 2) }} km
            </td>

            <!-- Timing -->
            <td>
                <i class="fas fa-sign-in-alt me-1"></i> {{ \Carbon\Carbon::parse($trip->start_time)->format('d M Y, h:i A') }}<br>
                <i class="fas fa-sign-out-alt me-1"></i> {{ \Carbon\Carbon::parse($trip->end_time)->format('d M Y, h:i A') }}
            </td>

            <!-- Status -->
            <td class="text-center">
                <span class="badge bg-{{ $trip->status == 'completed' ? 'success' : ($trip->status == 'cancelled' ? 'danger' : 'warning') }}">
                    <i class="fas fa-info-circle me-1"></i>{{ ucfirst($trip->status) }}
                </span>
            </td>

            <!-- Actions -->
            <td class="text-center">
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('trips.show', $trip->id) }}">
                                <i class="fas fa-eye text-primary me-1"></i> View
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('trips.location', $trip->id) }}">
                                <i class="fas fa-eye text-primary me-1"></i> Location
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('trips.edit', $trip->id) }}">
                                <i class="fas fa-edit text-warning me-1"></i> Edit
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>

            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $trips->links() }}
        </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No trips found.
            </div>
        @endif
    </div>
</div>
@endsection