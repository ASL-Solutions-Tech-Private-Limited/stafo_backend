@extends('admin.layouts.layout')

@section('title', 'Add Expense')

@section('content')
    <div class="card mt-4 p-3">

        <div class="container">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="fw-bold">Add Expense</h2>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('admin.expenseList') }}" class="btn btn-primary" style="float: right;">Expense
                        List</a>
                </div>
            </div>

            <form action="{{ route('admin.expenseStore') }}" method="POST" enctype="multipart/form-data">
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
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>  
                            @error('employee_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="text" name="amount" class="form-control" value="{{ old('amount') }}" required>

                            @error('amount')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label for="expense_type" class="form-label">Expense Type</label>
                            
                            <select name="expense_type" id="expense_type" class="form-control">
                                <option value="">Select Expense Type</option>
                                @foreach ($expenseTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>  
                            @error('expense_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="expense_form_display"></div>
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
                        url: "{{ route('expenseFormDisplay') }}",
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

            $('#company_id').on('change', function () {               
                const id = $(this).val();
                const url = '{{ route('admin.expenseCreate') }}'+'?company_id=' + id;
                window.location.href = url;
            });
        });
       
    </script>
@endsection