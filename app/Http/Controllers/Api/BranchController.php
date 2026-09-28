<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BranchController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }
    /**
     * @OA\Get(
     *      path="/api/branch/list",
     *      operationId="getBranchList",
     *      tags={"Branches"},
     *      summary="List Company Branches",
     *      description="Fetches branches for a company",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="company_id", in="query", required=true, @OA\Schema(type="integer", example=1)),
     *      @OA\Response(response=200, description="Branches retrieved successfully")
     * )
     */
    public function index(Request $request)
    {

        try {
            $companyId = $request->company_id;
            $branches = Branch::where('company_id', $companyId)->get();

            return response()->json([
                'success' => true,
                'message' => 'Branches retrieved successfully.',
                'data' => $branches
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'message' => 'An error occurred while retrieving the branches.',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * @OA\Post(
     *      path="/api/branch/create",
     *      operationId="createBranch",
     *      tags={"Branches"},
     *      summary="Create a new Branch",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"company_id","branch_name","branch_address"},
     *              @OA\Property(property="company_id", type="integer", example=1),
     *              @OA\Property(property="branch_name", type="string", example="Main Branch"),
     *              @OA\Property(property="branch_address", type="string", example="Plot 10, City Center")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Branch created successfully")
     * )
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'company_id' => 'required|exists:company_details,id',
                'branch_name' => 'required|string|max:255',
                'branch_address' => 'required|string|max:255',
                // 'status' => 'nullable|in:active,inactive',
            ]);

            // Create the branch
            $branch = Branch::create($request->all());

            return response()->json([
                'message' => 'Branch created successfully.',
                'data' => $branch
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the branch.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/branch/update/{id}",
     *      operationId="updateBranch",
     *      tags={"Branches"},
     *      summary="Update Branch Details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"company_id","branch_name","branch_address"},
     *              @OA\Property(property="company_id", type="integer", example=1),
     *              @OA\Property(property="branch_name", type="string", example="Main Branch Updated"),
     *              @OA\Property(property="branch_address", type="string", example="Plot 10, City Center")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Branch updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'company_id' => 'required|exists:company_details,id',
                'branch_name' => 'required|string|max:255',
                'branch_address' => 'required|string|max:255',
                // 'status' => 'required|in:active,inactive',
            ]);
            $branch = Branch::find($id);

            if (!$branch) {
                throw new ModelNotFoundException("Branch not found.");
            }
            $branch->update($request->all());

            return response()->json([
                'message' => 'Branch updated successfully.',
                'data' => $branch
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the branch.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/branch/delete/{id}",
     *      operationId="deleteBranch",
     *      tags={"Branches"},
     *      summary="Delete a Branch",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
     *      @OA\Response(response=200, description="Branch deleted successfully")
     * )
     */
    public function destroy($id)
    {
        try {
            $branch = Branch::find($id);

            if (!$branch) {
                throw new ModelNotFoundException("Branch not found.");
            }
            $branch->delete();

            return response()->json([
                'message' => 'Branch deleted successfully.',
                'status' => true // Indicating successful operation
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the branch.',
                'error' => $e->getMessage(),
                'status' => false // Indicating failure due to general error
            ], 500);
        }
    }
}