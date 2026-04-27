@extends('admin.layouts.layout')
@section('title', 'Followup List') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <div>
            <div class="row mb-3">
                <div class="col-md-9 col-6">
                    <h2 class="fw-bold">Followup List</h2>
                </div>
                <div class="col-md-3 col-6 text-end">
                    <a href="{{ route('admin.leadFollowupCreate',$lead->id) }}" class="btn btn-success shadow-sm"><i class="fas fa-plus"></i>
                        Create New</a>
                </div>
            </div>
            <div class="table-responsive ">
                <table class="table  table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>                            
                            <th>Type</th>
                            <th>Next Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($followups as $index => $followup)
                            <tr>
                                <!-- First index starts at 1 -->
                                <td>{{ $index + 1 }}</td> <!-- Shows 1-based index -->
                                <td>
                                    {{ $followup->type }}
                                </td>
                                <td>
                                    {{ $followup->next_date }}
                                </td>
                                <td>{{ $followup->status }}</td>
                                <td>{{ $followup->remarks }}</td>
                                
                                <td>
                                    <a href="{{ route('admin.followupEdit', $followup->id) }}" class="btn btn-warning btn-sm"><i
                                            class="fas fa-edit"></i></a>

                                    <!-- Delete Form with Confirmation -->
                                    <form id="delete-form-{{ $lead->id }}"
                                        action="{{ route('salarytype.destroy', $lead->id) }}" method="POST"
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