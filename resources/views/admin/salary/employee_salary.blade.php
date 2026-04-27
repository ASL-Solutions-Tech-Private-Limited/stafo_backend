@extends('admin.layouts.layout')
@section('title', 'Employee List') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <div>
            <form action="{{ route('admin.employeeSalaryList') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-4 col-6">
                        <h2 class="fw-bold">Employee Salary</h2>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-2">
                        <select name="month" class="form-select mb-2">
                            <option value="1" @if ($month == 1) selected @endif>January</option>
                            <option value="2" @if ($month == 2) selected @endif>February</option>
                            <option value="3" @if ($month == 3) selected @endif>March</option>
                            <option value="4" @if ($month == 4) selected @endif>April</option>
                            <option value="5" @if ($month == 5) selected @endif>May</option>
                            <option value="6" @if ($month == 6) selected @endif>June</option>
                            <option value="7" @if ($month == 7) selected @endif>July</option>
                            <option value="8" @if ($month == 8) selected @endif>August</option>
                            <option value="9" @if ($month == 9) selected @endif>September</option>
                            <option value="10" @if ($month == 10) selected @endif>October</option>
                            <option value="11" @if ($month == 11) selected @endif>November</option>
                            <option value="12" @if ($month == 12) selected @endif>December</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="year" class="form-select mb-2">
                            @for ($yr = 2023; $yr <= date('Y') + 10; $yr++)
                                <option value="{{ $yr }}" @if ($year == $yr) selected @endif>
                                    {{ $yr }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="company" required id="company">
                            <option value="">Select Company</option>
                            @if (!$companies->isEmpty())
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="employee" class="form-select mb-2" id="employee">
                            <option value="">Select Employee</option>
                            
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search "></i> Search</button>
                    </div>

                </div>
            </form>
            <div class="table-responsive table-same">
                <table class="table  table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>S.No</th>
                            <th>Employee Name</th>
                            <th>Salary Month</th>
                            <th>Basic salary</th>
                            <th>Gross salary</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employeeSalaries as $index => $salary)
                            <tr>
                                <!-- First index starts at 1 -->
                                <td>{{ $index + 1 }}</td> <!-- Shows 1-based index -->
                                <td>{{ $salary->employee->name }}</td>
                                <td>{{ $monthArray[$salary->salary_month - 1] }}</td>
                                <td>{{ $salary->basic_salary }}</td>
                                <td>{{ $salary->gross_salary }}</td>
                                <td>
                                    <a
                                        href="{{ route('admin.employeeSalaryDetails', $salary->employee_id) }}?company={{ $salary->company_id }}&month={{ $salary->salary_month }}&year={{ $salary->salary_year }}"><i
                                            class="fas fa-eye"></i></a>
                                    
                                    <a
                                        href="{{ route('admin.salaryPDF') }}?emp_id={{ $salary->employee_id }}&month={{ $salary->salary_month }}&year={{ $salary->salary_year }}">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
    @endsection
    @section('scripts')
    <script>
        function confirmDelete(event, branchId) {
            event.preventDefault(); // Prevent form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the delete form
                    document.getElementById(`delete-form-${branchId}`).submit();
                }
            });
        }
        $(document).ready(function() {
            $('#company').change(function() {
                var companyId = $(this).val();
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
    </script>
@endsection
