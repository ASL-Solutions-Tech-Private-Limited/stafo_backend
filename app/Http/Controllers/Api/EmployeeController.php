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
use App\Services\FaceVerificationService;
use App\Helpers\Helper;
use App\Models\Shift;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class EmployeeController extends Controller
{



    /**
     * @OA\Post(
     *      path="/api/employees-list",
     *      operationId="getEmployeesList",
     *      tags={"Employees"},
     *      summary="List Employees",
     *      description="Returns list of employees for the authenticated company",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="date", type="string", format="date", example="2026-09-26")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Employees list fetched successfully"),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     */
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
                        'hasSelfie' => !empty($employee->selfie_image) && file_exists(public_path('uploads/employees/selfie/' . $employee->selfie_image)),
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









    /**
     * @OA\Get(
     *      path="/api/employee-details/{id}",
     *      operationId="getEmployeeDetails",
     *      tags={"Employees"},
     *      summary="Get Employee Details by ID",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Employee details fetched successfully"),
     *      @OA\Response(response=404, description="Employee not found")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/employee-dashboard",
     *      operationId="getEmployeeDashboard",
     *      tags={"Employees"},
     *      summary="Employee Dashboard Details",
     *      description="Returns dashboard info for the authenticated employee including active shifts, branch, and punches",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Dashboard details fetched successfully"),
     *      @OA\Response(response=401, description="Unauthenticated")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/employees-list/by-company-id",
     *      operationId="getEmployeesByCompanyId",
     *      tags={"Employees"},
     *      security={{"sanctum":{}}},
     *      summary="List Employees by Company ID",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="company_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Employees fetched successfully"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
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



    /**
     * @OA\Post(
     *      path="/api/employees-create",
     *      operationId="createEmployee",
     *      tags={"Employees"},
     *      security={{"sanctum":{}}},
     *      summary="Register/Create a new employee",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="phone", type="string", example="9876543210"),
     *              @OA\Property(property="position", type="string", example="Software Engineer"),
     *              @OA\Property(property="branch_id", type="integer", example=1),
     *              @OA\Property(property="department_id", type="integer", example=1),
     *              @OA\Property(property="shift_ids", type="array", @OA\Items(type="integer"), example={1, 2}),
     *              @OA\Property(property="date_of_joining", type="string", format="date", example="2026-01-01"),
     *              @OA\Property(property="gender", type="string", example="Male"),
     *              @OA\Property(property="address", type="string", example="123 Street")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Employee registered successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
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



    /**
     * @OA\Post(
     *      path="/api/employees-update/{id}",
     *      operationId="updateEmployee",
     *      tags={"Employees"},
     *      security={{"sanctum":{}}},
     *      summary="Update employee details",
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="phone", type="string", example="9876543210"),
     *              @OA\Property(property="position", type="string", example="Senior Dev"),
     *              @OA\Property(property="salary", type="number", example=50000),
     *              @OA\Property(property="branch_id", type="integer", example=1),
     *              @OA\Property(property="department_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Employee updated successfully")
     * )
     */
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








    /**
     * @OA\Post(
     *      path="/api/leave-request",
     *      operationId="submitLeaveRequest",
     *      tags={"Leave Management"},
     *      security={{"sanctum":{}}},
     *      summary="Submit an employee leave request",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="from_date", type="string", format="date", example="2026-10-01"),
     *              @OA\Property(property="to_date", type="string", format="date", example="2026-10-03"),
     *              @OA\Property(property="leave_type", type="integer", description="1=Casual, 2=Sick, 3=Privileged", example=1),
     *              @OA\Property(property="reason", type="string", example="Medical emergency")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Leave request submitted successfully")
     * )
     */
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

            // Create notification for company
            try {
                Notification::create([
                    'employee_id' => $request->employee_id,
                    'company_id' => $employeeInfo->company_id,
                    'message' => "{$employeeInfo->name} applied for " . ($days + 1) . " day(s) leave ({$request->from_date} to {$request->to_date}).",
                    'status' => 'unread',
                ]);
            } catch (\Throwable $th) {}

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



    /**
     * @OA\Post(
     *      path="/api/pending-leave-request",
     *      operationId="getPendingLeaveRequests",
     *      tags={"Leave Management"},
     *      security={{"sanctum":{}}},
     *      summary="Get pending leave requests for company",
     *      @OA\Response(response=200, description="Pending leave list fetched")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/leave-list",
     *      operationId="getLeaveList",
     *      tags={"Leave Management"},
     *      security={{"sanctum":{}}},
     *      summary="List all leaves for company or employee",
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="company_id", type="integer", example=1),
     *              @OA\Property(property="employee_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Leave list fetched")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/leave-request-status-change",
     *      operationId="changeLeaveStatus",
     *      tags={"Leave Management"},
     *      security={{"sanctum":{}}},
     *      summary="Approve or Reject Employee Leave Request",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", description="Leave Request ID", example=1),
     *              @OA\Property(property="status", type="string", enum={"approved", "rejected"}, example="approved")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Leave request status updated successfully")
     * )
     */
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



    /**
     * @OA\Post(
     *      path="/api/employee/punch",
     *      operationId="employeePunch",
     *      tags={"Attendance"},
     *      summary="Employee Geo Punch-In / Punch-Out",
     *      description="Records punch-in or punch-out for the employee with latitude and longitude",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"employee_id","latitude","longitude"},
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="latitude", type="string", example="28.6139"),
     *              @OA\Property(property="longitude", type="string", example="77.2090")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Punch action recorded successfully"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
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

                try {
                    Notification::create([
                        'employee_id' => $employee->id,
                        'company_id' => $company_id,
                        'message' => $company_notification_message . " at " . now()->format('h:i A') . ".",
                        'status' => 'unread',
                    ]);
                } catch (\Throwable $th) {}

                Helper::sendAttendanceEmailNotification($employee, 'punch_out', now(), 'Mobile App Punch');

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

                try {
                    Notification::create([
                        'employee_id' => $employee->id,
                        'company_id' => $company_id,
                        'message' => $company_notification_message . " at " . now()->format('h:i A') . ".",
                        'status' => 'unread',
                    ]);
                } catch (\Throwable $th) {}
                
                Helper::sendAttendanceEmailNotification($employee, 'punch_in', now(), 'Mobile App Punch');

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

    /**
     * @OA\Post(
     *      path="/api/employee/punch-list",
     *      operationId="getEmployeePunchList",
     *      tags={"Attendance"},
     *      summary="Get Employee Punch History for a Date",
     *      description="Fetches all punch-in and punch-out logs for a given employee and date",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"employee_id","date"},
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="date", type="string", format="date", example="2026-09-26")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Punch list fetched successfully")
     * )
     */
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
    /**
     * @OA\Post(
     *      path="/api/employee/selfie-attendance",
     *      operationId="selfieAttendance",
     *      tags={"Attendance"},
     *      summary="Selfie Punch-In / Punch-Out",
     *      description="Records punch action with face image comparison",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"employee_id","image"},
     *                  @OA\Property(property="employee_id", type="integer", example=1),
     *                  @OA\Property(property="image", type="string", format="binary", description="Selfie image file")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="Selfie punch processed successfully")
     * )
     */
    public function selfieAttendance(Request $request)
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

            $employee = Employee::find($employeeId);
            
            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found.',
                ], 404);
            }

            // 1. Handle captured selfie image input (file upload, base64, or image string)
            $capturedImageTempPath = null;
            $punchImageName = '';
            $punchinDir = public_path('uploads/employees/punchin');
            $punchoutDir = public_path('uploads/employees/punchout');
            $selfieDir = public_path('uploads/employees/selfie');

            if (!file_exists($punchinDir)) {
                @mkdir($punchinDir, 0777, true);
            }
            if (!file_exists($punchoutDir)) {
                @mkdir($punchoutDir, 0777, true);
            }
            if (!file_exists($selfieDir)) {
                @mkdir($selfieDir, 0777, true);
            }

            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $punchImageName = 'selfie_' . time() . '_' . Str::random(6) . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move($punchinDir, $punchImageName);
                $capturedImageTempPath = $punchinDir . '/' . $punchImageName;
            } elseif ($request->filled('image_base64')) {
                $base64 = $request->input('image_base64');
                if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                    $base64 = substr($base64, strpos($base64, ',') + 1);
                    $type = strtolower($type[1]);
                    $base64Data = base64_decode($base64);
                    if ($base64Data !== false) {
                        $punchImageName = 'selfie_' . time() . '_' . Str::random(6) . '.' . ($type === 'jpeg' ? 'jpg' : $type);
                        file_put_contents($punchinDir . '/' . $punchImageName, $base64Data);
                        @chmod($punchinDir . '/' . $punchImageName, 0666);
                        $capturedImageTempPath = $punchinDir . '/' . $punchImageName;
                    }
                }
            } elseif ($request->filled('image') && is_string($request->input('image'))) {
                $rawImage = $request->input('image');
                if (str_starts_with($rawImage, 'data:image/') || base64_decode($rawImage, true) !== false) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $rawImage, $type)) {
                        $rawImage = substr($rawImage, strpos($rawImage, ',') + 1);
                        $type = strtolower($type[1]);
                    } else {
                        $type = 'jpg';
                    }
                    $base64Data = base64_decode($rawImage);
                    if ($base64Data !== false) {
                        $punchImageName = 'selfie_' . time() . '_' . Str::random(6) . '.' . ($type === 'jpeg' ? 'jpg' : $type);
                        file_put_contents($punchinDir . '/' . $punchImageName, $base64Data);
                        @chmod($punchinDir . '/' . $punchImageName, 0666);
                        $capturedImageTempPath = $punchinDir . '/' . $punchImageName;
                    }
                }
            }

            if (!$capturedImageTempPath || !file_exists($capturedImageTempPath)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Selfie image is required to mark attendance.',
                ], 200);
            }

            // 2. Determine Punch Action (Punch-In vs Punch-Out)
            $currentDate = now()->toDateString();
            $nowTime = now();
            $currentDateTime = $nowTime->toDateTimeString();

            $existingPunch = EmployeePunch::where('employee_id', $employee->id)
                ->whereDate('punch_in', $currentDate)
                ->whereNull('punch_out')
                ->latest()
                ->first();

            $actionType = $existingPunch ? 'punch_out' : 'punch_in';

            // 3. Strict Face Verification Check
            $similarity = 100;
            $isFirstRegistration = false;
            $referenceImagePath = !empty($employee->selfie_image) ? public_path('uploads/employees/selfie/' . $employee->selfie_image) : null;

            // If reference selfie is not registered or file missing, auto-register this first selfie image as the reference face
            if (empty($employee->selfie_image) || !$referenceImagePath || !file_exists($referenceImagePath)) {
                $ext = pathinfo($punchImageName, PATHINFO_EXTENSION) ?: 'jpg';
                $registeredSelfieName = 'employees_' . time() . '_' . Str::random(6) . '.' . $ext;
                @copy($capturedImageTempPath, $selfieDir . '/' . $registeredSelfieName);
                @chmod($selfieDir . '/' . $registeredSelfieName, 0666);

                $employee->selfie_image = $registeredSelfieName;
                $employee->save();

                $similarity = 100;
                $isFirstRegistration = true;
            } else {
                // Primary Check: Compare captured face with registered profile face using Python AI face recognition
                $verifyResult = FaceVerificationService::verify($referenceImagePath, $capturedImageTempPath);
               
              
                $similarity = $verifyResult['similarity'] ?? 0;
                $actionLabel = ($actionType === 'punch_out') ? 'Punch Out' : 'Punch In';

                // For Punch-Out: Also verify against today's punch-in image if available
                $punchinVerifyResult = null;
                if ($actionType === 'punch_out') {
                    $todayAtt = Attendance::where('employee_id', $employee->id)
                        ->whereDate('date', $currentDate)
                        ->first();

                    if ($todayAtt && !empty($todayAtt->punchin_image)) {
                        $todayPunchinPath = public_path('uploads/employees/punchin/' . $todayAtt->punchin_image);
                        if (file_exists($todayPunchinPath)) {
                            $punchinVerifyResult = FaceVerificationService::verify($todayPunchinPath, $capturedImageTempPath);
                        }
                    }
                }

                // Match is valid if registered profile photo matches, OR for punch-out if today's punch-in photo matches
                $isMatch = !empty($verifyResult['match']);
                $effectiveScore = $similarity;

                if ($actionType === 'punch_out' && $punchinVerifyResult !== null) {
                    if (!empty($punchinVerifyResult['match'])) {
                        $isMatch = true;
                    }
                    $effectiveScore = max($similarity, (float)($punchinVerifyResult['similarity'] ?? 0));
                }

                // If face verification fails
                if (!$isMatch) {
                    if (file_exists($capturedImageTempPath)) {
                        @unlink($capturedImageTempPath);
                    }

                    $errorCode = $verifyResult['error_code'] ?? ($punchinVerifyResult['error_code'] ?? null);
                    if ($errorCode === 'NO_FACE_IN_QUERY') {
                        $errorMessage = 'Face not detected in selfie. Please take a clear photo.';
                    } elseif ($errorCode === 'NO_FACE_IN_REF') {
                        $errorMessage = 'Face not detected in registered profile photo.';
                    } else {
                        $errorMessage = 'Face does not match registered employee.';
                    }

                    return response()->json([
                        'status' => false,
                        'face_matched' => false,
                        'action' => $actionType,
                        'similarity' => round($effectiveScore, 2),
                        'engine' => $verifyResult['engine'] ?? ($punchinVerifyResult['engine'] ?? 'face_service'),
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'message' => $errorMessage,
                    ], 200);
                }
            }

            // 4. Face Verified Successfully -> Record Punch-In or Punch-Out
            if ($existingPunch) {
                // Punch OUT
                if (file_exists($punchinDir . '/' . $punchImageName)) {
                    @copy($punchinDir . '/' . $punchImageName, $punchoutDir . '/' . $punchImageName);
                    @unlink($punchinDir . '/' . $punchImageName);
                }

                $existingPunch->update([
                    'punch_out' => $nowTime,
                ]);

                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();

                // Delete old punchout images for this employee so only 1 punchout image remains
                $previousPunchouts = Attendance::where('employee_id', $employee->id)
                    ->whereNotNull('punchout_image')
                    ->where('punchout_image', '!=', '')
                    ->get();

                foreach ($previousPunchouts as $prevAtt) {
                    if ($prevAtt->punchout_image !== $punchImageName) {
                        $oldPunchoutFile = $punchoutDir . '/' . $prevAtt->punchout_image;
                        if (file_exists($oldPunchoutFile)) {
                            @unlink($oldPunchoutFile);
                        }
                        if (!$attendance || $prevAtt->id !== $attendance->id) {
                            $prevAtt->update(['punchout_image' => null]);
                        }
                    }
                }

                if ($attendance) {
                    $attendance->update([
                        'out_time' => $currentDateTime,
                        'punchout_image' => $punchImageName,
                    ]);
                }

                Helper::sendAttendanceEmailNotification($employee, 'punch_out', $nowTime, 'Selfie Attendance');

                return response()->json([
                    'status' => true,
                    'face_matched' => true,
                    'is_first_registration' => $isFirstRegistration,
                    'type' => 'punch_out',
                    'similarity' => round($similarity, 2),
                    'message' => 'Selfie Out successfully at ' . $nowTime->format('h:i A') . '.',
                    'time' => $nowTime->format('h:i A'),
                    'image' => asset('uploads/employees/punchout/' . $punchImageName),
                ], 200);
            } else {
                // Punch IN
                // Delete old punchin images for this employee so only 1 punchin image remains
                $previousPunchins = Attendance::where('employee_id', $employee->id)
                    ->whereNotNull('punchin_image')
                    ->where('punchin_image', '!=', '')
                    ->get();

                foreach ($previousPunchins as $prevAtt) {
                    if ($prevAtt->punchin_image !== $punchImageName) {
                        $oldPunchinFile = $punchinDir . '/' . $prevAtt->punchin_image;
                        if (file_exists($oldPunchinFile)) {
                            @unlink($oldPunchinFile);
                        }
                        $prevAtt->update(['punchin_image' => null]);
                    }
                }

                $punchIn = EmployeePunch::create([
                    'employee_id' => $employee->id,
                    'punch_in' => $nowTime,
                ]);

                $existsAttendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $currentDate)
                    ->first();

                if ($existsAttendance) {
                    $existsAttendance->update([
                        'in_time' => $currentDateTime,
                        'punchin_image' => $punchImageName,
                        'attendance' => 'Present',
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
                        'punchin_image' => $punchImageName,
                    ]);
                }

                Helper::sendAttendanceEmailNotification($employee, 'punch_in', $nowTime, 'Selfie Attendance');

                return response()->json([
                    'status' => true,
                    'face_matched' => true,
                    'is_first_registration' => $isFirstRegistration,
                    'type' => 'punch_in',
                    'similarity' => round($similarity, 2),
                    'message' => $isFirstRegistration 
                        ? 'Profile selfie registered & Selfie In recorded successfully at ' . $nowTime->format('h:i A') . '.'
                        : 'Selfie In successfully at ' . $nowTime->format('h:i A') . '.',
                    'time' => $nowTime->format('h:i A'),
                    'image' => asset('uploads/employees/punchin/' . $punchImageName),
                ], 200);
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
                'message' => 'An error occurred while processing selfie attendance.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/employee/selfie-image-upload",
     *      operationId="uploadSelfieImage",
     *      tags={"Attendance"},
     *      security={{"sanctum":{}}},
     *      summary="Upload reference selfie image for face recognition",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"employee_id","selfie_image"},
     *                  @OA\Property(property="employee_id", type="integer", example=1),
     *                  @OA\Property(property="selfie_image", type="string", format="binary")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="Selfie uploaded successfully")
     * )
     */
    public function selfieImageUpload(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required',
                'selfie_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $employee = Employee::find($request->employee_id);
            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found.',
                ], 404);
            }

            $employeeImage = '';

            if ($request->hasFile('selfie_image')) {
                // Delete previous selfie image if it exists
                if (!empty($employee->selfie_image)) {
                    $oldImagePath = public_path('uploads/employees/selfie/' . $employee->selfie_image);
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }

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

    /**
     * @OA\Post(
     *      path="/api/employee/selfie-image-remove",
     *      operationId="removeSelfieImage",
     *      tags={"Attendance"},
     *      security={{"sanctum":{}}},
     *      summary="Remove registered employee selfie reference image",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Selfie removed successfully")
     * )
     */
    public function selfieImageRemove(Request $request)
    {
        try {
            $request->validate([
                'employee_id' => 'required',
            ]);

            $employee = Employee::find($request->employee_id);
            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found.',
                ], 404);
            }

            if (!empty($employee->selfie_image)) {
                $oldImagePath = public_path('uploads/employees/selfie/' . $employee->selfie_image);
                if (file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }
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

    /**
     * @OA\Post(
     *      path="/api/employee/qr-attendance",
     *      operationId="qrAttendance",
     *      tags={"Attendance"},
     *      summary="QR Code Punch-In / Punch-Out",
     *      description="Records punch action by scanning the company/branch QR code",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"employee_id","qrcode"},
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="qrcode", type="string", description="Base64 encoded QR string")
     *          )
     *      ),
     *      @OA\Response(response=200, description="QR punch processed successfully")
     * )
     */
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

                    Helper::sendAttendanceEmailNotification($employee, 'punch_out', now(), 'QR Code Attendance');

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

                    Helper::sendAttendanceEmailNotification($employee, 'punch_in', now(), 'QR Code Attendance');

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


    /**
     * @OA\Post(
     *      path="/api/update-geo-status",
     *      operationId="updateGeoStatus",
     *      tags={"Geo Tracking"},
     *      security={{"sanctum":{}}},
     *      summary="Update employee geo tracking status",
     *      description="Sets geo tracking status: 0 = disabled, 1 = requested by company, 2 = accepted by employee",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="geo_status", type="string", enum={"0", "1", "2"}, example="1")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Geo status updated successfully")
     * )
     */
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
     * @OA\Post(
     *      path="/api/accept-geo-tracking",
     *      operationId="acceptGeoTracking",
     *      tags={"Geo Tracking"},
     *      security={{"sanctum":{}}},
     *      summary="Employee accepts real-time geo tracking request",
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Geo tracking accepted")
     * )
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
     * @OA\Post(
     *      path="/api/reject-geo-tracking",
     *      operationId="rejectGeoTracking",
     *      tags={"Geo Tracking"},
     *      security={{"sanctum":{}}},
     *      summary="Reject or disable real-time geo tracking",
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Geo tracking disabled")
     * )
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
     * @OA\Get(
     *      path="/api/get-geo-tracking-status",
     *      operationId="getGeoTrackingStatus",
     *      tags={"Geo Tracking"},
     *      security={{"sanctum":{}}},
     *      summary="Get Current Geo Tracking Status and Shift Window for Employee",
     *      @OA\Parameter(name="employee_id", in="query", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Geo tracking status details")
     * )
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

    /**
     * @OA\Post(
     *      path="/api/store-geo-location",
     *      operationId="storeGeoLocation",
     *      tags={"Geo Tracking"},
     *      security={{"sanctum":{}}},
     *      summary="Store Employee Geo Location waypoint",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="latitude", type="string", example="28.6139"),
     *              @OA\Property(property="longitude", type="string", example="77.2090"),
     *              @OA\Property(property="battery_status", type="string", example="85%")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Geo location recorded")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/get-geo-location",
     *      operationId="getGeoLocation",
     *      tags={"Geo Tracking"},
     *      security={{"sanctum":{}}},
     *      summary="Get recorded employee geo location trail for a date",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="date", type="string", format="date", example="2026-09-26")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Geo location list fetched successfully")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/employeetype-list",
     *      operationId="getEmployeeTypeList",
     *      tags={"Employees"},
     *      summary="List all employee types",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Employee types fetched successfully")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/jobtitle-list",
     *      operationId="getJobTitleList",
     *      tags={"Employees"},
     *      summary="List all job titles / roles",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Job Title fetched successfully")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/set-attendance-type",
     *      operationId="setAttendanceType",
     *      tags={"Attendance"},
     *      summary="Set employee attendance type",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"employee_id","attendance_type"},
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="attendance_type", type="string", example="qr")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Record updated successfully.")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/cron-job/mark-absent",
     *      operationId="runMarkAbsentJob",
     *      tags={"Attendance"},
     *      summary="Run cron job to mark absent employees",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Employee absence marking job executed successfully.")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/employees-status-change",
     *      operationId="employeeStatusChange",
     *      tags={"Employees"},
     *      summary="Change employee status",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"id","status"},
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="status", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Status changed successfully.")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/employee-branch-info",
     *      operationId="getEmployeeBranchInfo",
     *      tags={"Employees"},
     *      summary="Get employee branch information",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"employee_id"},
     *              @OA\Property(property="employee_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Branch info fetched successfully.")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/employee/assign-branch",
     *      operationId="assignEmployeeBranch",
     *      tags={"Employees"},
     *      summary="Assign branch to employee",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"employee_id","branch_id"},
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="branch_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=201, description="Record updated successfully.")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/employee/assign-department",
     *      operationId="assignEmployeeDepartment",
     *      tags={"Employees"},
     *      summary="Assign department to employee",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"employee_id","department_id"},
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="department_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=201, description="Record updated successfully.")
     * )
     */
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

    /**
     * @OA\Post(
     *      path="/api/devicelog-store",
     *      operationId="storeDeviceLog",
     *      tags={"Employees"},
     *      summary="Store employee device log",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"employee_id","company_id","log_data"},
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="company_id", type="integer", example=1),
     *              @OA\Property(property="log_data", type="string", example="{}")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Record added successfully.")
     * )
     */
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

    /**
     * @OA\Get(
     *      path="/api/punchReminder",
     *      operationId="punchReminder",
     *      tags={"Attendance"},
     *      summary="Send punch reminders to employees based on shifts",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Punch reminder processed successfully.")
     * )
     */
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
            
            
            return response()->json([
                'status' => true,
                'message' => 'Punch reminder processed successfully.',
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

    /**
     * @OA\Post(
     *      path="/api/autoPunchOut",
     *      operationId="autoPunchOut",
     *      tags={"Attendance"},
     *      summary="Auto Punch-Out Employees",
     *      description="Automatically punches out employees who punched in today but forgot to punch out. Sets out_time as 00:00:00.",
     *      @OA\Parameter(
     *          name="date",
     *          in="query",
     *          description="Date for auto punch-out (format: YYYY-MM-DD, defaults to today)",
     *          required=false,
     *          @OA\Schema(type="string", format="date", example="2026-09-26")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Auto punch-out processed successfully with 00:00:00 punchout time."),
     *              @OA\Property(property="date", type="string", example="2026-09-26"),
     *              @OA\Property(property="total_punched_out", type="integer", example=2),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="employee_id", type="integer", example=5),
     *                      @OA\Property(property="name", type="string", example="John Doe"),
     *                      @OA\Property(property="punch_in", type="string", example="2026-09-26 09:30:00"),
     *                      @OA\Property(property="punch_out", type="string", example="2026-09-26 00:00:00"),
     *                      @OA\Property(property="out_time", type="string", example="00:00:00")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function autoPunchOut(Request $request)
    {
        try {
            $targetDate = $request->input('date', date('Y-m-d'));

            // Find all punches for targetDate where punch_out is null
            $openPunches = EmployeePunch::whereDate('punch_in', $targetDate)
                ->whereNull('punch_out')
                ->get();

            $updatedEmployees = [];

            foreach ($openPunches as $punch) {
                $employee = Employee::find($punch->employee_id);
                if (!$employee) {
                    continue;
                }

                // Ensure punch_out remains null
                $punch->update([
                    'punch_out' => null,
                ]);

                // Update Attendance record: ensure out_time remains null
                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $targetDate)
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'out_time' => null,
                    ]);
                }

                // Reset geo_status so employee is no longer marked as in-field/active
                $employee->geo_status = 0;
                $employee->save();

                // Send push notification to employee if fcm_token available
                if (!empty($employee->fcm_token)) {
                    $notification_message = "You forgot to punch-out for " . date('d M Y', strtotime($targetDate)) . ". Your punch-out has been recorded.";
                    Helper::sendPushNotification($employee->fcm_token, $notification_message);
                }

                $updatedEmployees[] = [
                    'employee_id' => $employee->id,
                    'name' => $employee->name,
                    'punch_in' => $punch->punch_in,
                    'punch_out' => null,
                    'out_time' => null,
                ];

                Log::info("Employee ID {$employee->id} ({$employee->name}) auto punch-out processed with null out_time for {$targetDate}");
            }

            // Also ensure any Attendance records for the date where in_time is not null and out_time is null have out_time as null
            $openAttendances = Attendance::whereDate('date', $targetDate)
                ->whereNotNull('in_time')
                ->whereNull('out_time')
                ->get();

            foreach ($openAttendances as $att) {
                $att->update(['out_time' => null]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Auto punch-out processed successfully (out_time set to null).',
                'date' => $targetDate,
                'total_punched_out' => count($updatedEmployees),
                'data' => $updatedEmployees,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in autoPunchOut API: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while processing auto punch-out.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/employee/missed-punchout-request",
     *      operationId="employeeMissedPunchOutRequest",
     *      tags={"Employee Attendance"},
     *      summary="[Employee] Submit Missed Punch-Out / Regularization Request",
     *      description="Employee submits a request for missed punch-out with date, punch_out_time and reason for company verification and approval.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"employee_id","date","punch_out_time","reason"},
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="company_id", type="integer", example=1),
     *              @OA\Property(property="date", type="string", format="date", example="2026-09-26"),
     *              @OA\Property(property="punch_out_time", type="string", example="18:30:00"),
     *              @OA\Property(property="reason", type="string", example="Forgot to punch out before leaving office due to client meeting")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Request submitted successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Punch-out request submitted successfully for approval."),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Validation Error"),
     *      @OA\Response(response=500, description="Server Error")
     * )
     */
    public function missedPunchOutRequest(Request $request)
    {
        try {
            $authUser = Auth::user();

            $validator = Validator::make($request->all(), [
                'employee_id' => 'nullable|integer|exists:employees,id',
                'date' => 'required|date',
                'punch_out_time' => 'required|string',
                'reason' => 'required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // If authenticated as Employee, use auth user; otherwise use requested employee_id
            if ($authUser instanceof Employee) {
                $employee = $authUser;
            } else {
                $employeeId = $request->employee_id ?? ($authUser ? $authUser->id : null);
                $employee = Employee::find($employeeId);
            }

            if (!$employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Employee not found',
                ], 404);
            }

            $companyId = $employee->company_id;
            $branchId = $employee->branch_id;
            $departmentId = $employee->department_id;

            // Find in_time from existing attendance or punch
            $inTime = '09:00:00';
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $request->date)
                ->first();

            if ($attendance && !empty($attendance->in_time)) {
                $inTime = $attendance->in_time;
            } else {
                $punch = EmployeePunch::where('employee_id', $employee->id)
                    ->whereDate('punch_in', $request->date)
                    ->first();
                if ($punch && !empty($punch->punch_in)) {
                    $inTime = date('H:i:s', strtotime($punch->punch_in));
                }
            }

            // Format punch out time to H:i:s
            $formattedOutTime = date('H:i:s', strtotime($request->punch_out_time));

            // Check if there is already an existing request for this employee on this date
            $attendanceRequest = AttendanceRequest::where('employee_id', $employee->id)
                ->whereDate('date', $request->date)
                ->where('status', 'Pending')
                ->first();

            if ($attendanceRequest) {
                $attendanceRequest->update([
                    'company_id' => $companyId,
                    'branch_id' => $branchId,
                    'department_id' => $departmentId,
                    'in_time' => $inTime,
                    'out_time' => $formattedOutTime,
                    'reason' => $request->reason,
                    'reject_reason' => null,
                    'status' => 'Pending',
                ]);
            } else {
                $attendanceRequest = AttendanceRequest::create([
                    'company_id' => $companyId,
                    'branch_id' => $branchId,
                    'employee_id' => $employee->id,
                    'department_id' => $departmentId,
                    'attendance' => 'Present',
                    'halfday' => 0,
                    'date' => $request->date,
                    'in_time' => $inTime,
                    'out_time' => $formattedOutTime,
                    'reason' => $request->reason,
                    'status' => 'Pending',
                ]);
            }

            // Send notification to company if available
            $company = CompanyDetail::find($companyId);
            if ($company && !empty($company->fcm_token)) {
                $notifMsg = "Punch-out regularization request received from {$employee->name} for {$request->date}.";
                Helper::sendPushNotification($company->fcm_token, $notifMsg);
            }

            try {
                Notification::create([
                    'employee_id' => $employee->id,
                    'company_id' => $companyId,
                    'message' => "Punch-out regularization request received from {$employee->name} for {$request->date}.",
                    'status' => 'unread',
                ]);
            } catch (\Throwable $th) {}

            return response()->json([
                'status' => true,
                'message' => 'Punch-out request submitted successfully for approval.',
                'data' => $attendanceRequest->load(['employee:id,name,email,phone', 'branch:id,branch_name', 'department:id,name']),
            ], 200);
        } catch (Exception $e) {
            Log::error('Error in missedPunchOutRequest: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while submitting punch-out request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/employee/missed-punchout-list",
     *      operationId="employeeMissedPunchOutList",
     *      tags={"Employee Attendance"},
     *      summary="[Employee] List My Missed Punch-Out Requests",
     *      description="Employee fetches their own submitted punch-out requests filtered by date or status.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="status", type="string", enum={"Pending","Approved","Rejected"}, example="Pending"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-09-26")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="List fetched successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Employee punch-out requests fetched successfully."),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      )
     * )
     */
    public function employeeMissedPunchOutList(Request $request)
    {
        try {
            $authUser = Auth::user();
            $employeeId = ($authUser instanceof Employee) ? $authUser->id : $request->employee_id;

            if (!$employeeId) {
                return response()->json([
                    'status' => false,
                    'message' => 'employee_id is required.',
                ], 422);
            }

            $requests = AttendanceRequest::with([
                'employee:id,name,email,phone,emp_id',
                'company:id,company_name',
                'branch:id,branch_name',
                'department:id,name'
            ])
            ->where('employee_id', $employeeId)
            ->whereNotNull('reason')
            ->when($request->filled('status'), function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                return $q->whereDate('date', $request->date);
            })
            ->orderBy('id', 'desc')
            ->get();

            return response()->json([
                'status' => true,
                'message' => 'Employee punch-out requests fetched successfully.',
                'total' => $requests->count(),
                'data' => $requests,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error in employeeMissedPunchOutList: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching employee punch-out requests.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/company/missed-punchout-list",
     *      operationId="companyMissedPunchOutList",
     *      tags={"Company Attendance"},
     *      summary="[Company] List Employee Missed Punch-Out Requests for Review",
     *      description="Company fetches all employee punch-out regularization requests for verification and review. Strictly restricted to the authenticated company.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1, description="Filter by employee (optional)"),
     *              @OA\Property(property="status", type="string", enum={"Pending","Approved","Rejected"}, example="Pending", description="Filter by status (optional)"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-09-26", description="Filter by date (optional)")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="List fetched successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Company punch-out requests fetched successfully."),
     *              @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *          )
     *      ),
     *      @OA\Response(response=403, description="Unauthorized - Only company login allowed")
     * )
     */
    public function companyMissedPunchOutList(Request $request)
    {
        try {
            $authUser = Auth::user();

            // Strict company-only authorization check (Reject Employees)
            if (!$authUser || $authUser instanceof Employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized. Only company login can access this API.',
                ], 403);
            }

            // Always strictly use authenticated company's ID
            $companyId = $authUser->id;

            $requests = AttendanceRequest::with([
                'employee:id,name,email,phone,emp_id',
                'company:id,company_name',
                'branch:id,branch_name',
                'department:id,name'
            ])
            ->where('company_id', $companyId)
            ->whereNotNull('reason')
            ->when($request->filled('employee_id'), function ($q) use ($request) {
                return $q->where('employee_id', $request->employee_id);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                return $q->whereDate('date', $request->date);
            })
            ->orderBy('id', 'desc')
            ->get();

            return response()->json([
                'status' => true,
                'message' => 'Company punch-out requests fetched successfully.',
                'total' => $requests->count(),
                'data' => $requests,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error in companyMissedPunchOutList: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching company punch-out requests.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/company/missed-punchout-action",
     *      operationId="companyMissedPunchOutAction",
     *      tags={"Company Attendance"},
     *      summary="[Company] Approve or Reject Employee Missed Punch-Out Request",
     *      description="Company verifies and approves or rejects employee missed punch-out request. Only company admin can access. If approved, actual punch-out time is saved. If rejected, reason is mandatory and company can select reject_attendance_type as 'Halfday' or 'Absent' which updates employee attendance accordingly.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"request_id","status"},
     *              @OA\Property(property="request_id", type="integer", example=1),
     *              @OA\Property(property="status", type="string", enum={"Approved","Rejected"}, example="Approved"),
     *              @OA\Property(property="reject_reason", type="string", example="Punchout time does not match security entry register"),
     *              @OA\Property(property="reject_attendance_type", type="string", enum={"Halfday","Absent"}, example="Halfday", description="Mandatory/Used if rejected: Mark as 'Halfday' or 'Absent'")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Action processed successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Punch-out request Approved successfully."),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(response=403, description="Unauthorized - Only company login allowed"),
     *      @OA\Response(response=422, description="Validation Error"),
     *      @OA\Response(response=404, description="Request Not Found")
     * )
     */
    public function companyMissedPunchOutAction(Request $request)
    {
        try {
            $authUser = Auth::user();

            // Strict company-only authorization check (Reject Employees)
            if (!$authUser || $authUser instanceof Employee) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized. Only company login can approve or reject punch-out requests.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'request_id' => 'required|integer|exists:attendance_requests,id',
                'status' => 'required|in:Approved,Rejected',
                'reject_reason' => 'required_if:status,Rejected|nullable|string|max:1000',
                'reject_attendance_type' => 'nullable|in:Halfday,Absent,Half Day,halfday,absent,half_day',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Always strictly use authenticated company's ID
            $companyId = $authUser->id;

            $attendanceRequest = AttendanceRequest::with('employee')
                ->where('id', $request->request_id)
                ->where('company_id', $companyId)
                ->first();

            if (!$attendanceRequest) {
                return response()->json([
                    'status' => false,
                    'message' => 'Attendance request not found or does not belong to your company.',
                ], 404);
            }

            $employee = $attendanceRequest->employee;
            $targetDate = $attendanceRequest->date;

            if ($request->status === 'Approved') {
                $attendanceRequest->status = 'Approved';
                $attendanceRequest->reject_reason = null;
                $attendanceRequest->halfday = 0;
                $attendanceRequest->attendance = 'Present';
                $attendanceRequest->save();

                $outTime = $attendanceRequest->out_time;
                $punchOutDateTime = $targetDate . ' ' . $outTime;

                // 1. Update Attendance record
                $attendance = Attendance::where('employee_id', $attendanceRequest->employee_id)
                    ->whereDate('date', $targetDate)
                    ->first();

                if (!$attendance) {
                    $attendance = new Attendance();
                    $attendance->company_id = $attendanceRequest->company_id;
                    $attendance->branch_id = $attendanceRequest->branch_id;
                    $attendance->employee_id = $attendanceRequest->employee_id;
                    $attendance->department_id = $attendanceRequest->department_id;
                    $attendance->date = $targetDate;
                    $attendance->in_time = $attendanceRequest->in_time ?? '';
                }

                $attendance->attendance = 'Present';
                $attendance->halfday = 0;
                $attendance->out_time = $outTime;
                $attendance->save();

                // 2. Update EmployeePunch record
                $punch = EmployeePunch::where('employee_id', $attendanceRequest->employee_id)
                    ->whereDate('punch_in', $targetDate)
                    ->latest('id')
                    ->first();

                if ($punch) {
                    $punch->update([
                        'punch_out' => $punchOutDateTime,
                    ]);
                } else {
                    EmployeePunch::create([
                        'employee_id' => $attendanceRequest->employee_id,
                        'punch_in' => $targetDate . ' ' . ($attendanceRequest->in_time ?? ''),
                        'punch_out' => $punchOutDateTime,
                    ]);
                }

                // Send push notification to employee
                if ($employee && !empty($employee->fcm_token)) {
                    $notifMsg = "Your punch-out regularisation request for " . date('d M Y', strtotime($targetDate)) . " has been Approved.";
                    Helper::sendPushNotification($employee->fcm_token, $notifMsg);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Punch-out request Approved successfully and attendance updated.',
                    'data' => $attendanceRequest->fresh(['employee:id,name,email,phone', 'branch:id,branch_name', 'department:id,name']),
                ], 200);
            } else {
                // Rejected Flow: Company selects 'Halfday' or 'Absent'
                $rejectType = strtolower($request->input('reject_attendance_type', 'absent'));
                $isHalfDay = in_array($rejectType, ['halfday', 'half day', 'half_day']) ? 1 : 0;
                $attendanceStatus = $isHalfDay ? 'Present' : 'Absent';
                $statusLabel = $isHalfDay ? 'Half Day' : 'Absent';

                // Update AttendanceRequest
                $attendanceRequest->status = 'Rejected';
                $attendanceRequest->reject_reason = $request->reject_reason;
                $attendanceRequest->halfday = $isHalfDay;
                $attendanceRequest->attendance = $attendanceStatus;
                $attendanceRequest->save();

                // Update Attendance record
                $attendance = Attendance::where('employee_id', $attendanceRequest->employee_id)
                    ->whereDate('date', $targetDate)
                    ->first();

                if (!$attendance) {
                    $attendance = new Attendance();
                    $attendance->company_id = $attendanceRequest->company_id;
                    $attendance->branch_id = $attendanceRequest->branch_id;
                    $attendance->employee_id = $attendanceRequest->employee_id;
                    $attendance->department_id = $attendanceRequest->department_id;
                    $attendance->date = $targetDate;
                    $attendance->in_time = $attendanceRequest->in_time ?? '';
                }

                $attendance->attendance = $attendanceStatus;
                $attendance->halfday = $isHalfDay;
                if (!$isHalfDay) {
                    $attendance->out_time = '00:00:00';
                }
                $attendance->save();

                // Send push notification to employee
                if ($employee && !empty($employee->fcm_token)) {
                    $notifMsg = "Your punch-out regularisation request for " . date('d M Y', strtotime($targetDate)) . " was Rejected and marked as {$statusLabel}. Reason: " . $request->reject_reason;
                    Helper::sendPushNotification($employee->fcm_token, $notifMsg);
                }

                return response()->json([
                    'status' => true,
                    'message' => "Punch-out request Rejected successfully and marked as {$statusLabel}.",
                    'attendance_marked_as' => $statusLabel,
                    'data' => $attendanceRequest->fresh(['employee:id,name,email,phone', 'branch:id,branch_name', 'department:id,name']),
                ], 200);
            }
        } catch (Exception $e) {
            Log::error('Error in companyMissedPunchOutAction: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while processing punch-out request action.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}