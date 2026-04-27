<?php

namespace App\Http\Controllers\User;


use App\Models\City;
use App\Models\Shift;
use App\Models\State;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Employee;
use App\Models\Department;
use App\Models\BankAccount;
use App\Models\PackageFeature;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\EmployeeLeave;
use App\Models\EmployeeGeoLocation;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use App\Imports\EmployeeImport;


class UserEmployeeController extends Controller
{

    public function export(Request $request)
    {
        // Get the user (company) ID
        $userId = Auth::id();

        // Get the filter values from the request
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $branchId = $request->input('branch_id');
        $departmentId = $request->input('department_id');

        // Start the query with the company ID to ensure we're only fetching employees from this company
        $employeesQuery = Employee::where('company_id', $userId)
            ->with('branch')  // Eager load branch relationship
            ->with('department')  // Eager load department relationship
            ->with('shift')  // Eager load shift relationship
            ->with('bankAccount')  // Eager load bank account if applicable
            ->with('document');  // Eager load document if applicable

        // Apply filters if they exist
        if ($name) {
            $employeesQuery->where('name', 'like', '%' . $name . '%');
        }

        if ($email) {
            $employeesQuery->where('email', 'like', '%' . $email . '%');
        }

        if ($phone) {
            $employeesQuery->where('phone', 'like', '%' . $phone . '%');
        }

        if ($branchId) {
            $employeesQuery->where('branch_id', $branchId);
        }

        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }

        // Fetch the filtered employees based on the above conditions
        $employees = $employeesQuery->get();

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the Excel file
        $headers = [
            'A1' => 'Name',
            'B1' => 'Email',
            'C1' => 'Phone',
            'D1' => 'Branch',
            'E1' => 'Department',
            'F1' => 'Position',
            'G1' => 'Salary',
            'H1' => 'Date of Birth',
            'I1' => 'Gender',
            'J1' => 'Marital Status',
            'K1' => 'Blood Group',
            'L1' => 'Guardian Name',
            'M1' => 'Country',
            'N1' => 'State',
            'O1' => 'City',
            'P1' => 'Address',
            'Q1' => 'Pin',
            'R1' => 'Date of Joining',
            'S1' => 'Date of Leaving',
            'T1' => 'Job Title',
            'U1' => 'Employee Type',
            'V1' => 'Official Email',
            'W1' => 'ESI Number',
            'X1' => 'PF Number',
            'Y1' => 'Privileged Leave',
            'Z1' => 'Sick Leave',
            'AA1' => 'Casual Leave',
            'AB1' => 'Image',
            'AC1' => 'Shift',
            'AD1' => 'Bank Account',
            'AE1' => 'Document',
        ];

        foreach ($headers as $key => $header) {
            $sheet->setCellValue($key, $header);
        }

        // Write employee data to the sheet
        $row = 2; // Start from row 2 since row 1 is the header
        foreach ($employees as $employee) {
            $sheet->setCellValue('A' . $row, $employee->name);
            $sheet->setCellValue('B' . $row, $employee->email);
            $sheet->setCellValue('C' . $row, $employee->phone);
            $sheet->setCellValue('D' . $row, $employee->branch ? $employee->branch->branch_name : 'N/A');
            $sheet->setCellValue('E' . $row, $employee->department ? $employee->department->name : 'N/A');
            $sheet->setCellValue('F' . $row, $employee->position);
            $sheet->setCellValue('G' . $row, $employee->salary);
            $sheet->setCellValue('H' . $row, $employee->date_of_birth);
            $sheet->setCellValue('I' . $row, $employee->gender);
            $sheet->setCellValue('J' . $row, $employee->marital_status);
            $sheet->setCellValue('K' . $row, $employee->blood_group);
            $sheet->setCellValue('L' . $row, $employee->guardian_name);
            $sheet->setCellValue('M' . $row, $employee->country);
            $sheet->setCellValue('N' . $row, $employee->state);
            $sheet->setCellValue('O' . $row, $employee->city);
            $sheet->setCellValue('P' . $row, $employee->address);
            $sheet->setCellValue('Q' . $row, $employee->pin);
            $sheet->setCellValue('R' . $row, $employee->date_of_joining);
            $sheet->setCellValue('S' . $row, $employee->date_of_leaving);
            $sheet->setCellValue('T' . $row, $employee->job_title_id); // Adjust according to actual relationship
            $sheet->setCellValue('U' . $row, $employee->employeeType ? $employee->employeeType->name : 'N/A');
            $sheet->setCellValue('V' . $row, $employee->official_email_id);
            $sheet->setCellValue('W' . $row, $employee->esi_number);
            $sheet->setCellValue('X' . $row, $employee->pf_number);
            $sheet->setCellValue('Y' . $row, $employee->privileged_leave);
            $sheet->setCellValue('Z' . $row, $employee->sick_leave);
            $sheet->setCellValue('AA' . $row, $employee->casual_leave);
            $sheet->setCellValue('AB' . $row, $employee->image); // Assuming image is a URL or path
            $sheet->setCellValue('AC' . $row, $employee->shift ? $employee->shift->shift_name : 'N/A');
            $sheet->setCellValue('AD' . $row, $employee->bankAccount ? $employee->bankAccount->account_number : 'N/A');
            $sheet->setCellValue('AE' . $row, $employee->document ? $employee->document->document_name : 'N/A');

            $row++;
        }

