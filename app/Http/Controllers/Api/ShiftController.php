<?php

namespace App\Http\Controllers\Api;

use App\Models\Shift;
use App\Models\Employee;
use App\Models\EmployeeShift;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShiftController extends Controller
{
    /**
     * Get a list of all shifts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            //$company_id = Auth::id();  // Get company_id from authenticated user
            $company_id = $request->company_id;

            $shifts = Shift::where('company_id', $company_id)->get(); // Get all shifts for this company

            return response()->json([
                'success' => true,
                'message' => 'Shifts retrieved successfully.',
                'data' => $shifts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching shifts.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new shift.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate input data with custom end_time validation
        $validator = Validator::make($request->all(), [
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 200);
        }

        $company_id = Auth::id(); // Get company_id from authenticated user

        try {
            // Create the shift
            $shift = Shift::create([
                'shift_name' => $request->shift_name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'company_id' => $company_id, // Store the company_id
                'sunday' => $request->sunday,
                'monday' => $request->monday,
                'tuesday' => $request->tuesday,
                'wednesday' => $request->wednesday,
                'thursday' => $request->thursday,
                'friday' => $request->friday,
                'saturday' => $request->saturday,               
                
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shift created successfully.',
                'data' => $shift,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the shift.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a specific shift by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $company_id = Auth::id(); // Get company_id from authenticated user

            $shift = Shift::where('company_id', $company_id)->findOrFail($id); // Find shift by ID for the specific company

            return response()->json([
                'success' => true,
                'message' => 'Shift retrieved successfully.',
                'data' => $shift,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shift not found.',
                'error' => $e->getMessage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the shift.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a specific shift.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validate input data with custom end_time validation
        $validator = Validator::make($request->all(), [
            'shift_name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 200);
        }

        $company_id = Auth::id();

        try {
            $shift = Shift::where('company_id', $company_id)->findOrFail($id); // Find shift by ID for the specific company

            // Update the shift
            $shift->update([
                'shift_name' => $request->shift_name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'sunday' => $request->sunday,
                'monday' => $request->monday,
                'tuesday' => $request->tuesday,
                'wednesday' => $request->wednesday,
                'thursday' => $request->thursday,
                'friday' => $request->friday,
                'saturday' => $request->saturday, 
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shift updated successfully.',
                'data' => $shift,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shift not found.',
                'error' => $e->getMessage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the shift.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific shift by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $company_id = Auth::id(); // Get company_id from authenticated user

            $shift = Shift::where('company_id', $company_id)->findOrFail($id); // Find shift by ID for the specific company
            $shift->delete(); // Delete shift

            return response()->json([
                'success' => true,
                'message' => 'Shift deleted successfully.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shift not found.',
                'error' => $e->getMessage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the shift.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function assignShift(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'shift_ids' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 200);
        }

        try {
            $company_id = Auth::id();
            $employee_id = $request->employee_id;
            $shift_ids = $request->shift_ids;
            
            EmployeeShift::where('employee_id', $employee_id)->delete();
            foreach($shift_ids as $shift_id){
                $employeeShift = EmployeeShift::create([
                    'employee_id' => $employee_id,
                    'shift_id' => $shift_id,
                    'company_id' => $company_id,
                ]);
            }
            $employee = Employee::with(['shifts'])
                ->where('id', $employee_id)->get();
            return response()->json([
                'success' => true,
                'message' => 'Shift assigned successfully.',
                'data' => [
                    'employee' => $employee,
                ],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Employee or shift not found.',
                'error' => $e->getMessage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while assigning the shift.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}