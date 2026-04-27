@extends('user.layouts.app')
@section('title', 'Expenseform List') <!-- Set your custom title here -->
@section('css')
@endsection

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">

        <div class="row mb-3">
            <div class="col-md-9 col-6">
                <h2 class="fw-bold">Expenseform</h2>
            </div>
            <div class="col-md-3 col-6 text-end">
                <a href="{{ route('expenseformCreate') }}" class="btn btn-success shadow-sm"><i class="fas fa-plus"></i>
                    Create</a>
            </div>
        </div>
       @include('user.layouts.alert')
        <!-- <form method="GET" action="{{ route('expenseformList') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Search by Name"
                        value="{{ request()->get('name') }}">
                </div>
                <div class="col-md-4 text-end">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search "></i>
                        Search</button>
                </div>
            </div>
        </form> -->

        <!-- Reimbursement Table -->
        <div class="table-responsive">
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

                    @foreach ($expenseforms as $index => $form)
                        <tr>
                            <td>{{ $expenseforms->firstItem() + $index }}</td>
                            <td>{{ $form->name }}</td>
                            <td>{{ $form->description }}</td>
                            <td>
                                {{ $form->status}}
                            </td>

                            <!-- Actions -->
                            <td>
                                <a href="{{ route('expenseformDetails', $form->id) }}" class="btn btn-info btn-sm"><i
                                        class="fas fa-eye"></i></a>
                                <a href="{{ route('expenseformEdit', $form->id) }}" class="btn btn-warning btn-sm"><i
                                        class="fas fa-edit"></i></a>
                                <form action="{{ route('expenseformDelete', $form->id) }}" method="POST"
                                    style="display:inline;" id="delete-form-{{ $form->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="confirmDelete(event, {{ $form->id }})"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $expenseforms->links('pagination::bootstrap-4') }} <!-- You can use any pagination style -->
        </div>
    </div>

    {{-- <script>
        // Toggle status on click
        document.querySelectorAll('.clickable-status').forEach(status => {
            status.addEventListener('click', function () {
                let reimbursementId = this.getAttribute('data-id');
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('CSRF token not found.');
                    return;
                }
                let toggleStatusUrl = '{{ route('reimbursements.toggleStatus', ':id') }}';
                toggleStatusUrl = toggleStatusUrl.replace(':id', reimbursementId);

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
                            let statusElement = document.getElementById('status-' + reimbursementId);
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
        function confirmDelete(event, reimbursementId) {
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
                    document.getElementById(`delete-form-${reimbursementId}`).submit();
                }
            });
        }
    </script>
@endsection