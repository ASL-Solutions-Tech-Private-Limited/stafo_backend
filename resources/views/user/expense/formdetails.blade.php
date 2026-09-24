@extends('user.layouts.app')

@section('title', 'Expense Form Details | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Expense Form Details</h3>
                <p class="text-muted small mb-0">Overview of configured form inputs, custom fields, and validation rules</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('expenseformList') }}" class="btn btn-outline-secondary px-3 py-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Form List
                </a>
                <a href="{{ route('expenseformEdit', $expense->id) }}" class="btn btn-primary px-3 py-2">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Edit Form
                </a>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6">
                <div class="p-3 bg-light rounded-4 border h-100">
                    <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Expense Category</span>
                    <h5 class="fw-bold text-dark mb-0">{{ $expense->name }}</h5>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-3 bg-light rounded-4 border h-100">
                    <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Receipt Attachment Requirement</span>
                    @if($expense->is_document_req == 1)
                        <span class="badge-stafo badge-stafo-info fs-6">
                            <i class="fa-solid fa-paperclip me-1"></i> Mandatory Document / Bill Receipt
                        </span>
                    @else
                        <span class="badge-stafo badge-stafo-secondary fs-6">
                            <i class="fa-solid fa-minus me-1"></i> Optional Attachment
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-12">
                <div class="p-3 bg-light rounded-4 border">
                    <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Description & Guidelines</span>
                    <p class="text-dark mb-0">{{ $expense->description ?: 'No specific description provided.' }}</p>
                </div>
            </div>
        </div>

        <!-- Custom Fields Section -->
        <div class="mt-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-list-check text-primary"></i> Configured Input Fields
            </h5>

            @if(count($expense->expenseForms) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 70px;" class="text-center">#</th>
                                <th style="width: 280px;">Field Name</th>
                                <th>Field Description / Helper Text</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expense->expenseForms as $idx => $field)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $field->field_name }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $field->description ?: '—' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 bg-light rounded-4 border text-muted">
                    <i class="fa-solid fa-circle-info fs-3 mb-2 d-block opacity-50"></i>
                    No custom fields attached to this form yet.
                </div>
            @endif
        </div>

    </div>
</div>
@endsection