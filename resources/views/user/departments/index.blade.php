@extends('user.layouts.app')
@section('title', 'Department List') <!-- Set your custom title here -->
@section('css')
@endsection

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">

        <div class="row mb-3">
            <div class="col-md-9 col-6">
                <h2 class="fw-bold">Department</h2>
            </div>
            <div class="col-md-3 col-6 text-end">
                <a href="{{ route('departments.create') }}" class="btn btn-success shadow-sm"><i class="fas fa-plus"></i>
                    Create</a>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('departments.index') }}" class="mb-4">
            <div class="row g-2">
                <!-- Name Filter -->
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Search by Name"
                        value="{{ request()->get('name') }}">
                </div>

                <!-- Status Filter -->
                <div class="col-md-4">
                    <select name="status" class="form-control">
                        <option value="">Select Status</option>
                        <option value="1" {{ request()->get('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request()->get('status') === '0' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>

                <!-- Submit and Reset Buttons -->
                <div class="col-md-4 text-end">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search "></i>
                        Search</button>
                    <!-- <a href="{{ route('departments.index') }}" class="btn btn-secondary btn-block mt-2">Reset</a> -->
                </div>
            </div>
        </form>

        <!-- Department Table -->
        <div class="table-responsive table-same">
            <table class="table table-striped table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($departments as $index => $department)
                        <tr>
                            <td>{{ $departments->firstItem() + $index }}</td>
                            <td>{{ $department->name }}</td>
                            <td>{{ $department->description }}</td>
                            <td>
                                @if ($department->status == 1)
                                    <!-- Only show "Active" text for active departments -->
                                    Active
                                @endif
                            </td>

                            <!-- Actions -->
                            <td>
                                <a href="{{ route('departments.show', $department->id) }}" class="btn btn-info btn-sm"><i
                                        class="fas fa-eye"></i></a>
                                <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-warning btn-sm"><i
                                        class="fas fa-edit"></i></a>
                                <form action="{{ route('departments.destroy', $department->id) }}" method="POST"
                                    style="display:inline;" id="delete-form-{{ $department->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="confirmDelete(event, {{ $department->id }})"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $departments->links('pagination::bootstrap-4') }} <!-- You can use any pagination style -->
        </div>
    </div>

    {{-- <script>
        // Toggle status on click
        document.querySelectorAll('.clickable-status').forEach(status => {
            status.addEventListener('click', function () {
                let departmentId = this.getAttribute('data-id');
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('CSRF token not found.');
                    return;
                }
                let toggleStatusUrl = '{{ route('departments.toggleStatus', ':id') }}';
                toggleStatusUrl = toggleStatusUrl.replace(':id', departmentId);

                fetch(toggleStatusUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute(
                            'content') // Fetch the token properly
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status !== undefined) {
                            let statusElement = document.getElementById('status-' + departmentId);
                            let icon = statusElement.querySelector('.toggle-icon');

                            if (data.status === 1) {
                                statusElement.textContent = ' Active';
                                icon.classList.remove('fa-toggle-off');
                                icon.classList.add('fa-toggle-on');
                                statusElement.style.backgroundColor = 'green';
                            } else {
                                statusElement.textContent = ' Inactive';
                                icon.classList.remove('fa-toggle-on');
                                icon.classList.add('fa-toggle-off');
                                statusElement.style.backgroundColor = 'red';
                            }
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            Swal.fire('Error', 'An error occurred while toggling status', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error toggling status:', error);
                        Swal.fire('Error', 'An unexpected error occurred.', 'error');
                    });
            });
        });
    </script> --}}

    <script>
        function confirmDelete(event, departmentId) {
            event.preventDefault();

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
                    document.getElementById(`delete-form-${departmentId}`).submit();
                }
            });
        }
    </script>
@endsection