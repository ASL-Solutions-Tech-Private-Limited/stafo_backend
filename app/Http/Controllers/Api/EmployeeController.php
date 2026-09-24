<?php

namespace App\Http\Controllers\Api;

use DB;
use Exception;
use Carbon\Carbon;
use App\Models\Branch;
use App\Models\JobRole;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\EmployeeType;
use App\Models\Notification;
use App\Models\EmployeeShift;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\EmployeeLeave;
use App\Models\EmployeePunch;
use App\Models\ProprietorDetail;
use App\Models\EmployeeGeoLocation;
use App\Models\DeviceLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use SapientPro\ImageComparatorLaravel\Facades\Comparator;
use SapientPro\ImageComparator\Strategy\DifferenceHashStrategy;
use App\Helpers\Helper;


class EmployeeController extends Controller
{



    public function index(Request $request)
    {
        try {
            $companyId = Auth::id();
            $targetDate = $request->input('date');
            $employeeId = $request->input('employee_id');

            $employeesQuery = Employee::with(['branch', 'department', 'attendances', 'shifts'])
                ->where('company_id', $companyId);

            // If employee_id is provided, filter by employee_id
            if ($employeeId) {
                $employeesQuery->where('id', $employeeId);
            }

            // Get the employees
            $employees = $employeesQuery->get();

            return response()->json([
                'status' => true,
                'message' => 'Employees fetched successfully',
                'data' => $employees->map(function ($employee) use ($targetDate) {
                    // Get the attendance records
                    $attendances = $employee->attendances;
                    
                    $punches = $employee->punches;
                    // If a target date is provided, filter attendances by that date
                    if ($targetDate) {
                        $attendances = $attendances->filter(function ($attendance) use ($targetDate) {
                            return $attendance->date == $targetDate; // Match attendance date
                        });
                        $punches = $punches->filter(function ($punch) use ($targetDate) {
                            return $punch->created_at == $targetDate; // Match attendance date
                        });
                    }

                    // If no attendance record is found for the target date, mark as 'Absent'
                    // if ($targetDate && $attendances->isEmpty()) {
                    //     // Create a new attendance object and mark it as Absent
                    //     $absentAttendance = new \stdClass();
                    //     $absentAttendance->id = '';
                    //     $absentAttendance->attendance = 'Absent';
                    //     $absentAttendance->halfday = 0;
                    //     $absentAttendance->date = $targetDate;
                    //     $absentAttendance->in_time = null;
                    //     $absentAttendance->out_time = null;
                    //     $absentAttendance->punchin_image = '';
                    //     $absentAttendance->punchout_image = '';
                    //     $attendances = collect([$absentAttendance]);
                    // }

                    // Sort attendance by date (latest first)
                    $attendances = $attendances->sortByDesc('date');
                    // Return the employee data with the related attendance

                    return [
                        'id' => $employee->id,
                        'emp_id' => $employee->emp_id,
                        'name' => $employee->name,
                        'email' => $employee->email,
                        'phone' => $employee->phone,
                        'position' => $employee->position,
                        'salary' => $employee->salary,
                        'image' => $employee->image,
                        'image_path' => asset('uploads/employees/'),
                        'selfie_image' => $employee->selfie_image,
                        'selfie_image_path' => asset('uploads/employees/selfie/'),
                        'company_id' => $employee->company_id,
                        'branch_name' => $employee->branch ? $employee->branch->branch_name : null,
                        'department_name' => $employee->department ? $employee->department->name : null,
                        'geo_status' => $employee->geo_status,
                        'status' => $employee->status,
                        'device_status' => $employee->device_status,
                        'device_name' => $employee->device_name,
                        'android_version' => $employee->android_version,
                        'attendance_type' => $employee->attendance_type,
                        'punches'=>$punches,
                        'attendances' => $attendances->map(function ($attendance) {
                            return (object)[
                                'id' => $attendance->id,
                                'attendance' => $attendance->attendance,
                                'halfday' => $attendance->halfday,
                                'date' => $attendance->date,
                                'in_time' => $attendance->in_time,
                                'out_time' => $attendance->out_time,
                                'punchin_image' => $attendance->punchin_image && file_exists(public_path('uploads/employees/punchin/' . $attendance->punchin_image))
                                    ? asset('uploads/employees/punchin/' . $attendance->punchin_image)
                                    : '',

                                'punchout_image' => $attendance->punchout_image && file_exists(public_path('uploads/employees/punchout/' . $attendance->punchout_image))
                                    ? asset('uploads/employees/punchout/' . $attendance->punchout_image)
                                    : '',

                            ];
                        })->values(),
                        'shifts' => $employee->shifts->map(function ($shift) {
                            return [
                                'id' => $shift->id,
                                'shift_name' => $shift->shift_name,
                                'start_time' => $shift->start_time,
                                'end_time' => $shift->end_time,
                                'company_id' => $shift->company_id,
                                'sunday' => $shift->sunday,
                                'monday' => $shift->monday,
                                'tuesday' => $shift->tuesday,
                                'wednesday' => $shift->wednesday,
                                'thursday' => $shift->thursday,
                                'friday' => $shift->friday,
                                'saturday' => $shift->saturday,
                                
                                
                            ];
                        })->values(),
                    ];
                }),
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }









    // employee details
    public function show($id)
    {
        try {
            // Find the employee by ID
            $employee = Employee::find($id);
            $imageUrl = asset('uploads/employees/' . $employee->image);
            if ($employee) {
                $company = CompanyDetail::find($employee->company_id);
                if ($company) {
                    $companyName = $company->company_name;
                } else {
                    $companyName = null;
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Employee details fetched successfully',
                    'data' => $employee,
                    'company_name' => $companyName,
                    'image_url' => $imageUrl,

                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found',
                    'data' => null
                ], 200);
            }
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function employeeDashboard(Request $request)
    {
        $emp = Auth::user();

        $employee_info = Employee::with(['branch', 'shifts', 'employeeType', 'punches' => function ($query) {
            $query->latest();
        }])->find($emp->id);
        $company_id = $employee_info->company_id;
        // dd($company_id);
        $today = date('Y-m-d');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $today = date('Y-m-d');

        $employeeCount = Employee::where('company_id', $company_id)->count();
        $presentCount = Attendance::where('date', $today)->count();
        $employeesOnLeave = EmployeeLeave::select('id', 'employee_id', 'from_date', 'to_date', 'reason', 'leave_type')->with(['employeeBasicInfo'])->where('company_id', $company_id)->where('status', 'approved')->whereMonth('from_date', $currentMonth)->whereYear('from_date', $currentYear)->where('from_date', '>=', $today)->get();
        $birthday = Employee::select('id', 'emp_id', 'date_of_birth', 'name', 'email', 'phone', 'image')->where('company_id', $company_id)->whereMonth('date_of_birth', date('m'))->where('date_of_birth', '>=', $today)->get();
        $annyversary = Employee::select('id', 'emp_id', 'date_of_joining', 'name', 'email', 'phone', 'image')->where('company_id', $company_id)->whereMonth('date_of_joining', date('m'))->get();

        $isLeaveToday = EmployeeLeave::where('employee_id', $emp->id)->where('status', 'approved')
            ->whereDate('from_date', '<=', Carbon::today())
            ->whereDate('to_date', '>=', Carbon::today())
            ->exists();


        return response()->json([
            'status' => true,
            'message' => 'Record fetched successfully',
            'employeeCount' => $employeeCount,
            'presentCount' => $presentCount,
            'isLeaveToday' => $isLeaveToday,
            'employeesOnLeave' => $employeesOnLeave,
            'birthday' => $birthday,
            'annyversary' => $annyversary,
            'employee_info' => $employee_info,
            'permissions' => $employee_info->getEffectivePermissions(),

        ], 200);
    }

    public function listByCompany(Request $request)
    {
        try {
            // Validate the company_id is present in the request
            $validated = $request->validate([
                'company_id' => 'required|exists:company_details,id' // Ensure the company_id exists in company_details
            ]);

            // Fetch employees based on the company_id
            $employees = Employee::where('company_id', $validated['company_id'])->get();

            // dd($employees);

            // Check if any employees are found
            if ($employees->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No employees found for this company',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Employees fetched successfully',
                'data' => $employees
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }



    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email',
                'phone' => 'required|numeric|unique:employees,phone',
            ]);

            $company_id = Auth::id();
            $company = CompanyDetail::findOrFail($company_id); 
            //$addRestriction = $this->sitesetting(5);
            $employeeCount = Employee::where('company_id', $company_id)->count();
            $company->max_employee_add = 40;
            if ($employeeCount >= $company->max_employee_add) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have reached the maximum limit of employees. Please upgrade your plan to add more employees.',
                ], 200);
            }

            // Check if branch_id is provided and exists, if not return error
            if ($request->has('branch_id') && !\App\Models\Branch::find($request->branch_id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'The provided branch ID does not exist. Please provide a valid branch.',
                ], 200);
            }

