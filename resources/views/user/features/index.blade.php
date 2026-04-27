@extends('user.layouts.app')
@section('title', 'Feature List') <!-- Set your custom title here -->

@section('css')
    <style>
        /* Custom styling */
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e0e0e0;
        }

        .card-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
        }

        .table th {
            background-color: #343a40;
            color: white;
            text-align: center;
        }

        .table td {
            text-align: center;
        }

        .badge-info {
            background-color: #17a2b8;
            font-size: 0.85rem;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .container {
            padding: 20px;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
    </style>
@endsection

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0 emp-data">
        <div class="card-header">
            <h3 class="card-title">Company Features List</h3>
        </div>
        <div class="container">
            <!-- Feature Table -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Feature Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Associated Packages</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($features as $index => $feature)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $feature->name }}</td>
                                <td>{{ strip_tags($feature->description) ?? 'N/A' }}</td> <!-- Strip HTML tags -->
                                <td>{{ ucfirst($feature->status) }}</td>

                                <td>
                                    <!-- List associated packages and their feature values -->
                                    @foreach ($feature->packages as $package)
                                        <span class="badge bg-info">
                                            {{ $package->package_name }}:
                                            {{ $package->pivot->feature_value ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Add custom JS if needed -->
@endsection
