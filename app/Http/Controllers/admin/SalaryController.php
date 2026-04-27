<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Salarytype;
use App\Models\Employee;
use App\Models\CompanyDetail;
use App\Models\EmployeeSalary;
use App\Exports\SalaryPDFExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    public function index()
    {
        
        $companyId = 1;
        $salarytypes = Salarytype::where('company_id', $companyId)->get();
        return view('admin.salary.index', compact('salarytypes')); // Return the view with all salarytypes
    }

    public function create()
    {
        
        return view('admin.salary.create'); // Return the view to create a new salarytype
    }


    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'salary_type' => 'required|string',
            'amount' => 'required|numeric',
        ]);
        $userId = Auth::id();
        Salarytype::create([
            'company_id' => $userId,
            'payment_type' => $request->payment_type,
            'salary_type' => $request->salary_type,
            'salary_type_description' => $request->salary_type_description,
            'amount' => $request->amount,
            'amount_type' => $request->amount_type,
            'status' => '1',
        ]);
        return redirect()->route('salarytype.index')->with('success', 'Salarytype created successfully.');
    }


    public function edit($id)
    {
        
        $salarytype = Salarytype::findOrFail($id); // Fetch the salarytype to edit
        return view('admin.salary.edit', compact('salarytype')); // Return the edit view
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'salary_type' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $userId = Auth::id();
        $salarytype = Salarytype::findOrFail($id);
        $salarytype->update([
            'payment_type' => $request->payment_type,
            'salary_type' => $request->salary_type,
            'salary_type_description' => $request->salary_type_description,
            'amount' => $request->amount,
            'amount_type' => $request->amount_type,
            'status' =>  $request->status,
        ]);

        // Redirect back with success message
        return redirect()->route('salarytype.index')->with('success', 'Salarytype updated successfully.');
    }


    public function destroy($id)
    {
        $salarytype = Salarytype::findOrFail($id); // Find the salarytype to delete
        $salarytype->delete(); // Delete the salarytype

        return redirect()->route('salarytype.index')->with('success', 'Salarytype deleted successfully.');
    }

    public function generateSalary()
    {
        $companies = CompanyDetail::orderBy('company_name','ASC')->get();
        $salarytypes = Salarytype::where('status', '1')->get();
        return view('admin.salary.generate_salary', compact('salarytypes','companies'));
    }
    public function getEmployeeSalary(Request $request, $id)
    {
        // dd($request->all());
        $employee = Employee::where('id', $id)->first();
        $data['basic_salary'] = $employee->salary;
        $section = '';
        $earning_section = '';
        $deduction_section = '';
        $amount = 0;
        $earning_amount = 0;
        $deduction_amount = 0;
        $gross_amount = 0;
        $salarytypes = Salarytype::where('company_id', $employee->company_id)->where('status', '1')->get();
        if (!$salarytypes->isEmpty()) {
            $basic_salary = $employee->salary;
            $section .= '<div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="basic_salary">Basic Salary</label>
                            <input type="text" id="basic_salary" name="basic_salary" class="form-control" value="' . $basic_salary . '"
                                required>
                        </div>
                    </div>';
            foreach ($salarytypes as $salarytype) {
                $salary_type_name = '';
                if ($salarytype->amount_type == 'Flat') {
                    $amount = $salarytype->amount;
                    $salary_type_name = $salarytype->salary_type;
                } else {
                    $amount = ($basic_salary * $salarytype->amount / 100);
                    $salary_type_name = $salarytype->salary_type . ' (' . $salarytype->amount . '%)';
                }
                if ($salarytype->payment_type == 'Earning') {
                    $earning_amount = $earning_amount + $amount;
                    $earning_section .= '<div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="salary_type">' . $salary_type_name . '</label>
                            <input type="text" name="salary_type_' . $salarytype->id . '" class="form-control salary_type_amount" data-paymenttype="Earning" data-value="' . $amount . '" value="' . $amount . '" required>
                        </div>
                    </div>';
                }
                if ($salarytype->payment_type == 'Deduction') {
                    $deduction_amount = $deduction_amount + $amount;
                    $deduction_section .= '<div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="salary_type">' . $salary_type_name . '</label>
                            <input type="text" name="salary_type_' . $salarytype->id . '" class="form-control salary_type_amount" data-paymenttype="Deduction" data-value="' . $amount . '" value="' . $amount . '" required>
                        </div>
                    </div>';
                }
            }

            $gross_amount = $basic_salary + $earning_amount - $deduction_amount;
            $section .= '<div class="row">
                            <div class="col-md-8">
                                <h6>Earning Amount</h6>
                            </div>' . $earning_section . '
                        </div>';
            $section .= '<div class="row">
                        <div class="col-md-8">
                            <h6>Deduction Amount</h6>
                        </div>' . $deduction_section . '
                    </div>';
            $section .= '<div class="row">
                            <div class="col-md-8">
                                <label>Gross Salary: <span id="gross_text">' . $gross_amount . '<span></label>
                                <input type="hidden" id="gross_amount" name="gross_amount" class="form-control" value="' . $gross_amount . '">
                            </div>
                        </div>';
        }

        $data['section'] = $section;
        return response()->json($data);
    }

    public function saveEmployeeSalary(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'employee' => 'required',
            'month' => 'required',
        ]);
        $companyId = $request->company_id;
        $salarytypes = Salarytype::where('company_id', $companyId)->where('status', '1')->get();
        $is_exist = EmployeeSalary::where('company_id', $companyId)->where('employee_id', $request->employee)->where('salary_month', $request->month)->where('salary_year', date('Y'))->first();
        if ($is_exist) {
            return redirect()->route('admin.generateSalary')->with('error', 'Salary already generated for this employee for this month.');
        }
        if (!$salarytypes->isEmpty()) {
            $total_amount = 0;
            foreach ($salarytypes as $salarytype) {

                $salary = new EmployeeSalary();
                $salary->company_id = $companyId;
                $salary->employee_id = $request->payment_type;
                $salary->employee_id = $request->employee;
                $salary->salary_month = $request->month;
                $salary->salary_year = date('Y');
                $salary->salary_type_id = $salarytype->id;
                $salary->salary_type_amount = $salarytype->amount;
                $salary->salary_type_amount_type = $salarytype->amount_type;
                $salary->amount = $request->input('salary_type_' . $salarytype->id);
                $salary->label = $salarytype->salary_type;
                $salary->basic_salary = $request->basic_salary;
                $salary->gross_salary = $request->gross_amount;
                $salary->save();
            }
        }
        return redirect()->route('admin.generateSalary')->with('success', 'Employee salary saved successfully.');
    }

    public function employeeSalaryList(Request $request)
    {
        
        $month = $request->input('month', date('m') - 1);
        $year = $request->input('year', date('Y'));
        $employee_id = $request->input('employee', '');
        $companyId = $request->input('company', '');

        $companies = CompanyDetail::orderBy('company_name','ASC')->get();
        $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $employees = Employee::where('company_id', $companyId)->select('id', 'name')->get();
        $employeeSalariesQuery = EmployeeSalary::where('company_id', $companyId)->groupBy('salary_month', 'salary_year', 'employee_id')->where('salary_month', $month)->where('salary_year', $year);
        if ($employee_id != '') {
            $employeeSalariesQuery->where('employee_id', $employee_id);
        }
        $employeeSalaries = $employeeSalariesQuery->get();
        //dd($employeeSalary);
        //$salarytypes = Salarytype::where('company_id', Auth::id())->where('status', '1')->get();
        return view('admin.salary.employee_salary', compact('companies','employeeSalaries', 'monthArray', 'employees', 'month', 'year', 'employee_id'));
    }

    public function employeeSalaryDetails($emp_id, Request $request)
    {
        
        $company = CompanyDetail::find($request->company);
        $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $employeeSalaries = EmployeeSalary::where('company_id', $company->id)->where('salary_month', $request->month)->where('salary_year', $request->year)->where('employee_id', $emp_id)->get();
        //dd($employeeSalary);
        return view('admin.salary.employee_salary_details', compact('employeeSalaries', 'monthArray', 'company'));
    }

    public function salaryPDF(Request $request)
    {
        //dd($request->all());
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $company = Auth::user();
        // dd($company);
        $emp_id = $request->emp_id;
        $month = $request->month;
        $year = $request->year;
        $export = new SalaryPDFExport($company, $emp_id, $month, $year);
        return $export->export();
    }
    public function getEmployee($companyId,Request $request)
    {
        
        $employees = Employee::where('company_id', $companyId)->get();
        return response()->json($employees);
    }
}