@extends('admin.layouts.layout')
@section('title', 'Proprietors List')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            <div class="d-flex justify-content-between user-access">
                <div class="user-welcome">
                    <h3>Proprietor</h3>
                </div>
                <a href="{{ route('proprietor.create') }}" class="btn btn-primary"><i class="fa fa-solid fa-plus"></i> Add
                    New proprietor</a>
            </div>
            <form method="GET" action="{{ route('proprietor.list') }}" class="mb-3">
                <div class="input-group">
                    <input type="text" name="first_name" class="form-control" placeholder="Search by First Name"
                        value="{{ request('first_name') }}">&nbsp;
                    <input type="text" name="last_name" class="form-control" placeholder="Search by Last Name"
                        value="{{ request('last_name') }}">&nbsp;
                    <input type="text" name="email" class="form-control" placeholder="Search by Email"
                        value="{{ request('email') }}">&nbsp;
                    <button type="submit" class="btn btn-info">Search</button>
                </div>
            </form>
            <div class="users-datatable mt-3 p-3">
                <div class="row">
                    <div class="col">
                        <div class="users-table">
                            <div class="table-responsive">
                                <table id="usersTable" class="table w-100">
                                    <thead>
                                        <tr>
                                            <th><b>S.N.</b></th>
                                            <th><b>First Name</b></th>
                                            <th><b>Last Name</b></th>
                                            <th><b>Mobile</b></th>
                                            <th><b>Email</b></th>
                                            <th><b>Status</b></th>
                                            <th><b>Action</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $key => $item)
                                            <tr>
                                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                                <td>{{ $item->first_name }}</td>
                                                <td>{{ $item->last_name }}</td>
                                                <td>{{ $item->mobile }}</td>
                                                <td>{{ $item->email }}</td>
                                                <td>
                                                    <span class="status-toggle {{ $item->status == 1 ? 'text-success' : 'text-danger' }}" 
                                                          data-id="{{ $item->id }}" 
                                                          style="cursor: pointer;">
                                                        {{ $item->status == 1 ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('proprietor.show', $item->id) }}" class="view">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a class="edit" href="{{ route('proprietor.edit', $item->id) }}"><i class="fa fa-solid fa-pen"></i></a>
                                                    {{-- <form method="POST" style="display:inline;" action="{{ route('proprietor.destroy', $item->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="text-primary" style="all:unset; border:none; background:none;" onclick="confirmDelete()">
                                                            <i class="fa fa-solid fa-trash"></i>
                                                        </button>
                                                    </form> --}}
                                                    <form method="POST" style="display:inline;" action="{{ route('proprietor.destroy', $item->id) }}" id="delete-form-{{ $item->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="text-primary" style="all:unset; border:none; background:none;" onclick="confirmDelete(event, {{ $item->id }})">
                                                            <i class="fa fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $data->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                    <!-- Tab End -->

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    function confirmDelete(event, itemId) {
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
                document.getElementById(`delete-form-${itemId}`).submit();
            }
        });
    }
</script>
<script>
    $(document).ready(function () {
    $('.status-toggle').on('click', function () {
        const id = $(this).data('id');
        const $this = $(this);

        $.ajax({
            url: `/admin/proprietor/status/${id}`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                if (response.success) {
                    $this.text(response.new_status === 1 ? 'Active' : 'Inactive')
                         .toggleClass('text-success', response.new_status === 1)
                         .toggleClass('text-danger', response.new_status === 0);
                } else {
                    alert('Failed to update status.');
                }
            },
            error: function (xhr) {
                console.error('Error:', xhr.responseText);
                alert('An error occurred while updating the status.');
            }
        });
    });
});
</script>
@endsection
