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
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }
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





    // Create a new expenseform
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

    // Update an existing expenseform
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

    // Delete a expenseform
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