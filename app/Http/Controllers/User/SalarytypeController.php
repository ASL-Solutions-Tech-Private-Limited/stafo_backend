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
        $salarytypes = Salarytype::with('package')
       ->where('company_id', Auth::id())
       ->get();
   
  
        return view('user.salarytype.index', compact('salarytypes')); // Return the view with all salarytypes
    }

    public function create()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }


        // Get all active packages for the company
       $packages = SalryTypePackage::where('company_id', Auth::id())
        ->where('status', 'Active')
        ->orderBy('package_name')
        ->get();

        return view('user.salarytype.create',compact('packages')); 
    }





    // public function store(Request $request)
    // {
    //     // Validate the incoming request
    //     $request->validate([
    //         'salary_type' => 'required|string',
    //         'amount' => 'required|numeric',
    //     ]);
    //     $userId = Auth::id();
    //     Salarytype::create([
    //         'company_id' => $userId,
    //         'payment_type' => $request->payment_type,
    //         'salary_type' => $request->salary_type,
    //         'salary_type_description' => $request->salary_type_description,
    //         'amount' => $request->amount,
    //         'amount_type' => $request->amount_type,
    //         'status' => '1',
    //     ]);
    //     return redirect()->route('salarytype.index')->with('success', 'Salarytype created successfully.');
    // }


    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'package_id' => 'required|exists:salry_type_packages,id',
            'items' => 'required|array|min:1',
            'items.*.payment_type' => 'required|in:Earning,Deduction',
            'items.*.salary_type' => 'required|string|max:255|unique:salarytypes,salary_type,NULL,id,company_id,' . Auth::id() . ',package_id,' . $request->package_id,
            'items.*.salary_type_description' => 'nullable|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.amount_type' => 'required|in:Flat,Percentage',
            'items.*.status' => 'sometimes|in:0,1',
        ]);
        
        $userId = Auth::id();
        $packageId = $request->package_id;
        $createdCount = 0;
        $errors = [];

        // Verify package belongs to company
        $package = SalryTypePackage::where('id', $packageId)
            ->where('company_id', $userId)
            ->first();
        
        if (!$package) {
            return redirect()->back()
                ->with('error', 'Invalid package selected!')
                ->withInput();
        }
        
        foreach ($request->items as $index => $item) {
        
            
            try {
                Salarytype::create([
                    'company_id' => $userId,
                    'package_id' => $packageId,
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
            $message = "$createdCount salary type(s) created successfully for package '{$package->package_name}'.";
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
        // dd("test data");
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $employees = Employee::where('company_id', Auth::id())->get();
        $salarytypes = Salarytype::where('company_id', Auth::id())->where('status', '1')->get();
        return view('user.salarytype.generate_salary', compact('employees', 'salarytypes'));
    }
    // public function getEmployeeSalary(Request $request, $id)
    // {
        
    //     $month = $request->month;
    //     $employee = Employee::where('id', $id)->first();
    //     $basic_salary = $employee->salary;
    //     if($request->basic_salary){
    //         $basic_salary = $request->basic_salary;
    //     }
    //     $section = '';
    //     $earning_section = '';
    //     $deduction_section = '';
    //     $amount = 0;
    //     $earning_amount = 0;
    //     $deduction_amount = 0;
    //     $gross_amount = 0;
    //     $other_deduction = 0;
        
    //     $salarytypes = Salarytype::where('company_id', Auth::id())->where('status', '1')->get();
    //     $gracesettings = GraceSetting::where('company_id', Auth::id())->get();

    
      
    //     $grace_time = $gracesettings[0]->value;
      
    //     $grace_day = $gracesettings[1]->value;

    //     if (!$salarytypes->isEmpty()) {
            
    //         $daily_salary = $basic_salary / 30;
    //         $weekoffday = [];
    //         $shift_time = '12:00:00';
    //         if($employee->shifts){
    //             foreach($employee->shifts as $shift){
    //                 if($shift->sunday == 0){
    //                     $weekoffday[] = 1;
    //                 }
    //                 if($shift->monday == 0){
    //                     $weekoffday[] = 2;
    //                 }
    //                 if($shift->tuesday == 0){
    //                     $weekoffday[] = 3;
    //                 }
    //                 if($shift->wednesday == 0){
    //                     $weekoffday[] = 4;
    //                 }
    //                 if($shift->thursday == 0){
    //                     $weekoffday[] = 5;
    //                 }
    //                 if($shift->friday == 0){
    //                     $weekoffday[] = 6;
    //                 }
    //                 if($shift->saturday == 0){
    //                     $weekoffday[] = 7;
    //                 }
    //                 $shift_time = $shift->start_time;
    //             }
    //         }
    //         if(count($weekoffday)==0){
    //             $weekoffday[] = 0;
    //         }

    //         $originalTime = Carbon::parse($shift_time);
    //         $updatedTime = $originalTime->addMinutes($grace_time);
    //         $grace_entry = $updatedTime->format('H:i:s');

    //         $placeholders = implode(',', array_fill(0, count($weekoffday), '?'));

    //         $absent = Attendance::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('date', $month)->where('attendance','Absent')->whereRaw("DAYOFWEEK(date) NOT IN ($placeholders)", $weekoffday)->count();
    //         $halfday = Attendance::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('date', $month)->where('attendance','Present')->where('halfday',1)->count();
    //         $entry = Attendance::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('date', $month)->where('attendance','Present')->where('in_time','>',$grace_entry)->count();
    //         $late = floor($entry/$grace_day);

    //         $employeeLeave = EmployeeLeave::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('from_date', $month)->get();

    //         $rm_sum = Expense::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('created_at', $month)->where('status', 'Approved')->get();
    //         $reimbursement = $rm_sum ->sum('amount');
    //         $other_deduction = ($absent * $daily_salary) + ($late * $daily_salary) + ($halfday * ($daily_salary / 2));
    //         $absent_days = $absent + $late + $halfday/2;
    //         $other_deduction = round($other_deduction,2);
    //         $holidayCount = $this->getHolidayDaysInMonth(date('Y'), $month);
    //         $working_days = $this->getWorkingDaysInMonth(date('Y'), $month, $weekoffday);

           
    //         $working_days = $working_days - $holidayCount;

    //         $section .= '<div class="col-md-6">
    //                     <div class="form-group mb-3">
    //                         <label for="basic_salary">Basic Salary</label>
    //                         <input type="text" id="basic_salary" name="basic_salary" class="form-control" value="' . $basic_salary . '"
    //                             required>
    //                         <input type="hidden" name="absent_days" value="' . $absent_days . '">
    //                         <input type="hidden" name="working_days" value="' . $working_days . '">
    //                     </div>
    //                 </div>';
    //         foreach ($salarytypes as $salarytype) {
    //             $salary_type_name = '';
    //             if ($salarytype->amount_type == 'Flat') {
    //                 $amount = $salarytype->amount;
    //                 $salary_type_name = $salarytype->salary_type;
    //             } else {
    //                 $amount = ($basic_salary * $salarytype->amount / 100);
    //                 $salary_type_name = $salarytype->salary_type . ' (' . $salarytype->amount . '%)';
    //             }
    //             if ($salarytype->payment_type == 'Earning') {
    //                 $earning_amount = $earning_amount + $amount;
    //                 $earning_section .= '<div class="col-md-6">
    //                     <div class="form-group mb-3">
    //                         <label for="salary_type">' . $salary_type_name . '</label>
    //                         <input type="text" name="salary_type_' . $salarytype->id . '" class="form-control salary_type_amount" data-paymenttype="Earning" data-value="' . $amount . '" value="' . $amount . '" required>
    //                     </div>
    //                 </div>';
    //             }
    //             if ($salarytype->payment_type == 'Deduction') {
    //                 $deduction_amount = $deduction_amount + $amount;
    //                 $deduction_section .= '<div class="col-md-6">
    //                     <div class="form-group mb-3">
    //                         <label for="salary_type">' . $salary_type_name . '</label>
    //                         <input type="text" name="salary_type_' . $salarytype->id . '" class="form-control salary_type_amount" data-paymenttype="Deduction" data-value="' . $amount . '" value="' . $amount . '" required>
    //                     </div>
    //                 </div>';
    //             }
    //         }

    //         $earning_section .= '<div class="col-md-6">
    //                     <div class="form-group mb-3">
    //                         <label for="salary_type">Expense</label>
    //                         <input type="text" name="reimbursement" class="form-control" data-paymenttype="Earning" data-value="' . $reimbursement . '" value="' . $reimbursement . '" >
    //                     </div>
    //                 </div>';

    //         $deduction_section .= '<div class="col-md-6">
    //                     <div class="form-group mb-3">
    //                         <label for="salary_type"> Other Deduction </label>
    //                         <input type="text" name="other_deduction" class="form-control" data-paymenttype="Deduction" data-value="' . $other_deduction . '" value="' . $other_deduction . '" required>
    //                     </div>
    //                 </div>';            

    //         $gross_amount = $basic_salary + $earning_amount - $deduction_amount - $other_deduction;
    //         $section .= '<div class="row">
    //                         <div class="col-md-8">
    //                             <h6>Earning Amount</h6>
    //                         </div>' . $earning_section . '
    //                     </div>';
    //         $section .= '<div class="row">
    //                     <div class="col-md-8">
    //                         <h6>Deduction Amount</h6>
    //                     </div>' . $deduction_section . '
    //                 </div>';
    //         $section .= '<div class="row">
    //                         <div class="col-md-8">
    //                             <label>Gross Salary: <span id="gross_text">' . $gross_amount . '<span></label>
    //                             <input type="hidden" id="gross_amount" name="gross_amount" class="form-control" value="' . $gross_amount . '">
    //                         </div>
    //                     </div>';
    //     }

    //     $data['section'] = $section;
    //     return response()->json($data);
    // }


// public function getEmployeeSalary(Request $request, $id)
// {
    
//     $month = $request->month;
//     $employee = Employee::where('id', $id)->first();
//     $basic_salary = $employee->salary;
//     if($request->basic_salary){
//         $basic_salary = $request->basic_salary;
//     }
//     $section = '';
//     $earning_section = '';
//     $deduction_section = '';
//     $amount = 0;
//     $earning_amount = 0;
//     $deduction_amount = 0;
//     $gross_amount = 0;
//     $other_deduction = 0;
    
//     $salarytypes = Salarytype::where('company_id', Auth::id())->where('status', '1')->get();
//     $gracesettings = GraceSetting::where('company_id', Auth::id())->get();

//     $grace_time = $gracesettings[0]->value;
//     $grace_day = $gracesettings[1]->value;

//     if (!$salarytypes->isEmpty()) {
        
//         $daily_salary = $basic_salary / 30;
//         $weekoffday = [];
//         $shift_time = '12:00:00';
//         if($employee->shifts){
//             foreach($employee->shifts as $shift){
//                 if($shift->sunday == 0){
//                     $weekoffday[] = 1;
//                 }
//                 if($shift->monday == 0){
//                     $weekoffday[] = 2;
//                 }
//                 if($shift->tuesday == 0){
//                     $weekoffday[] = 3;
//                 }
//                 if($shift->wednesday == 0){
//                     $weekoffday[] = 4;
//                 }
//                 if($shift->thursday == 0){
//                     $weekoffday[] = 5;
//                 }
//                 if($shift->friday == 0){
//                     $weekoffday[] = 6;
//                 }
//                 if($shift->saturday == 0){
//                     $weekoffday[] = 7;
//                 }
//                 $shift_time = $shift->start_time;
//             }
//         }
//         if(count($weekoffday)==0){
//             $weekoffday[] = 0;
//         }

//         $originalTime = Carbon::parse($shift_time);
//         $updatedTime = $originalTime->addMinutes($grace_time);
//         $grace_entry = $updatedTime->format('H:i:s');

//         $placeholders = implode(',', array_fill(0, count($weekoffday), '?'));

//         $absent = Attendance::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('date', $month)->where('attendance','Absent')->whereRaw("DAYOFWEEK(date) NOT IN ($placeholders)", $weekoffday)->count();
//         $halfday = Attendance::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('date', $month)->where('attendance','Present')->where('halfday',1)->count();
//         $entry = Attendance::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('date', $month)->where('attendance','Present')->where('in_time','>',$grace_entry)->count();
//         $late = floor($entry/$grace_day);

//         $employeeLeave = EmployeeLeave::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('from_date', $month)->get();

//         $rm_sum = Expense::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('created_at', $month)->where('status', 'Approved')->get();
//         $reimbursement = $rm_sum ->sum('amount');
        
//         // Calculate individual deduction amounts
//         $absent_deduction = $absent * $daily_salary;
//         $late_deduction = $late * $daily_salary;
//         $halfday_deduction = $halfday * ($daily_salary / 2);
//         $other_deduction = $absent_deduction + $late_deduction + $halfday_deduction;
        
//         $absent_days = $absent + $late + $halfday/2;
//         $other_deduction = round($other_deduction,2);
//         $holidayCount = $this->getHolidayDaysInMonth(date('Y'), $month);
//         $working_days = $this->getWorkingDaysInMonth(date('Y'), $month, $weekoffday);
//         $working_days = $working_days - $holidayCount;
     

//         $section .= '<div class="col-md-6">
//                     <div class="form-group mb-3">
//                         <label for="basic_salary">Basic Salary</label>
//                         <input type="text" id="basic_salary" name="basic_salary" class="form-control" value="' . $basic_salary . '"
//                             required>
//                         <input type="hidden" name="absent_days" value="' . $absent_days . '">
//                         <input type="hidden" name="working_days" value="' . $working_days . '">
//                     </div>
//                 </div>';
        
//         foreach ($salarytypes as $salarytype) {
//             $salary_type_name = '';
//             if ($salarytype->amount_type == 'Flat') {
//                 $amount = $salarytype->amount;
//                 $salary_type_name = $salarytype->salary_type;
//             } else {
//                 $amount = ($basic_salary * $salarytype->amount / 100);
//                 $salary_type_name = $salarytype->salary_type . ' (' . $salarytype->amount . '%)';
//             }
//             if ($salarytype->payment_type == 'Earning') {
//                 $earning_amount = $earning_amount + $amount;
//                 $earning_section .= '<div class="col-md-6">
//                     <div class="form-group mb-3">
//                         <label for="salary_type">' . $salary_type_name . '</label>
//                         <input type="text" name="salary_type_' . $salarytype->id . '" class="form-control salary_type_amount" data-paymenttype="Earning" data-value="' . $amount . '" value="' . $amount . '" required>
//                     </div>
//                 </div>';
//             }
//             if ($salarytype->payment_type == 'Deduction') {
//                 $deduction_amount = $deduction_amount + $amount;
//                 $deduction_section .= '<div class="col-md-6">
//                     <div class="form-group mb-3">
//                         <label for="salary_type">' . $salary_type_name . '</label>
//                         <input type="text" name="salary_type_' . $salarytype->id . '" class="form-control salary_type_amount" data-paymenttype="Deduction" data-value="' . $amount . '" value="' . $amount . '" required>
//                     </div>
//                 </div>';
//             }
//         }

//         $earning_section .= '<div class="col-md-6">
//                     <div class="form-group mb-3">
//                         <label for="salary_type">Expense Reimbursement</label>
//                         <input type="text" name="reimbursement" class="form-control" data-paymenttype="Earning" data-value="' . $reimbursement . '" value="' . $reimbursement . '" >
//                     </div>
//                 </div>';

//         // Enhanced deduction section with clear labels for each deduction type
//         $deduction_section .= '<div class="col-md-6">
//                     <div class="form-group mb-3">
//                         <label for="absent_deduction">Absent Deduction (' . $absent . ' days × ' . round($daily_salary,2) . ')</label>
//                         <input type="text" name="absent_deduction" class="form-control deduction_amount" data-paymenttype="Deduction" data-value="' . $absent_deduction . '" value="' . round($absent_deduction,2) . '" readonly>
//                     </div>
//                 </div>';
        
//         $deduction_section .= '<div class="col-md-6">
//                     <div class="form-group mb-3">
//                         <label for="late_deduction">Late Deduction (' . $late . ' days × ' . round($daily_salary,2) . ')</label>
//                         <input type="text" name="late_deduction" class="form-control deduction_amount" data-paymenttype="Deduction" data-value="' . $late_deduction . '" value="' . round($late_deduction,2) . '" readonly>
//                     </div>
//                 </div>';
        
//         $deduction_section .= '<div class="col-md-6">
//                     <div class="form-group mb-3">
//                         <label for="halfday_deduction">Half Day Deduction (' . $halfday . ' days × ' . round($daily_salary/2,2) . ')</label>
//                         <input type="text" name="halfday_deduction" class="form-control deduction_amount" data-paymenttype="Deduction" data-value="' . $halfday_deduction . '" value="' . round($halfday_deduction,2) . '" readonly>
//                     </div>
//                 </div>';
        
//         $deduction_section .= '<div class="col-md-6">
//                     <div class="form-group mb-3">
//                         <label for="other_deduction">Total Other Deduction</label>
//                         <input type="text" name="other_deduction" class="form-control" data-paymenttype="Deduction" data-value="' . $other_deduction . '" value="' . $other_deduction . '" required>
//                     </div>
//                 </div>';            

//         $gross_amount = $basic_salary + $earning_amount + $reimbursement - $deduction_amount - $other_deduction;
        
//         $section .= '<div class="row">
//                         <div class="col-md-12">
//                             <h5 class="mt-3 mb-2">Earnings</h5>
//                         </div>
//                         <div class="col-md-8">
//                             <div class="row">' . $earning_section . '</div>
//                         </div>
//                     </div>';
        
//         $section .= '<div class="row">
//                         <div class="col-md-12">
//                             <h5 class="mt-3 mb-2">Deductions</h5>
//                         </div>
//                         <div class="col-md-8">
//                             <div class="row">' . $deduction_section . '</div>
//                         </div>
//                     </div>';
        
//         $section .= '<div class="row mt-3">
//                         <div class="col-md-8">
//                             <div class="alert alert-info">
//                                 <strong>Gross Salary Calculation:</strong><br>
//                                 Basic Salary: ' . $basic_salary . '<br>
//                                 + Total Earnings: ' . ($earning_amount + $reimbursement) . '<br>
//                                 - Total Deductions: ' . ($deduction_amount + $other_deduction) . '<br>
//                                 <hr>
//                                 <strong>Gross Salary: <span id="gross_text">' . $gross_amount . '</span></strong>
//                                 <input type="hidden" id="gross_amount" name="gross_amount" class="form-control" value="' . $gross_amount . '">
//                             </div>
//                         </div>
//                     </div>';
//     }

//     $data['section'] = $section;
//     return response()->json($data);
// }


public function getEmployeeSalary(Request $request, $id)
{
    
    $month = $request->month;
    $employee = Employee::where('id', $id)->first();
    $basic_salary = $employee->salary;
    if($request->basic_salary){
        $basic_salary = $request->basic_salary;
    }
    $section = '';
    $earning_section = '';
    $deduction_section = '';
    $amount = 0;
    $earning_amount = 0;
    $deduction_amount = 0;
    $gross_amount = 0;
    $other_deduction = 0;
    
    $salarytypes = Salarytype::where('company_id', Auth::id())->where('status', '1')->get();
    $gracesettings = GraceSetting::where('company_id', Auth::id())->get();

    $grace_time = $gracesettings[0]->value;
    $grace_day = $gracesettings[1]->value;

    if (!$salarytypes->isEmpty()) {
        
        $daily_salary = $basic_salary / 30;
        $weekoffday = [];
        $shift_time = '12:00:00';
        if($employee->shifts){
            foreach($employee->shifts as $shift){
                if($shift->sunday == 0){ $weekoffday[] = 1; }
                if($shift->monday == 0){ $weekoffday[] = 2; }
                if($shift->tuesday == 0){ $weekoffday[] = 3; }
                if($shift->wednesday == 0){ $weekoffday[] = 4; }
                if($shift->thursday == 0){ $weekoffday[] = 5; }
                if($shift->friday == 0){ $weekoffday[] = 6; }
                if($shift->saturday == 0){ $weekoffday[] = 7; }
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

        // Get all dates in month
        $startDate = Carbon::create(date('Y'), $month, 1)->startOfMonth();
        $endDate = Carbon::create(date('Y'), $month, 1)->endOfMonth();
        $allDates = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $allDates[] = $date->format('Y-m-d');
        }

        // Calculate total working days (excluding weekoffs)
        $total_working_days = 0;
        $working_dates = [];
        foreach ($allDates as $date) {
            $dayOfWeek = Carbon::parse($date)->dayOfWeek;
            $mysqlDayOfWeek = $dayOfWeek + 1;
            if ($mysqlDayOfWeek == 8) $mysqlDayOfWeek = 1;
            
            if (!in_array($mysqlDayOfWeek, $weekoffday)) {
                $total_working_days++;
                $working_dates[] = $date;
            }
        }

        // Get holidays for the month
        $holidayCount = $this->getHolidayDaysInMonth(date('Y'), $month);
        
        // Final working days after excluding holidays
        $working_days = $total_working_days - $holidayCount;
        
        // Get attendance counts (only on working days)
        $absent = Attendance::where('employee_id', $id)
            ->where('company_id', Auth::id())
            ->whereMonth('date', $month)
            ->where('attendance', 'Absent')
            ->whereIn('date', $working_dates)
            ->count();
            
        $halfday = Attendance::where('employee_id', $id)
            ->where('company_id', Auth::id())
            ->whereMonth('date', $month)
            ->where('attendance', 'Present')
            ->where('halfday', 1)
            ->whereIn('date', $working_dates)
            ->count();
            
        $entry = Attendance::where('employee_id', $id)
            ->where('company_id', Auth::id())
            ->whereMonth('date', $month)
            ->where('attendance', 'Present')
            ->where('in_time', '>', $grace_entry)
            ->whereIn('date', $working_dates)
            ->count();
            
        $late = floor($entry / $grace_day);

        $employeeLeave = EmployeeLeave::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('from_date', $month)->get();

        $rm_sum = Expense::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('created_at', $month)->where('status', 'Approved')->get();
        $reimbursement = $rm_sum->sum('amount');
        
        // Calculate individual deduction amounts
        $absent_deduction = $absent * $daily_salary;
        $late_deduction = $late * $daily_salary;
        $halfday_deduction = $halfday * ($daily_salary / 2);
        $other_deduction = $absent_deduction + $late_deduction + $halfday_deduction;
        
        // Calculate total absent days (including late converted to days)
        $absent_days_count = $absent + $late + ($halfday / 2);
        
        // Validate - absent days cannot exceed working days
        if ($absent_days_count > $working_days) {
            $absent_days_count = $working_days;
        }
        
        $other_deduction = round($other_deduction, 2);
        
        // ==================== ACCORDION START ====================
        $section .= '<div class="accordion" id="salaryAccordion">';
        
        // -------------------- ACCORDION 1: Basic Salary & Attendance --------------------
        $section .= '
        <div class="accordion-item mb-3 border-primary">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button bg-primary text-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <i class="fas fa-money-bill-wave me-2"></i> Basic Salary & Attendance Details
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#salaryAccordion">
                <div class="accordion-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="basic_salary" class="font-weight-bold">Basic Salary <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₹</span>
                                    </div>
                                    <input type="text" id="basic_salary" name="basic_salary" class="form-control" value="' . $basic_salary . '" required>
                                </div>
                                <small class="form-text text-muted">Enter or modify basic salary</small>
                                <input type="hidden" name="absent_days" value="' . $absent_days_count . '">
                                <input type="hidden" name="working_days" value="' . $working_days . '">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <small><i class="fas fa-calendar-alt"></i> Total Working Days: <strong>' . $total_working_days . '</strong></small><br>
                                <small><i class="fas fa-calendar-times"></i> Holidays: <strong>' . $holidayCount . '</strong></small><br>
                                <small><i class="fas fa-user-clock"></i> Absent/Leave Days: <strong>' . $absent_days_count . '</strong></small><br>
                                <small><i class="fas fa-check-circle text-success"></i> Present Days: <strong>' . ($working_days - $absent_days_count) . '</strong></small>
                                <input type="hidden" name="total_working_days" value="' . $total_working_days . '">
                                <input type="hidden" name="holiday_count" value="' . $holidayCount . '">
                                <input type="hidden" name="working_days" value="' . $working_days . '">
                                <input type="hidden" name="absent_days" value="' . $absent_days_count . '">
                                <input type="hidden" name="present_days" value="' . ($working_days - $absent_days_count) . '">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        
        // -------------------- ACCORDION 2: Earnings Section --------------------
        $earning_html = '';
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
                $earning_html .= '<div class="col-md-6 mb-3">
                                    <div class="card border-success h-100">
                                        <div class="card-body py-2">
                                            <label class="font-weight-bold text-success mb-1">' . $salary_type_name . '</label>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">₹</span>
                                                </div>
                                                <input type="text" name="salary_type_' . $salarytype->id . '" class="form-control salary_type_amount" data-paymenttype="Earning" data-value="' . $amount . '" value="' . number_format($amount, 2) . '" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
            }
            if ($salarytype->payment_type == 'Deduction') {
                $deduction_amount = $deduction_amount + $amount;
                $deduction_section .= '<div class="col-md-6 mb-3">
                                        <div class="card border-danger h-100">
                                            <div class="card-body py-2">
                                                <label class="font-weight-bold text-danger mb-1">' . $salary_type_name . '</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">₹</span>
                                                    </div>
                                                    <input type="text" name="salary_type_' . $salarytype->id . '" class="form-control salary_type_amount" data-paymenttype="Deduction" data-value="' . $amount . '" value="' . number_format($amount, 2) . '" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
            }
        }

        // Add Reimbursement to Earnings
        $earning_html .= '<div class="col-md-6 mb-3">
                            <div class="card border-info h-100">
                                <div class="card-body py-2">
                                    <label class="font-weight-bold text-info mb-1">Expense Reimbursement</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₹</span>
                                        </div>
                                        <input type="text" name="reimbursement" class="form-control" data-paymenttype="Earning" data-value="' . $reimbursement . '" value="' . number_format($reimbursement, 2) . '">
                                    </div>
                                    <small class="form-text text-muted">Approved expenses for this month</small>
                                </div>
                            </div>
                        </div>';
        
        $section .= '
        <div class="accordion-item mb-3 border-success">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed bg-success text-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <i class="fas fa-plus-circle me-2"></i> Earnings Components
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#salaryAccordion">
                <div class="accordion-body">
                    <div class="row">' . $earning_html . '</div>
                </div>
            </div>
        </div>';
        
        // -------------------- ACCORDION 3: Attendance Deductions --------------------
        $attendance_deduction_html = '';
        if($absent > 0 || $late > 0 || $halfday > 0){
            $attendance_deduction_html .= '<div class="row">';
            
            if($absent > 0){
                $attendance_deduction_html .= '<div class="col-md-4 mb-2">
                                                    <div class="alert alert-danger mb-0 p-2">
                                                        <small><strong>Absent Deduction</strong></small><br>
                                                        <small>' . $absent . ' days × ' . number_format($daily_salary, 2) . '</small>
                                                        <h6 class="mb-0 mt-1">₹ ' . number_format($absent_deduction, 2) . '</h6>
                                                        <input type="hidden" name="absent_deduction" value="' . $absent_deduction . '">
                                                    </div>
                                                </div>';
            }
            
            if($late > 0){
                $attendance_deduction_html .= '<div class="col-md-4 mb-2">
                                                    <div class="alert alert-warning mb-0 p-2">
                                                        <small><strong>Late Deduction</strong></small><br>
                                                        <small>' . $late . ' days × ' . number_format($daily_salary, 2) . '</small>
                                                        <h6 class="mb-0 mt-1">₹ ' . number_format($late_deduction, 2) . '</h6>
                                                        <input type="hidden" name="late_deduction" value="' . $late_deduction . '">
                                                    </div>
                                                </div>';
            }
            
            if($halfday > 0){
                $attendance_deduction_html .= '<div class="col-md-4 mb-2">
                                                    <div class="alert alert-info mb-0 p-2">
                                                        <small><strong>Half Day Deduction</strong></small><br>
                                                        <small>' . $halfday . ' days × ' . number_format($daily_salary/2, 2) . '</small>
                                                        <h6 class="mb-0 mt-1">₹ ' . number_format($halfday_deduction, 2) . '</h6>
                                                        <input type="hidden" name="halfday_deduction" value="' . $halfday_deduction . '">
                                                    </div>
                                                </div>';
            }
            
            $attendance_deduction_html .= '</div>';
        }
        
        // Total Attendance Deduction Card
        $attendance_deduction_html .= '<div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="card border-dark">
                                                <div class="card-body py-2">
                                                    <label class="font-weight-bold mb-1">Total Attendance Deduction</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">₹</span>
                                                        </div>
                                                        <input type="text" name="other_deduction" class="form-control font-weight-bold" data-paymenttype="Deduction" data-value="' . $other_deduction . '" value="' . number_format($other_deduction, 2) . '" readonly>
                                                    </div>
                                                    <small class="form-text text-muted">Absent + Late + Half Day deductions</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
        
        $section .= '
        <div class="accordion-item mb-3 border-warning">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed bg-warning text-dark fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <i class="fas fa-clock me-2"></i> Attendance Based Deductions
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#salaryAccordion">
                <div class="accordion-body">
                    ' . $attendance_deduction_html . '
                </div>
            </div>
        </div>';
        
        // -------------------- ACCORDION 4: Other Deductions --------------------
        if(!empty($deduction_section)){
            $section .= '
            <div class="accordion-item mb-3 border-danger">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed bg-danger text-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        <i class="fas fa-minus-circle me-2"></i> Other Deductions
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#salaryAccordion">
                    <div class="accordion-body">
                        <div class="row">' . $deduction_section . '</div>
                    </div>
                </div>
            </div>';
        }
        
        // Calculate Gross Amount
        $total_earnings = $earning_amount + $reimbursement;
        $total_deductions = $deduction_amount + $other_deduction;
        $gross_amount = $basic_salary + $total_earnings - $total_deductions;
        
        // -------------------- ACCORDION 5: Salary Summary (Always Visible) --------------------
        $section .= '
        <div class="accordion-item border-primary">
            <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button bg-primary text-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                    <i class="fas fa-calculator me-2"></i> Salary Summary
                </button>
            </h2>
            <div id="collapseFive" class="accordion-collapse collapse show" aria-labelledby="headingFive" data-bs-parent="#salaryAccordion">
                <div class="accordion-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <tbody>
                                <tr class="table-info">
                                    <td width="60%"><strong>Basic Salary</strong></td>
                                    <td class="text-right"><strong>₹ ' . number_format($basic_salary, 2) . '</strong></td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Total Earnings</strong> <small class="text-muted">(Salary Types + Reimbursement)</small></td>
                                    <td class="text-right"><strong class="text-success">+ ₹ ' . number_format($total_earnings, 2) . '</strong></td>
                                </tr>
                                <tr class="table-danger">
                                    <td><strong>Total Deductions</strong> <small class="text-muted">(Salary Types + Attendance)</small></td>
                                    <td class="text-right"><strong class="text-danger">- ₹ ' . number_format($total_deductions, 2) . '</strong></td>
                                <table>
                                <tr class="table-warning">
                                    <td colspan="2" class="text-center py-2">
                                        <hr class="my-1">
                                    </td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong><i class="fas fa-rupee-sign"></i> Net Gross Salary</strong></td>
                                    <td class="text-right"><strong style="font-size: 18px;">₹ ' . number_format($gross_amount, 2) . '</strong>
                                        <input type="hidden" id="gross_amount" name="gross_amount" value="' . $gross_amount . '">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>';
        
        $section .= '</div>'; // Close accordion
        
    }

    $data['section'] = $section;
    return response()->json($data);
}


    // public function saveEmployeeSalary(Request $request)
    // {
    //   //  dd($request->all());
    //     $request->validate([
    //         'employee' => 'required',
    //         'month' => 'required',
    //     ]);
    //     $salarytypes = Salarytype::where('company_id', Auth::id())->where('status', '1')->get();
    //     $is_exist = EmployeeSalary::where('company_id', Auth::id())->where('employee_id', $request->employee)->where('salary_month', $request->month)->where('salary_year', date('Y'))->first();
    //     if ($is_exist) {
    //         return redirect()->route('generateSalary')->with('error', 'Salary already generated for this employee for this month.');
    //     }
    //     if (!$salarytypes->isEmpty()) {
    //         $total_amount = 0;
    //         foreach ($salarytypes as $salarytype) {

    //             $salary = new EmployeeSalary();
    //             $salary->company_id = Auth::id();
    //             $salary->employee_id = $request->employee;
    //             $salary->salary_month = $request->month;
    //             $salary->salary_year = date('Y');
    //             $salary->salary_type_id = $salarytype->id;
    //             $salary->salary_type_amount = $salarytype->amount;
    //             $salary->salary_type_amount_type = $salarytype->amount_type;
    //             $salary->amount = $request->input('salary_type_' . $salarytype->id);
    //             $salary->label = $salarytype->salary_type;
    //             $salary->basic_salary = $request->basic_salary;
    //             $salary->gross_salary = $request->gross_amount;
    //             $salary->other_deduction = $request->other_deduction;
    //             $salary->absent_days = $request->absent_days;
    //             $salary->working_days = $request->working_days;
    //             $salary->reimbursement = $request->reimbursement;
    //             $salary->save();
    //         }
    //     }
    //     return redirect()->route('generateSalary')->with('success', 'Employee salary saved successfully.');
    // }


public function saveEmployeeSalary(Request $request)
{
    $request->validate([
        'employee' => 'required|exists:employees,id',
        'month' => 'required'
       
    ]);
    
    // Check if salary already exists
    $is_exist = EmployeeSalary::where('company_id', Auth::id())
        ->where('employee_id', $request->employee)
        ->where('salary_month', $request->month)
        ->where('salary_year', date('Y'))
        ->first();
        
    if ($is_exist) {
        return redirect()->route('generateSalary')->with('error', 'Salary already generated for this employee for this month.');
    }
    
    $salarytypes = Salarytype::where('company_id', Auth::id())->where('status', '1')->get();
    
    if (!$salarytypes->isEmpty()) {
        
        // Get employee details
        $employee = Employee::find($request->employee);
    
        
        // Calculate totals
        $total_earning = 0;
        $total_deduction = 0;
        
        foreach ($salarytypes as $salarytype) {
            
            $salary = new EmployeeSalary();
            $salary->company_id = Auth::id();
            $salary->employee_id = $request->employee;
            $salary->salary_month = $request->month;
            $salary->salary_year = date('Y');
            $salary->salary_type_id = $salarytype->id;
            $salary->salary_type_amount = $salarytype->amount;
            $salary->salary_type_amount_type = $salarytype->amount_type;
            $salary->amount = str_replace(',', '', $request->input('salary_type_' . $salarytype->id));
            $salary->label = $salarytype->salary_type;
            $salary->basic_salary = str_replace(',', '', $request->basic_salary);
            $salary->gross_salary = str_replace(',', '', $request->gross_amount);
            $salary->other_deduction = str_replace(',', '', $request->other_deduction);
            $salary->absent_days = $request->absent_days;
            $salary->working_days = $request->working_days;
            $salary->reimbursement = str_replace(',', '', $request->reimbursement ?? 0);
            $salary->save();
            
            // Calculate totals for summary
            if ($salarytype->payment_type == 'Earning') {
                $total_earning += str_replace(',', '', $request->input('salary_type_' . $salarytype->id));
            } else {
                $total_deduction += str_replace(',', '', $request->input('salary_type_' . $salarytype->id));
            }
        }
        
        // Save salary summary in a separate table or update employee salary record
        $salarySummary = EmployeeSalarySummary::updateOrCreate(
            [
                'company_id' => Auth::id(),
                'employee_id' => $request->employee,
                'salary_month' => $request->month,
                'salary_year' => date('Y')
            ],
            [
                'employee_name' => $employee->name,
                'basic_salary' => str_replace(',', '', $request->basic_salary),
                'total_earning' => $total_earning,
                'total_deduction' => $total_deduction,
                'other_deduction' => str_replace(',', '', $request->other_deduction),
                'reimbursement' => str_replace(',', '', $request->reimbursement ?? 0),
                'net_salary' => str_replace(',', '', $request->gross_amount),
                'absent_days' => $request->absent_days,
                'working_days' => $request->working_days,
                'total_working_days' => $request->total_working_days,
                'holiday_count' => $request->holiday_count,
                'present_days' => $request->present_days,
                'status' => 'Generated',
                'generated_date' => now()
            ]
        );
        
        // Optional: Send notification to employee
        // Notification::send($employee, new SalaryGeneratedNotification($salarySummary));
        
        // return redirect()->route('generateSalary')->with('success', 'Employee salary saved successfully. <a href="'.route('salarySlip', ['id' => $request->employee, 'month' => $request->month]).'" class="alert-link">View Salary Slip</a>');
    }
    
     return redirect()->route('generateSalary')->with('success', 'Employee salary saved successfully.');
}



    public function employeeSalaryList(Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $month = $request->input('month', date('m') - 1);
        $year = $request->input('year', date('Y'));
        $employee_id = $request->input('employee_id', '');

        $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $employees = Employee::where('company_id', Auth::id())->select('id', 'name')->get();
        $employeeSalariesQuery = EmployeeSalary::where('company_id', Auth::id())->groupBy('salary_month', 'salary_year', 'employee_id')->where('salary_month', $month)->where('salary_year', $year);
        if ($employee_id != '') {
            $employeeSalariesQuery->where('employee_id', $employee_id);
        }
        $employeeSalaries = $employeeSalariesQuery->get();
        //dd($employeeSalary);
        //$salarytypes = Salarytype::where('company_id', Auth::id())->where('status', '1')->get();
        return view('user.salarytype.employee_salary', compact('employeeSalaries', 'monthArray', 'employees', 'month', 'year', 'employee_id'));
    }

    // public function employeeSalaryDetails($emp_id, Request $request)
    // {
    //     $is_verified = Auth::user()->is_verified;
    //     if ($is_verified == 'No') {
    //         return view('user.verify_check');
    //     }
    //     $company = Auth::user();
    //     $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    //     $employeeSalaries = EmployeeSalary::where('company_id', Auth::id())->where('salary_month', $request->month)->where('salary_year', $request->year)->where('employee_id', $emp_id)->get();
    //     //dd($employeeSalary);
    //     return view('user.salarytype.employee_salary_details', compact('employeeSalaries', 'monthArray', 'company'));
    // }

    public function employeeSalaryDetails($emp_id, Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        
        $company = Auth::user();
        $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        
        // Get salary summary for the employee
        $salarySummary = EmployeeSalarySummary::where('company_id', Auth::id())
            ->where('salary_month', $request->month)
            ->where('salary_year', $request->year)
            ->where('employee_id', $emp_id)
            ->first();
        
        if (!$salarySummary) {
            return back()->with('error', 'Salary details not found for the selected period.');
        }
        
        // Get all salary components (earnings and deductions)
        $employeeSalaries = EmployeeSalary::where('company_id', Auth::id())
            ->where('salary_month', $request->month)
            ->where('salary_year', $request->year)
            ->where('employee_id', $emp_id)
            ->get();
        
        return view('user.salarytype.employee_salary_details', compact('salarySummary', 'employeeSalaries', 'monthArray', 'company'));
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

    public function generateAllSalary(Request $request)
    {
      
        $month = $request->month;
        $employees = Employee::where('company_id', Auth::id())->get();
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
            
            $salarytypes = Salarytype::where('company_id', Auth::id())->where('status', '1')->get();
            $gracesettings = GraceSetting::where('company_id', Auth::id())->get();
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
                $id = $employee_id;
                $absent = Attendance::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('date', $month)->where('attendance','Absent')->whereRaw("DAYOFWEEK(date) NOT IN ($placeholders)", $weekoffday)->count();
                $halfday = Attendance::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('date', $month)->where('attendance','Present')->where('halfday',1)->count();
                $entry = Attendance::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('date', $month)->where('attendance','Present')->where('in_time','>',$grace_entry)->count();
                $late = floor($entry/$grace_day);

                // $employeeLeave = EmployeeLeave::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('from_date', $month)->where('status','approved')->sum('days')->get();

                $rm_sum = Reimbursement::where('employee_id', $id)->where('company_id', Auth::id())->whereMonth('date', $month)->where('status', 'Approved')->get();
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

            
            $is_exist = EmployeeSalary::where('company_id', Auth::id())->where('employee_id', $id)->where('salary_month', $month)->where('salary_year', date('Y'))->first();
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
                        $salary->amount = $amount;//$salarytype->salary_type;
                    } else {
                        $amount = ($basic_salary * $salarytype->amount / 100);
                        $salary->amount = $amount;//$salarytype->salary_type . ' (' . $salarytype->amount . '%)';
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
        $data['message'] = 'Salary generated successfully for all employees.';
        $data['status'] = 'success';
        return response()->json($data);
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