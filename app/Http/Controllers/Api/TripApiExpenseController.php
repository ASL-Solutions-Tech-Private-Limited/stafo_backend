<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TripExpense;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File; // for file deletion



class TripApiExpenseController extends Controller
{
    /**
     * Custom validator to return 200 status code on validation errors
     */
   


    /**
     * Get list of expenses for a trip
     */
   

      

    /**
     * @OA\Post(
     *     path="/api/trips-expense/list",
     *     summary="Get list of trip expenses",
     *     tags={"Trips"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="trip_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Expenses fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expenses fetched successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
public function index(Request $request)
{
    $user = Auth::user();
    $userType = class_basename($user); // 'CompanyDetail' or 'Employee'

    $companyId = null;
    $employeeId = null;
    $trip = null;

    if ($userType === 'CompanyDetail') {
        $companyId = $user->id;
        if ($request->filled('trip_id')) {
            $request->validate([
                'trip_id' => 'required|integer|exists:trips,id',
            ]);

            $trip = Trip::where('id', $request->trip_id)
                        ->where('company_id', $companyId)
                        ->first();

            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found for your company.',
                ], 200);
            }

            $expenses = TripExpense::where('trip_id', $trip->id)
                                   ->where('company_id', $companyId)
                                   ->get();
        } else {
            $expenses = TripExpense::where('company_id', $companyId)->get();
        }

    } elseif ($userType === 'Employee') {
        $companyId = $user->company_id;
        $employeeId = $user->id;

        if ($request->filled('trip_id')) {
            $request->validate([
                'trip_id' => 'required|integer|exists:trips,id',
            ]);

            $trip = Trip::where('id', $request->trip_id)
                        ->where('company_id', $companyId)
                        ->where('driver_id', $employeeId)
                        ->first();

            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found or unauthorized access.',
                ], 200);
            }

            $expenses = TripExpense::where('trip_id', $trip->id)
                                   ->where('company_id', $companyId)
                                   ->where('driver_id', $employeeId)
                                   ->get();
        } else {
            $expenses = TripExpense::where('company_id', $companyId)
                                   ->where('driver_id', $employeeId)
                                   ->get();
        }

    } else {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized user type.',
        ], 403);
    }

    // Add full path to bill receipt
    $expenses->transform(function ($expense) {
        if ($expense->bill_receipt) {
            $expense->bill_receipt = asset('uploads/bill_receipt/' . $expense->bill_receipt);
        }
        return $expense;
    });

    $message = $userType === 'CompanyDetail'
    ? 'Company expenses fetched successfully.'
    : 'driver expenses fetched successfully.';

    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $expenses
    ]);
}


    /**
     * @OA\Post(
     *     path="/api/trips-expense/create",
     *     summary="Create a new trip expense",
     *     tags={"Trips"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"trip_id","expense_type","amount"},
     *                 @OA\Property(property="trip_id", type="integer", example=1),
     *                 @OA\Property(property="expense_type", type="string", example="Fuel"),
     *                 @OA\Property(property="amount", type="number", example=1500),
     *                 @OA\Property(property="note", type="string", example="Diesel refill"),
     *                 @OA\Property(property="bill_receipt", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Expense created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expense created successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
 public function store(Request $request)
{
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required|integer|exists:trips,id',
                'expense_type' => 'required|string',
                'amount' => 'required|numeric',
                'note' => 'nullable|string',
                'bill_receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 200);
            }

            $user = Auth::user();
            $userType = class_basename($user); // 'CompanyDetail' or 'Employee'

            $trip = null;
            $companyId = null;
            $employeeId = null;

            if ($userType === 'CompanyDetail') {
                $companyId = $user->id;
                $trip = Trip::where('id', $request->trip_id)
                            ->where('company_id', $companyId)
                            ->first();
            } elseif ($userType === 'Employee') {
                $companyId = $user->company_id;
                $employeeId = $user->id;
                $trip = Trip::where('id', $request->trip_id)
                            ->where('company_id', $companyId)
                            ->first();
            }

            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found or unauthorized access.',
                ], 404);
            }

            // Handle file upload
            $billPath = null;
            if ($request->hasFile('bill_receipt')) {
                $file = $request->file('bill_receipt');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('uploads/bill_receipt');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file->move($destinationPath, $fileName);
                $billPath = $fileName;
            }

            // Create the TripExpense
            $expense = TripExpense::create([
                'trip_id' => $request->trip_id,
                'company_id' => $companyId,
                'driver_id' => $employeeId, // Will be null if not an employee
                'expense_type' => $request->expense_type,
                'amount' => $request->amount,
                'note' => $request->note,
                'bill_receipt' => $billPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense created successfully.',
                'data' => $expense,
            ]);
 }


    /**
     * @OA\Post(
     *     path="/api/trips-expense/update",
     *     summary="Update a trip expense",
     *     tags={"Trips"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"expense_id"},
     *                 @OA\Property(property="expense_id", type="integer", example=1),
     *                 @OA\Property(property="trip_id", type="integer", example=1),
     *                 @OA\Property(property="expense_type", type="string", example="Toll"),
     *                 @OA\Property(property="amount", type="number", example=200),
     *                 @OA\Property(property="note", type="string", example="Highway toll"),
     *                 @OA\Property(property="bill_receipt", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expense updated successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
public function update(Request $request)
{
    $validator = Validator::make($request->all(), [
        'expense_id' => 'required|integer|exists:trip_expenses,id',
        'trip_id' => 'sometimes|required|integer|exists:trips,id',
        'expense_type' => 'sometimes|required|string',
        'amount' => 'sometimes|required|numeric',
        'note' => 'nullable|string',
        'bill_receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 200);
    }

    $user = Auth::user();
    $userType = class_basename($user); // 'CompanyDetail' or 'Employee'
    $expense = null;

    if ($userType === 'CompanyDetail') {
        $companyId = $user->id;

        $expense = TripExpense::where('id', $request->expense_id)
            ->where('company_id', $companyId)
            ->first();

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found for your company.',
            ], 200);
        }

        if ($request->has('trip_id')) {
            $trip = Trip::where('id', $request->trip_id)
                ->where('company_id', $companyId)
                ->first();

            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found for your company.',
                ], 200);
            }
        }

    } elseif ($userType === 'Employee') {
        $companyId = $user->company_id;
        $employeeId = $user->id;

        $expense = TripExpense::where('id', $request->expense_id)
            ->where('company_id', $companyId)
            ->where('driver_id', $employeeId)
            ->first();

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found or unauthorized access.',
                'errors' => $validator->errors(),
            ], 200);
        }

        if ($request->has('trip_id')) {
            $trip = Trip::where('id', $request->trip_id)
                ->where('company_id', $companyId)
                ->where('driver_id', $employeeId)
                ->first();

            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found or unauthorized for this employee.',
                ], 200);
            }
        }
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized user type.',
        ], 403);
    }

    // Handle bill receipt upload
    if ($request->hasFile('bill_receipt')) {
        $file = $request->file('bill_receipt');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('uploads/bill_receipt');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $fileName);
        $expense->bill_receipt = $fileName;
    }

    // Update other fields
    $expense->update($request->only(['trip_id', 'expense_type', 'amount', 'note']));
    $expense->save();

    // Add asset URL for updated response
    if ($expense->bill_receipt) {
        $expense->bill_receipt = asset('uploads/bill_receipt/' . $expense->bill_receipt);
    }

    $message = $userType === 'CompanyDetail'
        ? 'Company expense updated successfully.'
        : 'driver expense updated successfully.';

    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $expense,
    ]);
}


    /**
     * @OA\Post(
     *     path="/api/trips-expense/delete",
     *     summary="Delete a trip expense",
     *     tags={"Trips"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"expense_id"},
     *             @OA\Property(property="expense_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Expense deleted successfully.")
     *         )
     *     )
     * )
     */
public function destroy(Request $request)
{
    $validator = Validator::make($request->all(), [
        'expense_id' => 'required|integer|exists:trip_expenses,id',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 200);
    }

    $user = Auth::user();
    $userType = class_basename($user); // 'CompanyDetail' or 'Employee'

    $expense = null;

    if ($userType === 'CompanyDetail') {
        $companyId = $user->id;

        $expense = TripExpense::where('id', $request->expense_id)
            ->where('company_id', $companyId)
            ->first();
    } elseif ($userType === 'Employee') {
        $companyId = $user->company_id;
        $employeeId = $user->id;

        $expense = TripExpense::where('id', $request->expense_id)
            ->where('company_id', $companyId)
            ->where('driver', $employeeId)
            ->first();
    }

    if (!$expense) {
        return response()->json([
            'success' => false,
            'message' => 'Expense not found or unauthorized access.',
        ], 200);
    }

    // Delete file if exists
    if ($expense->bill_receipt) {
        $filePath = public_path('uploads/bill_receipt/' . $expense->bill_receipt);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }

    // Delete expense record
    $expense->delete();

    $message = $userType === 'CompanyDetail'
        ? 'Company expense deleted successfully.'
        : 'Employee expense deleted successfully.';

    return response()->json([
        'success' => true,
        'message' => $message,
    ]);
}





}
