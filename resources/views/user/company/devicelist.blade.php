@extends('user.layouts.app')
@section('title', 'Device Request List')

@section('content')
<div class="card mt-4 p-3">

    <div class="row mb-3">
        <div class="col-md-6">
            <h2 class="fw-bold">Device Request List</h2>
        </div>
        
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('deviceList') }}" class="row g-3 mb-3">
        

        <div class="col-md-4">
            
        </div>

        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('deviceList') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <!-- table head and body here (same as before) -->
            <thead class="table-dark">
                <tr>
                    <th>S.no</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deviceRequests as $index => $request)
                    <tr>
                        <td>{{ 1 + $index }}</td>
                        <td>{{ $request->name }}</td>
                        <td>{{ $request->email }}</td>
                        <td>{{ $request->phone }}</td>
                        <td>
                            @if($request->device_status == 'pending')
                                <a href="javascript:void(0);" class="btn btn-primary" onclick="confirmApprove(this,'{{$request->id}}')">Approved</a>
                                <a href="javascript:void(0);" class="btn btn-danger"  onclick="confirmReject(this,'{{$request->id}}')">Rejected</a>
                            @else
                                {{ ucfirst($request->device_status) }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination Links -->
       
    </div>
</div>
@endsection

@section('js')
<script>
    function confirmApprove(event, id) {
        //event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "Approve the device.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('approveDevice', '') }}/" + id;
            }
        });
    }

    function confirmReject(event, id) {
        //event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "Reject the device.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Reject it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('rejectDevice', '') }}/" + id;
            }
        });
    }
</script>
@endsection

