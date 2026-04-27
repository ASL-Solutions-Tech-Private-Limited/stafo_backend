@extends('admin.layouts.layout')
@section('title', 'User List')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            <div class="d-flex justify-content-between user-access">
                <div class="user-welcome">
                    <h3>Cms</h3>
                </div>
                <a href="{{ route('cms.create') }}" class="btn d-flex align-items-center btn-primary"> Add
                    New Cms</a>
            </div>
            <div class="users-datatable mt-3">
                <div class="row">
                    <div class="col">
                        <div class="users-table">
                            <div class="table-responsive table-same">
                                <table id="usersTable" class="table w-100 table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            {{-- <th><input type="checkbox" name="" id=""></th> --}}
                                            <th>Title</th>
                                            <th>Image</th>
                                            <th>Short Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $key => $item)
                                            <tr>
                                                {{-- <td><input type="checkbox" name="" id=""></td> --}}
                                                <td>{{ $item->title }}</td>
                                                <td>
                                                    @if ($item->image)
                                                        <div>
                                                            <img src="{{ asset('uploads/cms/' . $item->image) }}"
                                                                alt="Current Image" width="50">
                                                        </div>
                                                    @endif
                                                    {{-- {{ $item->image }} --}}

                                                </td>
                                                <td>{{ $item->short_description }}</td>
                                                <td>
                                                    <div class="actions">
                                                        <a class="edit" href="{{ route('cms.edit', $item->id) }}"><i
                                                                class="fa fa-solid fa-pen"></i></a>
                                                        <form method="POST" style="all:unset;"
                                                            action="{{ route('cms.destroy', $item->id) }}">
                                                            @csrf

                                                            <input name="_method" type="hidden" value="DELETE">
                                                            <button class="delete " style="all:unset;"
                                                                onclick="confirmDelete()"><i
                                                                    class="fa fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                <div class=" d-flex justify-content-center">
                                    <ul class="pagination">{{ $data->links() }}</ul>
                                </div>
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
        function confirmDelete() {
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
                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    );
                }
            });
        }
    </script>
@endsection
