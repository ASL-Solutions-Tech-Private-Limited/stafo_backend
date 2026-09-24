@extends('user.layouts.app')
@section('title', 'Salary Components | STAFO HRMS')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Salary Components</h3>
                    <p class="text-muted small mb-0">Manage department-wise earnings, allowances, and deduction structures</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('salarytype.create') }}" class="btn btn-primary px-3 py-2">
                        <i class="fa-solid fa-plus me-1"></i> Add Component
                    </a>
                </div>
            </div>

            @php
                $groupedByDepartment = $salarytypes->groupBy(function($item) {
                    return $item->department ? $item->department->id : 'no_department';
                });
            @endphp

            <div class="accordion d-flex flex-column gap-3" id="departmentAccordion">
                @foreach($groupedByDepartment as $departmentId => $departmentSalaryTypes)
                    @php
                        $department = $departmentSalaryTypes->first()->department;
                        $departmentName = $department ? $department->name : 'General (No Department)';
                        $loopIndex = $loop->index;
                    @endphp

                    <div class="accordion-item rounded-3 overflow-hidden border shadow-sm" style="border-color: #e2e8f0 !important;">
                        <h2 class="accordion-header" id="heading{{ $loopIndex }}">
                            <button class="accordion-button {{ $loopIndex != 0 ? 'collapsed' : '' }} bg-light p-3" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $loopIndex }}" 
                                    aria-expanded="{{ $loopIndex == 0 ? 'true' : 'false' }}" 
                                    aria-controls="collapse{{ $loopIndex }}"
                                    style="box-shadow: none;">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100 me-3 gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            <i class="fa-solid fa-building"></i>
                                        </div>
                                        <strong class="text-dark fs-6">{{ $departmentName }}</strong>
                                        @if($department)
                                            <span class="badge-stafo {{ $department->status == 1 ? 'badge-stafo-success' : 'badge-stafo-danger' }}">
                                                {{ $department->status == 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge-stafo badge-stafo-success">
                                            <i class="fa-solid fa-plus-circle"></i> 
                                            Earnings: {{ $departmentSalaryTypes->where('payment_type', 'Earning')->count() }}
                                        </span>
                                        <span class="badge-stafo badge-stafo-danger">
                                            <i class="fa-solid fa-minus-circle"></i> 
                                            Deductions: {{ $departmentSalaryTypes->where('payment_type', 'Deduction')->count() }}
                                        </span>
                                        <span class="badge-stafo badge-stafo-primary">
                                            <i class="fa-solid fa-layer-group"></i> 
                                            Total: {{ $departmentSalaryTypes->count() }}
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $loopIndex }}" 
                             class="accordion-collapse collapse {{ $loopIndex == 0 ? 'show' : '' }}" 
                             aria-labelledby="heading{{ $loopIndex }}" 
                             data-bs-parent="#departmentAccordion">
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 60px;" class="text-center">S.No</th>
                                                <th style="width: 140px;">Type</th>
                                                <th style="width: 220px;">Salary Component</th>
                                                <th>Description</th>
                                                <th style="width: 130px;">Amount</th>
                                                <th style="width: 130px;">Rate Type</th>
                                                <th style="width: 110px;" class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($departmentSalaryTypes as $index => $salarytype)
                                                <tr>
                                                    <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                                    <td>
                                                        @if($salarytype->payment_type == 'Earning')
                                                            <span class="badge-stafo badge-stafo-success">
                                                                <i class="fa-solid fa-arrow-trend-up"></i> Earning
                                                            </span>
                                                        @else
                                                            <span class="badge-stafo badge-stafo-danger">
                                                                <i class="fa-solid fa-arrow-trend-down"></i> Deduction
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold text-dark">{{ $salarytype->salary_type }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted">{{ $salarytype->salary_type_description ?: '—' }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold text-dark">
                                                            @if($salarytype->amount_type == 'Percentage')
                                                                {{ $salarytype->amount }}%
                                                            @else
                                                                ₹{{ number_format((float)$salarytype->amount, 2) }}
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge-stafo {{ $salarytype->amount_type == 'Percentage' ? 'badge-stafo-info' : 'badge-stafo-primary' }}">
                                                            {{ $salarytype->amount_type }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                                            <a href="{{ route('salarytype.edit', $salarytype->id) }}" 
                                                               class="btn btn-sm btn-outline-warning p-0" title="Edit Component" style="width: 32px; height: 32px; border-radius: 8px;">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </a>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger p-0"
                                                                    onclick="confirmDelete(event, {{ $salarytype->id }})"
                                                                    title="Delete Component" style="width: 32px; height: 32px; border-radius: 8px;">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                            <form id="delete-form-{{ $salarytype->id }}"
                                                                action="{{ route('salarytype.destroy', $salarytype->id) }}" 
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-4 text-muted">No salary components found for this department.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($salarytypes->isEmpty())
                <div class="text-center py-5">
                    <i class="fa-solid fa-file-invoice-dollar fs-1 text-muted opacity-50 mb-3 d-block"></i>
                    <h5 class="text-dark fw-bold">No Salary Components Defined</h5>
                    <p class="text-muted">Click "Add Component" to define salary structures for departments.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function confirmDelete(event, salarytypeId) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this salary component!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${salarytypeId}`).submit();
                }
            });
        }
    </script>
@endsection