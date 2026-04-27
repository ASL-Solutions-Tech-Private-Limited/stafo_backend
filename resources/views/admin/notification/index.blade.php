@extends('admin.layouts.layout')

@section('title', 'Notification List')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-2">
                <h3>Business Types</h3>
            </div>
            <div class="col-md-6 mb-2 text-end">
                <a href="{{ route('businessTypes.create') }}" class="btn btn-primary">Create New Business Type</a>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('businessTypes.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <!-- Business Name Filter -->
                    <input type="text" name="business_name" class="form-control" placeholder="Search by Business Name"
                        value="{{ request('business_name') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <!-- Status Filter -->
                    <select name="status" class="form-control">
                        <option value="">Select Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3 text-end">
                    <!-- Search Button -->
                    <button type="submit" class="btn btn-info">Search</button>
                    <a href="{{ route('businessTypes.index') }}" class="btn btn-danger">Reset</a>
                </div>

            </div>
        </form>
        <div class="table-responsive table-same">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Business Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($businessTypes as $index => $businessType)
                        <tr>
                            <td>{{ $businessTypes->firstItem() + $index }}</td>
                            <td>{{ $businessType->business_name }}</td>
                            <td>{{ $businessType->status ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('businessTypes.edit', $businessType->id) }}"><i
                                            class="fa fa-solid fa-pen"></i></a>

                                    <!-- Modified the form to trigger confirmDelete function -->
                                    <form id="delete-form-{{ $businessType->id }}"
                                        action="{{ route('businessTypes.destroy', $businessType->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="prop-none" onclick="confirmDelete(event, {{ $businessType->id }})"><i
                                                class="fa fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-4">
            {{ $businessTypes->links('pagination::bootstrap-4') }} <!-- You can use any pagination style -->
        </div>

    </div>

@endsection

@section('scripts')
    <!-- SweetAlert2 library -->


    <script>
        function confirmDelete(event, itemId) {
            event.preventDefault();

            // SweetAlert2 confirmation popup
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
                    // If confirmed, submit the form
                    document.getElementById(`delete-form-${itemId}`).submit();
                }
            });
        }
    </script>
@endsection