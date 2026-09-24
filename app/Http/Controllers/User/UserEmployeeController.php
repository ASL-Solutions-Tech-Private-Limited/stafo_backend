<?php

namespace App\Http\Controllers\User;


use App\Models\City;
use App\Models\Shift;
use App\Models\State;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\CompanyRole;
use App\Models\BankAccount;
use App\Models\PackageFeature;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\EmployeeLeave;
use App\Models\EmployeeGeoLocation;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Notification;
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
            $cleanDigits = preg_replace('/[^0-9]/', '', $name);
            $employeesQuery->where(function ($q) use ($name, $cleanDigits) {
                $q->where('name', 'like', '%' . $name . '%')
                  ->orWhere('email', 'like', '%' . $name . '%')
                  ->orWhere('phone', 'like', '%' . $name . '%')
                  ->orWhere('emp_id', 'like', '%' . $name . '%');

                if (!empty($cleanDigits) && strlen($cleanDigits) >= 3) {
                    $q->orWhere('phone', 'like', '%' . $cleanDigits . '%');
                }
            });
        }

        if ($email) {
            $employeesQuery->where('email', 'like', '%' . $email . '%');
        }

        if ($phone) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            $employeesQuery->where(function ($q) use ($phone, $cleanPhone) {
                $q->where('phone', 'like', '%' . $phone . '%');
                if (!empty($cleanPhone)) {
                    $q->orWhere('phone', 'like', '%' . $cleanPhone . '%');
                }
            });
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
        $name = trim($request->input('name') ?? $request->input('search') ?? '');
        $email = trim($request->input('email') ?? '');
        $phone = trim($request->input('phone') ?? '');
        $branchId = $request->input('branch_id');
        $departmentId = $request->input('department_id');
        $kycStatus = $request->input('kyc_status');

        $branches = Branch::where('company_id', $userId)->get();
        $departments = Department::where('company_id', $userId)->get();
        $shifts = Shift::where('company_id', $userId)->get();

        $employeesQuery = Employee::where('company_id', $userId)
            ->with('branch')
            ->with('department')
            ->with('designation')
            ->with('companyRole')
            ->with('shifts')
            ->with('shift')
            ->orderBy('created_at', 'desc');

        if ($name !== '') {
            $cleanDigits = preg_replace('/[^0-9]/', '', $name);
            $employeesQuery->where(function ($q) use ($name, $cleanDigits) {
                $q->where('name', 'like', '%' . $name . '%')
                  ->orWhere('email', 'like', '%' . $name . '%')
                  ->orWhere('phone', 'like', '%' . $name . '%')
                  ->orWhere('emp_id', 'like', '%' . $name . '%');

                if (!empty($cleanDigits) && strlen($cleanDigits) >= 3) {
                    $q->orWhere('phone', 'like', '%' . $cleanDigits . '%');
                }
            });
        }

        if ($email !== '') {
            $employeesQuery->where('email', 'like', '%' . $email . '%');
        }

        if ($phone !== '') {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            $employeesQuery->where(function ($q) use ($phone, $cleanPhone) {
                $q->where('phone', 'like', '%' . $phone . '%');
                if (!empty($cleanPhone)) {
                    $q->orWhere('phone', 'like', '%' . $cleanPhone . '%');
                }
            });
        }

        if ($branchId) {
            $employeesQuery->where('branch_id', $branchId);
        }

        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }

        if ($kycStatus === 'verified') {
            $employeesQuery->where(function ($q) {
                $q->where('aadhar_verify', 'Yes')
                  ->orWhere('pan_verify', 'Yes')
                  ->orWhere('voter_verify', 'Yes')
                  ->orWhere('dl_verify', 'Yes');
            });
        } elseif ($kycStatus === 'unverified') {
            $employeesQuery->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('aadhar_verify')->orWhere('aadhar_verify', '!=', 'Yes');
                })->where(function ($sub) {
                    $sub->whereNull('pan_verify')->orWhere('pan_verify', '!=', 'Yes');
                })->where(function ($sub) {
                    $sub->whereNull('voter_verify')->orWhere('voter_verify', '!=', 'Yes');
                })->where(function ($sub) {
                    $sub->whereNull('dl_verify')->orWhere('dl_verify', '!=', 'Yes');
                });
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if ($perPage <= 0 || $perPage > 100) {
            $perPage = 10;
        }

        $employees = $employeesQuery->paginate($perPage)->withQueryString();

        // Calculate quick directory statistics
        $totalEmployees = Employee::where('company_id', $userId)->count();
        $activeEmployees = Employee::where('company_id', $userId)->where('status', '1')->count();
        $verifiedEmployees = Employee::where('company_id', $userId)->where(function ($q) {
            $q->where('aadhar_verify', 'Yes')
              ->orWhere('pan_verify', 'Yes')
              ->orWhere('voter_verify', 'Yes')
              ->orWhere('dl_verify', 'Yes');
        })->count();
        $unverifiedEmployees = max(0, $totalEmployees - $verifiedEmployees);

        $assignedShifts = [];
        foreach ($employees as $employee) {
            $assignedShifts[$employee->id] = $employee->shifts->pluck('id')->toArray();
        }

        $allEmployeesList = Employee::where('company_id', $userId)->orderBy('name', 'asc')->get();
        $calendarEmployeeId = $request->input('calendar_employee_id') ?: ($employees->first()->id ?? ($allEmployeesList->first()->id ?? null));
        $calendarEmployee = $calendarEmployeeId ? Employee::with(['branch', 'department', 'shift', 'shifts'])->find($calendarEmployeeId) : null;
        $currentMonth = date('m');
        $currentYear = date('Y');

        return view('user.employee.index', compact(
            'employees',
            'branches',
            'departments',
            'shifts',
            'assignedShifts',
            'totalEmployees',
            'activeEmployees',
            'verifiedEmployees',
            'unverifiedEmployees',
            'allEmployeesList',
            'calendarEmployee',
            'calendarEmployeeId',
            'currentMonth',
            'currentYear'
        ));
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
        $designations = Designation::where('company_id', $userId)->where('status', 1)->orderBy('name', 'asc')->get();
        $companyRoles = CompanyRole::where('company_id', $userId)->where('status', 1)->orderBy('name', 'asc')->get();

        if ($branches->isEmpty() || $departments->isEmpty()) {
            // Return the view with a flag to show the modal
            return view('user.employee.popup', ['showModal' => true]);
        }

        return view('user.employee.create', compact('userId', 'branches', 'departments', 'designations', 'companyRoles'));
    }

    // Store a newly created employee in the database
    public function store(Request $request)
    {
        $company_id = Auth::id();

        $request->validate([
            'name' => 'required|string|min:6|max:55',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|digits:10|unique:employees,phone',
            'designation_id' => 'nullable|exists:designations,id',
            'company_role_id' => 'nullable|exists:company_roles,id',
            'position' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric',
            'attendance_type' => 'nullable|string|in:geo,selfie,qr code,qr',
        ]);

        $position = $request->position;
        if ($request->filled('designation_id')) {
            $desig = Designation::where('company_id', $company_id)->find($request->designation_id);
            if ($desig) {
                $position = $desig->name;
            }
        }

        Employee::create([
            'name' =>  $request->name,
            'email' =>  $request->email,
            'phone' =>   $request->phone,
            'designation_id' => $request->filled('designation_id') ? $request->designation_id : null,
            'company_role_id' => $request->filled('company_role_id') ? $request->company_role_id : null,
            'position' =>   $position,
            'salary' =>   $request->salary ?: 0,
            'attendance_type' => $request->attendance_type ?: 'geo',
            'company_id' => $company_id,
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
        $userId = Auth::id();
        $employee = Employee::with([
            'country', 'state', 'city',
            'branch', 'department', 'designation', 'companyRole.permissions', 'permissionOverrides',
            'shift', 'shifts', 'bankAccount', 'document.documentType', 'documents.documentType', 'employeeType', 'leaves'
        ])->where('company_id', $userId)->findOrFail($id);

        $employee->image_url = ($employee->image && file_exists(public_path('uploads/employees/' . $employee->image)))
            ? asset('uploads/employees/' . $employee->image)
            : null;

        $employee->resume_url = null;
        if ($employee->resume) {
            if (file_exists(public_path('uploads/resumes/' . $employee->resume))) {
                $employee->resume_url = asset('uploads/resumes/' . $employee->resume);
            } elseif (file_exists(public_path('resumes/' . $employee->resume))) {
                $employee->resume_url = asset('resumes/' . $employee->resume);
            } else {
                $employee->resume_url = asset('uploads/resumes/' . $employee->resume);
            }
        }

        $employee->selfie_url = null;
        if ($employee->selfie_image) {
            if (file_exists(public_path('uploads/employees/selfie/' . $employee->selfie_image))) {
                $employee->selfie_url = asset('uploads/employees/selfie/' . $employee->selfie_image);
            } else {
                $employee->selfie_url = asset('uploads/employees/' . $employee->selfie_image);
            }
        }

        $companyId = $request->query('company_id', $userId);

        // Pass the data to the view
        return view('user.employee.show', compact('employee'));
    }



    public function destroy(Employee $employee)
    {
        $userId = Auth::id();
        if ($employee->company_id != $userId) {
            abort(403, 'Unauthorized access to delete this employee.');
        }

        DB::transaction(function () use ($employee) {
            $employee->delete();
        });

        Alert::success('Success', 'Employee records have been deleted successfully.');

        return redirect()->route('employee.index')->with('success', 'Employee records deleted successfully.');
    }

    public function edit($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $userId = Auth::id();
        $employee = Employee::with(['branch', 'department', 'shifts', 'shift', 'designation', 'companyRole'])
            ->where('company_id', $userId)
            ->findOrFail($id);
        $branches = Branch::where('company_id', $userId)->get();
        $departments = Department::where('company_id', $userId)->get();
        $shifts = Shift::where('company_id', $userId)->get();
        $designations = Designation::where('company_id', $userId)->where('status', 1)->orderBy('name', 'asc')->get();
        $companyRoles = CompanyRole::where('company_id', $userId)->where('status', 1)->orderBy('name', 'asc')->get();

        $assignedShiftIds = $employee->shifts->pluck('id')->toArray();
        if (empty($assignedShiftIds) && $employee->shift_id) {
            $assignedShiftIds = [$employee->shift_id];
        }

        return view('user.employee.edit', compact('employee', 'branches', 'departments', 'shifts', 'assignedShiftIds', 'designations', 'companyRoles'));
    }


    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|min:6|max:55',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'required|digits:10',
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'company_role_id' => 'nullable|exists:company_roles,id',
            'position' => 'nullable|string|max:255',
            'shift_ids' => 'nullable|array',
            'shift_ids.*' => 'exists:shifts,id',
            'attendance_type' => 'nullable|string|in:geo,selfie,qr code,qr',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'resume' => 'nullable|mimes:pdf,doc,docx|max:10240',
        ]);
        $employee = Employee::where('company_id', $userId)->findOrFail($id);

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
            'attendance_type',
            'designation_id',
            'company_role_id',
        ]);

        $data['attendance_type'] = $request->filled('attendance_type') ? $request->attendance_type : ($employee->attendance_type ?: 'geo');
        $data['designation_id'] = $request->filled('designation_id') ? $request->designation_id : null;
        $data['company_role_id'] = $request->filled('company_role_id') ? $request->company_role_id : null;

        // If designation selected, auto-sync position
        if ($request->filled('designation_id')) {
            $desig = Designation::where('company_id', $userId)->find($request->designation_id);
            if ($desig) {
                $data['position'] = $desig->name;
            }
        }

        // Branch and Department assignment
        $data['branch_id'] = $request->filled('branch_id') ? $request->branch_id : null;
        $data['department_id'] = $request->filled('department_id') ? $request->department_id : null;

        // Shift assignment logic
        if ($request->has('shift_ids')) {
            $shiftIds = array_values(array_filter((array) $request->input('shift_ids', [])));
            $data['shift_id'] = !empty($shiftIds) ? $shiftIds[0] : null;

            $shifts = Shift::whereIn('id', $shiftIds)
                ->where('company_id', $userId)
                ->get();

            $employee->shifts()->sync(
                $shifts->mapWithKeys(function ($shift) use ($userId) {
                    return [$shift->id => ['company_id' => $userId]];
                })->toArray()
            );
        } else {
            $data['shift_id'] = null;
            $employee->shifts()->sync([]);
        }

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
            if ($employee->resume && file_exists(public_path('resumes/' . $employee->resume))) {
                unlink(public_path('resumes/' . $employee->resume));
            }

            $extension = $request->file('resume')->getClientOriginalExtension();
            $resumeName = 'employee_' . $id . '_resume_' . time() . '.' . $extension;
            if (!file_exists(public_path('uploads/resumes'))) {
                mkdir(public_path('uploads/resumes'), 0777, true);
            }
            $request->file('resume')->move(public_path('uploads/resumes'), $resumeName);
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

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found.'], 404);
        }

        $verifyField = match ($type) {
            'aadhar' => 'aadhar_verify',
            'pan' => 'pan_verify',
            'voter' => 'voter_verify',
            'driving_license' => 'dl_verify',
            default => null
        };

        if ($verifyField && ($employee->{$verifyField} === 'Yes' || $employee->{$verifyField} === '1')) {
            return response()->json([
                'success' => false,
                'message' => 'This document is already verified and cannot be modified.'
            ], 422);
        }

        $employee->{$type} = $data;
        $employee->save();
        return response()->json(['success' => true, 'message' => 'Document saved successfully']);
    }

    public function location(Request $request, $id = null)
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

        $employees = Employee::where('company_id', $company->id)->orderBy('name', 'asc')->get();

        if ($request->filled('employee_id')) {
            $id = $request->input('employee_id');
        }

        if ($id) {
            $employee = Employee::with(['branch', 'shifts', 'shift'])->where('company_id', $company->id)->find($id);
        } else {
            // Prioritize employee who has active tracking (geo_status = '2'), or pending (geo_status = '1'), or first in list
            $preferredEmp = $employees->firstWhere('geo_status', '2') 
                ?? $employees->firstWhere('geo_status', '1') 
                ?? $employees->first();

            $employee = $preferredEmp 
                ? Employee::with(['branch', 'shifts', 'shift'])->where('company_id', $company->id)->find($preferredEmp->id) 
                : null;
        }

        if (!$employee && $employees->isEmpty()) {
            return view('user.not_found', ['message' => 'No employees found in your company.']);
        }

        $latlng = [];
        $rawPoints = [];
        $halts = [];
        $totalDistanceMeters = 0;
        $startPoint = null;
        $endPoint = null;
        $journeyStats = null;

        $shiftDetails = null;
        if ($employee) {
            $shiftDetails = $employee->getShiftWindowForDate($date);

            // When date is today: Location is strictly visible ONLY when the employee has accepted tracking (geo_status == '2')
            if ((string)$employee->geo_status !== '2' && $date === date('Y-m-d')) {
                $geoLocations = collect();
            } else {
                $query = EmployeeGeoLocation::where('employee_id', $employee->id);

                if ($shiftDetails && isset($shiftDetails['start']) && isset($shiftDetails['end'])) {
                    // If this is a scheduled off-day, we can check if off-day
                    if (!empty($shiftDetails['is_off_day'])) {
                        // Off day: tracking is inactive
                        $geoLocations = collect();
                    } else {
                        $query->whereBetween('created_at', [
                            $shiftDetails['start']->toDateTimeString(),
                            $shiftDetails['end']->toDateTimeString()
                        ]);
                        $geoLocations = $query->orderBy('created_at', 'asc')->get();
                    }
                } else {
                    $query->whereBetween('created_at', [$date . ' 00:00:00', $date . ' 23:59:59']);
                    $geoLocations = $query->orderBy('created_at', 'asc')->get();
                }
            }

            if ($geoLocations->isNotEmpty()) {
                foreach ($geoLocations as $loc) {
                    $pt = [
                        'lat' => (float)$loc->latitude,
                        'lng' => (float)$loc->longitude,
                        'time' => $loc->created_at->format('h:i A'),
                        'time_full' => $loc->created_at->format('d M Y, h:i:s A'),
                        'timestamp' => $loc->created_at->timestamp,
                        'battery' => $loc->battery_status ?? null,
                    ];
                    $rawPoints[] = $pt;
                    $latlng[] = ['lat' => $pt['lat'], 'lng' => $pt['lng']];
                }

                $totalPoints = count($rawPoints);
                $startPoint = $rawPoints[0];
                $endPoint = $rawPoints[$totalPoints - 1];

                // Calculate point-by-point speeds and peak speed
                $maxSpeedKmh = 0;
                $rawPoints[0]['speed_kmh'] = 0;
                for ($i = 1; $i < $totalPoints; $i++) {
                    $prevPt = $rawPoints[$i - 1];
                    $currPt = $rawPoints[$i];
                    $stepDist = $this->calculateDistanceMeters($prevPt['lat'], $prevPt['lng'], $currPt['lat'], $currPt['lng']);
                    $timeDeltaSec = max(1, $currPt['timestamp'] - $prevPt['timestamp']);
                    $calcSpeed = round(($stepDist / $timeDeltaSec) * 3.6, 1);
                    if ($calcSpeed > 130) {
                        $calcSpeed = $rawPoints[$i - 1]['speed_kmh'] ?? 0;
                    }
                    $rawPoints[$i]['speed_kmh'] = $calcSpeed;
                    if ($calcSpeed > $maxSpeedKmh) {
                        $maxSpeedKmh = $calcSpeed;
                    }
                }

                // Parameters for Stoppage / Pause detection
                // A halt is when consecutive points remain within ~60m for 3+ minutes (180s)
                $haltMinDuration = 180; // 3 minutes threshold
                $clusterRadiusMeters = 60; // 60 meters radius threshold

                $currentCluster = [$rawPoints[0]];

                for ($i = 1; $i < $totalPoints; $i++) {
                    $prev = $rawPoints[$i - 1];
                    $curr = $rawPoints[$i];

                    $stepDist = $this->calculateDistanceMeters($prev['lat'], $prev['lng'], $curr['lat'], $curr['lng']);
                    $totalDistanceMeters += $stepDist;

                    $distFromAnchor = $this->calculateDistanceMeters(
                        $currentCluster[0]['lat'], $currentCluster[0]['lng'],
                        $curr['lat'], $curr['lng']
                    );

                    if ($distFromAnchor <= $clusterRadiusMeters) {
                        $currentCluster[] = $curr;
                    } else {
                        // Check if previous cluster was a stoppage/pause
                        $firstPt = $currentCluster[0];
                        $lastPt = end($currentCluster);
                        $pauseSeconds = $lastPt['timestamp'] - $firstPt['timestamp'];

                        if ($pauseSeconds >= $haltMinDuration) {
                            $avgLat = array_sum(array_column($currentCluster, 'lat')) / count($currentCluster);
                            $avgLng = array_sum(array_column($currentCluster, 'lng')) / count($currentCluster);

                            $halts[] = [
                                'stop_number' => count($halts) + 1,
                                'lat' => round($avgLat, 6),
                                'lng' => round($avgLng, 6),
                                'start_time' => $firstPt['time'],
                                'end_time' => $lastPt['time'],
                                'start_time_full' => $firstPt['time_full'],
                                'end_time_full' => $lastPt['time_full'],
                                'duration_seconds' => $pauseSeconds,
                                'duration_text' => $this->formatDurationText($pauseSeconds),
                                'battery' => $lastPt['battery'] ?? $firstPt['battery'],
                                'points_count' => count($currentCluster),
                            ];
                        }

                        $currentCluster = [$curr];
                    }
                }

                // Check final cluster at the destination / end of day
                if (count($currentCluster) > 1) {
                    $firstPt = $currentCluster[0];
                    $lastPt = end($currentCluster);
                    $pauseSeconds = $lastPt['timestamp'] - $firstPt['timestamp'];

                    if ($pauseSeconds >= $haltMinDuration) {
                        $avgLat = array_sum(array_column($currentCluster, 'lat')) / count($currentCluster);
                        $avgLng = array_sum(array_column($currentCluster, 'lng')) / count($currentCluster);

                        $halts[] = [
                            'stop_number' => count($halts) + 1,
                            'lat' => round($avgLat, 6),
                            'lng' => round($avgLng, 6),
                            'start_time' => $firstPt['time'],
                            'end_time' => $lastPt['time'],
                            'start_time_full' => $firstPt['time_full'],
                            'end_time_full' => $lastPt['time_full'],
                            'duration_seconds' => $pauseSeconds,
                            'duration_text' => $this->formatDurationText($pauseSeconds),
                            'battery' => $lastPt['battery'] ?? $firstPt['battery'],
                            'points_count' => count($currentCluster),
                        ];
                    }
                }

                // Overall journey statistics
                $totalTripSeconds = max(0, $endPoint['timestamp'] - $startPoint['timestamp']);
                $totalHaltSeconds = array_sum(array_column($halts, 'duration_seconds'));
                $movingSeconds = max(0, $totalTripSeconds - $totalHaltSeconds);
                $avgSpeedKmh = $movingSeconds > 0 ? round(($totalDistanceMeters / 1000) / ($movingSeconds / 3600), 1) : 0;

                $journeyStats = [
                    'start_time' => $startPoint['time'],
                    'end_time' => $endPoint['time'],
                    'start_time_full' => $startPoint['time_full'],
                    'end_time_full' => $endPoint['time_full'],
                    'start_battery' => $startPoint['battery'],
                    'end_battery' => $endPoint['battery'],
                    'total_distance_km' => round($totalDistanceMeters / 1000, 2),
                    'total_duration' => $this->formatDurationText($totalTripSeconds),
                    'total_halt_duration' => $this->formatDurationText($totalHaltSeconds),
                    'moving_duration' => $this->formatDurationText($movingSeconds),
                    'total_stops' => count($halts),
                    'total_points' => $totalPoints,
                    'max_speed_kmh' => $maxSpeedKmh,
                    'avg_speed_kmh' => $avgSpeedKmh,
                ];
            }
        }

        // Branch Geofence Data
        $branchGeofence = null;
        if ($employee && $employee->branch && $employee->branch->latitude && $employee->branch->longitude) {
            $branchGeofence = [
                'name' => $employee->branch->branch_name,
                'address' => $employee->branch->branch_address ?? '',
                'lat' => (float)$employee->branch->latitude,
                'lng' => (float)$employee->branch->longitude,
                'radius' => (int)($employee->branch->radar ?? 200),
            ];
        }
        $branch_geofence_json = json_encode($branchGeofence);

        $map_view = $company->map_view;
        $location_info = json_encode($latlng);
        $raw_points_json = json_encode($rawPoints);
        $halts_json = json_encode($halts);
        $start_point_json = json_encode($startPoint);
        $end_point_json = json_encode($endPoint);

        return view('user.employee.location', compact(
            'employee',
            'employees',
            'location_info',
            'raw_points_json',
            'halts',
            'halts_json',
            'startPoint',
            'endPoint',
            'start_point_json',
            'end_point_json',
            'journeyStats',
            'shiftDetails',
            'date',
            'map_view',
            'branchGeofence',
            'branch_geofence_json'
        ));
    }

    public function getLiveLocation(Request $request, $id)
    {
        try {
            $companyId = Auth::id();
            $employee = Employee::where('company_id', $companyId)->find($id);

            if (!$employee) {
                return response()->json(['status' => false, 'message' => 'Employee not found'], 404);
            }

            if ((string)$employee->geo_status !== '2') {
                return response()->json([
                    'status' => false,
                    'tracking_active' => false,
                    'geo_status' => (string)$employee->geo_status,
                    'message' => (string)$employee->geo_status === '1'
                        ? 'Location tracking request is pending acceptance by employee.'
                        : 'Tracking is turned off by company.'
                ], 200);
            }

            $date = $request->input('date', date('Y-m-d'));
            $lastTimestamp = $request->input('since_timestamp');

            $query = EmployeeGeoLocation::where('employee_id', $employee->id)
                ->whereDate('created_at', $date);

            if ($lastTimestamp) {
                $query->where('created_at', '>', Carbon::createFromTimestamp($lastTimestamp)->toDateTimeString());
            }

            $locations = $query->orderBy('created_at', 'asc')->get();

            $newPoints = [];
            foreach ($locations as $loc) {
                $newPoints[] = [
                    'lat' => (float)$loc->latitude,
                    'lng' => (float)$loc->longitude,
                    'time' => $loc->created_at->format('h:i A'),
                    'time_full' => $loc->created_at->format('d M Y, h:i:s A'),
                    'timestamp' => $loc->created_at->timestamp,
                    'battery' => $loc->battery_status ?? null,
                ];
            }

            return response()->json([
                'status' => true,
                'tracking_active' => true,
                'total_new_points' => count($newPoints),
                'new_points' => $newPoints,
                'latest_timestamp' => count($newPoints) > 0 ? end($newPoints)['timestamp'] : $lastTimestamp,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateGeoStatus(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required',
                'geo_status' => 'required'
            ]);

            $companyId = Auth::id();
            $employee = Employee::where('company_id', $companyId)->find($request->employee_id);

            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found or does not belong to your company.',
                ], 404);
            }

            $newGeoStatus = (string) $request->geo_status;
            $employee->geo_status = $newGeoStatus;
            $employee->save();

            // When location tracking is requested/enabled (geo_status = 1), log in-app notification
            if ($newGeoStatus === '1') {
                Notification::create([
                    'employee_id' => $employee->id,
                    'company_id' => $companyId,
                    'message' => 'Company has requested your real-time location tracking.',
                    'status' => 'unread',
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => $newGeoStatus === '1' 
                    ? 'Geo tracking request sent to employee successfully.' 
                    : 'Geo tracking disabled successfully.',
                'geo_status' => $employee->geo_status,
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating status: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function calculateDistanceMeters($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function formatDurationText($seconds)
    {
        if ($seconds < 60) {
            return $seconds . 's';
        }
        $minutes = round($seconds / 60);
        if ($minutes < 60) {
            return $minutes . ' min' . ($minutes > 1 ? 's' : '');
        }
        $hours = floor($minutes / 60);
        $remMinutes = $minutes % 60;
        if ($remMinutes == 0) {
            return $hours . ' hr' . ($hours > 1 ? 's' : '');
        }
        return $hours . 'h ' . $remMinutes . 'm';
    }

    public function getMonthlyAttendance(Request $request, $id)
    {
        try {
            $companyId = Auth::id();
            $employee = Employee::with(['branch', 'department', 'shift', 'shifts'])
                ->where('company_id', $companyId)
                ->find($id);

            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found or does not belong to your company.'
                ], 404);
            }

            $month = sprintf('%02d', (int) $request->input('month', date('m')));
            $year = (int) $request->input('year', date('Y'));

            // Attendance records for the month
            $monthlyAttendances = Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get()
                ->keyBy(function($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                });

            // Company Holidays for the month
            $monthlyHolidays = Holiday::where('company_id', $companyId)
                ->where(function($q) use ($year, $month) {
                    $q->whereYear('start_date', $year)->whereMonth('start_date', $month)
                      ->orWhere(function($sq) use ($year, $month) {
                          $sq->whereYear('end_date', $year)->whereMonth('end_date', $month);
                      });
                })
                ->get();

            $holidayDates = [];
            foreach ($monthlyHolidays as $h) {
                $sDate = Carbon::parse($h->start_date);
                $eDate = !empty($h->end_date) ? Carbon::parse($h->end_date) : $sDate;
                for ($d = $sDate->copy(); $d->lte($eDate); $d->addDay()) {
                    if ($d->format('m') == $month && $d->format('Y') == $year) {
                        $holidayDates[$d->format('Y-m-d')] = [
                            'title' => $h->title,
                            'description' => $h->description ?? '',
                        ];
                    }
                }
            }

            // Approved Leaves for the month
            $approvedLeaves = EmployeeLeave::with('leavetype')
                ->where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where(function($q) use ($year, $month) {
                    $q->whereYear('from_date', $year)->whereMonth('from_date', $month)
                      ->orWhere(function($sq) use ($year, $month) {
                          $sq->whereYear('to_date', $year)->whereMonth('to_date', $month);
                      });
                })
                ->get();

            $leaveDates = [];
            foreach ($approvedLeaves as $l) {
                $sDate = Carbon::parse($l->from_date);
                $eDate = !empty($l->to_date) ? Carbon::parse($l->to_date) : $sDate;
                $leaveTitle = (!empty($l->leavetype->name)) ? $l->leavetype->name : ((!empty($l->leave_type) && !is_numeric($l->leave_type)) ? $l->leave_type : 'Approved Leave');
                for ($d = $sDate->copy(); $d->lte($eDate); $d->addDay()) {
                    if ($d->format('m') == $month && $d->format('Y') == $year) {
                        $leaveDates[$d->format('Y-m-d')] = [
                            'leave_type' => $leaveTitle,
                            'reason' => $l->reason ?? '',
                        ];
                    }
                }
            }

            // Days calculation
            $startOfMonth = Carbon::createFromDate($year, (int)$month, 1)->startOfMonth();
            $endOfMonth = Carbon::createFromDate($year, (int)$month, 1)->endOfMonth();
            $daysInMonth = $endOfMonth->day;
            $today = Carbon::today();

            $days = [];
            $presentCount = 0;
            $halfDayCount = 0;
            $leaveCount = 0;
            $holidayCount = 0;
            $weekendCount = 0;
            $absentCount = 0;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $cur = Carbon::createFromDate($year, (int)$month, $day);
                $dateStr = $cur->format('Y-m-d');
                $isSunday = $cur->isSunday();
                $isToday = $cur->isSameDay($today);
                $isFuture = $cur->gt($today) && !$isToday;

                $att = $monthlyAttendances[$dateStr] ?? null;
                $hol = $holidayDates[$dateStr] ?? null;
                $leave = $leaveDates[$dateStr] ?? null;

                $status = 'none';
                $statusText = 'No Record';
                $badgeClass = 'bg-light text-muted';
                $inTime = null;
                $outTime = null;
                $workDuration = null;

                if ($att) {
                    $inTime = $att->in_time ? Carbon::parse($att->in_time)->format('h:i A') : null;
                    $outTime = $att->out_time ? Carbon::parse($att->out_time)->format('h:i A') : null;
                    if ($att->in_time && $att->out_time) {
                        $inCarbon = Carbon::parse($att->in_time);
                        $outCarbon = Carbon::parse($att->out_time);
                        if ($outCarbon->gte($inCarbon)) {
                            $diffMin = $inCarbon->diffInMinutes($outCarbon);
                            $h = intdiv($diffMin, 60);
                            $m = $diffMin % 60;
                            $workDuration = ($h > 0 ? "{$h}h " : "") . "{$m}m";
                        }
                    }

                    if ((int)$att->halfday === 1 || strtolower($att->attendance) === 'half day' || strtolower($att->attendance) === 'halfday') {
                        $status = 'halfday';
                        $statusText = 'Half Day';
                        $badgeClass = 'bg-warning text-dark';
                        $halfDayCount++;
                    } elseif (strtolower($att->attendance) === 'leave') {
                        $status = 'leave';
                        $statusText = 'Leave';
                        $badgeClass = 'bg-info text-white';
                        $leaveCount++;
                    } elseif (strtolower($att->attendance) === 'absent') {
                        $status = 'absent';
                        $statusText = 'Absent';
                        $badgeClass = 'bg-danger text-white';
                        $absentCount++;
                    } else {
                        $status = 'present';
                        $statusText = 'Present';
                        $badgeClass = 'bg-success text-white';
                        $presentCount++;
                    }
                } elseif ($hol) {
                    $status = 'holiday';
                    $statusText = $hol['title'];
                    $badgeClass = 'bg-purple text-white';
                    $holidayCount++;
                } elseif ($leave) {
                    $status = 'leave';
                    $statusText = $leave['leave_type'];
                    $badgeClass = 'bg-primary text-white';
                    $leaveCount++;
                } elseif ($isSunday) {
                    $status = 'weekend';
                    $statusText = 'Weekly Off';
                    $badgeClass = 'bg-secondary text-white';
                    $weekendCount++;
                } elseif (!$isFuture) {
                    // Past working day without attendance/holiday/leave
                    if ($cur->lt($today)) {
                        $status = 'absent';
                        $statusText = 'Absent';
                        $badgeClass = 'bg-danger text-white';
                        $absentCount++;
                    } else {
                        // Today without punch yet
                        $status = 'pending';
                        $statusText = 'Pending Check-in';
                        $badgeClass = 'bg-warning-subtle text-warning';
                    }
                } else {
                    $status = 'future';
                    $statusText = 'Upcoming';
                    $badgeClass = 'bg-light text-muted';
                }

                $days[$dateStr] = [
                    'day' => $day,
                    'day_name' => $cur->format('D'),
                    'full_date' => $cur->format('d M, Y'),
                    'status' => $status,
                    'status_text' => $statusText,
                    'badge_class' => $badgeClass,
                    'is_today' => $isToday,
                    'is_future' => $isFuture,
                    'is_weekend' => $isSunday,
                    'in_time' => $inTime,
                    'out_time' => $outTime,
                    'work_duration' => $workDuration,
                    'punchin_image' => $att && $att->punchin_image ? asset($att->punchin_image) : null,
                    'punchout_image' => $att && $att->punchout_image ? asset($att->punchout_image) : null,
                    'location_url' => route('employee.location', ['id' => $employee->id]) . '?date=' . $dateStr,
                ];
            }

            return response()->json([
                'status' => true,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'emp_id' => $employee->emp_id,
                    'phone' => $employee->phone,
                    'email' => $employee->email,
                    'image' => ($employee->image && file_exists(public_path('uploads/employees/' . $employee->image))) ? asset('uploads/employees/' . $employee->image) : null,
                    'initials' => strtoupper(substr($employee->name ?? 'E', 0, 2)),
                    'branch' => $employee->branch->branch_name ?? 'Not Assigned',
                    'department' => $employee->department->name ?? 'Not Assigned',
                    'shift' => $employee->shift->shift_name ?? 'Not Assigned',
                    'geo_status' => (string)$employee->geo_status,
                ],
                'month' => $month,
                'month_name' => Carbon::createFromDate($year, (int)$month, 1)->format('F'),
                'year' => $year,
                'start_day_of_week' => $startOfMonth->dayOfWeekIso, // 1 (Mon) to 7 (Sun)
                'days_in_month' => $daysInMonth,
                'summary' => [
                    'total_days' => $daysInMonth,
                    'working_days' => max(0, $daysInMonth - $weekendCount - $holidayCount),
                    'present_count' => $presentCount,
                    'half_day_count' => $halfDayCount,
                    'leave_count' => $leaveCount,
                    'holiday_count' => $holidayCount,
                    'weekend_count' => $weekendCount,
                    'absent_count' => $absentCount,
                ],
                'days' => $days,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}