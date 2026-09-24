<?php

namespace App\Http\Controllers\Employee;

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
use App\Models\Department;
use App\Models\User;
use App\Exports\SalaryPDFExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RealRashid\SweetAlert\Facades\Alert;

class EmployeePayrollManagementController extends Controller
{
    /**
     * Get the authenticated employee.
     */
    protected function getAuthEmployee()
    {
        return Auth::guard('employee')->user();
    }

    /**
     * Ensure the employee has the requested permission or abort.
     */
    protected function authorizePermission($permissionKey)
    {
        $employee = $this->getAuthEmployee();
        if (!$employee || !$employee->hasPermission($permissionKey)) {
            abort(403, 'Unauthorized. Your designation does not have permission to access this payroll module.');
        }
        return $employee;
    }

    /**
     * 1. Monthly Salary Records (Default Register)
     */
    public function records(Request $request)
    {
        $employee = $this->authorizePermission('payroll.view');
        $companyId = $employee->company_id;

        $month = (int)($request->month ?? $request->selected_month ?? date('m'));
        $year = (int)($request->year ?? $request->selected_year ?? date('Y'));
        $search = $request->input('search');

        $query = EmployeeSalarySummary::with(['employee.department', 'employee.designation'])
            ->where('company_id', $companyId)
            ->where('salary_month', $month)
            ->where('salary_year', $year);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                  ->orWhere('department_name', 'like', "%{$search}%");
            });
        }

        $salarySummaries = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Metric Aggregations
        $totalDisbursed = EmployeeSalarySummary::where('company_id', $companyId)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->sum('net_salary');

        $employeesPaid = EmployeeSalarySummary::where('company_id', $companyId)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->count();

        $totalActiveEmployees = Employee::where('company_id', $companyId)->where('status', '1')->count();
        $averageSalary = $employeesPaid > 0 ? ($totalDisbursed / $employeesPaid) : 0;
        $totalDeductions = EmployeeSalarySummary::where('company_id', $companyId)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->sum('total_deduction');

        return view('employee.management.payroll.index', compact(
            'employee',
            'salarySummaries',
            'month',
            'year',
            'search',
            'totalDisbursed',
            'employeesPaid',
            'totalActiveEmployees',
            'averageSalary',
            'totalDeductions'
        ));
    }

    /**
     * 2. Run / Generate Salary Studio
     */
    public function generateSalary(Request $request)
    {
        $employee = $this->authorizePermission('payroll.view');
        $companyId = $employee->company_id;

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

        return view('employee.management.payroll.generate', compact(
            'employee',
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
     * Live AJAX Payroll Calculation
     */
    public function getEmployeeSalary(Request $request, $id)
    {
        $authEmployee = $this->authorizePermission('payroll.view');
        $companyId = $authEmployee->company_id;

        $month = (int)$request->month;
        $year = (int)($request->year ?? date('Y'));

        $targetEmployee = Employee::with(['department', 'designation', 'shifts'])
            ->where('company_id', $companyId)
            ->where('id', $id)
            ->first();

        if (!$targetEmployee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found.',
                'section' => '<div class="alert alert-danger py-3"><i class="fa-solid fa-triangle-exclamation me-2"></i> Employee not found.</div>'
            ], 404);
        }

        $calc = $this->calculateEmployeePayroll($targetEmployee, $month, $year, $request->basic_salary);

        // Build HTML preview exactly matching company panel
        $html = '';

        // 1. Attendance & Base Salary Header Card
        $html .= '
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-primary bg-gradient text-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            ' . strtoupper(substr($targetEmployee->name, 0, 1)) . '
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-white">' . e($targetEmployee->name) . '</h5>
                            <small class="text-white-50">' . e($calc['department_name']) . ' &bull; Emp ID: ' . e($targetEmployee->emp_id ?? 'N/A') . '</small>
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
                <div class="card-body p-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                    <div>
                        <span class="text-white-50 small text-uppercase fw-bold letter-spacing">Net Take-Home Payable</span>
                        <h2 class="fw-bold text-white mb-0 mt-1" id="net_salary_display">₹' . number_format($calc['net_salary'], 2) . '</h2>
                        <small class="text-white-50">Disbursement for ' . Carbon::create($year, $month, 1)->format('F Y') . ' &bull; Direct Transfer</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-3 py-2 rounded-pill fs-6">
                            <i class="fa-solid fa-shield-check me-1"></i> Ready for Confirmation
                        </span>
                    </div>
                </div>
            </div>
        </div>';

        return response()->json([
            'status' => 'success',
            'department_name' => $calc['department_name'],
            'department_id' => $targetEmployee->department_id,
            'employee_name' => $targetEmployee->name,
            'data' => $calc,
            'section' => $html,
            'basic_salary' => $calc['basic_salary'],
            'gross_amount' => $calc['net_salary'],
            'other_deduction' => $calc['other_deduction'],
            'absent_days' => $calc['absent_days'],
            'working_days' => $calc['working_days'],
            'reimbursement' => $calc['reimbursement']
        ]);
    }

    /**
     * Save Single Employee Salary
     */
    public function saveEmployeeSalary(Request $request)
    {
        $authEmployee = $this->authorizePermission('payroll.create');
        $companyId = $authEmployee->company_id;

        $request->validate([
            'employee' => 'required|exists:employees,id',
            'month' => 'required|integer|between:1,12',
        ]);

        $employeeId = $request->employee;
        $month = (int)$request->month;
        $year = (int)($request->year ?? date('Y'));

        $targetEmployee = Employee::where('company_id', $companyId)->findOrFail($employeeId);

        // Clean up previous components to avoid duplicates
        EmployeeSalary::where('company_id', $companyId)
            ->where('employee_id', $employeeId)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->delete();

        $basicSalary = (float)str_replace(',', '', $request->input('basic_salary', $targetEmployee->salary ?? 0));
        $grossAmount = (float)str_replace(',', '', $request->input('gross_amount', 0));
        $otherDeduction = (float)str_replace(',', '', $request->input('other_deduction', 0));
        $reimbursement = (float)str_replace(',', '', $request->input('reimbursement', 0));
        $absentDays = (float)$request->input('absent_days', 0);
        $workingDays = (int)$request->input('working_days', 26);
        $totalWorkingDays = (int)$request->input('total_working_days', 26);
        $holidayCount = (int)$request->input('holiday_count', 0);
        $presentDays = (float)$request->input('present_days', max(0, $workingDays - $absentDays));
        $departmentName = $request->input('department_name', $targetEmployee->department ? $targetEmployee->department->name : 'General');

        // Fetch salary types
        $salarytypes = collect();
        if ($targetEmployee->department_id) {
            $salarytypes = Salarytype::where('company_id', $companyId)
                ->where('department_id', $targetEmployee->department_id)
                ->where('status', '1')
                ->get();
        }
        if ($salarytypes->isEmpty()) {
            $salarytypes = Salarytype::where('company_id', $companyId)
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
                $salary->company_id = $companyId;
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
            // Nullable foreign key when no custom salary types configured
            $salary = new EmployeeSalary();
            $salary->company_id = $companyId;
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

        EmployeeSalarySummary::updateOrCreate(
            [
                'company_id' => $companyId,
                'employee_id' => $employeeId,
                'salary_month' => $month,
                'salary_year' => $year
            ],
            [
                'employee_name' => $targetEmployee->name,
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

        return redirect()->route('employee.management.payroll.records', [
            'month' => $month,
            'year' => $year,
            'search' => $targetEmployee->name
        ])->with('success', 'Payroll successfully generated for ' . $targetEmployee->name . ' (' . Carbon::create($year, $month, 1)->format('F Y') . ').');
    }

    /**
     * Batch Generate Payroll for All Active Staff
     */
    public function generateAllSalary(Request $request)
    {
        $authEmployee = $this->authorizePermission('payroll.create');
        $companyId = $authEmployee->company_id;

        $month = (int)$request->month;
        $year = (int)($request->year ?? date('Y'));

        if (!$month || $month < 1 || $month > 12) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please select a valid payroll month.'
            ], 422);
        }

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

        foreach ($employees as $emp) {
            $calc = $payrollService->calculate($emp, $month, $year);
            $payrollService->savePayrollRecord($companyId, $emp->id, $month, $year, $calc);
            $processedCount++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Payroll generated successfully for {$processedCount} employees.",
            'processed_count' => $processedCount,
            'redirect' => route('employee.management.payroll.records', ['month' => $month, 'year' => $year])
        ]);
    }

    /**
     * View Employee Salary Breakdown / Details
     */
    public function details($emp_id, Request $request)
    {
        $employee = $this->authorizePermission('payroll.view');
        $companyId = $employee->company_id;
        $company = User::find($companyId);

        $month = (int)($request->month ?? date('m'));
        $year = (int)($request->year ?? date('Y'));
        $monthArray = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        $salarySummary = EmployeeSalarySummary::with(['employee.department', 'employee.designation', 'employee.bankAccount'])
            ->where('company_id', $companyId)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->where('employee_id', $emp_id)
            ->first();

        if (!$salarySummary) {
            return back()->with('error', 'Salary details not found for the selected period.');
        }

        $employeeSalaries = EmployeeSalary::with('salarytype')
            ->where('company_id', $companyId)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->where('employee_id', $emp_id)
            ->get();

        return view('employee.management.payroll.details', compact(
            'employee',
            'salarySummary',
            'employeeSalaries',
            'monthArray',
            'company'
        ));
    }

    /**
     * Download Payslip PDF
     */
    public function downloadSlip(Request $request)
    {
        $authEmployee = $this->authorizePermission('payroll.view');
        $companyId = $authEmployee->company_id;
        $company = User::find($companyId);

        $emp_id = $request->emp_id;
        $month = $request->month;
        $year = $request->year;

        $export = new SalaryPDFExport($company, $emp_id, $month, $year);
        return $export->export();
    }

    /**
     * Delete Salary Disbursement Record
     */
    public function deleteSalary($id)
    {
        $authEmployee = $this->authorizePermission('payroll.delete');
        $companyId = $authEmployee->company_id;

        $summary = EmployeeSalarySummary::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if ($summary) {
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

    /**
     * Export Monthly Payroll to Excel (.xlsx)
     */
    public function export(Request $request)
    {
        $authEmployee = $this->authorizePermission('payroll.view');
        $companyId = $authEmployee->company_id;
        $company = User::find($companyId);

        $month = (int)$request->input('selected_month', date('m'));
        $year = (int)$request->input('selected_year', date('Y'));
        $monthName = Carbon::create($year, $month, 1)->format('F');

        $summaries = EmployeeSalarySummary::with(['employee.department', 'employee.designation'])
            ->where('company_id', $companyId)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->orderBy('employee_name')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Payroll {$monthName} {$year}");

        // Title Header
        $sheet->setCellValue('A1', ($company->name ?? 'Company') . " - Payroll Register");
        $sheet->setCellValue('A2', "Cycle: {$monthName} {$year} | Generated on " . now()->format('d M Y, h:i A'));
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');

        // Column Headers
        $headers = [
            'A4' => 'Emp ID',
            'B4' => 'Employee Name',
            'C4' => 'Department',
            'D4' => 'Working Days',
            'E4' => 'Present Days',
            'F4' => 'Absent / LOP',
            'G4' => 'Basic Salary (₹)',
            'H4' => 'Earnings (₹)',
            'I4' => 'Deductions (₹)',
            'J4' => 'Loss of Pay (₹)',
            'K4' => 'Net Salary (₹)'
        ];

        foreach ($headers as $cell => $title) {
            $sheet->setCellValue($cell, $title);
        }

        $row = 5;
        foreach ($summaries as $item) {
            $sheet->setCellValue('A' . $row, $item->employee->emp_id ?? $item->employee_id);
            $sheet->setCellValue('B' . $row, $item->employee_name);
            $sheet->setCellValue('C' . $row, $item->department_name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $item->working_days);
            $sheet->setCellValue('E' . $row, $item->present_days);
            $sheet->setCellValue('F' . $row, $item->absent_days);
            $sheet->setCellValue('G' . $row, $item->basic_salary);
            $sheet->setCellValue('H' . $row, $item->total_earning);
            $sheet->setCellValue('I' . $row, $item->total_deduction);
            $sheet->setCellValue('J' . $row, $item->other_deduction);
            $sheet->setCellValue('K' . $row, $item->net_salary);
            $row++;
        }

        // Auto width
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Payroll_Register_' . $monthName . '_' . $year . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * 3. Salary Components Management
     */
    public function componentsIndex()
    {
        $employee = $this->authorizePermission('payroll.view');
        $companyId = $employee->company_id;

        $salarytypes = Salarytype::with('department')
            ->where('company_id', $companyId)
            ->get();

        return view('employee.management.payroll.components.index', compact('employee', 'salarytypes'));
    }

    public function componentsCreate()
    {
        $employee = $this->authorizePermission('payroll.create');
        $companyId = $employee->company_id;

        $departments = Department::where('company_id', $companyId)->get();

        return view('employee.management.payroll.components.create', compact('employee', 'departments'));
    }

    public function componentsStore(Request $request)
    {
        $authEmployee = $this->authorizePermission('payroll.create');
        $companyId = $authEmployee->company_id;

        $validatedData = $request->validate([
            'salary_types' => 'required|array|min:1',
            'salary_types.*.payment_type' => 'required|string|in:Earning,Deduction',
            'salary_types.*.salary_type' => 'required|string|max:255',
            'salary_types.*.salary_type_description' => 'nullable|string',
            'salary_types.*.amount' => 'required|numeric|min:0',
            'salary_types.*.amount_type' => 'required|string|in:Flat,Percentage',
            'salary_types.*.department_id' => 'required|exists:departments,id',
            'salary_types.*.status' => 'required|in:0,1',
        ]);

        foreach ($validatedData['salary_types'] as $salaryTypeData) {
            Salarytype::create([
                'company_id' => $companyId,
                'department_id' => $salaryTypeData['department_id'],
                'payment_type' => $salaryTypeData['payment_type'],
                'salary_type' => $salaryTypeData['salary_type'],
                'salary_type_description' => $salaryTypeData['salary_type_description'] ?? null,
                'amount' => $salaryTypeData['amount'],
                'amount_type' => $salaryTypeData['amount_type'],
                'status' => $salaryTypeData['status'],
            ]);
        }

        Alert::success('Success', 'Salary Component(s) added successfully!');
        return redirect()->route('employee.management.payroll.components')->with('success', 'Salary Components created successfully.');
    }

    public function componentsEdit($id)
    {
        $employee = $this->authorizePermission('payroll.edit');
        $companyId = $employee->company_id;

        $salarytype = Salarytype::where('company_id', $companyId)->findOrFail($id);
        $departments = Department::where('company_id', $companyId)->get();

        return view('employee.management.payroll.components.edit', compact('employee', 'salarytype', 'departments'));
    }

    public function componentsUpdate(Request $request, $id)
    {
        $authEmployee = $this->authorizePermission('payroll.edit');
        $companyId = $authEmployee->company_id;

        $salarytype = Salarytype::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'payment_type' => 'required|string|in:Earning,Deduction',
            'salary_type' => 'required|string|max:255',
            'salary_type_description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'amount_type' => 'required|string|in:Flat,Percentage',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:0,1',
        ]);

        $salarytype->update([
            'payment_type' => $request->payment_type,
            'salary_type' => $request->salary_type,
            'salary_type_description' => $request->salary_type_description,
            'amount' => $request->amount,
            'amount_type' => $request->amount_type,
            'department_id' => $request->department_id,
            'status' => $request->status,
        ]);

        Alert::success('Success', 'Salary Component updated successfully!');
        return redirect()->route('employee.management.payroll.components')->with('success', 'Salary Component updated successfully.');
    }

    public function componentsDestroy($id)
    {
        $authEmployee = $this->authorizePermission('payroll.delete');
        $companyId = $authEmployee->company_id;

        $salarytype = Salarytype::where('company_id', $companyId)->findOrFail($id);
        $salarytype->delete();

        Alert::success('Success', 'Salary Component deleted successfully!');
        return redirect()->route('employee.management.payroll.components')->with('success', 'Salary Component deleted successfully.');
    }

    /**
     * Shared Indian HRMS Payroll Engine
     */
    private function calculateEmployeePayroll($targetEmployee, $month, $year, $overrideBasic = null)
    {
        return app(\App\Services\Payroll\PayrollCalculatorService::class)->calculate(
            $targetEmployee,
            (int)$month,
            (int)$year,
            $overrideBasic
        );
    }
}