            // Check if department_id is provided and exists, if not return error
            if ($request->has('department_id') && !\App\Models\Department::find($request->department_id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'The provided department ID does not exist. Please provide a valid department.',
                ], 200);
            }

            // Generate employee ID
            $emp_id = 'EMP-' . str_pad(Employee::count() + 1, 5, '0', STR_PAD_LEFT);



            // Create the employee
            $employee = Employee::create([
                'emp_id' => $emp_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'position' => $request->position,
                'company_id' => $company_id,
                //'branch_id' => $request->branch_id,
                //'department_id' => $request->department_id,
                'date_of_joining' => $request->date_of_joining,
                'gender' => $request->gender,
                'address' => $request->address,
                'status' => '1'
            ]);
            $employee_id = $employee->id;
            
            if(!empty($employee)){
                $employee_id = $employee->id;
                $shift_ids = $request->shift_ids;
                //dd($employee_id);
                EmployeeShift::where('employee_id', $employee_id)->delete();
                foreach($shift_ids as $shift_id){
                    $employeeShift = EmployeeShift::create([
                        'employee_id' => $employee_id,
                        'shift_id' => $shift_id,
                        'company_id' => $company_id,
                    ]);
                }
                
                $company->increment('employee_added', 1);
            }
            
            
            // Load related branch and department data
            $employee->load('branch', 'department');
            //$employee = Employee::with(['shifts'])
                //->where('id', $employee_id)->get();
            // Return successful response
            return response()->json([
                'status' => true,
                'message' => 'Employee registered successfully',
                'data' => [
                    'employee' => $employee,
                    'branch_name' => isset($employee->branch->branch_name) ?: '',
                    'department_name' => isset($employee->department->name) ?: '',
                ]
            ], 200);
        } catch (ValidationException $e) {
            $errors = $e->errors();

            // Check if the email or phone is already used
            if (isset($errors['email'])) {
                $message = 'The email is already in use. Please choose a different one.';
            } elseif (isset($errors['phone'])) {
                $message = 'The phone number is already in use. Please choose a different one.';
            } else {
                $message = 'Validation Error';
            }

            return response()->json([
                'status' => false,
                'message' => $message,
                'data' => $errors
            ], 200);
        } catch (QueryException $e) {
            // Handle database errors
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {

        try {

            $employee = Employee::findOrFail($id);
            // $company_id = Auth::id();

            if ($request->has('email') && $request->email != $employee->email) {
                $emailExists = Employee::where('email', $request->email)->exists();
                if ($emailExists) {
                    return response()->json([
                        'status' => false,
                        'message' => 'The email is already in use. Please choose a different one.',
                    ], 200);
                }
            }

            if ($request->has('branch_id') && !Branch::where('id', $request->branch_id)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid branch ID provided.',
                ], 200);
            }

            if ($request->has('department_id') && !Department::where('id', $request->department_id)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid department ID provided.',
                ], 200);
            }
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);
                $image = $request->file('image');
                $imageName = 'employee_' . time() . '.' . $image->getClientOriginalExtension();

                $image->move(public_path('uploads/employees'), $imageName);
                $employee->image =  $imageName;
            }

            // Update the employee record
            $employee->update([
                'name' => $request->name ?? $employee->name,
                'email' => $request->email ?? $employee->email,
                'phone' => $request->phone ?? $employee->phone,
                'position' => $request->position ?? $employee->position,
                'salary' => $request->salary ?? $employee->salary,
                'branch_id' => $request->branch_id ?? $employee->branch_id,
                'department_id' => $request->department_id ?? $employee->department_id,
                //   'company_id' => $company_id,
                'marital_status' => $request->marital_status ?? $employee->marital_status,
                'guardian_name' => $request->guardian_name ?? $employee->guardian_name,
                'blood_group' => $request->blood_group ?? $employee->blood_group,
                'date_of_joining' => $request->date_of_joining ?? $employee->date_of_joining,
                'date_of_birth' => $request->date_of_birth ?? $employee->date_of_birth,
                'gender' => $request->gender ?? $employee->gender,
                'address' => $request->address ?? $employee->address,
                'country' => $request->country ?? $employee->country,
                'state' => $request->state ?? $employee->state,
                'city' => $request->city ?? $employee->city,
                'date_of_leaving' => $request->date_of_leaving ?? $employee->date_of_leaving,
                'job_title_id' => $request->job_title_id ?? $employee->job_title_id,
                'employee_type_id' => $request->employee_type_id ?? $employee->employee_type_id,
                'official_email_id' => $request->official_email_id ?? $employee->official_email_id,
                'pf_number' => $request->pf_number ?? $employee->pf_number,
                'esi_number' => $request->esi_number ?? $employee->esi_number,
            ]);
            $employee->load('branch', 'department');

            // $imageUrl = url($employee->image);
            $imageUrl = asset('uploads/employees/' . $employee->image);

            return response()->json([
                'status' => true,
                'message' => 'Employee updated successfully',
                'data' => [
                    'employee' => $employee,
                    'branch_name' => isset($employee->branch->branch_name) ? $employee->branch->branch_name : '',
                    'department_name' => isset($employee->department->name) ? $employee->department->name : '',
                    'image_url' => $imageUrl,
                ]
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }








    public function leaveRequest(Request $request)
    {
        //$id = Auth::id();

        //dd($id);
        //dd($request->employee_id);
        try {

            $request->validate([
                'employee_id' => 'required',
                'from_date' => 'required',
                'to_date' => 'required'
            ]);
            $employeeInfo = Employee::find($request->employee_id);
            $startDate = strtotime($request->from_date);
            $endDate = strtotime($request->to_date);

            $days = ($endDate - $startDate) / (60 * 60 * 24);



            $employee = EmployeeLeave::create([
                'company_id' => $employeeInfo['company_id'],
                'branch_id' => $employeeInfo['branch_id'],
                'department_id' => $employeeInfo['department_id'],
                'employee_id' => $request->employee_id,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'reason' => $request->reason,
                'leave_type' => $request->leave_type,
                'days' => $days+1,

            ]);

            // Create a notification for the employee
            // Notification::create([
            //     'employee_id' => $request->employee_id,
            //     'company_id' => $employeeInfo->company_id,
            //     'message' => "Your leave request for {$days} days from {$request->from_date} to {$request->to_date} has been submitted successfully.",
            //     'status' => 'unread',
            //     'source' => 'compnay',

            // ]);

            return response()->json([
                'status' => true,
                'message' => 'Leave request submitted successfully.',
                'employleave' => $employee,

            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function pendingLeaveRequest()
    {
        $company_id = Auth::user()->id;
        try {
            $leave = EmployeeLeave::with(['employeeBasicInfo'])->where('company_id', $company_id)->where('status', 'pending')->get();

            return response()->json([
                'status' => true,
                'message' => 'Pending leave list',
                'data' => $leave
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while retrieving the list.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function leaveList(Request $request)
    {
        try {
            $leave = EmployeeLeave::with(['employeeBasicInfo','leavetype:id,name,no_of_days,description,is_paid'])
                ->when($request->company_id, fn($q) => $q->where('company_id', $request->company_id))
                ->when($request->employee_id, fn($q) => $q->where('employee_id', $request->employee_id))
                ->get();
            $leaveCount = [];
            if (!empty($request->employee_id)) {
                $fromdate = date('Y') . '-01-01';
                $todate = date('Y') . '-12-31';
                $leaveCount = EmployeeLeave::where('employee_id', $request->employee_id)
                    ->select('leave_type', DB::raw('SUM(days) as total_days'))
                    ->whereBetween('from_date', [$fromdate, $todate])
                    ->groupBy('leave_type')
                    ->get();
            }



            return response()->json([
                'status' => true,
                'message' => 'Leave list',
                'leaveCount' => $leaveCount,
                'data' => $leave,

            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while retrieving the list.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function leaveRequestStatusChange(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'status' => 'required'
            ]);

            $leave = EmployeeLeave::find($request->id);
            $leave->status = $request->status;
            $leave->save();

            if ($request->status == 'approved') {
                $employee = Employee::find($leave->employee_id);
                if ($leave->leave_type == 1)
                    $employee->casual_leave = $employee->casual_leave - $leave->days;
                if ($leave->leave_type == 2)
                    $employee->sick_leave = $employee->sick_leave - $leave->days;
                if ($leave->leave_type == 3)
                    $employee->privileged_leave = $employee->privileged_leave - $leave->days;
                $employee->save();
            }

            return response()->json([
                'message' => 'Leave request status updated successfully.',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function punch(Request $request)
    {
        // dd("test");
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'latitude' => 'required',
                'longitude' => 'required'
            ]);

            $employee = Employee::find($request->employee_id);
            if (!$employee->hasPermission('attendance.punch')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Attendance punch is disabled for your account by your company.',
                ], 403);
            }
            $company_id = $employee->company_id;
            $company = CompanyDetail::find($company_id);
            $branch_info = Branch::find($employee->branch_id);
            //dd($branch_info);
            $getDistance = $this->calculateDistance($request->latitude, $request->longitude, $branch_info->latitude, $branch_info->longitude);
            //dd($getDistance);
            $fcm = $employee->fcm_token;
            // Check if the employee is within range
            if ($getDistance > $branch_info->radar) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are out of range',
                ], 200);
            }

            // Get the current date (to ensure punch-ins and punch-outs are on the same day)
            $currentDate = now()->toDateString();

            // Check if the employee has already punched in today
            $existingPunch = EmployeePunch::where('employee_id', $employee->id)
                ->whereDate('punch_in', $currentDate) // Ensure the punch-in is for today
                ->whereNull('punch_out') // Ensure there's no punch-out yet
                ->latest()
                ->first();

            // If the employee has punched in today, only allow punch-out
            if ($existingPunch) {
                // If there's an existing punch-in record (today), it's time to punch-out
                $existingPunch->update([
                    'punch_out' => now(), // Set punch-out time as the current timestamp
                ]);

                // Update the attendance table with the punch-out time
                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();

                if ($attendance) {
                    // If attendance exists, update the out_time
                    $attendance->update([
                        'out_time' => now(),
                    ]);
                }
                $notification_message = "You have successfully punched out";                
                Helper::sendPushNotification($fcm,$notification_message);
                $company_notification_message = $employee->name." successfully punched out";                
                Helper::sendPushNotification($company->fcm_token,$company_notification_message);

                return response()->json([
                    'status' => true,
                    'message' => 'Punch-out successful.',
                    'data' => $existingPunch,
                ], 200);
            } else {
                // If the employee hasn't punched in today, create a new punch-in record
                $punchIn = EmployeePunch::create([
                    'employee_id' => $employee->id,
                    'punch_in' => now(), // Record punch-in time as the current timestamp
                ]);

                $existsAttendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();
                if (!$existsAttendance) {
                    // Create a new attendance record for the punch-in action
                    Attendance::create([
                        'company_id' => $employee->company_id,
                        'branch_id' => $employee->branch_id,
                        'department_id' => $employee->department_id,
                        'employee_id' => $employee->id,
                        'attendance' => 'Present', // Set as 'Present' for the punch-in
                        'date' => $currentDate,
                        'in_time' => now(),
                        'out_time' => null, // No punch-out yet
                    ]);
                }
                //$notification_message = "You have successfully punched in at " . now()->format('h:i A') . " on " . now()->format('d M Y') . ".";
                $notification_message = "You have successfully punched";                
                Helper::sendPushNotification($fcm,$notification_message);
                $company_notification_message = $employee->name." successfully punched in";                
                Helper::sendPushNotification($company->fcm_token,$company_notification_message);
                
                return response()->json([
                    'status' => true,
                    'message' => 'Punch-in successful.',
                    'data' => $punchIn,
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred during punching.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function punchList(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required',
                'date' => 'required|date_format:Y-m-d',
            ]);

            // Fetch the punch list for the employee on the specified date
            $punches = EmployeePunch::where('employee_id', $request->employee_id)
                ->whereDate('punch_in', $request->date)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Punch list fetched successfully.',
                'data' => $punches,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function selfieAttendance(Request $request)
    {
        try {
            // $request->validate([
            //     'employee_id' => 'required',
            //     'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // ]);

            $employee = Employee::find($request->employee_id);
            $similarity = 100;
            if ($employee->selfie_image != null) {
                $employeeImage = asset('uploads/employees/selfie') . '/' . $employee->selfie_image;
                Comparator::setHashStrategy(new DifferenceHashStrategy());
                $similarity = Comparator::compare($employeeImage, $request->file('image')->getPathname());
            }


            if ($similarity < 30) {
                $word = $employee->id . "<" . date('Y-m-d H:i:s') . ">" . "=" . $similarity;
                //$filePath = storage_path('app/selfie.txt');
                //file_put_contents($filePath, $word . PHP_EOL, FILE_APPEND);

                return response()->json([
                    'status' => false,
                    'similarity' => $similarity,
                    'message' => 'Selfie does not match with the employee image',
                ], 200);
            } else {
                $currentDate = now()->toDateString();
                $currentDateTime = now()->toDateTimeString();
                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();
                if ($attendance) {
                    $punchoutImage = '';
                    if ($request->hasFile('image')) {
                        // dd("test");
                        $image = $request->file('image');
                        $punchoutImage = 'punchout_' . time() . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('uploads/employees/punchout'), $punchoutImage);
                    }

                    $attendance->update([
                        'out_time' => $currentDateTime,
                        'punchout_image' => $punchoutImage,
                    ]);
                } else {
                    $punchinImage = '';
                    if ($request->hasFile('image')) {
                        $image = $request->file('image');
                        $punchinImage = 'punchin_' . time() . '.' . $image->getClientOriginalExtension();

                        $image->move(public_path('uploads/employees/punchin'), $punchinImage);
                    }
                    Attendance::create([
                        'company_id' => $employee->company_id,
                        'branch_id' => $employee->branch_id,
                        'department_id' => $employee->department_id,
                        'employee_id' => $employee->id,
                        'attendance' => 'Present',
                        'date' => $currentDate,
                        'in_time' => $currentDateTime,
                        'out_time' => null,
                        'punchin_image' => $punchinImage,
                    ]);
                }



                // Get the current date (to ensure punch-ins and punch-outs are on the same day)
                $currentDate = now()->toDateString();

                // Check if the employee has already punched in today
                $existingPunch = EmployeePunch::where('employee_id', $employee->id)
                    ->whereDate('punch_in', $currentDate) // Ensure the punch-in is for today
                    ->whereNull('punch_out') // Ensure there's no punch-out yet
                    ->latest()
                    ->first();

                // If the employee has punched in today, only allow punch-out
                if ($existingPunch) {
                    // If there's an existing punch-in record (today), it's time to punch-out
                    $existingPunch->update([
                        'punch_out' => now(), // Set punch-out time as the current timestamp
                    ]);

                    // Update the attendance table with the punch-out time
                    $attendance = Attendance::where('employee_id', $employee->id)
                        ->whereDate('date', $currentDate)
                        ->first();

                    if ($attendance) {
                        // If attendance exists, update the out_time
                        $attendance->update([
                            'out_time' => now(),
                        ]);
                    }

                    return response()->json([
                        'status' => true,
                        'similarity' => $similarity,
                        'message' => 'Selfie Out successfully.',
                    ], 200);
                } else {
                    // If the employee hasn't punched in today, create a new punch-in record
                    $punchIn = EmployeePunch::create([
                        'employee_id' => $employee->id,
                        'punch_in' => now(), // Record punch-in time as the current timestamp
                    ]);

                    $existsAttendance = Attendance::where('employee_id', $employee->id)
                        ->whereDate('date', $currentDate)
                        ->first();
                    if (!$existsAttendance) {
                        // Create a new attendance record for the punch-in action
                        Attendance::create([
                            'company_id' => $employee->company_id,
                            'branch_id' => $employee->branch_id,
                            'department_id' => $employee->department_id,
                            'employee_id' => $employee->id,
                            'attendance' => 'Present', // Set as 'Present' for the punch-in
                            'date' => $currentDate,
                            'in_time' => now(),
                            'out_time' => null, // No punch-out yet
                        ]);
                    }

                    return response()->json([
                        'status' => true,
                        'similarity' => $similarity,
                        'message' => 'Selfie In successfully.',
                    ], 200);
                }
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function selfieImageUpload(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required',
                'selfie_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $employee = Employee::find($request->employee_id);
            $employeeImage = '';

            if ($request->hasFile('selfie_image')) {
                $image = $request->file('selfie_image');
                $imageName = 'employees_' . time() . '.' . $image->getClientOriginalExtension();

                $image->move(public_path('uploads/employees/selfie'), $imageName);
                $employee->selfie_image =  $imageName;
                $employeeImage = asset('uploads/employees/selfie') . '/' . $imageName;
            }
            //$this->uploadImage($request, $employee);
            $employee->save();
            return response()->json([
                'status' => true,
                'selfie_image' => $employeeImage,
                'message' => 'Selfie uploaded successfully.',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function selfieImageRemove(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required',
            ]);

            $employee = Employee::find($request->employee_id);


            @unlink(public_path('uploads/employees/selfie') . '/' . $employee->selfie_image);
            $employee->selfie_image = null;
            //$this->uploadImage($request, $employee);
            $employee->save();
            return response()->json([
                'status' => true,
                'message' => 'Selfie uploaded successfully.',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function qrAttendance(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required',
                'qrcode' => 'required',
            ]);

            $employee = Employee::find($request->employee_id);
            $qrcode = base64_decode($request->qrcode);
            $qrcode = json_decode($qrcode);
            if ($qrcode->company_id != $employee->company_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid QR Code',
                ], 200);
            } else {
                $currentDate = now()->toDateString();
                $currentDateTime = now()->toDateTimeString();
                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();
                if ($attendance) {
                    $attendance->update([
                        'out_time' => $currentDateTime,
                    ]);
                } else {
                    Attendance::create([
                        'company_id' => $employee->company_id,
                        'branch_id' => $employee->branch_id,
                        'department_id' => $employee->department_id,
                        'employee_id' => $employee->id,
                        'attendance' => 'Present',
                        'date' => $currentDate,
                        'in_time' => $currentDateTime,
                        'out_time' => null,
                    ]);
                }



                // Get the current date (to ensure punch-ins and punch-outs are on the same day)
                $currentDate = now()->toDateString();

                // Check if the employee has already punched in today
                $existingPunch = EmployeePunch::where('employee_id', $employee->id)
                    ->whereDate('punch_in', $currentDate) // Ensure the punch-in is for today
                    ->whereNull('punch_out') // Ensure there's no punch-out yet
                    ->latest()
                    ->first();

                // If the employee has punched in today, only allow punch-out
                if ($existingPunch) {
                    // If there's an existing punch-in record (today), it's time to punch-out
                    $existingPunch->update([
                        'punch_out' => now(), // Set punch-out time as the current timestamp
                    ]);

                    // Update the attendance table with the punch-out time
                    $attendance = Attendance::where('employee_id', $employee->id)
                        ->whereDate('date', $currentDate)
                        ->first();

                    if ($attendance) {
                        // If attendance exists, update the out_time
                        $attendance->update([
                            'out_time' => now(),
                        ]);
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'QR attendance Out successfully.',
                    ], 200);
                } else {
                    // If the employee hasn't punched in today, create a new punch-in record
                    $punchIn = EmployeePunch::create([
                        'employee_id' => $employee->id,
                        'punch_in' => now(), // Record punch-in time as the current timestamp
                    ]);

                    $existsAttendance = Attendance::where('employee_id', $employee->id)
                        ->whereDate('date', $currentDate)
                        ->first();
                    if (!$existsAttendance) {
                        // Create a new attendance record for the punch-in action
                        Attendance::create([
                            'company_id' => $employee->company_id,
                            'branch_id' => $employee->branch_id,
                            'department_id' => $employee->department_id,
                            'employee_id' => $employee->id,
                            'attendance' => 'Present', // Set as 'Present' for the punch-in
                            'date' => $currentDate,
                            'in_time' => now(),
                            'out_time' => null, // No punch-out yet
                        ]);
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'QR attendance In successfully.',
                    ], 200);
                }
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updateGeoStatus(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required',
                'geo_status' => 'required'
            ]);
            $employeeInfo = Employee::find($request->employee_id);

            if (!$employeeInfo) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found.',
                ], 404);
            }

            $employeeInfo->geo_status = (string) $request->geo_status;
            $employeeInfo->save();

            // Step 1: Company passes request to Employee -> store 1
            if ($employeeInfo->geo_status === '1') {
                Notification::create([
                    'employee_id' => $employeeInfo->id,
                    'company_id' => $employeeInfo->company_id,
                    'message' => 'Company has requested your real-time location tracking.',
                    'status' => 'unread',
                ]);
            }
            // Step 1 (acceptance): Employee accepts the request -> store 2
            elseif ($employeeInfo->geo_status === '2') {
                Notification::create([
                    'employee_id' => $employeeInfo->id,
                    'company_id' => $employeeInfo->company_id,
                    'message' => ($employeeInfo->name ?? 'Employee') . ' has accepted your real-time location tracking request.',
                    'status' => 'unread',
                ]);
            }

            $message = 'Record updated successfully.';
            if ($employeeInfo->geo_status === '1') {
                $message = 'Location tracking requested successfully.';
            } elseif ($employeeInfo->geo_status === '2') {
                $message = 'Location tracking accepted successfully.';
            } elseif ($employeeInfo->geo_status === '0') {
                $message = 'Location tracking disabled successfully.';
            }

            return response()->json([
                'status' => true,
                'message' => $message,
                'geo_status' => (string) $employeeInfo->geo_status,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dedicated API for Employee to Accept Geo Tracking Request (Sets geo_status = 2)
     */
    public function acceptGeoTracking(Request $request)
    {
        try {
            $employeeId = $request->input('employee_id');
            if (!$employeeId && Auth::guard('sanctum')->check()) {
                $employeeId = Auth::guard('sanctum')->id();
            }

            if (!$employeeId) {
                return response()->json([
                    'status' => false,
                    'message' => 'The employee_id field is required.',
                ], 422);
            }

            $employee = Employee::with('company')->find($employeeId);
            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found.',
                ], 404);
            }

            // Step 1 accepted: store 2
            $employee->geo_status = '2';
            $employee->save();

            // Create notification for Company
            Notification::create([
                'employee_id' => $employee->id,
                'company_id' => $employee->company_id,
                'message' => ($employee->name ?? 'Employee') . ' has accepted your real-time location tracking request.',
                'status' => 'unread',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Real-time location tracking request accepted successfully.',
                'geo_status' => '2',
                'data' => [
                    'employee_id' => $employee->id,
                    'name' => $employee->name,
                    'geo_status' => '2',
                    'tracking_active' => true,
                    'company_id' => $employee->company_id,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while accepting location tracking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dedicated API for Employee / Company to Reject or Disable Geo Tracking (Sets geo_status = 0)
     */
    public function rejectGeoTracking(Request $request)
    {
        try {
            $employeeId = $request->input('employee_id');
            if (!$employeeId && Auth::guard('sanctum')->check()) {
                $employeeId = Auth::guard('sanctum')->id();
            }

            if (!$employeeId) {
                return response()->json([
                    'status' => false,
                    'message' => 'The employee_id field is required.',
                ], 422);
            }

            $employee = Employee::with('company')->find($employeeId);
            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found.',
                ], 404);
            }

            // Step 2 disable: store 0
            $employee->geo_status = '0';
            $employee->save();

            // Create notification for Company
            Notification::create([
                'employee_id' => $employee->id,
                'company_id' => $employee->company_id,
                'message' => ($employee->name ?? 'Employee') . ' has declined or disabled real-time location tracking.',
                'status' => 'unread',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Real-time location tracking declined / disabled successfully.',
                'geo_status' => '0',
                'data' => [
                    'employee_id' => $employee->id,
                    'name' => $employee->name,
                    'geo_status' => '0',
                    'tracking_active' => false,
                    'company_id' => $employee->company_id,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while disabling location tracking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Current Geo Tracking Status and Shift Window for Employee
     */
    public function getGeoTrackingStatus(Request $request)
    {
        try {
            $employeeId = $request->input('employee_id');
            if (!$employeeId && Auth::guard('sanctum')->check()) {
                $employeeId = Auth::guard('sanctum')->id();
            }

            if (!$employeeId) {
                return response()->json([
                    'status' => false,
                    'message' => 'The employee_id field is required.',
                ], 422);
            }

            $employee = Employee::with(['company', 'shifts', 'shift'])->find($employeeId);
            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found.',
                ], 404);
            }

            $geoStatus = (string)($employee->geo_status ?? '0');
            $statusLabel = 'Tracking OFF';
            if ($geoStatus === '1') {
                $statusLabel = 'Request Sent (Pending Acceptance)';
            } elseif ($geoStatus === '2') {
                $statusLabel = 'Tracking ON (Active)';
            }

            $shiftCheck = $employee->getActiveShiftWindow();

            return response()->json([
                'status' => true,
                'geo_status' => $geoStatus,
                'status_label' => $statusLabel,
                'is_requested' => ($geoStatus === '1'),
                'is_active' => ($geoStatus === '2'),
                'is_off' => ($geoStatus === '0'),
                'shift_window' => $shiftCheck,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'company_id' => $employee->company_id,
                    'company_name' => $employee->company ? $employee->company->company_name : null,
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching tracking status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeGeoLocation(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required',
                'latitude' => 'required',
                'longitude' => 'required'
            ]);

            // Shift-based and status-based location tracking:
            // Location is strictly tracked only when employee has accepted tracking (geo_status = 2)
            // and during the employee's assigned shift time.
            $employee = Employee::with(['shifts', 'shift'])->find($request->employee_id);
            if ($employee) {
                // If geo_status is not 2 (accepted), reject storing location
                if ((string)$employee->geo_status !== '2') {
                    return response()->json([
                        'status' => false,
                        'tracking_active' => false,
                        'geo_status' => (string)$employee->geo_status,
                        'message' => (string)$employee->geo_status === '1'
                            ? 'Location tracking request is pending acceptance.'
                            : 'Location tracking is disabled by company.',
                    ], 200);
                }

                $shiftCheck = $employee->getActiveShiftWindow();
                // If shifts are configured for this employee and current time is outside the shift window
                if ($shiftCheck !== null && empty($shiftCheck['active'])) {
                    return response()->json([
                        'status' => false,
                        'tracking_active' => false,
                        'message' => 'Location tracking is active only during shift hours. Next tracking will start at the next scheduled shift time.',
                    ], 200);
                }
            }

            $employeeGeoLocation = EmployeeGeoLocation::create([
                'employee_id' => $request->employee_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'battery_status' => $request->battery_status,
            ]);

            return response()->json([
                'status' => true,
                'tracking_active' => true,
                'message' => 'Geo location submitted successfully.',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getGeoLocation(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required'
            ]);

            $date = $request->input('date', date('Y-m-d'));
            $employeeGeoLocation = EmployeeGeoLocation::where('employee_id', $request->employee_id);

            $employee = Employee::with(['shifts', 'shift'])->find($request->employee_id);
            if ($employee) {
                $shiftDetails = $employee->getShiftWindowForDate($date);
                if ($shiftDetails && isset($shiftDetails['start']) && isset($shiftDetails['end'])) {
                    $employeeGeoLocation->whereBetween('created_at', [
                        $shiftDetails['start']->toDateTimeString(),
                        $shiftDetails['end']->toDateTimeString()
                    ]);
                } else {
                    $employeeGeoLocation->whereDate('created_at', $date);
                }
            } else {
                $employeeGeoLocation->whereDate('created_at', $date);
            }

            $location = $employeeGeoLocation->get();


            return response()->json([
                'status' => true,
                'message' => 'Geo location fetched successfully.',
                'data' => $location
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371 * 1000; // Radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in meters
    }

    public function employeetypeList()
    {
        try {
            // Fetch all employee types
            $employeeTypes = EmployeeType::all();

            return response()->json([
                'status' => true,
                'message' => 'Employee types fetched successfully',
                'data' => $employeeTypes
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function jobtitleList()
    {
        try {
            // Fetch all employee types
            $JobRole = JobRole::all();

            return response()->json([
                'status' => true,
                'message' => 'Job Title fetched successfully',
                'data' => $JobRole
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function setAttendanceType(Request $request)
    {
        try {

            $request->validate([
                'employee_id' => 'required',
                'attendance_type' => 'required'
            ]);
            $employeeInfo = Employee::find($request->employee_id);

            $employeeInfo->attendance_type = $request->attendance_type;
            $employeeInfo->save();

            return response()->json([
                'status' => true,
                'message' => 'Record updated successfully.',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function runMarkAbsentJob()
    {
        try {
            // Run the cron job via Artisan command
            Artisan::call('employee:mark-absent');
            return response()->json([
                'status' => true,
                'message' => 'Employee absence marking job executed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error executing the job.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function employeeStatusChange(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'status' => 'required'
            ]);

            $employee = Employee::find($request->id);
            $employee->status = $request->status;
            $employee->save();
            return response()->json([
                'status' => true,
                'message' => 'Status changed successfully.',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function employeeBranchInfo(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required'
            ]);

            $employee = Employee::find($request->employee_id);
            $branch = Branch::find($employee->branch_id);

            return response()->json([
                'status' => true,
                'message' => 'Branch info fetched successfully.',
                'data' => $branch
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignBranch(Request $request)
    {
        try {

            $request->validate([
                'employee_id' => 'required',
                'branch_id' => 'required'
            ]);
            $employeeInfo = Employee::find($request->employee_id);

            $employeeInfo->branch_id = $request->branch_id;
            $employeeInfo->save();

            return response()->json([
                'status' => true,
                'message' => 'Record updated successfully.',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignDepartment(Request $request)
    {
        try {

            $request->validate([
                'employee_id' => 'required',
                'department_id' => 'required'
            ]);
            $employeeInfo = Employee::find($request->employee_id);

            $employeeInfo->department_id = $request->department_id;
            $employeeInfo->save();

            return response()->json([
                'status' => true,
                'message' => 'Record updated successfully.',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function devicelogStore(Request $request)
    {
        try {

            $request->validate([
                'employee_id' => 'required',
                'company_id' => 'required',
                'log_data' => 'required'
            ]);
            $today = date('Y-m-d');
            $deviceLog = DeviceLog::where('employee_id', $request->employee_id)
                ->where('company_id', $request->company_id)
                ->whereDate('created_at', $today)
                ->first();
            $log_data = json_decode($request->log_data, true);
            $log_data = json_encode($log_data);             
            if (!$deviceLog) {
                $deviceLog = new DeviceLog();
                $deviceLog->employee_id = $request->employee_id;
                $deviceLog->company_id = $request->company_id;
                $deviceLog->log_data = $log_data;
            } else {
                $deviceLog->log_data = $log_data;
            }
            $deviceLog->save();
            

            return response()->json([
                'status' => true,
                'message' => 'Record added successfully.',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while creating the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function punchReminder(Request $request)
    {
        try {
            $employees = Employee::where('status', 1)->get();
            $currenttime = date('H:i');
            $currentday = strtolower(date('l')); 
            foreach ($employees as $employee) { 

                $shift_start = $employee->shifts()->where($currentday,'!=',0)->first();  
                // Add 5 minutes to shift start time

                
                $intimecheck = null;
                if (isset($shift_start->start_time)) {
                    $intimecheck = date('H:i', strtotime($shift_start->start_time . ' +5 minutes'));
                    $maxtimecheck = date('H:i', strtotime($shift_start->start_time . ' +7 minutes'));
                }
                if ($currenttime >= $intimecheck && $currenttime <= $maxtimecheck) {
                    // Send push notification to employee
                    $fcm = $employee->fcm_token;
                    $notification_message = "Please check your punch-in time for today";                
                    Helper::sendPushNotification($fcm,$notification_message);
                    echo "Notification sent to employee ID: " . $employee->id . "\n";
                }

                $outtimecheck = null;
                if (isset($shift_start->end_time)) {
                    $outtimecheck = date('H:i', strtotime($shift_start->end_time . ' +5 minutes'));
                    $maxtimecheck = date('H:i', strtotime($shift_start->end_time . ' +7 minutes'));
                }
                if ($currenttime >= $outtimecheck && $currenttime <= $maxtimecheck) {
                    // Send push notification to employee
                    $fcm = $employee->fcm_token;
                    $notification_message = "Please check your punch-out time for today";                
                    Helper::sendPushNotification($fcm,$notification_message);
                    echo "Notification sent to employee ID: " . $employee->id . "\n";
                }
                
            }
            
            
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching the request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}