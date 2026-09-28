<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Expensetype;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExpensetypeController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/expensetype/list",
     *      operationId="getExpenseTypeList",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="List expense types",
     *      @OA\Parameter(name="company_id", in="query", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Expense types fetched successfully")
     * )
     */
    public function index(Request $request)
    {

        try {
            $companyId = $request->company_id;
            $expensetypees = Expensetype::where('company_id', $companyId)->get();

            return response()->json([
                'success' => true,
                'message' => 'Record retrieved successfully.',
                'data' => $expensetypees
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'message' => 'An error occurred while retrieving the record.',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * @OA\Post(
     *      path="/api/expensetype/create",
     *      operationId="createExpenseType",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Create a new expense type",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="company_id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Travel & Fuel"),
     *              @OA\Property(property="description", type="string", example="Expenses related to business travel")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Expense type created")
     * )
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'company_id' => 'required',
                'name' => 'required|string|max:255',
            ]);

            // Create the expensetype
            $expensetype = Expensetype::create($request->all());

            return response()->json([
                'message' => 'Record created successfully.',
                'success' => true,
                'data' => $expensetype
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

    /**
     * @OA\Post(
     *      path="/api/expensetype/update/{id}",
     *      operationId="updateExpenseType",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Update expense type",
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Updated Travel Type"),
     *              @OA\Property(property="description", type="string", example="Updated description")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Expense type updated")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
               
                'name' => 'required|string|max:255',
            ]);
            $expensetype = Expensetype::find($id);

            if (!$expensetype) {
                throw new ModelNotFoundException("Record not found.");
            }
            $expensetype->update($request->all());

            return response()->json([
                'message' => 'Record updated successfully.',
                'success' => true,
                'data' => $expensetype
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
                'message' => 'An error occurred while updating the record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/expensetype/delete/{id}",
     *      operationId="deleteExpenseType",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Delete expense type",
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Expense type deleted")
     * )
     */
    public function destroy($id)
    {
        try {
            $expensetype = Expensetype::find($id);

            if (!$expensetype) {
                throw new ModelNotFoundException("Record not found.");
            }
            $expensetype->delete();

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