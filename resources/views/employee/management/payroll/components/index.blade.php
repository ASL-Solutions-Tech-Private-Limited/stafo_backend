@extends('employee.layouts.app')

@section('title', 'Salary Components | Management Portal')

@section('content')
<div class="container-fluid p-0">

    <!-- Top Action & Navigation Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                    <i class="fa-solid fa-sliders me-1"></i> Management Portal
                </span>
                <span class="text-muted small">• Compensation Architecture</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Salary Components</h3>
            <p class="text-muted small mb-0">Manage department-wise earnings, allowances, and deduction structures.</p>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">
            <a href="{{ route('employee.management.payroll.records') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold">
                <i class="fa-solid fa-table-list me-1"></i> Payroll Register
            </a>
            @if(Auth::guard('employee')->user()->hasPermission('payroll.create'))
            <a href="{{ route('employee.management.payroll.components.create') }}" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Add Component
            </a>
            @endif
        </div>
    </div>

    <!-- Sub-Module Pill Tabs -->
    <ul class="nav nav-pills gap-2 mb-4 bg-light p-2 rounded-4 border">
        <li class="nav-item">
            <a class="nav-link px-4 py-2 rounded-3 fw-semibold text-dark" href="{{ route('employee.management.payroll.records') }}">
                <i class="fa-solid fa-receipt me-2"></i> Monthly Salary Records
            </a>
        </li>
        @if(Auth::guard('employee')->user()->hasPermission('payroll.create'))
        <li class="nav-item">
            <a class="nav-link px-4 py-2 rounded-3 fw-semibold text-dark" href="{{ route('employee.management.payroll.generate') }}">
                <i class="fa-solid fa-calculator me-2"></i> Run / Generate Salary
            </a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link active px-4 py-2 rounded-3 fw-semibold" href="{{ route('employee.management.payroll.components') }}">
                <i class="fa-solid fa-sliders me-2"></i> Salary Components
            </a>
        </li>
    </ul>

    @php
        $groupedByDepartment = $salarytypes->groupBy(function($item) {
            return $item->department ? $item->department->id : 'no_department';
        });
    @endphp

    <div class="card shadow-sm border-0 rounded-4 mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="card-body p-4">
            
            <div class="accordion d-flex flex-column gap-3" id="departmentAccordion">
                @forelse($groupedByDepartment as $departmentId => $departmentSalaryTypes)
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
                                            <span class="badge {{ $department->status == 1 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} rounded-pill px-2.5 py-0.5" style="font-size: 0.72rem;">
                                                {{ $department->status == 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-circle-plus me-1"></i> Earnings: {{ $departmentSalaryTypes->where('payment_type', 'Earning')->count() }}
                                        </span>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-circle-minus me-1"></i> Deductions: {{ $departmentSalaryTypes->where('payment_type', 'Deduction')->count() }}
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
                                        <thead class="table-light">
                                            <tr class="text-muted small">
                                                <th class="ps-3 py-3" style="width: 50px;">#</th>
                                                <th class="py-3">Type</th>
                                                <th class="py-3">Component Name</th>
                                                <th class="py-3">Description</th>
                                                <th class="py-3">Amount / Calculation</th>
                                                <th class="py-3 text-center">Status</th>
                                                <th class="py-3 text-end pe-3">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($departmentSalaryTypes as $index => $salarytype)
                                                <tr>
                                                    <td class="ps-3 text-muted fw-semibold">{{ $index + 1 }}</td>
                                                    <td>
                                                        @if($salarytype->payment_type == 'Earning')
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold">
                                                                <i class="fa-solid fa-arrow-trend-up me-1"></i> Earning
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-semibold">
                                                                <i class="fa-solid fa-arrow-trend-down me-1"></i> Deduction
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong class="text-dark">{{ $salarytype->salary_type }}</strong>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted small">{{ Str::limit($salarytype->salary_type_description ?? 'No description provided', 50) }}</span>
                                                    </td>
                                                    <td>
                                                        @if($salarytype->amount_type == 'Flat')
                                                            <span class="fw-bold text-dark">₹{{ number_format($salarytype->amount, 2) }}</span>
                                                            <small class="text-muted d-block" style="font-size: 0.72rem;">Fixed Rate</small>
                                                        @else
                                                            <span class="fw-bold text-primary">{{ $salarytype->amount }}%</span>
                                                            <small class="text-muted d-block" style="font-size: 0.72rem;">Of Basic Salary</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge {{ $salarytype->status == 1 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} rounded-pill px-2.5 py-1">
                                                            {{ $salarytype->status == 1 ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <div class="d-inline-flex gap-1">
                                                            @if(Auth::guard('employee')->user()->hasPermission('payroll.edit'))
                                                            <a href="{{ route('employee.management.payroll.components.edit', $salarytype->id) }}" class="btn btn-sm btn-outline-primary px-2 py-1 rounded-2" title="Edit Component">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </a>
                                                            @endif
                                                            @if(Auth::guard('employee')->user()->hasPermission('payroll.delete'))
                                                            <form action="{{ route('employee.management.payroll.components.destroy', $salarytype->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this salary component?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 rounded-2" title="Delete Component">
                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                </button>
                                                            </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-sliders fs-1 opacity-50 mb-3 text-warning"></i>
                        <h5 class="fw-bold text-dark">No Salary Components Defined</h5>
                        <p class="small text-muted mb-3">Add earning and deduction components for company departments.</p>
                        @if(Auth::guard('employee')->user()->hasPermission('payroll.create'))
                        <a href="{{ route('employee.management.payroll.components.create') }}" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">
                            <i class="fa-solid fa-plus me-1"></i> Add First Component
                        </a>
                        @endif
                    </div>
                @endforelse
            </div>

        </div>
    </div>

</div>
@endsection
