<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Leavetype;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LeavetypeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }
    public function index(Request $request)
    {

        try {
            $companyId = $request->company_id;
            $leavetypees = Leavetype::where('company_id', $companyId)->get();

            return response()->json([
                'success' => true,
                'message' => 'Record retrieved successfully.',
                'data' => $leavetypees
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'message' => 'An error occurred while retrieving the record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    // Create a new leavetype
    public function store(Request $request)
    {
        try {

            $request->validate([
                'company_id' => 'required',
                'name' => 'required|string|max:255',
            ]);

            // Create the leavetype
            $leavetype = Leavetype::create($request->all());

            return response()->json([
                'message' => 'Record created successfully.',
                'success' => true,
                'data' => $leavetype
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'success' => false,
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the record.',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update an existing leavetype
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
               
                'name' => 'required|string|max:255',
            ]);
            $leavetype = Leavetype::find($id);

            if (!$leavetype) {
                throw new ModelNotFoundException("Record not found.");
            }
            $leavetype->update($request->all());

            return response()->json([
                'message' => 'Record updated successfully.',
                'success' => true,
                'data' => $leavetype
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'success' => false,
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a leavetype
    public function destroy($id)
    {
        try {
            $leavetype = Leavetype::find($id);

            if (!$leavetype) {
                throw new ModelNotFoundException("Record not found.");
            }
            $leavetype->delete();

            return response()->json([
                'message' => 'Record deleted successfully.',
                'status' => true // Indicating successful operation
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the record.',
                'error' => $e->getMessage(),
                'status' => false // Indicating failure due to general error
            ], 500);
        }
    }
}