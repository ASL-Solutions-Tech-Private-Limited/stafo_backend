@extends('user.layouts.app')

@section('title', 'Create Salary Types | STAFO HRMS')

@section('content')
@include('user.layouts.alert')

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Create Salary Types for Department</h3>
                <p class="text-muted small mb-0">Configure department-wide earning allowances and deduction components</p>
            </div>
            <a href="{{ route('salarytype.index') }}" class="btn btn-outline-secondary px-3 py-2">
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
                            <option value="{{ $department->id }}" 
                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
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
                        <span id="selected-department-name" class="badge-stafo badge-stafo-primary"></span>
                    </div>
                </div>
            </div>
        </div>
        
        <form action="{{ route('salarytype.store') }}" method="POST" id="salaryTypeForm">
            @csrf
            <input type="hidden" name="department_id" id="selected_department_id" value="{{ old('department_id') }}">
            
            <div id="salary-type-container">
                <!-- Default Salary Type Row -->
                <div class="salary-type-row card mb-3 border p-3 rounded-4 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill">1</span> Salary Component #1
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row" style="display: none; border-radius: 8px;">
                            <i class="fas fa-trash me-1"></i> Remove
                        </button>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label fw-semibold text-dark">Payment Type <span class="text-danger">*</span></label>
                                <select name="items[0][payment_type]" class="form-select" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="Earning" {{ old('items.0.payment_type') == 'Earning' ? 'selected' : '' }}>Earning (Addition)</option>
                                    <option value="Deduction" {{ old('items.0.payment_type') == 'Deduction' ? 'selected' : '' }}>Deduction (Subtraction)</option>
                                </select>
                                @error('items.0.payment_type')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="form-label fw-semibold text-dark">Salary Component Name <span class="text-danger">*</span></label>
                                <input type="text" name="items[0][salary_type]" class="form-control" value="{{ old('items.0.salary_type') }}" placeholder="e.g. Basic, HRA, Medical, PF" required>
                                @error('items.0.salary_type')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label class="form-label fw-semibold text-dark">Description</label>
                                <textarea name="items[0][salary_type_description]" class="form-control" rows="2" placeholder="Enter description (optional)">{{ old('items.0.salary_type_description') }}</textarea>
                                @error('items.0.salary_type_description')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label fw-semibold text-dark">Amount / Value <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="items[0][amount]" class="form-control" value="{{ old('items.0.amount') }}" placeholder="Enter amount" required>
                                @error('items.0.amount')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label fw-semibold text-dark">Amount Type <span class="text-danger">*</span></label>
                                <select name="items[0][amount_type]" class="form-select" required>
                                    <option value="Flat" {{ old('items.0.amount_type') == 'Flat' ? 'selected' : '' }}>Flat Amount (₹)</option>
                                    <option value="Percentage" {{ old('items.0.amount_type') == 'Percentage' ? 'selected' : '' }}>Percentage of Basic (%)</option>
                                </select>
                                @error('items.0.amount_type')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="form-label fw-semibold text-dark">Status</label>
                                <select name="items[0][status]" class="form-select">
                                    <option value="1" {{ old('items.0.status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('items.0.status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add More Button -->
            <div class="mb-4">
                <button type="button" class="btn btn-outline-primary" id="addMoreBtn">
                    <i class="fas fa-plus me-1"></i> Add Another Component
                </button>
            </div>
            
            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('salarytype.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 fw-bold">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save All Components
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let rowCount = 1;
    
    // When department is selected
    $('#department_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const departmentId = $(this).val();
        const departmentName = selectedOption.text();
        
        if (departmentId) {
            $('#selected_department_id').val(departmentId);
            $('#selected-department-name').text(departmentName);
            $('#department-info').show();
        } else {
            $('#selected_department_id').val('');
            $('#department-info').hide();
        }
    });
    
    // Initialize values on page load
    $(document).ready(function() {
        if ($('#department_id').val()) {
            $('#department_id').trigger('change');
        }
    });
    
    // Add more button click
    $('#addMoreBtn').on('click', function() {
        const currentCount = $('#salary-type-container .salary-type-row').length;
        const newIndex = currentCount;
        
        const newRow = `
            <div class="salary-type-row card mb-3 border p-3 rounded-4 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-pill">${currentCount + 1}</span> Salary Component #${currentCount + 1}
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row" style="border-radius: 8px;">
                        <i class="fas fa-trash me-1"></i> Remove
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="form-label fw-semibold text-dark">Payment Type <span class="text-danger">*</span></label>
                            <select name="items[${newIndex}][payment_type]" class="form-select" required>
                                <option value="">Select Payment Type</option>
                                <option value="Earning">Earning (Addition)</option>
                                <option value="Deduction">Deduction (Subtraction)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label class="form-label fw-semibold text-dark">Salary Component Name <span class="text-danger">*</span></label>
                            <input type="text" name="items[${newIndex}][salary_type]" class="form-control" placeholder="e.g. Basic, HRA, Medical, PF" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-2">
                            <label class="form-label fw-semibold text-dark">Description</label>
                            <textarea name="items[${newIndex}][salary_type_description]" class="form-control" rows="2" placeholder="Enter description (optional)"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="form-label fw-semibold text-dark">Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="items[${newIndex}][amount]" class="form-control" placeholder="Enter amount" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="form-label fw-semibold text-dark">Amount Type <span class="text-danger">*</span></label>
                            <select name="items[${newIndex}][amount_type]" class="form-select" required>
                                <option value="Flat">Flat Amount (₹)</option>
                                <option value="Percentage">Percentage of Basic (%)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="form-label fw-semibold text-dark">Status</label>
                            <select name="items[${newIndex}][status]" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#salary-type-container').append(newRow);
        updateRemoveButtons();
    });
    
    // Remove row handler (event delegation)
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.salary-type-row').remove();
        updateRowNumbers();
        updateRemoveButtons();
    });
    
    function updateRemoveButtons() {
        const rowCount = $('#salary-type-container .salary-type-row').length;
        if (rowCount <= 1) {
            $('.remove-row').hide();
        } else {
            $('.remove-row').show();
        }
    }
    
    function updateRowNumbers() {
        $('#salary-type-container .salary-type-row').each(function(index) {
            // Update heading
            $(this).find('h6').html(`<span class="badge bg-primary rounded-pill">${index + 1}</span> Salary Component #${index + 1}`);
            
            // Update all input names
            $(this).find('input, select, textarea').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
                    $(this).attr('name', newName);
                }
            });
        });
    }
    
    // Form validation before submit
    $('#salaryTypeForm').on('submit', function(e) {
        let isValid = true;
        let errorMessage = '';
        
        // Check if department is selected
        if (!$('#selected_department_id').val()) {
            isValid = false;
            errorMessage = 'Please select a department first!';
        }
        
        // Check each row for required fields
        $('#salary-type-container .salary-type-row').each(function(index) {
            const paymentType = $(this).find('select[name*="[payment_type]"]').val();
            const salaryType = $(this).find('input[name*="[salary_type]"]').val();
            const amount = $(this).find('input[name*="[amount]"]').val();
            
            if (!paymentType) {
                isValid = false;
                errorMessage = `Row ${index + 1}: Please select Payment Type`;
                return false;
            }
            if (!salaryType) {
                isValid = false;
                errorMessage = `Row ${index + 1}: Please enter Salary Type`;
                return false;
            }
            if (!amount) {
                isValid = false;
                errorMessage = `Row ${index + 1}: Please enter Amount`;
                return false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: errorMessage
            });
            return false;
        }
    });
    
    // Initialize
    updateRemoveButtons();
</script>
@endsection