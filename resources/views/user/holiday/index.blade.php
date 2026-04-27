@extends('user.layouts.app')
@section('title', 'Holiday List') <!-- Set your custom title here -->

@section('content')
@include('user.layouts.alert')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <div>
            <div class="row mb-3">
                <div class="col-md-9 col-6">
                    <h2 class="fw-bold">Holiday</h2>
                </div>
                <div class="col-md-3 col-6 text-end">
                    <a href="{{ route('holiday.create') }}" class="btn btn-success shadow-sm"><i class="fas fa-plus"></i>
                        Create</a>
                </div>
            </div>
            <div class="table-responsive table-same">
                <table class="table  table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($holidayes as $index => $holiday)
                            <tr>
                                <!-- First index starts at 1 -->
                                <td>{{ $index + 1 }}</td> <!-- Shows 1-based index -->
                                <td>{{ $holiday->title }}</td>
                                <td>{{ $holiday->description }}</td>
                                <td>{{ $holiday->start_date }}</td>
                                <td>{{ $holiday->end_date }}</td>
                                <td>
                                    <a href="{{ route('holiday.edit', $holiday->id) }}" class="btn btn-warning btn-sm"><i
                                            class="fas fa-edit"></i></a>

                                    <!-- Delete Form with Confirmation -->
                                    <form id="delete-form-{{ $holiday->id }}"
                                        action="{{ route('holiday.destroy', $holiday->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete(event, {{ $holiday->id }})"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No record found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(event, branchId) {
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
                    document.getElementById(`delete-form-${branchId}`).submit();
                }
            });
        }
    </script>
@endsection