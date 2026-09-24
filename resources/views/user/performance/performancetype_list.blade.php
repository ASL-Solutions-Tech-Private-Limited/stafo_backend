@extends('user.layouts.app')
@section('title', 'Performance KPI Types | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Performance KPI Types</h3>
                <p class="text-muted small mb-0">Define evaluation metrics, productivity benchmarks, and appraisal parameters</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('performancetypeCreate') }}" class="btn btn-primary px-3 py-2">
                    <i class="fa-solid fa-plus me-1"></i> Create Performance Type
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 70px;" class="text-center">S.No</th>
                        <th style="width: 280px;">KPI Name</th>
                        <th>Description</th>
                        <th style="width: 140px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($performancetypes as $index => $performancetype)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-chart-line"></i>
                                    </div>
                                    <span class="fw-bold text-dark">{{ $performancetype->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ $performancetype->description ?: '—' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('performancetypeEdit', $performancetype->id) }}" 
                                       class="btn btn-sm btn-outline-warning p-0" 
                                       style="width: 32px; height: 32px; border-radius: 8px;"
                                       title="Edit KPI">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger p-0" 
                                            style="width: 32px; height: 32px; border-radius: 8px;"
                                            title="Delete KPI"
                                            onclick="confirmDelete(event, {{ $performancetype->id }})">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                    <form id="delete-form-{{ $performancetype->id }}"
                                        action="{{ route('performancetypeDelete', $performancetype->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')  
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-chart-line fs-2 mb-2 d-block opacity-50"></i>
                                No performance types defined yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    function confirmDelete(event, Id) {
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
                document.getElementById(`delete-form-${Id}`).submit();
            }
        });
    }
</script>
@endsection