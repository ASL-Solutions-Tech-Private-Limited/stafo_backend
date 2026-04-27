@extends('admin.layouts.layout')

@section('content')
    <div class="container">
        <h3 class="mb-3">Package Details</h3>

        <div class="card">
            <div class="card-body">
                <p class="card-text"><span class="fw-bold">Package Name:</span> {{ $package->package_name }}
                </p>
                <p class="card-text"><span class="fw-bold">Description:</span>
                    {{ strip_tags($package->description) ?? 'N/A' }}</p>

                <p class="card-text"><span class="fw-bold">Price:</span>
                    ₹{{ number_format($package->price, 2) }}</p>

                <!-- Display discount_price if it's set -->
                <p class="card-text"><span class="fw-bold">Discount Price:</span>
                    @if ($package->discount_price)
                        ₹{{ number_format($package->discount_price, 2) }}
                    @else
                        N/A
                    @endif
                </p>

                <!-- Display days if it's set -->
                <p class="card-text"><span class="fw-bold">Days:</span>
                    {{ $package->days ? $package->days : 'N/A' }}
                </p>

                <p class="card-text"><span class="fw-bold">Status:</span> {{ ucfirst($package->status) }}</p>
                <p class="card-text"><span class="fw-bold">Created At:</span>
                    {{ $package->created_at->format('d M Y, h:i A') }}</p>

                <!-- Display Features -->
                <div class="mt-3">
                    <h5>Features:</h5>
                    @if ($package->features->isNotEmpty())
                        <ul>
                            @foreach ($package->features as $feature)
                                <li>
                                    <span class="fw-bold">{{ $feature->name }}:</span>
                                    {{ $feature->pivot->feature_value ?? 'N/A' }}
                                    <!-- Assuming the pivot table has the feature_value -->
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No features assigned to this package.</p>
                    @endif
                </div>

            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('packages.index') }}" class="btn btn-warning">Back</a>
        </div>
    </div>
@endsection