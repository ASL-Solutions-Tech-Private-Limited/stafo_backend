<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Salarytype;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeeLeave;
use App\Models\GraceSetting;
use App\Models\Reimbursement;
use App\Models\EmployeeSalary;
use App\Models\EmployeeSalarySummary;
use App\Models\Holiday;
use App\Models\Expense;
use App\Models\CompanyDetail;
use App\Models\SalryTypePackage;
use App\Models\Department;
use App\Exports\SalaryPDFExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalarytypeController extends Controller
{
    public function index()
    {
   
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $companyId = Auth::id();
        $salarytypes = Salarytype::with('department')
       ->where('company_id', Auth::id())
       ->get();
   
  
        return view('user.salarytype.index', compact('salarytypes')); // Return the view with all salarytypes
    }

    // public function create()
    // {
    //     $is_verified = Auth::user()->is_verified;
    //     if ($is_verified == 'No') {
    //         return view('user.verify_check');
    //     }

    //         // Get all active employees for the company
    //     $employees = Employee::where('company_id', Auth::id())
    //         ->where('status', '1')
    //         ->orderBy('name')
    //         ->get();
   

    //     // return view('user.salarytype.create',compact('packages','departments')); 
    //   return view('user.salarytype.create', compact('employees'));

    // }


    public function create()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }

        // Get all active departments for the company
        $departments = Department::where('company_id', Auth::id())
            ->where('status', '1')
            ->orderBy('name')
            ->get();

        return view('user.salarytype.create', compact('departments'));
    }


    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'items' => 'required|array|min:1',
            'items.*.payment_type' => 'required|in:Earning,Deduction',
            'items.*.salary_type' => 'required|string|max:255',
            'items.*.salary_type_description' => 'nullable|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.amount_type' => 'required|in:Flat,Percentage',
            'items.*.status' => 'sometimes|in:0,1',
        ]);
        
        $userId = Auth::id();
        $employeeId = $request->employee_id;
        $departmentId = $request->department_id;
        $createdCount = 0;
        $errors = [];

        
        foreach ($request->items as $index => $item) {
           
            
            try {
                Salarytype::create([
                    'company_id' => $userId,
                    'department_id' => $departmentId,
                    'payment_type' => $item['payment_type'],
                    'salary_type' => $item['salary_type'],
                    'salary_type_description' => $item['salary_type_description'] ?? null,
                    'amount' => $item['amount'],
                    'amount_type' => $item['amount_type'],
                    'status' => $item['status'] ?? '1',
                ]);
                $createdCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to create '{$item['salary_type']}': " . $e->getMessage();
            }
        }
        
        // Return response
        if ($createdCount > 0) {
            $message = "$createdCount salary type(s) created successfully for department .";
            if (!empty($errors)) {
                $message .= " But " . count($errors) . " item(s) failed: " . implode(' ', $errors);
            }
            return redirect()->route('salarytype.index')->with('success', $message);
        } else {
            $errorMsg = !empty($errors) ? implode(' ', $errors) : 'No salary types were created.';
            return redirect()->back()
                ->with('error', 'Failed to create salary types. ' . $errorMsg)
                ->withInput();
        }
    }



    public function edit($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $salarytype = Salarytype::findOrFail($id); // Fetch the salarytype to edit
        return view('user.salarytype.edit', compact('salarytype')); // Return the edit view
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
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }

        $companyId = Auth::id();
        $employees = Employee::with(['department', 'designation'])
            ->where('company_id', $companyId)
            ->where('status', '1')
            ->orderBy('name')
            ->get();

        $departments = Department::where('company_id', $companyId)
            ->where('status', '1')
            ->orderBy('name')
            ->get();

        $salarytypes = Salarytype::where('company_id', $companyId)
            ->where('status', '1')
            ->get();

        $currentMonth = (int)date('m');
        $currentYear = (int)date('Y');

        $totalEmployees = $employees->count();
        $processedCount = EmployeeSalarySummary::where('company_id', $companyId)
            ->where('salary_month', $currentMonth)
            ->where('salary_year', $currentYear)
            ->count();
        $pendingCount = max(0, $totalEmployees - $processedCount);

        return view('user.salarytype.generate_salary', compact(
            'employees',
            'departments',
            'salarytypes',
            'totalEmployees',
            'processedCount',
            'pendingCount',
            'currentMonth',
            'currentYear'
        ));
    }

    /**
     * Shared payroll calculator for accurate Indian HRMS calculations
     */
    private function calculateEmployeePayroll($employee, $month, $year, $overrideBasic = null)
    {
        return app(\App\Services\Payroll\PayrollCalculatorService::class)->calculate(
            $employee,
            (int)$month,
            (int)$year,
            $overrideBasic
        );
    }

    public function getEmployeeSalary(Request $request, $id)
    {
        $month = (int)$request->month;
        $year = (int)($request->year ?? date('Y'));

        $employee = Employee::with(['department', 'designation', 'shifts'])
            ->where('company_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found.',
                'section' => '<div class="alert alert-danger py-3"><i class="fa-solid fa-triangle-exclamation me-2"></i> Employee not found.</div>'
            ], 404);
        }

        $calc = $this->calculateEmployeePayroll($employee, $month, $year, $request->basic_salary);

        // Build Modern HRMS Preview HTML
        $html = '';

        // 1. Attendance & Base Salary Header Card
        $html .= '
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-primary bg-gradient text-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            ' . strtoupper(substr($employee->name, 0, 1)) . '
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-white">' . e($employee->name) . '</h5>
                            <small class="text-white-50">' . e($calc['department_name']) . ' &bull; Emp ID: ' . e($employee->emp_id ?? 'N/A') . '</small>
                        </div>
                    </div>
                    <span class="badge bg-white text-primary fw-semibold px-3 py-2 rounded-pill">
                        ' . Carbon::create($year, $month, 1)->format('F Y') . ' Cycle
                    </span>
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold text-dark mb-1">
                                <i class="fa-solid fa-money-bill-wave text-success me-1"></i> Basic Salary <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 fw-bold text-muted">₹</span>
                                <input type="number" step="0.01" id="basic_salary" name="basic_salary" class="form-control border-start-0 fw-bold text-dark fs-6" value="' . $calc['basic_salary'] . '" required>
                            </div>
                            <small class="text-muted">Daily Rate: ₹' . number_format($calc['daily_salary'], 2) . ' / day</small>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="row g-2">
                                <div class="col-6 col-sm-3">
                                    <div class="p-2 rounded-3 bg-light text-center border">
                                        <small class="text-muted d-block">Working Days</small>
                                        <span class="fw-bold text-dark fs-6">' . $calc['working_days'] . '</span>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="p-2 rounded-3 bg-light text-center border">
                                        <small class="text-muted d-block">Holidays</small>
                                        <span class="fw-bold text-primary fs-6">' . $calc['holiday_count'] . '</span>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="p-2 rounded-3 bg-success bg-opacity-10 text-center border border-success border-opacity-25">
                                        <small class="text-success fw-semibold d-block">Present Days</small>
                                        <span class="fw-bold text-success fs-6">' . $calc['present_days'] . '</span>
                                    </div>
                                </div>
                                <div class="col-6 col-sm-3">
                                    <div class="p-2 rounded-3 bg-danger bg-opacity-10 text-center border border-danger border-opacity-25">
                                        <small class="text-danger fw-semibold d-block">Absent / LOP</small>
                                        <span class="fw-bold text-danger fs-6">' . $calc['absent_days'] . '</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        // Hidden Calculation Data Inputs for Form Submission
        $html .= '
        <input type="hidden" name="department_name" value="' . e($calc['department_name']) . '">
        <input type="hidden" name="total_working_days" value="' . $calc['total_working_days'] . '">
        <input type="hidden" name="holiday_count" value="' . $calc['holiday_count'] . '">
        <input type="hidden" name="working_days" value="' . $calc['working_days'] . '">
        <input type="hidden" name="absent_days" value="' . $calc['absent_days'] . '">
        <input type="hidden" name="present_days" value="' . $calc['present_days'] . '">
        <input type="hidden" name="other_deduction" id="other_deduction_input" value="' . ($calc['other_deduction'] ?? 0) . '">
        <input type="hidden" name="absent_deduction" value="' . ($calc['absent_deduction'] ?? 0) . '">
        <input type="hidden" name="late_deduction" value="' . ($calc['late_deduction'] ?? 0) . '">
        <input type="hidden" name="halfday_deduction" value="' . ($calc['halfday_deduction'] ?? 0) . '">
        <input type="hidden" name="reimbursement" id="reimbursement_input" value="' . ($calc['reimbursement'] ?? 0) . '">
        <input type="hidden" name="gross_amount" id="gross_amount" value="' . ($calc['net_salary'] ?? 0) . '">';

        // 2. Earnings & Deductions Split (Side by Side)
        $html .= '<div class="col-12 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-success bg-gradient text-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <span class="fw-bold"><i class="fa-solid fa-arrow-trend-up me-2"></i> Earnings & Allowances</span>
                    <span class="badge bg-white text-success fw-bold">₹' . number_format($calc['gross_earnings'], 2) . '</span>
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <span class="fw-semibold text-dark">Basic Salary</span>
                            <small class="text-muted d-block">Base compensation</small>
                        </div>
                        <span class="fw-bold text-dark basic-salary-display">₹' . number_format($calc['basic_salary'], 2) . '</span>
                    </div>';

        if (!empty($calc['earnings'])) {
            foreach ($calc['earnings'] as $earn) {
                $html .= '
                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <div>
                        <span class="fw-semibold text-dark">' . e($earn['label']) . '</span>
                        <small class="text-muted d-block">' . e($earn['amount_type']) . ' allowance</small>
                    </div>
                    <div class="input-group input-group-sm" style="width: 140px;">
                        <span class="input-group-text bg-light">₹</span>
                        <input type="number" step="0.01" name="salary_type_' . $earn['id'] . '" class="form-control text-end fw-semibold salary_type_amount" data-paymenttype="Earning" value="' . $earn['amount'] . '">
                    </div>
                </div>';
            }
        }

        if ($calc['reimbursement'] > 0) {
            $html .= '
            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                <div>
                    <span class="fw-semibold text-info">Expense Reimbursement</span>
                    <small class="text-muted d-block">Approved claims</small>
                </div>
                <span class="fw-bold text-info">₹' . number_format($calc['reimbursement'], 2) . '</span>
            </div>';
        }

        $html .= '
                    <div class="d-flex align-items-center justify-content-between pt-3 mt-2 fw-bold text-success fs-6">
                        <span>Total Gross Earnings</span>
                        <span id="total_earnings_display">₹' . number_format($calc['gross_earnings'], 2) . '</span>
                    </div>
                </div>
            </div>
        </div>';

        // Deductions Column
        $html .= '<div class="col-12 col-lg-6 mb-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-danger bg-gradient text-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <span class="fw-bold"><i class="fa-solid fa-arrow-trend-down me-2"></i> Deductions & LOP</span>
                    <span class="badge bg-white text-danger fw-bold">₹' . number_format($calc['total_deductions'], 2) . '</span>
                </div>
                <div class="card-body p-4 bg-white">';

        if (!empty($calc['deductions'])) {
            foreach ($calc['deductions'] as $ded) {
                $html .= '
                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <div>
                        <span class="fw-semibold text-dark">' . e($ded['label']) . '</span>
                        <small class="text-muted d-block">Statutory / policy deduction</small>
                    </div>
                    <div class="input-group input-group-sm" style="width: 140px;">
                        <span class="input-group-text bg-light">₹</span>
                        <input type="number" step="0.01" name="salary_type_' . $ded['id'] . '" class="form-control text-end fw-semibold salary_type_amount" data-paymenttype="Deduction" value="' . $ded['amount'] . '">
                    </div>
                </div>';
            }
        }

        // Attendance Deduction Box
        $html .= '
                <div class="p-3 bg-light rounded-3 my-2 border">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-semibold text-danger"><i class="fa-solid fa-clock-rotate-left me-1"></i> Attendance Loss of Pay (LOP)</span>
                        <span class="fw-bold text-danger" id="lop_display">₹' . number_format($calc['other_deduction'], 2) . '</span>
                    </div>
                    <div class="row g-1 text-muted" style="font-size: 0.8rem;">
                        <div class="col-4">Absent: <strong>' . ($calc['absent_count'] ?? $calc['absent_days'] ?? 0) . 'd</strong> (₹' . number_format($calc['absent_deduction'] ?? 0, 2) . ')</div>
                        <div class="col-4">Late: <strong>' . ($calc['late_count'] ?? 0) . 'd</strong> (₹' . number_format($calc['late_deduction'] ?? 0, 2) . ')</div>
                        <div class="col-4">Half-day: <strong>' . ($calc['halfday_count'] ?? 0) . 'd</strong> (₹' . number_format($calc['halfday_deduction'] ?? 0, 2) . ')</div>
                    </div>
                </div>';

        $html .= '
                    <div class="d-flex align-items-center justify-content-between pt-3 mt-2 fw-bold text-danger fs-6">
                        <span>Total Deductions</span>
                        <span id="total_deductions_display">₹' . number_format($calc['total_deductions'], 2) . '</span>
                    </div>
                </div>
            </div>
        </div>';

        // 3. Net Take-Home Hero Pill
        $html .= '
        <div class="col-12 mt-2">
            <div class="card border-0 rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white;">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-12 col-md-7">
                            <span class="text-white-50 text-uppercase small fw-bold letter-spacing d-block mb-1">Calculated Net Payable (Take Home)</span>
                            <h2 class="fw-bold text-white mb-1" id="net_salary_display">₹' . number_format($calc['net_salary'], 2) . '</h2>
                            <small class="text-white-50" id="net_words_display">
                                <i class="fa-solid fa-receipt me-1"></i> ' . ucwords(\App\Helpers\Helper::convert($calc['net_salary'])) . ' Only
                            </small>
                        </div>
                        <div class="col-12 col-md-5 text-md-end">
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 text-white">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span class="small fw-semibold">Ready for Disbursement</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return response()->json([
            'status' => 'success',
            'department_name' => $calc['department_name'],
            'department_id' => $employee->department_id,
            'employee_name' => $employee->name,
            'data' => $calc,
            'section' => $html
        ]);
    }

    public function saveEmployeeSalary(Request $request)
    {
        $request->validate([
            'employee' => 'required|exists:employees,id',
            'month' => 'required|integer|between:1,12',
        ]);

        $employeeId = $request->employee;
        $month = (int)$request->month;
        $year = (int)($request->year ?? date('Y'));

        $employee = Employee::where('company_id', Auth::id())->findOrFail($employeeId);

        // Delete any existing components for this month/year to allow clean re-generation
        EmployeeSalary::where('company_id', Auth::id())
            ->where('employee_id', $employeeId)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->delete();

        $basicSalary = (float)str_replace(',', '', $request->input('basic_salary', $employee->salary ?? 0));
        $grossAmount = (float)str_replace(',', '', $request->input('gross_amount', 0));
        $otherDeduction = (float)str_replace(',', '', $request->input('other_deduction', 0));
        $reimbursement = (float)str_replace(',', '', $request->input('reimbursement', 0));
        $absentDays = (float)$request->input('absent_days', 0);
        $workingDays = (int)$request->input('working_days', 26);
        $totalWorkingDays = (int)$request->input('total_working_days', 26);
        $holidayCount = (int)$request->input('holiday_count', 0);
        $presentDays = (float)$request->input('present_days', max(0, $workingDays - $absentDays));
        $departmentName = $request->input('department_name', $employee->department ? $employee->department->name : 'General');

        // Fetch salary types for this department or company
        $salarytypes = collect();
        if ($employee->department_id) {
            $salarytypes = Salarytype::where('company_id', Auth::id())
                ->where('department_id', $employee->department_id)
                ->where('status', '1')
                ->get();
        }
        if ($salarytypes->isEmpty()) {
            $salarytypes = Salarytype::where('company_id', Auth::id())
                ->where(function ($q) {
                    $q->whereNull('department_id')->orWhere('department_id', 0);
                })
                ->where('status', '1')
                ->get();
        }

        $totalEarning = 0;
        $totalDeduction = 0;

        if (!$salarytypes->isEmpty()) {
            foreach ($salarytypes as $salarytype) {
                $componentAmount = str_replace(',', '', $request->input('salary_type_' . $salarytype->id));
                $componentAmount = is_numeric($componentAmount) ? (float)$componentAmount : 0;

                $salary = new EmployeeSalary();
                $salary->company_id = Auth::id();
                $salary->employee_id = $employeeId;
                $salary->salary_month = $month;
                $salary->salary_year = $year;
                $salary->salary_type_id = $salarytype->id;
                $salary->salary_type_amount = $salarytype->amount;
                $salary->salary_type_amount_type = $salarytype->amount_type;
                $salary->amount = $componentAmount;
                $salary->label = $salarytype->salary_type;
                $salary->basic_salary = $basicSalary;
                $salary->gross_salary = $grossAmount;
                $salary->other_deduction = $otherDeduction;
                $salary->absent_days = $absentDays;
                $salary->working_days = $workingDays;
                $salary->reimbursement = $reimbursement;
                $salary->save();

                if ($salarytype->payment_type == 'Earning') {
                    $totalEarning += $componentAmount;
                } else {
                    $totalDeduction += $componentAmount;
                }
            }
        } else {
            // If no custom salary types configured, record base salary component
            $salary = new EmployeeSalary();
            $salary->company_id = Auth::id();
            $salary->employee_id = $employeeId;
            $salary->salary_month = $month;
            $salary->salary_year = $year;
            $salary->salary_type_id = null;
            $salary->salary_type_amount = $basicSalary;
            $salary->salary_type_amount_type = 'Flat';
            $salary->amount = $basicSalary;
            $salary->label = 'Basic Salary';
            $salary->basic_salary = $basicSalary;
            $salary->gross_salary = $grossAmount;
            $salary->other_deduction = $otherDeduction;
            $salary->absent_days = $absentDays;
            $salary->working_days = $workingDays;
            $salary->reimbursement = $reimbursement;
            $salary->save();
        }

        // Save canonical EmployeeSalarySummary record
        EmployeeSalarySummary::updateOrCreate(
            [
                'company_id' => Auth::id(),
                'employee_id' => $employeeId,
                'salary_month' => $month,
                'salary_year' => $year
            ],
            [
                'employee_name' => $employee->name,
                'department_name' => $departmentName,
                'basic_salary' => $basicSalary,
                'total_earning' => $totalEarning,
                'total_deduction' => $totalDeduction,
                'other_deduction' => $otherDeduction,
                'reimbursement' => $reimbursement,
                'net_salary' => $grossAmount,
                'absent_days' => $absentDays,
                'working_days' => $workingDays,
                'total_working_days' => $totalWorkingDays,
                'holiday_count' => $holidayCount,
                'present_days' => $presentDays,
                'status' => 'Generated',
                'generated_date' => now()
            ]
        );

        return redirect()->route('employeeSalaryList', [
            'month' => $month,
            'year' => $year,
            'employee_id' => $employeeId
        ])->with('success', 'Payroll successfully generated for ' . $employee->name . ' (' . Carbon::create($year, $month, 1)->format('F Y') . ').');
    }

    public function generateAllSalary(Request $request)
    {
        $month = (int)$request->month;
        $year = (int)($request->year ?? date('Y'));

        if (!$month || $month < 1 || $month > 12) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please select a valid payroll month.'
            ], 422);
        }

        $companyId = Auth::id();
        $employees = Employee::with(['department', 'shifts'])
            ->where('company_id', $companyId)
            ->where('status', '1')
            ->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active employees found to generate salary for.'
            ], 404);
        }

        $payrollService = app(\App\Services\Payroll\PayrollCalculatorService::class);
        $processedCount = 0;

        foreach ($employees as $employee) {
            $calc = $payrollService->calculate($employee, $month, $year);
            $payrollService->savePayrollRecord($companyId, $employee->id, $month, $year, $calc);
            $processedCount++;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Payroll generated successfully for ' . $processedCount . ' employees.',
            'processed_count' => $processedCount,
            'redirect' => route('employeeSalaryList') . '?month=' . $month . '&year=' . $year
        ]);
    }

    public function employeeSalaryList(Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }

        $companyId = Auth::id();
        $month = (int)$request->input('month', date('m'));
        $year = (int)$request->input('year', date('Y'));
        $employee_id = $request->input('employee_id', '');
        $department_id = $request->input('department_id', '');

        $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

        $employees = Employee::where('company_id', $companyId)
            ->where('status', '1')
            ->select('id', 'name', 'emp_id', 'department_id', 'designation_id')
            ->orderBy('name')
            ->get();

        $departments = Department::where('company_id', $companyId)
            ->where('status', '1')
            ->orderBy('name')
            ->get();

        $summariesQuery = EmployeeSalarySummary::with(['employee.department', 'employee.designation'])
            ->where('company_id', $companyId)
            ->where('salary_month', $month)
            ->where('salary_year', $year);

        if (!empty($employee_id)) {
            $summariesQuery->where('employee_id', $employee_id);
        }

        if (!empty($department_id)) {
            $summariesQuery->whereHas('employee', function ($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $salarySummaries = $summariesQuery->orderBy('id', 'desc')->paginate(20);

        // KPI metrics for current selected period
        $kpiQuery = EmployeeSalarySummary::where('company_id', $companyId)
            ->where('salary_month', $month)
            ->where('salary_year', $year);

        if (!empty($employee_id)) {
            $kpiQuery->where('employee_id', $employee_id);
        }

        $totalDisbursed = (float)$kpiQuery->sum('net_salary');
        $employeesPaid = (int)$kpiQuery->count();
        $totalBasic = (float)$kpiQuery->sum('basic_salary');
        $totalEarnings = (float)$kpiQuery->sum('total_earning');
        $totalDeductions = (float)($kpiQuery->sum('total_deduction') + $kpiQuery->sum('other_deduction'));

        // Alias for backwards-compatibility with views referencing $employeeSalaries
        $employeeSalaries = $salarySummaries;

        return view('user.salarytype.employee_salary', compact(
            'salarySummaries',
            'employeeSalaries',
            'monthArray',
            'employees',
            'departments',
            'month',
            'year',
            'employee_id',
            'department_id',
            'totalDisbursed',
            'employeesPaid',
            'totalBasic',
            'totalEarnings',
            'totalDeductions'
        ));
    }

    public function employeeSalaryDetails($emp_id, Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }

        $company = Auth::user();
        $month = (int)($request->month ?? date('m'));
        $year = (int)($request->year ?? date('Y'));
        $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

        $salarySummary = EmployeeSalarySummary::with(['employee.department', 'employee.designation', 'employee.bankAccount'])
            ->where('company_id', Auth::id())
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->where('employee_id', $emp_id)
            ->first();

        if (!$salarySummary) {
            return back()->with('error', 'Salary details not found for the selected period.');
        }

        $employeeSalaries = EmployeeSalary::with('salarytype')
            ->where('company_id', Auth::id())
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->where('employee_id', $emp_id)
            ->get();

        return view('user.salarytype.employee_salary_details', compact('salarySummary', 'employeeSalaries', 'monthArray', 'company'));
    }

    public function salaryPDF(Request $request)
    {
        
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }

        $company = Auth::user();
        $emp_id = $request->emp_id;
        $month = $request->month;
        $year = $request->year;
        $export = new SalaryPDFExport($company, $emp_id, $month, $year);
        return $export->export();
    }

    public function deleteSalary($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }

        $companyId = Auth::user()->id;
        $summary = EmployeeSalarySummary::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if ($summary) {
            // Delete associated employee_salaries component records
            EmployeeSalary::where('employee_id', $summary->employee_id)
                ->where('salary_month', $summary->salary_month)
                ->where('salary_year', $summary->salary_year)
                ->where('company_id', $companyId)
                ->delete();

            $empName = $summary->employee_name ?? 'Employee';
            $summary->delete();

            return redirect()->back()->with('success', "Salary disbursement record for {$empName} has been deleted successfully.");
        }

        return redirect()->back()->with('error', 'Salary record not found or unauthorized.');
    }

    public function export(Request $request)
    {
        $month = $request->input('selected_month', date('m') - 1);
        $year = $request->input('selected_year', date('Y'));
        $emp_id = $request->input('employee_id', '');
        $company = Auth::user();
        $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        
        

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'EmpId',
            'Company',
            'Employee',
            'PF Number',
            'ESI Number',
            'Salary Month',
            'Salary Year',
            'Basic Salary',
            'Gross Salary',
            'Reimbursement',
            'Absent Days',
            'Working Days',
            //'Payment Type'
        ];

        $salarytypes = Salarytype::where('company_id', Auth::id())->where('status', '1')->get();
        foreach ($salarytypes as $salarytype) {            
            $headers[] = $salarytype->salary_type;
        }

        // Headers
        $sheet->fromArray($headers, null, 'A1');

        // Data
        $row = 2;
        
        $employees = Employee::where('company_id', Auth::id())->select('id', 'name')->get();
        foreach ($employees as $employee) {
            $emp_id = $employee->id;
            $employeeSalaries = EmployeeSalary::where('company_id', Auth::id())->where('salary_month', $month)->where('salary_year', $year)->where('employee_id', $emp_id)->get();
            $employeeSalary = EmployeeSalary::where('company_id', Auth::id())->where('salary_month', $month)->where('salary_year', $year)->where('employee_id', $emp_id)->first();
            if(!empty($employeeSalary)){
                $body_array = [
                    $employeeSalary->employee->emp_id,
                    $employeeSalary->company->company_name,
                    $employeeSalary->employee->name,
                    $employeeSalary->pf_number,
                    $employeeSalary->esi_number,
                    $monthArray[ $employeeSalary->salary_month - 1],
                    $employeeSalary->salary_year,
                    $employeeSalary->basic_salary,
                    $employeeSalary->gross_salary,
                    $employeeSalary->reimbursement,
                    $employeeSalary->absent_days,
                    $employeeSalary->working_days,                    
                    //$employeeSalary->salarytype->payment_type,
                ];
            
                foreach ($employeeSalaries as $salary) {
                    //$body_array[]=$salary->salarytype->payment_type;
                    $body_array[]=$salary->amount;
                }
            } else {
                $body_array = array();
            }
            $sheet->fromArray($body_array, null, "A{$row}");

                $row++;
        }
        $writer = new Xlsx($spreadsheet);

        // Return as a streamed response
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="Salary.xlsx"',
        ]);
    }

    function getWorkingDaysInMonth($year, $month, $offDays = [6, 0])
    {
        // Create a date range for the full month
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        $workingDays = 0;

        foreach ($period as $date) {
            if (!in_array($date->dayOfWeek, $offDays)) {
                $workingDays++;
            }
        }

        // dd($workingDays);

        return $workingDays;
    }

    function getHolidayDaysInMonth($year, $month)
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $holidays = Holiday::where('company_id', Auth::id())
            ->whereDate('end_date', '>=', $startOfMonth)
            ->whereDate('start_date', '<=', $endOfMonth)
            ->get();

        $holidayDates = collect();

        foreach ($holidays as $holiday) {
            $start = Carbon::parse($holiday->start_date)->greaterThan($startOfMonth) ? Carbon::parse($holiday->start_date) : $startOfMonth;
            $end = Carbon::parse($holiday->end_date)->lessThan($endOfMonth) ? Carbon::parse($holiday->end_date) : $endOfMonth;

            $period = CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $holidayDates->push($date->toDateString());
            }
        }

        // Remove duplicates in case of overlapping holidays
        $uniqueHolidayDates = $holidayDates->unique();

        return $uniqueHolidayDates->count();
    }

    public function defaultGraceSetting(Request $request)
    {
        $companies = CompanyDetail::all();
        foreach ($companies as $company) {
            $this->createDefaultGraceSettings($company->id);
        }
    }

    private function createDefaultGraceSettings($company)
    {
        $graceSettings = GraceSetting::where('company_id', $company)->get();    
        if ($graceSettings->isEmpty()) {
            GraceSetting::create([
                'company_id' => $company,
                'name' => 'Grace Time',
                'label' => 'grace_time',
                'value' => '15',
                'status' => 'Active', // Set status to 1 (active) by default
            ]);
            GraceSetting::create([
                'company_id' => $company,
                'name' => 'Grace Days',
                'label' => 'grace_days',
                'value' => '3',
                'status' => 'Active', // Set status to 1 (active) by default
            ]);
            GraceSetting::create([
                'company_id' => $company,
                'name' => 'Over Time Charges',
                'label' => 'over_time_charges',
                'value' => '3',
                'status' => 'Active', // Set status to 1 (active) by default
            ]);
            echo $company;
        }
    }
}