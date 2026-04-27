@extends('user.layouts.app')

@section('title', 'Salary Type Add - Package Wise')

@section('content')
<div class="card mt-4 p-3">
    <div class="container">
        <h2 class="fw-bold mb-3">New Salary Type</h2>
        
        <!-- Package Selection Section -->
        <div class="alert alert-info mb-4">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label for="package_id" class="fw-bold">Select Package <span class="text-danger">*</span></label>
                </div>
                <div class="col-md-9">
                    <select name="package_id" id="package_id" class="form-control" required>
                        <option value="">-- Select Package --</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                {{ $package->package_name }} ({{ $package->package_type }})
                            </option>
                        @endforeach
                    </select>
                    @error('package_id')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <form action="{{ route('salarytype.store') }}" method="POST" id="salaryTypeForm">
            @csrf
            <input type="hidden" name="package_id" id="selected_package_id" value="{{ old('package_id') }}">
            
            <div id="salary-type-container">
                <!-- Default Salary Type Row -->
                <div class="salary-type-row card mb-3 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Salary Type</h5>
                        <button type="button" class="btn btn-danger btn-sm remove-row" style="display: none;">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Payment Type <span class="text-danger">*</span></label>
                                <select name="items[0][payment_type]" class="form-control" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="Earning" {{ old('items.0.payment_type') == 'Earning' ? 'selected' : '' }}>Earning</option>
                                    <option value="Deduction" {{ old('items.0.payment_type') == 'Deduction' ? 'selected' : '' }}>Deduction</option>
                                </select>
                                @error('items.0.payment_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Salary Type <span class="text-danger">*</span></label>
                                <input type="text" name="items[0][salary_type]" class="form-control" value="{{ old('items.0.salary_type') }}" placeholder="Enter salary type" required>
                                @error('items.0.salary_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Description</label>
                                <textarea name="items[0][salary_type_description]" class="form-control" rows="2" placeholder="Enter description (optional)">{{ old('items.0.salary_type_description') }}</textarea>
                                @error('items.0.salary_type_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="items[0][amount]" class="form-control" value="{{ old('items.0.amount') }}" placeholder="Enter amount" required>
                                @error('items.0.amount')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Amount Type <span class="text-danger">*</span></label>
                                <select name="items[0][amount_type]" class="form-control" required>
                                    <option value="Flat" {{ old('items.0.amount_type') == 'Flat' ? 'selected' : '' }}>Flat</option>
                                    <option value="Percentage" {{ old('items.0.amount_type') == 'Percentage' ? 'selected' : '' }}>Percentage</option>
                                </select>
                                @error('items.0.amount_type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Status</label>
                                <select name="items[0][status]" class="form-control">
                                    <option value="1" {{ old('items.0.status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('items.0.status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add More Button -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <button type="button" class="btn btn-success" id="addMoreBtn">
                        <i class="fas fa-plus"></i> Add More Salary Type
                    </button>
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="row">
                <div class="col-md-12 text-end">
                    <a href="{{ route('salarytype.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save All</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    let rowCount = 1;
    
    // Update hidden package_id when dropdown changes
    $('#package_id').on('change', function() {
        $('#selected_package_id').val($(this).val());
    });
    
    // Initialize package_id on page load
    $(document).ready(function() {
        if ($('#package_id').val()) {
            $('#selected_package_id').val($('#package_id').val());
        }
    });
    
    // Add more button click
    $('#addMoreBtn').on('click', function() {
        const currentCount = $('#salary-type-container .salary-type-row').length;
        const newIndex = currentCount;
        
        const newRow = `
            <div class="salary-type-row card mb-3 p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Salary Type</h5>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Payment Type <span class="text-danger">*</span></label>
                            <select name="items[${newIndex}][payment_type]" class="form-control" required>
                                <option value="">Select Payment Type</option>
                                <option value="Earning">Earning</option>
                                <option value="Deduction">Deduction</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Salary Type <span class="text-danger">*</span></label>
                            <input type="text" name="items[${newIndex}][salary_type]" class="form-control" placeholder="Enter salary type" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Description</label>
                            <textarea name="items[${newIndex}][salary_type_description]" class="form-control" rows="2" placeholder="Enter description (optional)"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="items[${newIndex}][amount]" class="form-control" placeholder="Enter amount" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Amount Type <span class="text-danger">*</span></label>
                            <select name="items[${newIndex}][amount_type]" class="form-control" required>
                                <option value="Flat">Flat</option>
                                <option value="Percentage">Percentage</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Status</label>
                            <select name="items[${newIndex}][status]" class="form-control">
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
            $(this).find('h5').text(`Salary Type #${index + 1}`);
            
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
        
        // Check if package is selected
        if (!$('#selected_package_id').val()) {
            isValid = false;
            errorMessage = 'Please select a package first!';
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

@section('css')
<style>
    .salary-type-row {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
        transition: all 0.3s ease;
    }
    
    .salary-type-row:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .remove-row {
        transition: all 0.2s ease;
    }
    
    .remove-row:hover {
        transform: scale(1.05);
    }
</style>
@endsection