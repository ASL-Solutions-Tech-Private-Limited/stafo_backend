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





    // Create a new branch
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

    // Update an existing branch
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

    // Delete a branch
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