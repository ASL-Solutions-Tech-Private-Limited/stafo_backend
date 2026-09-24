@extends('user.layouts.app')
@section('title', 'Branch List | STAFO HRMS')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            
            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Branches</h3>
                    <p class="text-muted small mb-0">Manage company branch offices and operational locations</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('branche.create') }}" class="btn btn-primary px-3 py-2">
                        <i class="fa-solid fa-plus me-1"></i> Add Branch
                    </a>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 70px;" class="text-center">S.No</th>
                            <th style="width: 220px;">Branch Name</th>
                            <th>Address</th>
                            <th style="width: 130px;" class="text-center">Status</th>
                            <th style="width: 120px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($branches as $index => $branch)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                            <i class="fa-solid fa-building"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $branch->branch_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary">
                                        <i class="fa-solid fa-location-dot me-1 text-muted"></i>
                                        {{ $branch->branch_address ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($branch->status == 1)
                                        <span class="badge-stafo badge-stafo-success">
                                            <i class="fa-solid fa-circle-check"></i> Active
                                        </span>
                                    @else
                                        <span class="badge-stafo badge-stafo-danger">
                                            <i class="fa-solid fa-circle-xmark"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('branche.edit', $branch->id) }}" class="btn btn-sm btn-outline-warning p-0" title="Edit Branch" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Branch" onclick="confirmDelete(event, {{ $branch->id }})" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        <form id="delete-form-{{ $branch->id }}" action="{{ route('branche.destroy', $branch->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-building-circle-xmark fs-2 mb-2 d-block opacity-50"></i>
                                    No branches found. Click <strong>Add Branch</strong> to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        function confirmDelete(event, branchId) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${branchId}`).submit();
                }
            });
        }
    </script>
@endsection