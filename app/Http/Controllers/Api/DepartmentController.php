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
     * @OA\Get(
     *      path="/api/departments",
     *      operationId="getDepartmentsList",
     *      tags={"Departments"},
     *      summary="List Departments",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="company_id", in="query", required=true, @OA\Schema(type="integer", example=1)),
     *      @OA\Response(response=200, description="Departments retrieved successfully")
     * )
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
     * @OA\Post(
     *      path="/api/departments",
     *      operationId="createDepartment",
     *      tags={"Departments"},
     *      summary="Create Department",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Human Resources"),
     *              @OA\Property(property="description", type="string", example="HR Department"),
     *              @OA\Property(property="status", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=201, description="Department created successfully")
     * )
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
     * @OA\Get(
     *      path="/api/departments/{id}",
     *      operationId="getDepartmentDetails",
     *      tags={"Departments"},
     *      summary="Get Department by ID",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *      @OA\Response(response=200, description="Department retrieved successfully")
     * )
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
     * @OA\Put(
     *      path="/api/departments/{id}",
     *      operationId="updateDepartment",
     *      tags={"Departments"},
     *      summary="Update Department",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","status"},
     *              @OA\Property(property="name", type="string", example="HR & Admin"),
     *              @OA\Property(property="description", type="string", example="HR and Administrative team"),
     *              @OA\Property(property="status", type="boolean", example=true)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Department updated successfully")
     * )
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
     * @OA\Delete(
     *      path="/api/departments/{id}",
     *      operationId="deleteDepartment",
     *      tags={"Departments"},
     *      summary="Delete Department",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *      @OA\Response(response=200, description="Department deleted successfully")
     * )
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