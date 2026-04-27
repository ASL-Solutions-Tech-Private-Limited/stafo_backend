@extends('user.layouts.app')

@section('title', 'Salary Setting List')
@section('content')

    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h3>Salary Settings</h3>
            <!-- <a href="{{ route('grace_settings.create') }}" class="btn btn-primary">Add Setting</a> -->
        </div>

        <div class="table-responsive table-same">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($settings as $index => $setting)
                        <tr>
                            <td>{{ $settings->firstItem() + $index }}</td>

                            <td>{{ $setting->name }}</td>
                            <td>{{ $setting->value }}</td>
                            <td class="actions">
                                <a href="{{ route('grace_settings.edit', $setting->id) }}"><i
                                        class="fa fa-solid fa-pen"></i></a>
                                <form action="{{ route('grace_settings.destroy', $setting->id) }}" method="POST"
                                    style="display:inline;" id="delete-form-{{ $setting->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <!-- <button class="prop-none" onclick="confirmDelete(event, {{ $setting->id }})"><i
                                                        class="fa fa-solid fa-trash"></i></button> -->
                                </form>
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
        function confirmDelete(event, settingId) {
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
                    document.getElementById(`delete-form-${settingId}`).submit();
                }
            });
        }
    </script>
@endsection
