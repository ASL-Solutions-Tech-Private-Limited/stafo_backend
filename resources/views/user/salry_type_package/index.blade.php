@extends('user.layouts.app')
@section('title', 'Salary Type Packages')

@section('content')
@include('user.layouts.alert')
<div class="card mt-4 p-3 shadow-sm border-0">
    <div>
        <div class="row mb-3">
            <div class="col-md-9 col-6">
                <h2 class="fw-bold">
                    <i class="fas fa-box me-2" style="color: #2c6e9e;"></i>Salary Type Packages
                </h2>
                <p class="text-muted small mb-0">Manage and monitor salary packages</p>
            </div>
            <div class="col-md-3 col-6 text-end">
                <a href="{{ route('salary-package-type.create') }}" class="btn btn-success shadow-sm">
                    <i class="fas fa-plus"></i> Create Package
                </a>
            </div>
        </div>

        @if($packages->isEmpty())
            <div class="alert alert-info text-center py-5" style="border-radius: 1rem;">
                <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                <h5>No Packages Found</h5>
                <p>Click on "Create Package" button to add your first salary package.</p>
                <a href="{{ route('salary-package-type.create') }}" class="btn btn-primary rounded-pill mt-2">
                    <i class="fas fa-plus"></i> Create Package
                </a>
            </div>
        @else
            <div class="table-responsive table-same">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th><i class="fas fa-tag"></i> Package Name</th>
                            <th><i class="fas fa-align-left"></i> Description</th>
                            <th><i class="fas fa-layer-group"></i> Package Type</th>
                            <th><i class="fas fa-circle"></i> Status</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($packages as $index => $package)
                            <tr>
                                <td>{{ $packages->firstItem() + $index ?? $index + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $package->package_name }}</div>
                                   
                                 </div>
                                </td>
                                <td>
                                    {{ Str::limit($package->package_description, 50) ?: '—' }}
                                 </div>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'Basic' => 'info',
                                            'Standard' => 'primary',
                                            'Premium' => 'warning',
                                            'Custom' => 'secondary'
                                        ];
                                        $typeColor = $typeColors[$package->package_type] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $typeColor }} rounded-pill px-3 py-2">
                                        <i class="{{ $package->package_type_icon }} me-1"></i>
                                        {{ $package->package_type }}
                                    </span>
                                 </div>
                                </td>
                               
                                <td>
                                    {!! $package->status_badge !!}
                                 </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <!-- View Button -->
                                        <a href="{{ route('salary-package-type.show', $package->id) }}" 
                                           class="btn btn-info btn-sm rounded-circle" 
                                           style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Edit Button -->
                                        <a href="{{ route('salary-package-type.edit', $package->id) }}" 
                                           class="btn btn-warning btn-sm rounded-circle" 
                                           style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                           title="Edit Package">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- Add Salary Type Button -->
                                        <a href="{{ route('salarytype.create', ['package_id' => $package->id]) }}" 
                                           class="btn btn-success btn-sm rounded-circle" 
                                           style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                           title="Add Salary Type">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        
                                        <!-- Toggle Status Button -->
                                        <button type="button" 
                                                class="btn btn-{{ $package->status == 'Active' ? 'secondary' : 'success' }} btn-sm rounded-circle toggle-status"
                                                data-id="{{ $package->id }}"
                                                data-status="{{ $package->status }}"
                                                style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                                title="{{ $package->status == 'Active' ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $package->status == 'Active' ? 'ban' : 'check' }}"></i>
                                        </button>
                                        
                                        <!-- Delete Button -->
                                        <form id="delete-form-{{ $package->id }}"
                                            action="{{ route('salary-package-type.destroy', $package->id) }}" 
                                            method="POST"
                                            style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm rounded-circle delete-btn"
                                                    data-id="{{ $package->id }}"
                                                    data-name="{{ $package->package_name }}"
                                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                                    title="Delete Package">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                 </div>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                    No records found.
                                 </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($packages, 'links'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $packages->links('pagination::bootstrap-4') }}
                </div>
            @endif
        @endif
    </div>
</div>

<script>
    // Toggle Status
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const packageId = this.dataset.id;
            const currentStatus = this.dataset.status;
            const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to ${newStatus} this package?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `Yes, ${newStatus} it!`
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('salary-package-type') }}/${packageId}/toggle-status`, {
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
                            setTimeout(() => location.reload(), 1500);
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Something went wrong!', 'error');
                    });
                }
            });
        });
    });

    // Delete Confirmation
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const packageId = this.dataset.id;
            const packageName = this.dataset.name;
            
            Swal.fire({
                title: 'Are you sure?',
                text: `You won't be able to revert "${packageName}" package!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
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