        // Set the writer to be an Excel file
        $writer = new Xlsx($spreadsheet);

        // Set the response for downloading the file
        $filename = 'employees_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        // Set the headers for the response to force the download
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->headers->set('Cache-Control', 'max-age=1');

        return $response;
    }

    public function import(Request $request)
    {
        $request->validate([
            'attendance_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('attendance_file');
            $import = new EmployeeImport;

            // Import the file
            $import->import($file);

            // Check if any failure occurred
            if ($import->failureOccurred()) {
                // If any failure occurred, show an error message
                Alert::error('Error', 'There was an issue while importing the attendance data.');
            } else {
                // If no failure occurred, show success
                Alert::success('Success', 'Data imported successfully.');
            }

            return back();
        } catch (\Exception $e) {

            return back();
        }
    }




    public function index(Request $request)
    {

        $userId = Auth::id();

        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }

        // Get filter values from the request
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $branchId = $request->input('branch_id');
        $departmentId = $request->input('department_id');


        $branches = Branch::where('company_id', $userId)->get();
        $departments = Department::where('company_id', $userId)->get();

        $shifts = Shift::where('company_id', $userId)->get();

        $employeesQuery = Employee::where('company_id', $userId)
            ->with('branch')
            ->with('department')
            ->with('shifts')
            ->orderBy('created_at', 'desc');

        if ($name) {
            $employeesQuery->where('name', 'like', '%' . $name . '%');
        }

        if ($email) {
            $employeesQuery->where('email', 'like', '%' . $email . '%');
        }

        if ($phone) {
            $employeesQuery->where('phone', 'like', '%' . $phone . '%');
        }

        if ($branchId) {
            $employeesQuery->where('branch_id', $branchId);
        }

        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }

        $employees = $employeesQuery->get();


        $assignedShifts = [];
        foreach ($employees as $employee) {
            $assignedShifts[$employee->id] = $employee->shifts->pluck('id')->toArray();
        }
        return view('user.employee.index', compact('employees', 'branches', 'departments', 'shifts', 'assignedShifts'));
    }


    public function create()
    {
        $userId = Auth::id();
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $addRestriction = $this->sitesetting(5);
        $employeeCount = Employee::where('company_id', $userId)->where('status', '1')->count();
        $package_id = Auth::user()->package_id;
        $subscription_end = Auth::user()->subscription_end;
        $is_active_plan = true;
        $message = '';

        if ($subscription_end < date('Y-m-d')) {
            $is_active_plan = false;
            $message = "Your current subscription plan has expired. Please upgrade your plan to add more employees.";
        }
        if (empty($package_id)) {
            $is_active_plan = false;
            $message = "You don't have any active plan. Please upgrade your plan to add more employees.";
        }
        if ($employeeCount >= $addRestriction) {
            if ($is_active_plan == false) {
                return view('user.restriction_check', compact('message'));
            } else {
                $packagefeature = PackageFeature::where('package_id', $package_id)->where('features_id', '1')->select('feature_value')->first();
                $max_employee_add = $packagefeature->feature_value;
                $max_employee_add = Auth::user()->max_employee_add;
                if ($employeeCount >= $max_employee_add) {
                    $is_active_plan = false;
                    $message = "You have reached the maximum limit of employees. Please upgrade your plan to add more employees.";
                }
                if ($is_active_plan == false) {
                    return view('user.restriction_check', compact('message'));
                }
            }
        }

        $branches = Branch::where('company_id', $userId)->get();
        $departments = Department::where('company_id', $userId)->get();

        if ($branches->isEmpty() || $departments->isEmpty()) {
            // Return the view with a flag to show the modal
            return view('user.employee.popup', ['showModal' => true]);
        }



        return view('user.employee.create', compact('userId', 'branches', 'departments'));
    }

    // Store a newly created employee in the database
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|min:6|max:55',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|digits:10|unique:employees,phone',

        ]);

        $company_id = Auth::id();
        Employee::create([
            'name' =>  $request->name,
            'email' =>  $request->email,
            'phone' =>   $request->phone,
            'position' =>   $request->position,
            'salary' =>   $request->salary ?: 0,
            'company_id' => $company_id,
            // 'branch_id' => $request->branch_id ?? null,
            // 'department_id' => $request->department_id ?? null,
            'status' => 1,
        ]);

       $company = CompanyDetail::findOrFail($company_id); 
       $company->increment('employee_added', 1);


        Alert::success('Success', 'Employee Details has been saved successfully.');

        return redirect()->route('employee.index')->with('success', 'Employee added successfully.');
    }



    public function show($id, Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        // $employee = Employee::findOrFail($id);
        $employee = Employee::with(['country', 'state', 'city'])->findOrFail($id);



        $employee->image_url = $employee->image ? asset('images/employees/' . $employee->image) : null;
        $employee->resume_url = $employee->resume ? asset('resumes/' . $employee->resume) : null;
        $employee->selfie_url = $employee->resume ? asset('uploads/employees/selfie' . $employee->selfie_image) : null;

        $companyId = $request->query('company_id');

        // Pass the data to the view
        return view('user.employee.show', compact('employee'));
    }



    public function destroy(Employee $employee)
    {
        $employee->delete();
        Alert::success('Success', 'Employee Details has been Deleted successfully.');

        return redirect()->route('employee.index')->with('success', 'Employee deleted successfully.');
    }

    public function edit($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $userId = Auth::id();
        $employee = Employee::with(['branch', 'department'])->findOrFail($id);
        $branches = Branch::where('company_id', $userId)->get();
        $departments = Department::where('company_id', $userId)->get();
        return view('user.employee.edit', compact('employee', 'branches', 'departments'));
    }


    public function update(Request $request, $id)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|min:6|max:55',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'required|digits:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:10240',
        ]);
        $employee = Employee::findOrFail($id);

        // Prepare the data to be updated
        $data = $request->only([
            'name',
            'email',
            'phone',
            'position',
            'salary',
            'date_of_birth',
            'gender',
            'marital_status',
            'blood_group',
            'address',
            'date_of_joining',
            'date_of_leaving',
        ]);

        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $imageName = 'employee_' . $id . '_' . time() . '.' . $extension;
            $request->file('image')->move(public_path('uploads/employees'), $imageName);
            $data['image'] = $imageName;
        }


        // Handling resume upload
        if ($request->hasFile('resume')) {
            // Check if a resume already exists, and delete it if needed
            if ($employee->resume && file_exists(public_path('uploads/resumes/' . $employee->resume))) {
                unlink(public_path('uploads/resumes/' . $employee->resume));
            }

            $extension = $request->file('resume')->getClientOriginalExtension();
            $resumeName = 'employee_' . $id . '_resume_' . time() . '.' . $extension;
            $request->file('resume')->move(public_path('resumes'), $resumeName);
            $data['resume'] = $resumeName;
        }


        // Update the employee data
        $employee->update($data);

        // Redirect back with a success message
        Alert::success('Success', 'Employee details updated successfully.');
        return redirect()->route('employee.index');
    }



    public function showBankAccountForm($employeeId)
    {

        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $employee = Employee::find($employeeId);

        // Show the bank account form for the employee
        return view('user.employee.add_bank_account', compact('employee'));
    }

    public function storeBankAccount(Request $request, $employeeId)
    {
        $validated = $request->validate([
            'account_number' => 'required|numeric|digits_between:9,18',
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|size:11|regex:/^[A-Z]{4}[0][A-Z0-9]{6}$/',
            'account_holder_name' => 'required|string|regex:/^[a-zA-Z\s]+$/',
        ]);

        // Find the employee
        $employee = Employee::find($employeeId);

        // Store the bank account information
        BankAccount::create([
            'employee_id' => $employee->id,
            'account_number' => $validated['account_number'],
            'bank_name' => $validated['bank_name'],
            'branch_name' => $validated['branch_name'],
            'ifsc_code' => $validated['ifsc_code'],
            'account_holder_name' => $validated['account_holder_name'],
        ]);

        // Redirect back with a success message
        return redirect()->route('employee.index')->with('success', 'Bank account added successfully.');
    }

    public function editBank($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $bankAccount = BankAccount::findOrFail($id);
        return view('user.employee.bank_account_edit', compact('bankAccount'));
    }

    public function updateBank(Request $request, $id)
    {
        $validated = $request->validate([
            'account_number' => 'required|numeric|digits_between:9,18',
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|size:11|regex:/^[A-Z]{4}[0][A-Z0-9]{6}$/',
            'account_holder_name' => 'required|string|regex:/^[a-zA-Z\s]+$/',
        ]);

        // Find and update the bank account
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->update($validated);

        return redirect()->route('employee.index')->with('success', 'Bank account updated successfully.');
    }



    // public function assignShift(Request $request, $employeeId)
    // {
    //     $userId = Auth::id();
    //     $request->validate([
    //         'shift_id' => 'required|exists:shifts,id,company_id,' . $userId,
    //     ]);
    //     $employee = Employee::where('id', $employeeId)
    //         ->where('company_id', $userId)
    //         ->firstOrFail();
    //     $shift = Shift::where('id', $request->input('shift_id'))
    //         ->where('company_id', $userId)
    //         ->firstOrFail();
    //     $employee->shift()->associate($shift);
    //     $employee->save();

    //     if ($request->ajax()) {
    //         return response()->json(['message' => 'Shift assigned successfully!']);
    //     }

    //     // Redirect back with success message if it's not an AJAX request
    //     return redirect()->route('employee.index')->with('success', 'Shift assigned successfully!');
    // }




    public function assignShift(Request $request, $employeeId)
    {
        $userId = Auth::id(); // Get the logged-in user's company ID

        // Validate the input
        $request->validate([
            'shift_ids' => 'required|array', // Ensure shift_ids is an array
            'shift_ids.*' => 'exists:shifts,id,company_id,' . $userId, // Validate each shift id exists for the company
        ]);

        // Find the employee by their ID
        $employee = Employee::where('id', $employeeId)
            ->where('company_id', $userId)
            ->firstOrFail();

        // Get the shifts that the user selected
        $shifts = Shift::whereIn('id', $request->input('shift_ids'))
            ->where('company_id', $userId)
            ->get();

        // Sync shifts with employee. This will remove unselected shifts and add new ones.
        $employee->shifts()->sync(
            $shifts->mapWithKeys(function ($shift) use ($userId) {
                return [$shift->id => ['company_id' => $userId]];
            })->toArray()
        );

        // Return a success response
        return response()->json(['message' => 'Shifts updated successfully!']);
    }

    public function assignBranch(Request $request, Employee $employee)
    {
        $employee->branch_id = $request->branch_id;
        $employee->save();

        return response()->json(['message' => 'Branch assigned successfully!']);
    }
    public function assignDepartment(Request $request, Employee $employee)
    {
        $employee->department_id = $request->department_id;
        $employee->save();

        return response()->json(['message' => 'Department assigned successfully!']);
    }





    public function leaveList(Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $companyId = Auth::id();
        $employees = Employee::where('company_id', $companyId)->get();


        $validated = $request->validate([
            'leave_type' => 'nullable|in:1,2,3',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        // Query for leave records with optional filters for leave_type, status, and company_id
        $leave = EmployeeLeave::with(['employeeBasicInfo','leavetype'])
            ->where('company_id', $companyId)  // Ensure the leaves are for the current company
            ->when($request->leave_type, fn($q) => $q->where('leave_type', $request->leave_type))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->employee_id, fn($q) => $q->where('employee_id', $request->employee_id))
            ->paginate(10);

        $leaveCount = [];
        if (!empty($request->employee_id)) {
            $fromdate = date('Y') . '-01-01';
            $todate = date('Y') . '-12-31';
            $leaveCount = EmployeeLeave::where('employee_id', $request->employee_id)
                ->where('company_id', $companyId)  // Ensure the leave count is for the current company
                ->select('leave_type', DB::raw('SUM(days) as total_days'))
                ->whereBetween('from_date', [$fromdate, $todate])
                ->groupBy('leave_type')
                ->get();
        }

        // Return view with leave data, leave count, and employees for filtering
        return view('user.employee.leave-list', compact('leave', 'leaveCount', 'employees'));
    }




    public function updateLeaveStatus(Request $request)
    {
        try {
            // Validate inputs
            $validated = $request->validate([
                'leave_id' => 'required|exists:employee_leaves,id',
                'action' => 'required|in:approve,reject,pending',
            ]);

            // Find the leave record
            $leave = EmployeeLeave::findOrFail($request->leave_id);

            // Perform the action based on the value of the 'action' parameter
            if ($request->action == 'approve') {
                if ($leave->status == 'pending' || $leave->status == 'rejected') {
                    $leave->status = 'approved';
                } else {
                    return response()->json(['success' => false, 'message' => 'Leave already approved']);
                }
            } elseif ($request->action == 'reject') {
                if ($leave->status == 'pending' || $leave->status == 'approved') {
                    $leave->status = 'rejected';
                } else {
                    return response()->json(['success' => false, 'message' => 'Leave already rejected']);
                }
            } elseif ($request->action == 'pending') {
                // Allow marking the leave as pending if it was either approved or rejected
                if ($leave->status == 'approved' || $leave->status == 'rejected') {
                    $leave->status = 'pending';
                } else {
                    return response()->json(['success' => false, 'message' => 'Leave is already pending']);
                }
            }

            $leave->save();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Leave status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update leave status: ' . $e->getMessage()]);
        }
    }

    public function deleteLeave($id)
    {
        try {
            $leave = EmployeeLeave::findOrFail($id);
            $leave->delete();
            return redirect()->back()->with('success', 'Leave record deleted successfully.');
        } catch (\Exception $e) {
            // Return an error message if deletion fails
            return redirect()->back()->with('error', 'Failed to delete leave record.');
        }
    }

    public function documentVerification($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $employee = Employee::find($id);
        return view('user.employee.document_verification', compact('employee'));
    }

    public function employeesDataUpdate(Request $request, $id)
    {
        $type = $request->type;
        $data = $request->data;
        $employee = Employee::find($id);
        $employee->{$type} = $data;
        $employee->save();
        return response()->json(['success' => true, 'message' => 'Document verified successfully']);
    }

    public function location(Request $request,$id)
    {
        $company = Auth::user();
        $is_verified = $company->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $date = date('Y-m-d');
        if ($request->has('date')) {
            $date = $request->input('date');
        }
        $employee = Employee::where('company_id',$company->id)->find($id);
        if (!$employee) {
            return view('user.not_found', ['message' => 'Employee not found']);
        }
        $geoLocations = EmployeeGeoLocation::where('employee_id', $id)->whereDate('created_at',$date)->get();
        if ($geoLocations->isEmpty()) {
            $latlng = [];
        } else {
            foreach ($geoLocations as $k => $location) {
                $latlng[$k]['lat'] = (float)$location->latitude;
                $latlng[$k]['lng'] = (float)$location->longitude;
            }
        }
        $map_view = $company->map_view;
        //$map_view = "MapMyIndia";
        $location_info = json_encode($latlng);
        //$location_info = $latlng;
        return view('user.employee.location', compact('employee', 'location_info','date','map_view'));
    }
}