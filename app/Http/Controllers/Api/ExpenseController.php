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
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }
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





    // Create a new expense
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

    // Update an existing expense
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

    // Delete a expense
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