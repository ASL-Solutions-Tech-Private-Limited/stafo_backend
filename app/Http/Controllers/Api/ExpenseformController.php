<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Expenseform;
use App\Models\Expensetype;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExpenseformController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/expenseform/list",
     *      operationId="getExpenseFormList",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="List expense custom forms by company",
     *      @OA\Parameter(name="company_id", in="query", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Expense forms fetched successfully")
     * )
     */
    public function index(Request $request)
    {

        try {
            $companyId = $request->company_id;
            $expenseformes = Expensetype::with(['expenseForms'])->where('company_id', $companyId)->get();

            return response()->json([
                'status' => true,
                'message' => 'Record retrieved successfully.',
                'data' => $expenseformes
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'message' => 'An error occurred while retrieving the record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * @OA\Post(
     *      path="/api/expenseform/create",
     *      operationId="createExpenseForm",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Create a new dynamic expense type form with custom fields",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="company_id", type="integer", example=1),
     *              @OA\Property(property="type_name", type="string", example="Hotel Stay"),
     *              @OA\Property(property="description", type="string", example="Hotel accommodation expenses"),
     *              @OA\Property(property="isDocumentReq", type="integer", example=1),
     *              @OA\Property(
     *                  property="fields",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="fieldName", type="string", example="Hotel Name"),
     *                      @OA\Property(property="fieldType", type="string", example="text"),
     *                      @OA\Property(property="description", type="string", example="Name of hotel")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=201, description="Expense form created")
     * )
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'company_id' => 'required',
                'type_name' => 'required|string|max:255',
            ]);

            // Create the expenseform
            $Expensetype = new Expensetype();
            $Expensetype->company_id = $request->company_id;    
            $Expensetype->name = $request->type_name;
            $Expensetype->description = $request->description;
            $Expensetype->is_document_req = $request->isDocumentReq;
            $Expensetype->save();
    
            if ($request->has('fields')) {
                foreach ($request->fields as $field) {
                    $Expenseform = new Expenseform();
                    $Expenseform->type_id = $Expensetype->id;
                    $Expenseform->company_id = $request->company_id;
                    $Expenseform->field_name = $field['fieldName'];
                    $Expenseform->field_type = $field['fieldType'];
                    $Expenseform->description = $field['description'];
                    $Expenseform->save();
                }
            }

            return response()->json([
                'message' => 'Record created successfully.',
                'status' => true,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'status' => false,
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the record.',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/expenseform/update/{id}",
     *      operationId="updateExpenseForm",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Update dynamic expense form and its fields",
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="company_id", type="integer", example=1),
     *              @OA\Property(property="type_name", type="string", example="Hotel Stay Updated"),
     *              @OA\Property(property="description", type="string", example="Updated description"),
     *              @OA\Property(property="isDocumentReq", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Expense form updated")
     * )
     */
    public function update(Request $request, $type_id)
    {
        try {
            
            // Create the expenseform
            $Expensetype = Expensetype::find($type_id);  
            $Expensetype->name = $request->type_name;
            $Expensetype->description = $request->description;
            $Expensetype->is_document_req = $request->isDocumentReq;
            $Expensetype->save();
    
            if ($request->has('fields')) {
                Expenseform::where('type_id', $Expensetype->id)->delete(); // Clear existing fields for this type
                foreach ($request->fields as $field) {
                    $Expenseform = new Expenseform();
                    $Expenseform->type_id = $Expensetype->id;
                    $Expenseform->company_id = $request->company_id;
                    $Expenseform->field_name = $field['fieldName'];
                    $Expenseform->field_type = $field['fieldType'];
                    $Expenseform->description = $field['description'];
                    $Expenseform->save();
                }
            }

            return response()->json([
                'message' => 'Record updated successfully.',
                'status' => true,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'status' => false,
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the record.',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/expenseform/delete/{id}",
     *      operationId="deleteExpenseForm",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Delete expense custom form",
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Expense form deleted")
     * )
     */
    public function destroy($id)
    {
        try {
            Expensetype::find($id)->delete();
            Expenseform::where('type_id', $id)->delete(); 
            

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