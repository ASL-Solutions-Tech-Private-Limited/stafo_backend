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
     * @OA\Get(
     *      path="/api/shifts",
     *      operationId="getShiftsList",
     *      tags={"Shifts"},
     *      summary="List Company Shifts",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="company_id", in="query", required=true, @OA\Schema(type="integer", example=1)),
     *      @OA\Response(response=200, description="Shifts retrieved successfully")
     * )
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
     * @OA\Post(
     *      path="/api/shifts",
     *      operationId="createShift",
     *      tags={"Shifts"},
     *      summary="Create a new Shift",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"shift_name","start_time","end_time"},
     *              @OA\Property(property="shift_name", type="string", example="Morning Shift"),
     *              @OA\Property(property="start_time", type="string", example="09:00"),
     *              @OA\Property(property="end_time", type="string", example="18:00"),
     *              @OA\Property(property="monday", type="integer", example=1),
     *              @OA\Property(property="tuesday", type="integer", example=1),
     *              @OA\Property(property="wednesday", type="integer", example=1),
     *              @OA\Property(property="thursday", type="integer", example=1),
     *              @OA\Property(property="friday", type="integer", example=1),
     *              @OA\Property(property="saturday", type="integer", example=1),
     *              @OA\Property(property="sunday", type="integer", example=0)
     *          )
     *      ),
     *      @OA\Response(response=201, description="Shift created successfully")
     * )
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
     * @OA\Put(
     *      path="/api/shifts/{id}",
     *      operationId="updateShift",
     *      tags={"Shifts"},
     *      summary="Update Shift",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"shift_name","start_time","end_time"},
     *              @OA\Property(property="shift_name", type="string", example="General Shift"),
     *              @OA\Property(property="start_time", type="string", example="10:00"),
     *              @OA\Property(property="end_time", type="string", example="19:00")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Shift updated successfully")
     * )
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



    /**
     * @OA\Post(
     *      path="/api/employees/assign-shift",
     *      operationId="assignShiftToEmployee",
     *      tags={"Shifts"},
     *      summary="Assign Shift to Employee",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"employee_id","shift_ids"},
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="shift_ids", type="array", @OA\Items(type="integer", example=1))
     *          )
     *      ),
     *      @OA\Response(response=200, description="Shift assigned successfully")
     * )
     */
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