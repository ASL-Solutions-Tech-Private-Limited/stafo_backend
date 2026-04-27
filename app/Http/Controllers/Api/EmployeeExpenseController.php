<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeExpenseController extends Controller
{
    // 1. Create Expense
    public function store(Request $request)
    {
     
        // dd($request->all());
            $validator = Validator::make($request->all(), [
            'expense_type' => 'required|string',
            'amount' => 'required|numeric',
            'note' => 'nullable|string',
            'bill_receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 200); 
        }

        $user = Auth::user();
        $userType = class_basename($user); // CompanyDetail or Employee
        $company_id = null;
        $employee_id = null;
        $fileName = null;

            if ($userType === 'CompanyDetail') {
                $company_id = $user->id;
            } elseif ($userType === 'Employee') {
                $company_id = $user->company_id;
                $employee_id = $user->id;

            }

        if ($request->hasFile('bill_receipt')) 
        {
        $file     = $request->file('bill_receipt');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $folder   = public_path('uploads/employee_expenses');
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $file->move($folder, $fileName);
       }

     
        //   dd($request->note);
        $expense = EmployeeExpense::create([
            'company_id' => $company_id,
            'employee_id' => $employee_id,
            'expense_type' => $request->expense_type,
            'amount' => $request->amount,
            'note' => $request->note,
            'bill_receipt' => $fileName,
        ]);


        return response()->json([
            'status' => true,
            'message' => 'Expense submitted successfully',
            'data' => $expense
        ]);
    }

    // 2. View Own Expenses
    public function index()
    {
        $user = Auth::user();
        $expenses = EmployeeExpense::where('employee_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(['status' => true, 'data' => $expenses]);
    }

    // 3. Update Expense
    public function update(Request $request, $id)
    {
            $validator = Validator::make($request->all(), [
                'expense_type' => 'nullable|string',
                'amount' => 'nullable|numeric',
                'note' => 'nullable|string',
                'bill_receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 200);
            }

            $user = Auth::user();
            $userType = class_basename($user);
            $company_id = $userType === 'CompanyDetail' ? $user->id : $user->company_id;
            $employee_id = $userType === 'Employee' ? $user->id : null;

            $expense = EmployeeExpense::where('id', $id)
                ->where('company_id', $company_id)
                ->when($employee_id, fn($q) => $q->where('employee_id', $employee_id))
                ->firstOrFail();

            // File upload logic
            if ($request->hasFile('bill_receipt')) {
                $file = $request->file('bill_receipt');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $folder = public_path('uploads/employee_expenses');
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                }

                if ($expense->bill_receipt) {
                    $oldFilePath = public_path('uploads/employee_expenses/' . $expense->bill_receipt);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $file->move($folder, $fileName);
                $expense->bill_receipt = $fileName;
            }

            // Optional fields update
            if ($request->filled('expense_type')) {
                $expense->expense_type = $request->expense_type;
            }
            if ($request->filled('amount')) {
                $expense->amount = $request->amount;
            }
            if ($request->filled('note')) {
                $expense->note = $request->note;
            }

            $expense->save();

            return response()->json([
                'status' => true,
                'message' => 'Expense updated successfully',
                'data' => $expense
            ]);
    }


    // 4. Delete Expense
    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|exists:employee_expenses,id']);
        $user = Auth::user();

        $expense = EmployeeExpense::where('id', $request->id)
            ->where('employee_id', $user->id)
            ->firstOrFail();

        if ($expense->status !== 'pending') {
            return response()->json(['status' => false, 'message' => 'Only pending expenses can be deleted'], 403);
        }

        $expense->delete();

        return response()->json(['status' => true, 'message' => 'Expense deleted']);
    }
}
