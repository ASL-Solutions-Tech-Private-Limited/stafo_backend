@extends('admin.layouts.layout')

@section('title', 'Branches Management')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Branches Lists</h3>
            <a href="{{ route('branches.create') }}" class="btn btn-primary">Create Branch</a>
        </div>
        <form method="GET" action="{{ route('branches.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <select name="company_id" id="company_id" class="form-control">
                        <option value="">Select Company</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" {{ request()->get('company_id') == $company->id ? 'selected' : ''
                                                                                                                }}>
                                {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <select name="branch_id" id="branch_id" class="form-control">
                        <option value="">Select Branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request()->get('branch_id') == $branch->id ? 'selected' : ''
                                                                                                                }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 text-end mb-3">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('branches.index') }}" class="btn btn-danger">Reset</a> <!-- Reset Button -->

                </div>

            </div>
        </form>

        <div class="table-responsive table-same">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Branch Name</th>

                        <th>Branch Address</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $branch->company->company_name }}</td>
                            <td>{{ $branch->branch_name }}</td>
                            <td>{{ $branch->branch_address }}</td>
                            <td>{{ $branch->status ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('branches.edit', $branch->id) }}"><i class="fa fa-solid fa-pen"></i></a>
                                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST"
                                        style="display:inline;" id="delete-form-{{ $branch->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="prop-none" onclick="confirmDelete(event, {{ $branch->id }})"><i
                                                class="fa fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No branches found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $branches->links('pagination::bootstrap-4') }} <!-- You can use any pagination style -->
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function confirmDelete(event, branchId) {
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
                    document.getElementById(`delete-form-${branchId}`).submit();
                }
            });
        }
    </script>

    <script>
        document.getElementById('company_id').addEventListener('change', function () {
            let companyId = this.value;
            fetch(`branches/get-branches/${companyId}`)
                .then(response => response.json())
                .then(data => {
                    let branchSelect = document.getElementById('branch_id');
                    branchSelect.innerHTML = '<option value="">Select Branch</option>';

                    data.branches.forEach(function (branch) {
                        let option = document.createElement('option');
                        option.value = branch.id;
                        option.textContent = branch.branch_name;
                        branchSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching branches:', error));
        });
    </script>
@endsection