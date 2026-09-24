@extends('user.layouts.app')

@section('title', 'Generate Salary') <!-- Set your custom title here -->

@section('content')

    @include('user.layouts.alert')
    <div class="card mt-4 p-3 ">
        <div class="container">
            <h2 class="fw-bold mb-3">Generate Salary</h2>
            <div class="mb-3">
                <button class="btn btn-primary" id="all_employee">Generate Salary for All Employees</button>
            </div>
            <form action="{{ route('saveEmployeeSalary') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="employee">Employee</label>
                            <select class="form-control" name="employee" required id="employee">
                                <option value="">Select Employee</option>
                                @if (!$employees->isEmpty())
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                </div>
                <div class="row" id="salary_section"></div>

                <div class="col-md-12 text-center mt-3">
                    <button type="submit" class="btn btn-primary ">Save</button>
                </div>
        </div>
        </form>
    </div>
    </div>
@endsection


@section('js')
<script>
    $(document).ready(function() {
        
        // Function to load salary details
        function loadSalaryDetails() {
            var employeeId = $('#employee').val();
            var month = $('select[name="month"]').val();
            
            if (employeeId && month) {
                $.ajax({
                    url: '{{ route('getEmployeeSalary', '') }}/' + employeeId,
                    type: 'GET',
                    dataType: 'json',
                    data: { 
                        month: month,
                        basic_salary: $('#basic_salary').val() 
                    },
                    success: function(data) {
                        if (data) {
                            if (data.basic_salary) {
                                $('input[name="basic_salary"]').val(data.basic_salary);
                            }
                            if (data.section) {
                                $('#salary_section').html(data.section);
                            }
                            // Rebind calculation trigger after loading new section
                            bindCalculationEvents();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        $('#salary_section').html('<div class="alert alert-danger">Error loading salary details</div>');
                    }
                });
            } else {
                // Reset fields if either employee or month is not selected
                $('input[name="basic_salary"]').val('');
                $('#salary_section').empty();
                @foreach ($salarytypes as $salarytype)
                    $('input[name="salary_type_{{ $salarytype->id }}"]').val('');
                @endforeach
            }
        }

        // Function to bind calculation events
        function bindCalculationEvents() {
            $(document).off('keyup', '.salary_type_amount');
            $(document).on('keyup', '.salary_type_amount', function() {
                calculateTotal();
            });
        }

        // Function to calculate total
        function calculateTotal() {
            var basic = $('#basic_salary').val() == '' ? 0 : parseInt($('#basic_salary').val());
            var earning = 0;
            var deduction = 0;
            var total = 0;
            
            $('.salary_type_amount').each(function() {
                var value = $(this).val() == '' ? 0 : parseInt($(this).val());
                if ($(this).data('paymenttype') == 'Earning') {
                    earning += value;
                }
                if ($(this).data('paymenttype') == 'Deduction') {
                    deduction += value;
                }
            });
            
            total = basic + earning - deduction;
            $('#gross_amount').val(total);
            $('#gross_text').html(total);
        }

        // Trigger when month changes
        $('select[name="month"]').change(function() {
            loadSalaryDetails();
        });

        // Trigger when employee changes
        $('#employee').change(function() {
            loadSalaryDetails();
        });

        // Trigger when basic salary changes
        $(document).on('blur', '#basic_salary', function() {
            loadSalaryDetails();
        });

        // Generate salary for all employees
        $('#all_employee').click(function() {
            var month = $('select[name="month"]').val();
            var year = new Date().getFullYear(); // You can modify this as needed
            
            if(month == '') {
                alert('Please select a month first.');
                return false;
            }
            
            if(confirm('Are you sure you want to generate salary for ALL employees for this month?')) {
                $.ajax({
                    url: '{{ route('generateAllSalary') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: { 
                        month: month,
                        year: year 
                    },
                    success: function(data) {
                        if(data.success) {
                            alert(data.message);
                            if(data.redirect) {
                                window.location.href = data.redirect;
                            }
                        } else {
                            alert(data.message);
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        // Initial bindings
        bindCalculationEvents();
        
        // If both are already selected on page load, load details
        if ($('#employee').val() && $('select[name="month"]').val()) {
            loadSalaryDetails();
        }
    });
</script>
@endsection