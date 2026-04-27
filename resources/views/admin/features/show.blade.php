@extends('admin.layouts.layout')

@section('content')
    <div class="container">
        <h3 class="mb-3">Feature Details</h3>

        <div class="card">
            <div class="card-body">
                <p class="card-text"><strong>Feature Name:</strong> {{ $feature->name }}</p>
                <p class="card-text"><strong>Description:</strong> {!! strip_tags($feature->description), '<br>' !!}</p>

                <p class="card-text"><strong>Status:</strong> {{ $feature->status == 1 ? 'Active' : 'Inactive' }}</p>
                {{-- <p class="card-text"><strong>Created At:</strong> {{ $feature->created_at->format('d M Y, h:i A') }}</p> --}}
                @if ($feature->icon)
                    <p class="card-text"><strong>Icon:</strong>
                        <img src="{{ asset('storage/icons/' . $feature->icon) }}" alt="{{ $feature->name }}" width="50">
                    </p>
                @else
                    <p class="card-text"><strong>Icon:</strong> No icon available</p>
                @endif
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('features.index') }}" class="btn btn-warning">Back</a>
        </div>
    </div>
@endsection
