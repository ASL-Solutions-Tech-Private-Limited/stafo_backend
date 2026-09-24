@extends('user.layouts.app')

@section('title', 'Edit Expense Form | STAFO HRMS')

@section('content')
@include('user.layouts.alert')

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Edit Expense Form</h3>
                <p class="text-muted small mb-0">Modify category details and customize fields required for reimbursement</p>
            </div>
            <a href="{{ route('expenseformList') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Form List
            </a>
        </div>

        <form action="{{ route('expenseformUpdate', $expense->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Form Category Info -->
            <div class="p-3 bg-light rounded-4 border mb-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label fw-semibold text-dark">
                            Expense Type / Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $expense->name) }}" placeholder="e.g. Travel Reimbursement, Client Lunch" required>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="is_document_req" class="form-label fw-semibold text-dark">
                            Receipt / Bill Attachment Required?
                        </label>
                        <select name="is_document_req" id="is_document_req" class="form-select">
                            <option value="0" {{ $expense->is_document_req == 0 ? 'selected' : '' }}>No (Optional)</option>
                            <option value="1" {{ $expense->is_document_req == 1 ? 'selected' : '' }}>Yes (Mandatory)</option>
                        </select>
                        @error('is_document_req')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold text-dark">Category Description & Policy Guidelines</label>
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter instructions or spending policy limits for employees...">{{ old('description', $expense->description) }}</textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Custom Form Fields Section -->
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-list-check text-primary"></i> Custom Form Fields
                    </h5>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add_more_field">
                        <i class="fa-solid fa-plus me-1"></i> Add Custom Field
                    </button>
                </div>

                <div id="fieldSection">
                    @if(count($expense->expenseForms) > 0)
                        @foreach($expense->expenseForms as $field)
                            <div class="row g-3 align-items-end mb-3 field-row">
                                <div class="col-12 col-md-5">
                                    <label class="form-label fw-semibold text-dark small">Field Name <span class="text-danger">*</span></label>
                                    <input type="text" name="fieldName[]" class="form-control" value="{{ $field->field_name }}" placeholder="e.g. Flight PNR, Hotel Name, Kilometers" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-semibold text-dark small">Field Description / Placeholder</label>
                                    <input type="text" name="fielddescription[]" class="form-control" value="{{ $field->description }}" placeholder="Helper text or guidance for this field">
                                </div>
                                <div class="col-12 col-md-1">
                                    <button type="button" class="btn btn-outline-danger w-100 remove_field" title="Remove Field" style="height: 38px;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="row g-3 align-items-end mb-3 field-row">
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-semibold text-dark small">Field Name <span class="text-danger">*</span></label>
                                <input type="text" name="fieldName[]" class="form-control" placeholder="e.g. Flight PNR, Hotel Name, Kilometers" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark small">Field Description / Placeholder</label>
                                <input type="text" name="fielddescription[]" class="form-control" placeholder="Helper text or guidance for this field">
                            </div>
                            <div class="col-12 col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100 remove_field" title="Remove Field" style="height: 38px;">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                <a href="{{ route('expenseformList') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 fw-bold">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Update Expense Form
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addMoreButton = document.getElementById('add_more_field');
        const fieldSection = document.getElementById('fieldSection');
        
        addMoreButton.addEventListener('click', function() {
            const newField = `
            <div class="row g-3 align-items-end mb-3 field-row">
                <div class="col-12 col-md-5">
                    <label class="form-label fw-semibold text-dark small">Field Name <span class="text-danger">*</span></label>
                    <input type="text" name="fieldName[]" class="form-control" placeholder="e.g. Toll Charges, Number of Days" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark small">Field Description / Placeholder</label>
                    <input type="text" name="fielddescription[]" class="form-control" placeholder="Helper text or guidance for this field">
                </div>
                <div class="col-12 col-md-1">
                    <button type="button" class="btn btn-outline-danger w-100 remove_field" title="Remove Field" style="height: 38px;">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>`;
            fieldSection.insertAdjacentHTML('beforeend', newField);
        });
    });

    document.addEventListener('click', function(event) {
        const removeBtn = event.target.closest('.remove_field');
        if (removeBtn) {
            const row = removeBtn.closest('.field-row');
            if (row) {
                row.remove();
            }
        }
    });
</script>
@endsection