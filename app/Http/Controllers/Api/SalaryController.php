<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\Salarytype;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\EmployeeSalary;
use App\Models\Attendance;
use App\Models\GraceSetting;
use App\Models\Expense;
use App\Models\Holiday;
use App\Models\EmployeeLeave;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Carbon\CarbonPeriod;



class SalaryController extends Controller
{

    public function storeSalaryType(Request $request)
    {
        // Optional: Manual validation
        if (!$request->has('salary_type') || !$request->has('amount')) {
            return response()->json([
                'success' => false,
                'message' => 'Required fields are missing.',
            ], 200);
        }

        $company = CompanyDetail::find($request->company_id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid company_id. Company not found.',
            ], 200);
        }

        // Save to DB
        $salarytype = Salarytype::create([
            'company_id' => $company->id,
            'payment_type' => $request->payment_type ?? null,
            'salary_type' => $request->salary_type ?? null,
            'salary_type_description' => $request->salary_type_description ?? null,
            'amount' => $request->amount,
            'amount_type' => $request->amount_type ?? 'Fixed', // Optional default
            'status' => '1',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Salary type created successfully.',
            'data' => $salarytype
        ], 200);
    }

    public function listSalaryType(Request $request)
    {
        // Ensure company_id is provided
        if (!$request->has('company_id') || empty($request->company_id)) {
            return response()->json([
                'success' => false,
                'message' => 'company_id is required.',
                'data' => [],
            ], 200);
        }

        // Fetch salary types for the specific company
        $salaryTypes = Salarytype::where('company_id', $request->company_id)
            ->orderBy('id', 'desc')
            ->get();

        if ($salaryTypes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No salary types found for the given company.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Salary types retrieved successfully.',
            'data' => $salaryTypes
        ], 200);
    }

    public function deleteSalaryType(Request $request)
    {
        $request->validate([
            'salary_type_id' => 'required|integer',
            'company_id' => 'required|integer',
        ]);

        $salaryType = Salarytype::where('id', $request->salary_type_id)
            ->where('company_id', $request->company_id)
            ->first();

        if (!$salaryType) {
            return response()->json([
                'success' => false,
                'message' => 'Salary type not found for this company.',
            ], 201);
        }

        $salaryType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Salary type deleted successfully.',
        ], 200);
    }


    public function generateEmployeeSalaryPreview(Request $request)
    {
        $request->validate([
            'company_id' => 'required|numeric',
            'employee_id' => 'required|numeric',
        ]);

        $company_id = (int)$request->company_id;
        $employee_id = (int)$request->employee_id;
        $month = (int)($request->month ?? date('m'));
        $year = (int)($request->year ?? date('Y'));

        $employee = Employee::with(['department', 'shifts'])
            ->where('id', $employee_id)
            ->where('company_id', $company_id)
            ->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        $payrollService = app(\App\Services\Payroll\PayrollCalculatorService::class);
        $calc = $payrollService->calculate($employee, $month, $year, $request->basic_salary);

        $earning = [];
        foreach ($calc['earnings'] as $e) {
            $earning[] = [
                'id' => $e['id'],
                'label' => $e['label'] ?? $e['name'],
                'amount' => $e['amount'],
                'amount_type' => $e['amount_type'],
                'payment_type' => 'Earning',
                'percentage' => ($e['amount_type'] === 'Percentage') ? $e['rate'] : null,
            ];
        }

        $deduction = [];
        foreach ($calc['deductions'] as $d) {
            $deduction[] = [
                'id' => $d['id'],
                'label' => $d['label'] ?? $d['name'],
                'amount' => $d['amount'],
                'amount_type' => $d['amount_type'],
                'payment_type' => 'Deduction',
                'percentage' => ($d['amount_type'] === 'Percentage') ? $d['rate'] : null,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Salary preview generated.',
            'data' => [
                'basic_salary' => $calc['basic_salary'],
                'earning' => $earning,
                'deduction' => $deduction,
                'other_deduction' => $calc['other_deduction'],
                'gross_salary' => $calc['net_salary'],
                'gross_earnings' => $calc['gross_earnings'],
                'absent_days' => $calc['absent_days'],
                'working_days' => $calc['working_days'],
                'expense' => $calc['reimbursement'],
                'pf_employee' => $calc['pf_employee'],
                'esi_employee' => $calc['esi_employee'],
                'pt_amount' => $calc['pt_amount'],
            ]
        ], 200);
    }

    public function saveGeneratedEmployeeSalary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|numeric',
            'employee_id' => 'required|numeric',
            'month' => 'required|numeric|min:1|max:12',
            'basic_salary' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $companyId = (int)$request->company_id;
        $employeeId = (int)$request->employee_id;
        $month = (int)$request->month;
        $year = (int)($request->year ?? date('Y'));

        $employee = Employee::with(['department', 'shifts'])
            ->where('id', $employeeId)
            ->where('company_id', $companyId)
            ->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        $payrollService = app(\App\Services\Payroll\PayrollCalculatorService::class);
        $calc = $payrollService->calculate($employee, $month, $year, $request->basic_salary);

        try {
            $summary = $payrollService->savePayrollRecord($companyId, $employeeId, $month, $year, $calc);

            return response()->json([
                'success' => true,
                'message' => 'Salary saved successfully.',
                'data' => $summary,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    public function salaryPDF(Request $request)
    {
        $company = CompanyDetail::find($request->company_id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid company_id. Company not found.',
            ], 200);
        }

        $employee_id = $request->employee_id;

        $month = $request->month;
        $year = $request->year;

        $monthArray = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        $employeeSalaries = EmployeeSalary::where('company_id', $company->id)
            ->where('salary_month', $month)
            ->where('salary_year', $year)
            ->where('employee_id', $employee_id)
            ->get();
        if($employeeSalaries->count()>0){
            $employee = Employee::with(['department', 'city', 'bankAccount'])
                ->find($employee_id);

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid employee_id. Employee not found.',
                ], 201);
            }

            $salaryGroups = [];
            $salaryTotals = [];

            foreach ($employeeSalaries as $salary) {
                $type = $salary->salaryType->salary_type ?? 'Others';
                $label = $salary->label ?? $salary->salaryType->name;

                $entry = [
                    'label' => $label,
                    'amount' => (float) $salary->amount,
                ];

                if (!isset($salaryGroups[$type])) {
                    $salaryGroups[$type] = [];
                    $salaryTotals[$type] = 0;
                }

                $salaryGroups[$type][] = $entry;
                $salaryTotals[$type] += $entry['amount'];
            }

            $data = [
                'monthArray' => $monthArray,
                'salaryMonth' => $month,
                'salaryYear' => $year,
                'employeeSalaries' => $employeeSalaries,
                'company' => $company,
                'employee' => $employee,
                'salaryGroups' => $salaryGroups,
                'salaryTotals' => $salaryTotals,
            ];

            $filename = 'salary_' . $month . '_' . $year . '_' . time() . '.pdf';
            $pdf = Pdf::loadView('user.salarytype.salary_pdf', $data)->setPaper('a4', 'portrait');
            $pdfPath = public_path('uploads/salary_slips/' . $filename);

            // Make sure directory exists
            if (!File::exists(public_path('uploads/salary_slips'))) {
                File::makeDirectory(public_path('uploads/salary_slips'), 0755, true);
            }

            file_put_contents($pdfPath, $pdf->output());

            $downloadUrl = asset('uploads/salary_slips/' . $filename);

            return response()->json([
                'success' => true,
                'message' => 'Salary slip generated successfully.',
                'data' => [
                    'filename' => $filename,
                    'download_url' => $downloadUrl
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No salary records found for the specified month.',
            ], 200);
        }
    }

    public function gracesettingList(Request $request)
    {

        try {
            $companyId = $request->company_id;
            $leavetypees = GraceSetting::where('company_id', $companyId)->get();

            return response()->json([
                'success' => true,
                'message' => 'Record retrieved successfully.',
                'data' => $leavetypees
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'message' => 'An error occurred while retrieving the record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

     // Update an existing leavetype
    public function gracesettingUpdate(Request $request, $id)
    {
        try {
            $request->validate([
               
                'name' => 'required|string|max:255',
            ]);
            $leavetype = Leavetype::find($id);

            if (!$leavetype) {
                throw new ModelNotFoundException("Record not found.");
            }
            $leavetype->update($request->all());

            return response()->json([
                'message' => 'Record updated successfully.',
                'success' => true,
                'data' => $leavetype
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'success' => false,
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generateAllSalary(Request $request)
    {
        try {
            $month = (int)($request->month ?? date('m'));
            $year = (int)($request->year ?? date('Y'));
            $company_id = (int)$request->company_id;

            $employees = Employee::with(['department', 'shifts'])
                ->where('company_id', $company_id)
                ->where('status', '1')
                ->get();

            if ($employees->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active employees found to generate salary for.'
                ], 404);
            }

            $payrollService = app(\App\Services\Payroll\PayrollCalculatorService::class);
            $processedCount = 0;

            foreach ($employees as $employee) {
                $calc = $payrollService->calculate($employee, $month, $year);
                $payrollService->savePayrollRecord($company_id, $employee->id, $month, $year, $calc);
                $processedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => 'Salary generated successfully for ' . $processedCount . ' employees.',
                'processed_count' => $processedCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating payroll: ' . $e->getMessage(),
            ], 500);
        }
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

        return $workingDays;
    }

    function getHolidayDaysInMonth($year, $month, $company_id)
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $holidays = Holiday::where('company_id', $company_id)
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
}