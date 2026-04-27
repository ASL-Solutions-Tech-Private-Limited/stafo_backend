@extends('user.layouts.app')

@section('title', 'Shift List')

@section('content')
    <div class="card mt-4 p-4 shadow-sm border-0">
        <div>
            <div class="row ">
                <div class="col-md-9 ">
                    <h2 class="fw-bold">Shifts List</h2>
                </div>
                <div class="col-md-3  text-end">
                    <a href="{{ route('shifts.create') }}" class="btn btn-success  shadow-sm"><i class="fas fa-plus"></i>
                        Create</a>
                </div>
            </div>
            <!-- Filter Form -->
            <form method="GET" action="{{ route('shifts.index') }}" class="d-flex mb-3 mt-3 row g-2">
                @csrf
                <div class="form-group  col-md-3 col-sm-6">
                    <input type="text" name="shift_name" class="form-control " value="{{ request()->get('shift_name') }}"
                        placeholder="Shift Name" style="border-radius: 5px; padding: 10px;">
                </div>
                <div class="form-group  col-md-3 col-sm-6 ">
                    <input type="text" name="start_time" class="form-control " value="{{ request()->get('start_time') }}"
                        placeholder="Start Time" style="border-radius: 5px; padding: 10px;">
                </div>
                <div class="form-group  col-md-3 col-sm-6 ">
                    <input type="text" name="end_time" class="form-control " value="{{ request()->get('end_time') }}"
                        placeholder="End Time" style="border-radius: 5px; padding: 10px;">
                </div>

                <div class="d-flex gap-3 justify-content-end col-md-3 col-sm-6 ">
                    <button type="submit" class="btn btn-primary "><i class="fas fa-search "></i> Search</button>

                    <!-- <a href="{{ route('shifts.index') }}" class="btn btn-secondary shadow-sm"
                                                                                                                                                                                                                                                                style="border-radius: 5px; padding: 10px 20px;">Reset</a> -->
                </div>
            </form>
            <!-- Shifts Table -->
            <div class="table-responsive table-same">
                <table class="table table-striped table-bordered table-hover mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>S.no</th>
                            <th>Shift Name</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shifts as $index => $shift)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $shift->shift_name }}</td>
                                <td>{{ $shift->start_time }}</td>
                                <td>{{ $shift->end_time }}</td>
                                <td>
                                    <a href="{{ route('shifts.show', $shift->id) }}" class="btn btn-info btn-sm"><i
                                            class="fas fa-eye"></i></a>
                                    <a href="{{ route('shifts.edit', $shift->id) }}" class="btn btn-warning btn-sm"><i
                                            class="fas fa-edit"></i></a>
                                    <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST"
                                        style="display:inline;" id="delete-form-{{ $shift->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete(event, {{ $shift->id }})"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{--
    <script>
        function confirmDelete(event, shiftId) {
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
                    document.getElementById(`delete-form-${shiftId}`).submit();
                }
            });
        }
    </script> --}}
@endsection