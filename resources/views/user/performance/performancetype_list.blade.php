@extends('user.layouts.app')
@section('title', 'Performance Type List') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <div>
            <div class="row mb-3">
                <div class="col-md-9 col-6">
                    <h2 class="fw-bold">Performance Types</h2>
                </div>
                <div class="col-md-3 col-6 text-end">
                    <a href="{{ route('performancetypeCreate') }}" class="btn btn-success shadow-sm"><i class="fas fa-plus"></i>
                        Create New</a>
                </div>
            </div>
            <div class="table-responsive table-same">
                <table class="table  table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th>Performance Type</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($performancetypes as $index => $performancetype)
                            <tr>
                                <!-- First index starts at 1 -->
                                <td>{{ $index + 1 }}</td> <!-- Shows 1-based index -->
                                <td>{{ $performancetype->name }}</td>
                                <td>{{ $performancetype->description }}</td>
                                
                                <td>
                                    <a href="{{ route('performancetypeEdit', $performancetype->id) }}" class="btn btn-warning btn-sm"><i
                                            class="fas fa-edit"></i></a>
                                    <!-- Delete Form with Confirmation -->
                                    <form id="delete-form-{{ $performancetype->id }}"
                                        action="{{ route('performancetypeDelete', $performancetype->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')  
                                        <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="confirmDelete(event, {{ $performancetype->id }})" title="Delete">
                                            <i class="fas fa-trash-alt"></i> 
                                        </button>                                      
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
        function confirmDelete(event, Id) {
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
                    document.getElementById(`delete-form-${Id}`).submit();
                }
            });
        }
    </script>
@endsection