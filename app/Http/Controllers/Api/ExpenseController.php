<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Expense;
use App\Models\ExpenseDetail;
use App\Models\ExpenseAttachment;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExpenseController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/expense/list",
     *      operationId="getExpenseList",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="List expenses",
     *      @OA\Parameter(name="company_id", in="query", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Parameter(name="employee_id", in="query", required=false, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Expenses fetched successfully")
     * )
     */
    public function index(Request $request)
    {

        try {
            $companyId = $request->company_id;
            $expensees_query = Expense::with(['expense_type:id,name,description','expense_details','attachments'])->where('company_id', $companyId);
            $expensees_query->when($request->has('employee_id'), function ($query) use ($request) {
                return $query->where('employee_id', $request->employee_id);
            });
            $expensees = $expensees_query->get();

            return response()->json([
                'success' => true,
                'message' => 'Record retrieved successfully.',
                'data' => $expensees
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'message' => 'An error occurred while retrieving the record.',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * @OA\Post(
     *      path="/api/expense/create",
     *      operationId="createExpense",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Create a new expense claim",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="company_id", type="integer", example=1),
     *                  @OA\Property(property="employee_id", type="integer", example=1),
     *                  @OA\Property(property="amount", type="number", example=550.00),
     *                  @OA\Property(property="expensetype_id", type="integer", example=1),
     *                  @OA\Property(property="attachments[]", type="array", @OA\Items(type="string", format="binary"))
     *              )
     *          )
     *      ),
     *      @OA\Response(response=201, description="Expense created successfully")
     * )
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'company_id' => 'required',
                'employee_id' => 'required',
            ]);
            
            $expense = new Expense();
            $expense->company_id = $request->company_id;    
            $expense->employee_id = $request->employee_id;
            $expense->amount = $request->amount;
            $expense->expensetype_id = $request->expensetype_id;
            $expense->save();
    
            if ($request->has('expense_details')) {
                foreach ($request->expense_details as $detail) {
                    $expenseDetail = new ExpenseDetail();
                    $expenseDetail->expense_id = $expense->id;
                    $expenseDetail->expenseform_id = $detail['expenseform_id'];
                    $expenseDetail->expense_value = $detail['expense_value'];
                    $expenseDetail->save();
                }
            }

            
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $key=>$file) {
                    $fileName = 'attachment_' .$key. time() . '.' . $file->getClientOriginalExtension();
                    $folder   = public_path('uploads/expense_attachments');
                    if (!file_exists($folder)) {
                        mkdir($folder, 0777, true);
                    }
                    $file->move($folder, $fileName);
                    $expensefiles = New ExpenseAttachment;
                    $expensefiles->expense_id=$expense->id;
                    $expensefiles->filename =  $fileName;
                    $expensefiles->save();
                }
            }


            return response()->json([
                'message' => 'Record created successfully.',
                'success' => true
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
     *      path="/api/expense/update/{id}",
     *      operationId="updateExpense",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Update an existing expense",
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="company_id", type="integer", example=1),
     *                  @OA\Property(property="employee_id", type="integer", example=1),
     *                  @OA\Property(property="amount", type="number", example=600.00),
     *                  @OA\Property(property="status", type="string", example="approved")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="Expense updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            
            $expense = Expense::find($id);

            if (!$expense) {
                throw new ModelNotFoundException("Record not found.");
            }
            
            $expense->company_id = $request->company_id;    
            $expense->employee_id = $request->employee_id;
            $expense->amount = $request->amount;
            $expense->status = $request->status;
            $expense->save();
            
            if ($request->has('expense_details')) {
                ExpenseDetail::where('expense_id', $id)->delete(); // Clear existing details
                foreach ($request->expense_details as $detail) {
                    $expenseDetail = new ExpenseDetail();
                    $expenseDetail->expense_id = $expense->id;
                    $expenseDetail->expenseform_id = $detail['expenseform_id'];
                    $expenseDetail->expense_value = $detail['expense_value'];
                    $expenseDetail->save();
                }
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $key=>$file) {
                    $fileName = 'attachment_' .$key. time() . '.' . $file->getClientOriginalExtension();
                    $folder   = public_path('uploads/expense_attachments');
                    if (!file_exists($folder)) {
                        mkdir($folder, 0777, true);
                    }
                    $file->move($folder, $fileName);
                    $expensefiles = New ExpenseAttachment;
                    $expensefiles->expense_id=$expense->id;
                    $expensefiles->filename =  $fileName;
                    $expensefiles->save();
                }
            }

            return response()->json([
                'message' => 'Record updated successfully.',
                'success' => true,
                'data' => $expense
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
     * @OA\Get(
     *      path="/api/expense/details",
     *      operationId="getExpenseDetails",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Get expense details with attachments",
     *      @OA\Parameter(name="expense_id", in="query", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Parameter(name="company_id", in="query", required=false, @OA\Schema(type="integer"), example=1),
     *      @OA\Parameter(name="employee_id", in="query", required=false, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Expense details fetched successfully")
     * )
     */
    public function expense_details(Request $request)
    {

        try {
            
            $expensees_query = Expense::with(['expense_type:id,name,description','expense_details:id,expense_id,expenseform_id,expense_value','expense_details.expenseFormDetails:id,field_name,description','attachments']);
            $expensees_query->when($request->has('employee_id'), function ($query) use ($request) {
                return $query->where('employee_id', $request->employee_id);
            });
            $expensees_query->when($request->has('company_id'), function ($query) use ($request) {
                return $query->where('company_id', $request->company_id);
            });
            $expensees_query->where('id', $request->expense_id);
            $expensees = $expensees_query->first();

            return response()->json([
                'success' => true,
                'message' => 'Record retrieved successfully.',
                'data' => $expensees
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'message' => 'An error occurred while retrieving the record.',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/expense/delete/{id}",
     *      operationId="deleteExpense",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Delete an expense claim",
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Expense deleted")
     * )
     */
    public function destroy($id)
    {
        try {
            $expense = Expense::find($id);

            if (!$expense) {
                throw new ModelNotFoundException("Record not found.");
            }
            $expense->delete();
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

    /**
     * @OA\Delete(
     *      path="/api/expense/attachment-delete/{id}",
     *      operationId="deleteExpenseAttachment",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Delete an expense attachment file",
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Attachment deleted")
     * )
     */
    public function attachmentDelete($id)
    {
        try {
            $data = ExpenseAttachment::find($id);
            if(isset($data->filename)){
                $fileName = $data->filename;
                $file = public_path('uploads/expense_attachments/').$fileName;            
                @unlink($file);
            }
            if (!$data) {
                throw new ModelNotFoundException("Record not found.");
            }
            $data->delete();
            

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

    /**
     * @OA\Post(
     *      path="/api/expense/status-change",
     *      operationId="changeExpenseStatus",
     *      tags={"Expenses"},
     *      security={{"sanctum":{}}},
     *      summary="Change status of an expense (e.g. approved, rejected)",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="status", type="string", enum={"approved", "rejected", "pending"}, example="approved")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Expense status changed")
     * )
     */
    public function statusChange(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'status' => 'required'
            ]);

            $employee = Expense::find($request->id);
            $employee->status = $request->status;
            $employee->save();
            return response()->json([
                'status' => true,
                'message' => 'Status changed successfully.',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}