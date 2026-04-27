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

        $company_id = $request->company_id;
        $employee_id = $request->employee_id;
        $month= date('m');//$request->month ?? date('m');

        $employee = Employee::where('id', $employee_id)->where('company_id', $company_id)->first();
        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        // dd($company_id);
        $salarytypes = Salarytype::where('company_id', $company_id)->where('status', '1')->get();
        $gracesettings = GraceSetting::where('company_id', $company_id)->get();
        
        if(count($gracesettings)>0){
            $grace_time = $gracesettings[0]->value;
            $grace_day = $gracesettings[1]->value;
        }else{
            $grace_time = 15;
            $grace_day = 2;
        }
        $basic_salary = $employee->salary;
        $earning = [];
        $deduction = [];
        $earning_amount = 0;
        $deduction_amount = 0;
        $daily_salary = $basic_salary / 30;
        $weekoffday = [];
        $shift_time = '12:00:00';

        if($employee->shifts){
                foreach($employee->shifts as $shift){
                    if($shift->sunday == 0){
                        $weekoffday[] = 1;
                    }
                    if($shift->monday == 0){
                        $weekoffday[] = 2;
                    }
                    if($shift->tuesday == 0){
                        $weekoffday[] = 3;
                    }
                    if($shift->wednesday == 0){
                        $weekoffday[] = 4;
                    }
                    if($shift->thursday == 0){
                        $weekoffday[] = 5;
                    }
                    if($shift->friday == 0){
                        $weekoffday[] = 6;
                    }
                    if($shift->saturday == 0){
                        $weekoffday[] = 7;
                    }
                    $shift_time = $shift->start_time;
                }
            }
            if(count($weekoffday)==0){
                $weekoffday[] = 0;
            }

            $originalTime = Carbon::parse($shift_time);
            $updatedTime = $originalTime->addMinutes($grace_time);
            $grace_entry = $updatedTime->format('H:i:s');

            $placeholders = implode(',', array_fill(0, count($weekoffday), '?'));

            $absent = Attendance::where('employee_id', $employee_id)->where('company_id', $company_id)->whereMonth('date', $month)->where('attendance','Absent')->whereRaw("DAYOFWEEK(date) NOT IN ($placeholders)", $weekoffday)->count();
            $halfday = Attendance::where('employee_id',$employee_id)->where('company_id', $company_id)->whereMonth('date', $month)->where('attendance','Present')->where('halfday',1)->count();
            $entry = Attendance::where('employee_id', $employee_id)->where('company_id', $company_id)->whereMonth('date', $month)->where('attendance','Present')->where('in_time','>',$grace_entry)->count();
            $late = floor($entry/$grace_day);
            
            $other_deduction = ($absent * $daily_salary) + ($late * $daily_salary) + ($halfday * ($daily_salary / 2));
            $absent_days = $absent + $late + $halfday/2;

            $other_deduction = round($other_deduction,2);

            $rm_sum = Expense::where('employee_id', $employee_id)->where('company_id', $company_id)->whereMonth('created_at', $month)->where('status', 'Approved')->get();
            if(empty($rm_sum)){
                $expense = 0;
            }else{
                $expense = $rm_sum->sum('amount');
            }

            $holidayCount = $this->getHolidayDaysInMonth(date('Y'), $month, $company_id);
            $working_days = $this->getWorkingDaysInMonth(date('Y'), $month, $weekoffday);

            $working_days = $working_days - $holidayCount;

        // foreach ($salarytypes as $salarytype) {
        //     $amount = ($salarytype->amount_type === 'Flat') ? $salarytype->amount : ($basic_salary * $salarytype->amount / 100);




        //     $type_data = [
        //         'id' => $salarytype->id,
        //         'label' => $salarytype->salary_type,
        //         'amount' => round($amount),
        //         'amount_type' => $salarytype->amount_type,
        //         'payment_type' => $salarytype->payment_type,
        //     ];

        //     if ($salarytype->payment_type === 'Earning') {
        //         $earning_amount += $amount;
        //         $earning[] = $type_data;
        //     } elseif ($salarytype->payment_type === 'Deduction') {
        //         $deduction_amount += $amount;
        //         $deduction[] = $type_data;
        //     }
        // }

        foreach ($salarytypes as $salarytype) {
            $isPercentage = $salarytype->amount_type === 'Percentage';
            $amount = ($salarytype->amount_type === 'Flat')
                ? $salarytype->amount
                : ($basic_salary * $salarytype->amount / 100);

            $type_data = [
                'id' => $salarytype->id,
                'label' => $salarytype->salary_type,
                'amount' => round($amount),
                'amount_type' => $salarytype->amount_type,
                'payment_type' => $salarytype->payment_type,
                'percentage' => $isPercentage ? $salarytype->amount : null, // Always present

            ];


            if ($salarytype->payment_type === 'Earning') {
                $earning_amount += $amount;
                $earning[] = $type_data;
            } elseif ($salarytype->payment_type === 'Deduction') {
                $deduction_amount += $amount;
                $deduction[] = $type_data;
            }
        }


        $gross_salary = $basic_salary + $earning_amount - $deduction_amount - $other_deduction;

        return response()->json([
            'success' => true,
            'message' => 'Salary preview generated.',
            'data' => [
                'basic_salary' => $basic_salary,
                'earning' => $earning,
                'deduction' => $deduction,
                'other_deduction' => $other_deduction,
                'gross_salary' => round($gross_salary),
                'absent_days' => $absent_days,
                'working_days' => $working_days,
                'expense' => $expense,
                
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
            'gross_salary' => 'required|numeric',
            'components' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 201);
        }

        $year = date('Y');
        $alreadyExists = EmployeeSalary::where('company_id', $request->company_id)
            ->where('employee_id', $request->employee_id)
            ->where('salary_month', $request->month)
            ->where('salary_year', $year)
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'success' => false,
                'message' => 'Salary already saved for this employee for this month.',
            ], 201);
        }

        foreach ($request->components as $component) {
            EmployeeSalary::create([
                'company_id' => $request->company_id,
                'employee_id' => $request->employee_id,
                'salary_month' => $request->month,
                'salary_year' => $year,
                'salary_type_id' => $component['id'],
                'salary_type_amount' => $component['amount'],
                'salary_type_amount_type' => $component['amount_type'],
                'amount' => $component['amount'],
                'label' => $component['label'],
                'basic_salary' => $request->basic_salary,
                'gross_salary' => $request->gross_salary,
                'other_deduction' => $request->other_deduction,
                'absent_days' => $request->absent_days,
                'working_days' => $request->working_days,
                'reimbursement' => $request->expense,
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Salary saved successfully.',
        ], 200);
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
            $month = $request->month;
            $company_id = $request->company_id;
            $employees = Employee::where('company_id', $company_id)->get();
            foreach($employees as $employee){        
                $data['basic_salary'] = $employee->salary;
                $section = '';
                $earning_section = '';
                $deduction_section = '';
                $amount = 0;
                $earning_amount = 0;
                $deduction_amount = 0;
                $gross_amount = 0;
                $other_deduction = 0;
                
                $salarytypes = Salarytype::where('company_id', $company_id)->where('status', '1')->get();
                $gracesettings = GraceSetting::where('company_id', $company_id)->get();
                $grace_time = $gracesettings[0]->value;
                $grace_day = $gracesettings[1]->value;

                if (!$salarytypes->isEmpty()) {
                    $basic_salary = $employee->salary;
                    $daily_salary = $basic_salary / 30;
                    $weekoffday = [];
                    $shift_time = '12:00:00';
                    if($employee->shifts){
                        foreach($employee->shifts as $shift){
                            if($shift->sunday == 0){
                                $weekoffday[] = 1;
                            }
                            if($shift->monday == 0){
                                $weekoffday[] = 2;
                            }
                            if($shift->tuesday == 0){
                                $weekoffday[] = 3;
                            }
                            if($shift->wednesday == 0){
                                $weekoffday[] = 4;
                            }
                            if($shift->thursday == 0){
                                $weekoffday[] = 5;
                            }
                            if($shift->friday == 0){
                                $weekoffday[] = 6;
                            }
                            if($shift->saturday == 0){
                                $weekoffday[] = 7;
                            }
                            $shift_time = $shift->start_time;
                        }
                    }
                    if(count($weekoffday)==0){
                        $weekoffday[] = 0;
                    }

                    $originalTime = Carbon::parse($shift_time);
                    $updatedTime = $originalTime->addMinutes($grace_time);
                    $grace_entry = $updatedTime->format('H:i:s');

                    $placeholders = implode(',', array_fill(0, count($weekoffday), '?'));
                    $employee_id = $employee->id;
                    $absent = Attendance::where('employee_id', $employee_id)->where('company_id', $company_id)->whereMonth('date', $month)->where('attendance','Absent')->whereRaw("DAYOFWEEK(date) NOT IN ($placeholders)", $weekoffday)->count();
                    $halfday = Attendance::where('employee_id', $employee_id)->where('company_id', $company_id)->whereMonth('date', $month)->where('attendance','Present')->where('halfday',1)->count();
                    $entry = Attendance::where('employee_id', $employee_id)->where('company_id', $company_id)->whereMonth('date', $month)->where('attendance','Present')->where('in_time','>',$grace_entry)->count();
                    $late = floor($entry/$grace_day);

                    $employeeLeave = EmployeeLeave::where('employee_id', $employee_id)->where('company_id', $company_id)->whereMonth('from_date', $month)->where('status','approved')->whereIn('leave_type',[1,2])->sum('days')->get();

                    $rm_sum = Reimbursement::where('employee_id', $employee_id)->where('company_id', $company_id)->whereMonth('date', $month)->where('status', 'Approved')->get();
                    $reimbursement = $rm_sum ->sum('amount');
                    $other_deduction = ($absent * $daily_salary) + ($late * $daily_salary) + ($halfday * ($daily_salary / 2));
                    $absent_days = $absent + $late + $halfday/2;
                    $other_deduction = round($other_deduction,2);

                    $holidayCount = $this->getHolidayDaysInMonth(date('Y'), $month);
                    $working_days = $this->getWorkingDaysInMonth(date('Y'), $month, $weekoffday);

                    $working_days = $working_days - $holidayCount;
                    
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
                        }
                        if ($salarytype->payment_type == 'Deduction') {
                            $deduction_amount = $deduction_amount + $amount;                    
                        }
                    }         

                    $gross_amount = $basic_salary + $earning_amount - $deduction_amount - $other_deduction;
                    
                }

                
                $is_exist = EmployeeSalary::where('company_id', Auth::id())->where('employee_id', $employee_id)->where('salary_month', $month)->where('salary_year', date('Y'))->first();
                if (!$is_exist) {
                    
                    $total_amount = 0;
                    foreach ($salarytypes as $salarytype) {

                        $salary = new EmployeeSalary();
                        $salary->company_id = Auth::id();
                        $salary->employee_id = $employee->id;
                        $salary->salary_month = $month;
                        $salary->salary_year = date('Y');
                        $salary->salary_type_id = $salarytype->id;
                        $salary->salary_type_amount = $salarytype->amount;
                        $salary->salary_type_amount_type = $salarytype->amount_type;                   

                        if ($salarytype->amount_type == 'Flat') {
                            $amount = $salarytype->amount;
                            $salary->amount = $amount;
                        } else {
                            $amount = ($basic_salary * $salarytype->amount / 100);
                            $salary->amount = $amount;
                        }
                        $salary->label = $salarytype->salary_type;
                        $salary->basic_salary = $basic_salary;
                        $salary->gross_salary = $gross_amount;
                        $salary->other_deduction = $other_deduction;
                        $salary->absent_days = $absent_days;
                        $salary->working_days = $working_days;
                        $salary->reimbursement = $reimbursement;
                        $salary->save();
                    }
                }
            }

            return response()->json([
                'message' => 'Salary generated successfully.',
                'success' => true,
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