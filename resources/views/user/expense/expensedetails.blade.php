@extends('user.layouts.app')

@section('title', 'Form Details')

@section('content')



@php
    // Example: $expense is passed from controller
@endphp

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Expense Details</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mb-4">
                        <tr>
                            <th>Expense Type</th>
                            <td>{{ $expense->expense_type->name }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>{{ $expense->amount }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $expense->status }}</td>
                        </tr>                        
                    </table>
                    
                    @if(count($expense->expense_details) > 0)
                        <!-- <h5 class="mb-3">Form Fields</h5> -->
                        <table class="table table-striped">
                            <!-- <thead>
                                <tr>
                                    <th>Field Name</th>
                                    <th>Field Description</th>
                                </tr>
                            </thead> -->
                            <tbody>
                                @foreach($expense->expense_details as $details)
                                    <tr>
                                        <td>{{ $details->expenseFormDetails->field_name }}</td>
                                        <td>{{ $details->expense_value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info mb-0">No form fields available.</div>
                    @endif

                    @if(count($expense->attachments) > 0)
                        <h6 class="mb-3">Attachments</h6>
                        <table class="table table-striped">
                            <!-- <thead>
                                <tr>
                                    <th>Field Name</th>
                                    <th>Field Description</th>
                                </tr>
                            </thead> -->
                            <tbody>
                                @foreach($expense->attachments as $attachment)
                                    <tr>
                                        <td>{{ $attachment->filename }}</td>
                                        <td><a href="{{ asset('uploads/expense_attachments/' . $attachment->filename) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    View Attachment
                                </a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info mb-0">No form fields available.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection