<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Branch;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\EmployeeShift;

use App\Models\Holiday;
use App\Models\AttendanceRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\EmployeePunch; // Import the EmployeePunch model
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{


  

    public function index(Request $request)
    {
         
        try {

            $companyId = Auth::user()->id;
            if (isset(Auth::user()->company_id)) {
                $companyId = Auth::user()->company_id;
            }

            $employeeId = $request->get('employee_id');
            $month = $request->get('month');
            $query = Attendance::with(['branch', 'employee', 'department'])
                ->where('company_id', $companyId);

            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            if ($month) {
                $query->whereMonth('date', date('m', strtotime($month)))
                    ->whereYear('date', date('Y', strtotime($month)));
            }

            $attendances = $query->get();
            foreach ($attendances as $attendance) {
                if ($attendance->punchin_image) {
                    $attendance->punchin_image = url('uploads/employees/punchin/' . $attendance->punchin_image);
                } else {
                    $attendance->punchin_image = null;
                }

                if ($attendance->punchout_image) {
                    $attendance->punchout_image = url('uploads/employees/punchout/' . $attendance->punchout_image);
                } else {
                    $attendance->punchout_image = null;
                }
            }
            
            
          if (!empty($employeeId)) {
                $employeeShifts = EmployeeShift::with('shift')
                    ->where('employee_id', $employeeId)
                    ->get();
                
                $shifts = $employeeShifts->pluck('shift')->filter();
            }
            
            $holidays = Holiday::where('company_id', $companyId)->get();



            return response()->json([
                'status' => true,
                'message' => 'Attendance records fetched successfully',
                'data' => $attendances,
                'shifts' => $shifts,
               'holidays' => $holidays,
                
            ], 200); // 200 OK

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching attendance records',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }






    // public function index(Request $request)
    // {
    //     try {
    //         $companyId = Auth::user()->id;
    //         if (isset(Auth::user()->company_id)) {
    //             $companyId = Auth::user()->company_id;
    //         }

    //         $employeeId = $request->get('employee_id'); // Employee ID filter
    //         $month = $request->get('month');

    //         $query = Attendance::with(['branch', 'employee', 'department'])
    //             ->where('company_id', $companyId);

    //         if ($employeeId) {
    //             $query->where('employee_id', $employeeId);
    //         }
    //         if ($month) {
    //             $query->whereMonth('date', date('m', strtotime($month)))
    //                 ->whereYear('date', date('Y', strtotime($month)));
    //         }

    //         $attendances = $query->get();

    //         // Loop through each attendance and add the full URL for punch-in and punch-out images
    //         foreach ($attendances as $attendance) {
    //             if ($attendance->punchin_image) {
    //                 $attendance->punchin_image_url = url('uploads/employees/punchin/' . $attendance->punchin_image);
    //             } else {
    //                 $attendance->punchin_image_url = null;
    //             }

    //             if ($attendance->punchout_image) {
    //                 $attendance->punchout_image_url = url('uploads/employees/punchout/' . $attendance->punchout_image);
    //             } else {
    //                 $attendance->punchout_image_url = null;
    //             }
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Attendance records fetched successfully',
    //             'data' => $attendances,
    //         ], 200); // 200 OK
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'An error occurred while fetching attendance records',
    //             'error' => $e->getMessage(),
    //         ], 500); // 500 Internal Server Error
    //     }
    // }



    //
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'branch_id' => 'required|exists:branches,id',
                'employee_id' => 'required|exists:employees,id',
                'department_id' => 'required|exists:departments,id',
                'attendance' => 'required|in:Present,Absent,Leave',
                'date' => 'required|date',
                'in_time' => 'required|date_format:H:i',
                'out_time' => 'nullable|date_format:H:i|after:in_time',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 200);
            }

            $userId = Auth::id();
            // Create a new attendance record
            $attendance = Attendance::create([
                'company_id' =>   $userId,
                'branch_id' => $request->branch_id,
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id,
                'attendance' => $request->attendance,
                'halfday' => $request->halfday ?? 0, // Default to 0 if not provided
                'date' => $request->date,
                'in_time' => $request->in_time,
                'out_time' => $request->out_time ?? null, // If out_time is not provided, it can be null
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Attendance added successfully',
                'data' => $attendance,
            ], 201); // 201 Created
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while adding attendance',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

    public function update(Request $request,$id)
    {
        try {
            // Validate the incoming request
            // $validator = Validator::make($request->all(), [
            //     'branch_id' => 'required|exists:branches,id',
            //     'employee_id' => 'required|exists:employees,id',
            //     'department_id' => 'required|exists:departments,id',
            //     'attendance' => 'required|in:Present,Absent,Leave',
            //     'date' => 'required|date',
            //     'in_time' => 'required|date_format:H:i',
            //     'out_time' => 'nullable|date_format:H:i|after:in_time',
            // ]);

            // if ($validator->fails()) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Validation failed',
            //         'errors' => $validator->errors(),
            //     ], 200);
            // }


            $attendance = Attendance::find($id);
            if(isset($request->branch_id)){
                $attendance->branch_id = $request->branch_id;
            }
            if(isset($request->employee_id)){
                $attendance->employee_id = $request->employee_id;
            }
            if(isset($request->department_id)){
                $attendance->department_id = $request->department_id;
            }
            if(isset($request->attendance)){
                $attendance->attendance = $request->attendance;
            }
            if(isset($request->halfday)){
                $attendance->halfday = $request->halfday;
            }
            if(isset($request->date)){
                $attendance->date = $request->date;
            }
            if(isset($request->in_time)){
                $attendance->in_time = $request->in_time;
            }
            if(isset($request->out_time)){
                $attendance->out_time = $request->out_time;
            }

            $attendance->save();
            

            return response()->json([
                'status' => true,
                'message' => 'Attendance updated successfully',
                'data' => $attendance,
            ], 201); // 201 Created
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating attendance',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

    //
    public function attendance_request_store(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'company_id' => 'required',
                'employee_id' => 'required',
                'attendance' => 'required|in:Present,Absent,Leave',
                'date' => 'required|date',
                'in_time' => 'nullable|date_format:H:i',
                'out_time' => 'nullable|date_format:H:i|after:in_time',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 200);
            }

            // Create a new attendance record
            $attendance = AttendanceRequest::create([
                'company_id' =>  $request->company_id,
                'branch_id' => $request->branch_id,
                'employee_id' => $request->employee_id,
                'department_id' => $request->department_id,
                'attendance' => $request->attendance,
                'halfday' => $request->halfday ?? 0, 
                'date' => $request->date,
                'in_time' => $request->in_time,
                'out_time' => $request->out_time ?? null, // If out_time is not provided, it can be null
                'status' => 'Pending', // Default status for attendance requests
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Attendance request send successfully',
                'data' => $attendance,
            ], 201); // 201 Created
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while adding attendance',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

    public function attendance_request_list(Request $request)
    {
        try {
            

            $attendanceRequests = AttendanceRequest::with(['employee:id,name,email,phone','company:id,company_name','branch:id,branch_name',  'department:id,name'])
                ->when($request->has('company_id'), function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                })
                ->when($request->has('employee_id'), function ($query) use ($request) {
                    return $query->where('employee_id', $request->employee_id);
                })
                ->when($request->has('status'), function ($query) use ($request) {
                    return $query->where('status', $request->status);
                })
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Attendance requests fetched successfully',
                'data' => $attendanceRequests,
            ], 200); // 200 OK
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching attendance requests',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

    public function attendance_request_status_update(Request $request, $id)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Approved,Rejected',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 200);
            }

            // Find the attendance request by ID
            $attendanceRequest = AttendanceRequest::findOrFail($id);
            $attendanceRequest->status = $request->status;
            $attendanceRequest->save();

            if($request->status == 'Approved'){
                $attendance = Attendance::where('employee_id', $attendanceRequest->employee_id)
                    ->where('date', $attendanceRequest->date)
                    ->first();
                if (!$attendance) {
                    $attendance = new Attendance();
                }
                $attendance->company_id = $attendanceRequest->company_id;
                $attendance->branch_id = $attendanceRequest->branch_id;
                $attendance->employee_id = $attendanceRequest->employee_id;
                $attendance->department_id = $attendanceRequest->department_id;
                $attendance->attendance = $attendanceRequest->attendance;
                $attendance->halfday = $attendanceRequest->halfday;
                $attendance->date = $attendanceRequest->date;
                $attendance->in_time = $attendanceRequest->in_time;
                $attendance->out_time = $attendanceRequest->out_time;
                $attendance->save();

            }

            return response()->json([
                'status' => true,
                'message' => 'Attendance status updated successfully',
                //'data' => $attendanceRequest,
            ], 200); // 200 OK
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating attendance request',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }
}