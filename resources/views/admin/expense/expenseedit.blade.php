@extends('admin.layouts.layout')

@section('title', 'Edit Expense')

@section('content')
    <div class="card mt-4 p-3">

        <div class="container">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="fw-bold">Edit Expense</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('admin.expenseList') }}" class="btn btn-primary" style="float: right;">Expense
                        List</a>
                </div>
            </div>

            <form action="{{ route('admin.expenseUpdate',$expense->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-end">
                    
                    <!-- Reimbursement Name -->
                    <div class="col-md-9">
                        <div class="col-md-12">
                            <label for="employee_id" class="form-label">Company</label>
                            
                            <select name="company_id" id="company_id" class="tomselect">
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" {{ $companyId == $company->id ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select> 
                            @error('employee_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="employee_id" class="form-label">Employee</label>
                            
                            <select name="employee_id" id="employee_id" class="form-control">
                                <option value="">Select Employee</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ ($expense->employee_id == $employee->id)?'selected':'' }}>{{ $employee->name }}</option>
                                @endforeach
                            </select>  
                            @error('employee_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="text" name="amount" class="form-control" value="{{ old('amount',$expense->amount) }}" required>

                            @error('amount')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="expense_type" class="form-label">Expense Type</label>
                            <div>{{ $expense->expense_type->name }}</div>
                            
                        </div>

                        <div id="expense_form_display">
                            @foreach ($expense->expense_details as $detail)
                                <div class="form-group">
                                    <label for="expense_details[{{ $detail->id }}][expense_value]">{{ $detail->expenseFormDetails->field_name }}</label>
                                    <input type="text" name="expense_details[{{ $detail->id }}][expense_value]" class="form-control" value="{{ old('expense_details.'.$detail->id.'.expense_value', $detail->expense_value) }}" placeholder="{{ $detail->expenseFormDetails->description }}">
                                    <input type="hidden" name="expense_details[{{ $detail->id }}][id]" value="{{ $detail->id }}">
                                </div>
                            @endforeach
                            
                        </div>

                        <div class="col-md-12">
                            <label for="attachments" class="form-label">Attachments</label>
                            <input type="file" name="attachments[]" class="form-control" multiple>
                            @error('attachments')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror                        


                        </div>
                        
                    </div> 
                    
                 </div>
                  
                <div class="row">
                    <!-- Save Button -->
                    <div class="col-md-6 mb-3 text-end">
                        <button type="submit" class="btn btn-success ">Save</button>
                    </div>
                </div>
            </form>
            @if($attachments->count() > 0)
                <div class="mt-2">
                    <h6>Existing Attachments:</h6>
                    <ul class="list-group">
                        @foreach($attachments as $attachment)
                            <li class="list-group-item d-flex justify-content-between align-items-left">
                                <a href="{{ asset('uploads/expense_attachments/' . $attachment->filename) }}" target="_blank">{{ $attachment->filename }}</a>
                                <form action="{{ route('expenseAttachmentDelete', $attachment->id) }}" method="POST" class="mb-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#expense_type').change(function() {
                var expenseTypeId = $(this).val();
                if (expenseTypeId) {
                    $.ajax({
                        url: "{{ route('admin.expenseFormDisplay') }}",
                        type: "GET",
                        data: { expense_type_id: expenseTypeId },
                        success: function(response) {
                            console.log(response);
                            $('#expense_form_display').html(response.html);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching expense form:", error);
                        }
                    });
                } else {
                    $('#expense_form_display').empty();
                }
            });
        });
       
    </script>
@endsection