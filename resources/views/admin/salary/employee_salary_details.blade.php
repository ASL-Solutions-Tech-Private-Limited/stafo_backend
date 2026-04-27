@extends('admin.layouts.layout')

@section('title', 'Employee Salary') <!-- Set your custom title here -->

@section('content')

    <div class="card mt-4 p-3 ">
        <div class="container">
            <div class="col-md-6 text-end">
                <h2 class="fw-bold mb-3">
                    {{ $company->company_name }}
                </h2>
                <span>
                {{ $company->address }},
                {{ $company->pin }}
                </span>
                <h6>
                Pay Slip of {{$monthArray[ $employeeSalaries[0]->salary_month - 1]}},{{  $employeeSalaries[0]->salary_year }}
                </h6>
            </div>

            <table class="salary-main-table">
                <tr>
                    <td>
                        <b>Employee Name:</b> {{ $employeeSalaries[0]->employee->name }} <br>
                        <b>Employee Code:</b> {{ $employeeSalaries[0]->employee->emp_id }} <br>
                        <b>DOB:</b> {{ date('dS F,Y', strtotime($employeeSalaries[0]->employee->date_of_birth)) }} <br>
                        <b>DOJ:</b> {{ date('dS F,Y', strtotime($employeeSalaries[0]->employee->date_of_joining)) }} <br>
                    </td>
                    <td >
                        <b>PF Number:</b> {{ $employeeSalaries[0]->employee->pf_number }} <br>
                        <b>ESI Number:</b> {{ $employeeSalaries[0]->employee->esi_number }} <br>
                    </td>
                </tr>
                <tr>
                    <td>Earning</td>
                    <td>Deduction</td>
                </tr>
                @php
                $earning = '';
                $deduction = '';
                
                foreach($employeeSalaries as $salary){
  
                    if($salary->salarytype->payment_type == 'Earning'){
                        
                        $earning .= '<div class="row mb-3">
                            <div class="col-md-6">'. $salary->salarytype->salary_type.' </div>
                            <div class="col-md-6">'. $salary->amount.'</div>
                        </div>';
                    }
                    if($salary->salarytype->payment_type == 'Deduction'){
                        $deduction .= '<div class="row mb-3">
                            <div class="col-md-6">'. $salary->salarytype->salary_type.' </div>
                            <div class="col-md-6">'. $salary->amount.'</div>
                        </div>';
                    }
                }
                @endphp
                <tr>
                    <td>
                        {!! $earning !!}
                    </td>
                    <td>
                    {!! $deduction !!}
                    </td>
                </tr>
                <tr>
                    <td colspan = "2">
                        Gross Salary: {{ $employeeSalaries[0]->gross_salary }}
                    </td>
                </tr>
            </table>            
           
        </div>
    </div>

@endsection