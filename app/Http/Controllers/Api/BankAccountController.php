<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BankAccount;
use Exception;
use Illuminate\Database\QueryException;

class BankAccountController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/bank-accounts",
     *      operationId="getBankAccountsList",
     *      tags={"Bank Accounts"},
     *      security={{"sanctum":{}}},
     *      summary="List bank details of all employees",
     *      @OA\Response(response=200, description="Bank accounts fetched successfully")
     * )
     */
    public function index()
    {
        try {
            // Get all bank account details and load the associated employee details
            $bankAccounts = BankAccount::with('employee')->get();

            // Check if there are any bank accounts
            if ($bankAccounts->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No bank account details found.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Bank account details fetched successfully',
                'data' => $bankAccounts
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 200);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/bank-accounts/employee/{employee_id}",
     *      operationId="getEmployeeBankAccount",
     *      tags={"Bank Accounts"},
     *      security={{"sanctum":{}}},
     *      summary="Fetch bank account details for a specific employee",
     *      @OA\Parameter(name="employee_id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Bank details fetched successfully"),
     *      @OA\Response(response=404, description="No bank account found")
     * )
     */
    public function showByEmployeeId($employee_id)
    {
        try {
            // Fetch the bank account details for a specific employee
            $bankAccounts = BankAccount::with('employee')->where('employee_id', $employee_id)->get();

            // Check if any bank accounts were found for the employee
            if ($bankAccounts->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No bank account details found for the specified employee.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Bank account details fetched successfully',
                'data' => $bankAccounts
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }
}