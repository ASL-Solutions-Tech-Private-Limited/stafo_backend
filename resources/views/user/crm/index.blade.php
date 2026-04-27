@extends('user.layouts.app')
@section('title', 'Lead List') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <div>
            <div class="row mb-3">
                <div class="col-md-9 col-6">
                    <h2 class="fw-bold">Lead List</h2>
                </div>
                <div class="col-md-3 col-6 text-end">
                    <a href="{{ route('leadCreate') }}" class="btn btn-success shadow-sm"><i class="fas fa-plus"></i>
                        Create New</a>
                </div>
            </div>
            <div class="table-responsive ">
                <table class="table  table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>                            
                            <th>Employee Name</th>
                            <th>Name</th>
                            <th>Company Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leads as $index => $lead)
                            <tr>
                                <!-- First index starts at 1 -->
                                <td>{{ $index + 1 }}</td> <!-- Shows 1-based index -->
                                <td>
                                    {{ isset($lead->employee->name)?$lead->employee->name:'' }}
                                </td>
                                <td>
                                    {{ $lead->name }}
                                </td>
                                <td>{{ $lead->company_name }}</td>
                                <td>{{ $lead->email }}</td>
                                <td>
                                    {{ $lead->phone }}
                                </td>
                                <td>
                                    <a href="{{ route('leadEdit', $lead->id) }}" class="btn btn-warning btn-sm"><i
                                            class="fas fa-edit"></i></a>
                                    <a href="{{ route('leadFollowup', $lead->id) }}" class="btn btn-primary btn-sm"><i
                                            class="fas fa-plus"></i></a>

                                    <!-- Delete Form with Confirmation -->
                                    <form id="delete-form-{{ $lead->id }}"
                                        action="{{ route('leadDelete', $lead->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <!-- <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete(event, {{ $lead->id }})"><i
                                                class="fas fa-trash"></i></button> -->
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(event, salarytypeId) {
            event.preventDefault(); // Prevent form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the delete form
                    document.getElementById(`delete-form-${salarytypeId}`).submit();
                }
            });
        }
    </script>
@endsection