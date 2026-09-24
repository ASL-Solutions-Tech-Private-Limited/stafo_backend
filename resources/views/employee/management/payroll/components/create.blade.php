@extends('employee.layouts.app')

@section('title', 'Create Salary Components | Management Portal')

@section('content')
<div class="container-fluid p-0">

    <div class="card shadow-sm border-0 rounded-4 mb-4" style="background: var(--bs-card-bg, #ffffff);">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Create Salary Types for Department</h4>
                    <p class="text-muted small mb-0">Configure department-wide earning allowances and deduction components</p>
                </div>
                <a href="{{ route('employee.management.payroll.components') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Salary Types
                </a>
            </div>
            
            <!-- Department Selection Section -->
            <div class="p-3 bg-light rounded-4 border mb-4">
                <div class="row align-items-center g-3">
                    <div class="col-md-3">
                        <label for="department_id" class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-building text-primary me-1"></i> Select Department <span class="text-danger">*</span>
                        </label>
                    </div>
                    <div class="col-md-5">
                        <select name="department_id" id="department_id" class="form-select" required>
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <div id="department-info" style="display: none;">
                            <span class="text-muted small">Configuring for:</span> 
                            <span id="selected-department-name" class="badge bg-primary text-white px-2 py-1 rounded-pill"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('employee.management.payroll.components.store') }}" method="POST" id="salaryTypeForm">
                @csrf
                <input type="hidden" name="department_id" id="selected_department_id" value="{{ old('department_id') }}">
                
                <div id="salary-type-container">
                    <!-- Default Salary Type Row -->
                    <div class="salary-type-row card mb-3 border p-3 rounded-4 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark">Component #1</h6>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-row" style="display: none;">
                                <i class="fa-solid fa-trash me-1"></i> Remove
                            </button>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-semibold text-dark small">Payment Type <span class="text-danger">*</span></label>
                                <select name="salary_types[0][payment_type]" class="form-select payment-type" required>
                                    <option value="Earning">Earning (Addition)</option>
                                    <option value="Deduction">Deduction (Subtraction)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-semibold text-dark small">Component Name <span class="text-danger">*</span></label>
                                <input type="text" name="salary_types[0][salary_type]" class="form-control" placeholder="e.g. HRA, Medical, PF" required>
                            </div>
                            
                            <div class="col-md-6 col-lg-2">
                                <label class="form-label fw-semibold text-dark small">Amount Type <span class="text-danger">*</span></label>
                                <select name="salary_types[0][amount_type]" class="form-select amount-type" required>
                                    <option value="Flat">Flat Amount (₹)</option>
                                    <option value="Percentage">Percentage (%)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 col-lg-2">
                                <label class="form-label fw-semibold text-dark small">Value <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="salary_types[0][amount]" class="form-control" placeholder="Amount or %" required>
                            </div>
                            
                            <div class="col-md-6 col-lg-2">
                                <label class="form-label fw-semibold text-dark small">Status <span class="text-danger">*</span></label>
                                <select name="salary_types[0][status]" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark small">Description</label>
                                <input type="text" name="salary_types[0][salary_type_description]" class="form-control" placeholder="Optional details regarding this salary component">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <button type="button" class="btn btn-outline-primary px-3 py-2 rounded-3 fw-semibold" id="add-more-btn">
                        <i class="fa-solid fa-plus me-1"></i> Add Another Component
                    </button>
                    
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm" id="submit-btn" disabled>
                        <i class="fa-solid fa-check me-1"></i> Save All Components
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;
    const container = document.getElementById('salary-type-container');
    const addBtn = document.getElementById('add-more-btn');
    const submitBtn = document.getElementById('submit-btn');
    const deptSelect = document.getElementById('department_id');
    const deptInfo = document.getElementById('department-info');
    const deptNameSpan = document.getElementById('selected-department-name');
    const hiddenDeptInput = document.getElementById('selected_department_id');
    
    function updateDepartmentState() {
        const selectedValue = deptSelect.value;
        const selectedText = deptSelect.options[deptSelect.selectedIndex]?.text;
        
        if (selectedValue) {
            submitBtn.disabled = false;
            deptInfo.style.display = 'block';
            deptNameSpan.textContent = selectedText;
            hiddenDeptInput.value = selectedValue;
        } else {
            submitBtn.disabled = true;
            deptInfo.style.display = 'none';
            hiddenDeptInput.value = '';
        }
    }
    
    deptSelect.addEventListener('change', updateDepartmentState);
    if (deptSelect.value) updateDepartmentState();
    
    addBtn.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'salary-type-row card mb-3 border p-3 rounded-4 bg-light';
        newRow.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">Component #${rowIndex + 1}</h6>
                <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                    <i class="fa-solid fa-trash me-1"></i> Remove
                </button>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold text-dark small">Payment Type <span class="text-danger">*</span></label>
                    <select name="salary_types[${rowIndex}][payment_type]" class="form-select payment-type" required>
                        <option value="Earning">Earning (Addition)</option>
                        <option value="Deduction">Deduction (Subtraction)</option>
                    </select>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold text-dark small">Component Name <span class="text-danger">*</span></label>
                    <input type="text" name="salary_types[${rowIndex}][salary_type]" class="form-control" placeholder="e.g. HRA, Medical, PF" required>
                </div>
                
                <div class="col-md-6 col-lg-2">
                    <label class="form-label fw-semibold text-dark small">Amount Type <span class="text-danger">*</span></label>
                    <select name="salary_types[${rowIndex}][amount_type]" class="form-select amount-type" required>
                        <option value="Flat">Flat Amount (₹)</option>
                        <option value="Percentage">Percentage (%)</option>
                    </select>
                </div>
                
                <div class="col-md-6 col-lg-2">
                    <label class="form-label fw-semibold text-dark small">Value <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="salary_types[${rowIndex}][amount]" class="form-control" placeholder="Amount or %" required>
                </div>
                
                <div class="col-md-6 col-lg-2">
                    <label class="form-label fw-semibold text-dark small">Status <span class="text-danger">*</span></label>
                    <select name="salary_types[${rowIndex}][status]" class="form-select" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <label class="form-label fw-semibold text-dark small">Description</label>
                    <input type="text" name="salary_types[${rowIndex}][salary_type_description]" class="form-control" placeholder="Optional details regarding this salary component">
                </div>
            </div>
        `;
        
        container.appendChild(newRow);
        rowIndex++;
        updateRemoveButtons();
    });
    
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            const row = e.target.closest('.salary-type-row');
            row.remove();
            updateRemoveButtons();
        }
    });
    
    function updateRemoveButtons() {
        const rows = container.querySelectorAll('.salary-type-row');
        rows.forEach((r, idx) => {
            const h = r.querySelector('h6');
            if (h) h.textContent = `Component #${idx + 1}`;
            const btn = r.querySelector('.remove-row');
            if (btn) btn.style.display = rows.length > 1 ? 'inline-block' : 'none';
        });
    }

    document.getElementById('salaryTypeForm').addEventListener('submit', function(e) {
        const currentDeptId = deptSelect.value;
        if (!currentDeptId) {
            e.preventDefault();
            alert('Please select a department before submitting.');
            return;
        }
        
        const rows = container.querySelectorAll('.salary-type-row');
        rows.forEach((row, i) => {
            let deptInput = row.querySelector('.row-dept-id');
            if (!deptInput) {
                deptInput = document.createElement('input');
                deptInput.type = 'hidden';
                deptInput.className = 'row-dept-id';
                row.appendChild(deptInput);
            }
            deptInput.name = `salary_types[${i}][department_id]`;
            deptInput.value = currentDeptId;
        });
    });
});
</script>
@endsection
