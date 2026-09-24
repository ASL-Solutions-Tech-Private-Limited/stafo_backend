@extends('user.layouts.app')
@section('title', 'Salary Type Packages | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Salary Packages</h3>
                <p class="text-muted small mb-0">Create and manage pre-configured compensation bundles for employee designations</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('salary-package-type.create') }}" class="btn btn-primary px-3 py-2">
                    <i class="fa-solid fa-plus me-1"></i> Create Package
                </a>
            </div>
        </div>

        @if($packages->isEmpty())
            <div class="text-center py-5 bg-light rounded-4 border">
                <i class="fa-solid fa-box-open fs-1 text-muted opacity-50 mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">No Packages Found</h5>
                <p class="text-muted small">Click on the button below to configure your first salary package.</p>
                <a href="{{ route('salary-package-type.create') }}" class="btn btn-primary mt-2">
                    <i class="fa-solid fa-plus me-1"></i> Create Package
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 60px;" class="text-center">S.No</th>
                            <th style="width: 220px;">Package Name</th>
                            <th>Description</th>
                            <th style="width: 150px;">Tier Type</th>
                            <th style="width: 120px;" class="text-center">Status</th>
                            <th style="width: 170px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($packages as $index => $package)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $packages->firstItem() + $index ?? $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                            <i class="fa-solid fa-box"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('salary-package-type.show', $package->id) }}" class="text-dark fw-bold text-decoration-none d-block">
                                                {{ $package->package_name }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ Str::limit($package->package_description, 60) ?: '—' }}</span>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'Basic' => 'badge-stafo-info',
                                            'Standard' => 'badge-stafo-primary',
                                            'Premium' => 'badge-stafo-warning',
                                            'Custom' => 'badge-stafo-success'
                                        ];
                                        $typeBadge = $typeColors[$package->package_type] ?? 'badge-stafo-info';
                                    @endphp
                                    <span class="badge-stafo {{ $typeBadge }}">
                                        <i class="{{ $package->package_type_icon ?? 'fa-solid fa-layer-group' }} me-1"></i>
                                        {{ $package->package_type }}
                                    </span>
                                </td>
                                
                                <td class="text-center">
                                    @if(strtolower($package->status) == 'active')
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
                                        <a href="{{ route('salary-package-type.show', $package->id) }}" 
                                           class="btn btn-sm btn-outline-info p-0" 
                                           style="width: 32px; height: 32px; border-radius: 8px;"
                                           title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('salary-package-type.edit', $package->id) }}" 
                                           class="btn btn-sm btn-outline-warning p-0" 
                                           style="width: 32px; height: 32px; border-radius: 8px;"
                                           title="Edit Package">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        
                                        <a href="{{ route('salarytype.create', ['package_id' => $package->id]) }}" 
                                           class="btn btn-sm btn-outline-success p-0" 
                                           style="width: 32px; height: 32px; border-radius: 8px;"
                                           title="Add Salary Type">
                                            <i class="fa-solid fa-plus"></i>
                                        </a>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary p-0 toggle-status"
                                                data-id="{{ $package->id }}"
                                                data-status="{{ $package->status }}"
                                                style="width: 32px; height: 32px; border-radius: 8px;"
                                                title="{{ $package->status == 'Active' ? 'Deactivate' : 'Activate' }}">
                                            <i class="fa-solid fa-{{ $package->status == 'Active' ? 'ban' : 'check' }}"></i>
                                        </button>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger p-0 delete-btn"
                                                data-id="{{ $package->id }}"
                                                data-name="{{ $package->package_name }}"
                                                style="width: 32px; height: 32px; border-radius: 8px;"
                                                title="Delete Package">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        <form id="delete-form-{{ $package->id }}"
                                            action="{{ route('salary-package-type.destroy', $package->id) }}" 
                                            method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-box-open fs-2 mb-2 d-block opacity-50"></i>
                                    No records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($packages, 'links') && $packages->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                    <small class="text-muted">Showing {{ $packages->firstItem() }} to {{ $packages->lastItem() }} of {{ $packages->total() }} packages</small>
                    <div>{{ $packages->links('pagination::bootstrap-4') }}</div>
                </div>
            @endif
        @endif
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const packageId = this.dataset.id;
            const currentStatus = this.dataset.status;
            const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to ${newStatus.toLowerCase()} this package?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: `Yes, ${newStatus} it!`
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('company/salary-package-type') }}/${packageId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Updated!', data.message, 'success');
                            setTimeout(() => location.reload(), 1200);
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Something went wrong!', 'error');
                    });
                }
            });
        });
    });

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const packageId = this.dataset.id;
            const packageName = this.dataset.name;
            
            Swal.fire({
                title: 'Are you sure?',
                text: `You won't be able to revert "${packageName}" package!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${packageId}`).submit();
                }
            });
        });
    });
</script>
@endsection