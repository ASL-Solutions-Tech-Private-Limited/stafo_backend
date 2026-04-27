<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DepartmentController extends Controller
{
    /**
     * Get a list of all departments.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $companyId = $request->company_id;
            $departments = Department::where('company_id', $companyId)->where('status', '1')->get(); // Get all departments

            // if ($departments->isEmpty()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'No departments found.',
            //     ], 404);
            // }

            return response()->json([
                'success' => true,
                'message' => 'Departments retrieved successfully.',
                'data' => $departments,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching departments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new department.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate input data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            //'status' => 'required|boolean', // Assuming status is a boolean (active/inactive)
        ]);

        try {
            // Create new department
            $companyId = Auth::id();
            $department = Department::create([
                'company_id' => $companyId,
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status ?? 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Department created successfully.',
                'data' => $department,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the department.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a specific department by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $department = Department::findOrFail($id); // Fetch department by ID

            return response()->json([
                'success' => true,
                'message' => 'Department retrieved successfully.',
                'data' => $department,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found.',
                'error' => $e->getMessage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the department.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a specific department.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validate input data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        try {
            $department = Department::findOrFail($id); // Find department by ID

            // Update department
            $department->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Department updated successfully.',
                'data' => $department,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found.',
                'error' => $e->getMessage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the department.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific department by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $department = Department::findOrFail($id); // Find department by ID
            $department->delete(); // Delete department

            return response()->json([
                'success' => true,
                'message' => 'Department deleted successfully.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found.',
                'error' => $e->getMessage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the department.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}