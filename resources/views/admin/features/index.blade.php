@extends('admin.layouts.layout')

@section('title', 'Features Management')
@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col-sm-6 col-5">
                <h3>Features</h3>
            </div>
            <div class="col-sm-6 col-7 text-end"> <a href="{{ route('features.create') }}" class="btn btn-primary "><i
                        class="fa fa-solid fa-plus me-1"></i>Add New Feature</a></div>
        </div>




        <!-- Styling the table with a cleaner design -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Feature Name</th>
                        <th>Description</th>
                        {{-- <th>Icon</th> --}}
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($features as $index => $feature)
                        <tr>
                            <td>{{ $features->firstItem() + $index }}</td>
                            <td>{{ $feature->name }}</td>
                            <td>{!! strip_tags($feature->description) !!}</td>
                            {{-- <td>
                                <img src="{{ asset('storage/icons/' . $feature->icon) }}" alt="{{ $feature->name }}" width="30"
                                    class="rounded-circle">
                            </td> --}}
                            <td>
                                <!-- Status dropdown for changing the status -->
                                <select class="form-select form-select-sm status-toggle" data-id="{{ $feature->id }}"
                                    data-status="{{ $feature->status }}">
                                    <option value="1" {{ $feature->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $feature->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('features.show', $feature->id) }}"><i class="fa fa-eye"></i></a>
                                    <a href="{{ route('features.edit', $feature->id) }}"><i
                                            class="fa fa-solid fa-pen"></i></a>
                                    <form action="{{ route('features.destroy', $feature->id) }}" method="POST"
                                        style="display:inline;" id="delete-form-{{ $feature->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <!-- <button style="all:unset; border:none; background:none;"
                                            onclick="confirmDelete(event, {{ $feature->id }})"><i
                                                class="fa fa-solid fa-trash"></i></button> -->
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
        function confirmDelete(event, featureId) {
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
                    document.getElementById(`delete-form-${featureId}`).submit();
                }
            });
        }

        // Handle status change using onchange event
        document.addEventListener('DOMContentLoaded', function() {
            const statusElements = document.querySelectorAll('.status-toggle');

            statusElements.forEach(function(element) {
                element.addEventListener('change', function() {
                    const featureId = element.getAttribute('data-id');
                    const newStatus = element.value;

                    // Send AJAX request to update the status
                    fetch(`features/${featureId}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Successfully updated, you can also show a success message if needed
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Status Updated',
                                    text: `The feature status is now ${data.status}.`
                                });
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
@endsection
