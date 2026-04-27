@extends('admin.layouts.layout')

@section('title', 'Packages management')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-4">
                <h3>Packages</h3>
            </div>
            <div class="col-sm-6 col-8 text-end">
                <a href="{{ route('packages.create') }}" class="btn btn-primary">Add
                    New
                    Package</a>
            </div>
        </div>


        <div class="table-responsive table-same">
            <table class="table mt-3 table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Package Name</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packages as $index => $package)
                        <tr>
                            <td>{{ $packages->firstItem() + $index }}</td>
                            <td>{{ $package->package_name }}</td>
                            <td>₹{{ $package->price }}</td>
                            <td>{{ ucfirst($package->status) }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('packages.show', $package->id) }}"><i class="fa fa-eye"></i></a>
                                    <a href="{{ route('packages.edit', $package->id) }}"><i
                                            class="fa fa-solid fa-pen"></i></a>
                                    <form action="{{ route('packages.destroy', $package->id) }}" method="POST"
                                        style="display:inline;" id="delete-form-{{ $package->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button style="all:unset; border:none; background:none;"
                                            onclick="confirmDelete(event, {{ $package->id }})"><i
                                                class="fa fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function confirmDelete(event, packageId) {
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
                    document.getElementById(`delete-form-${packageId}`).submit();
                }
            });
        }
    </script>
@endsection
