@extends('admin.layouts.layout')

@section('title', 'Generate Salary') <!-- Set your custom title here -->

@section('content')
@include('user.layouts.alert')
        <div class="container">
            <h2 class="fw-bold mb-3">Generate Salary</h2>
            <form action="{{ route('admin.saveEmployeeSalary') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="month">Month</label>
                            <select class="form-control" name="month" required>
                                <option value ="">Select Month</option>
                                <option value = "1">January</option>
                                <option value = "2">February</option>
                                <option value = "3">March</option>
                                <option value = "4">April</option>
                                <option value = "5">May</option>
                                <option value = "6">June</option>
                                <option value = "7">July</option>
                                <option value = "8">August</option>
                                <option value = "9">September</option>
                                <option value = "10">October</option>
                                <option value = "11">November</option>
                                <option value = "12">December</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="company">Company</label>
                            <select class="tomselect" name="company" required id="company">
                                <option value="">Select Company</option>
                                @if (!$companies->isEmpty())
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="employee">Employee</label>
                            <select class="form-control" name="employee" required id="employee">
                                <option value="">Select Employee</option>
                                
                            </select>
                        </div>
                    </div>

                </div>
                <div class="row" id="salary_section"></div>

                <div class="col-md-12 text-end">
                    <input type="hidden" name="company_id" id="company_id" value="">
                    <button type="submit" class="btn btn-primary ">Save</button>
                </div>
        
            </form>
        </div>
   
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#employee').change(function() {
                var employeeId = $(this).val();
                if (employeeId) {
                    $.ajax({
                        url: '{{ url('/') }}' + '/admin/get-employee-salary/' + employeeId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data) {
                                $('input[name="basic_salary"]').val(data.basic_salary);
                                $('#salary_section').html(data.section);
                            }
                        }
                    });
                } else {
                    
                }
            });
            $('#company').change(function() {
                var companyId = $(this).val();
                $('#company_id').val(companyId);
                if (companyId) {
                    $.ajax({
                        url: '{{ url('/') }}' + '/admin/get-employee/' + companyId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data) {
                                $('#employee').empty();
                                $('#employee').append('<option value="">Select Employee</option>');
                                $.each(data, function(key, value) {
                                    $('#employee').append('<option value="' + value.id + '">' + value.name + '</option>');
                                });
                            } else {
                                $('#employee').empty();
                            }
                        }
                    });
                }
            });
        });
        $(document).on('keyup', '.salary_type_amount', function() {

            var basic = $('#basic_salary').val() == '' ? 0 : parseInt($('#basic_salary').val());
            var earning = 0;
            var deduction = 0;
            var total = 0;
            $('.salary_type_amount').each(function() {
                if ($(this).data('paymenttype') == 'Earning')
                    earning += $(this).val() == '' ? 0 : parseInt($(this).val());
                if ($(this).data('paymenttype') == 'Deduction')
                    deduction += $(this).val() == '' ? 0 : parseInt($(this).val());
            });
            total = basic + earning - deduction;
            $('#gross_amount').val(total);
            $('#gross_text').html(total);
        });
    </script>
@endsection
