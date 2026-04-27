<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BbpsOperatorMaster;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\Library\CommonFunction;

class BbpsOperatorMasterController extends Controller
{
    // List all operators
    public function index(Request $request)
    {
        try {
            $category_name = $request->input('category');

            $operators = BbpsOperatorMaster::where('status', 1)
                ->where('category', $category_name)
                ->select('id', 'name', 'operator_code', 'status')
                ->get();

            // dd($operators);
            return response()->json([
                'success' => true,
                'message' => 'Operator list fetched successfully.',
                'data'    => $operators
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e,
                'data'    => []
            ], 500);
        }
    }

    // Create new operator
    public function store(Request $request)
    {
        try {
            $operator = BbpsOperatorMaster::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Operator created successfully.',
                'data'    => $operator
            ], 201); // 201 for Created

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create operator.',
                'data'    => []
            ], 500);
        }
    }

    // Show specific operator
    // public function show($id)
    // {
    //     try {
    //         $operator = BbpsOperatorMaster::findOrFail($id);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Operator details fetched successfully.',
    //             'data'    => $operator
    //         ], 200);
    //     } catch (ModelNotFoundException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Operator not found.',
    //             'data'    => []
    //         ], 404);
    //     }
    // }

    public function show($operator_code)
    {
        // dd($operator_code);
        try {
            // Fetch operator details based on operator_code
            $operator = BbpsOperatorMaster::where('operator_code', $operator_code)->first();
            // dd($operator);
            if (!$operator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Operator not found.',
                    'data'    => []
                ], 404);
            }

            $message = $operator->message;
            $refid = CommonFunction::generateRandomString();
            if (empty($message)) {
                $mdmRequestNew = CommonFunction::mdmRequestNew($operator_code, $refid);
                $resp2_arr = json_decode($mdmRequestNew, true);

                // dd($resp2_arr);
                if ($resp2_arr['responseCode'] == '000') {
                    $operator->message = $mdmRequestNew;
                    $operator->save();
                }
            } else {
                // If message is not empty, use the existing message
                $mdmRequestNew = $message;
            }

            // Decode the message response
            $resp2_arr = json_decode($mdmRequestNew, true);
            $msg = '';
            $status = 0;

            if ($resp2_arr['responseCode'] == '000') {
                $status = 1;
                $msg = "Success";
                return response()->json([
                    'status' => $status,
                    'refid'  => $refid,
                    'message' => $msg,
                    'mdmRequestNew' => $resp2_arr
                ], 200);
            } else {
                throw new Exception($resp2_arr['errorInfo']['error']['errorMessage']);
            }
        } catch (Exception $e) {
            //  dd($e);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch operator details.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    // Update operator
    public function update(Request $request, $id)
    {

        try {
            $operator = BbpsOperatorMaster::findOrFail($id);
            $operator->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Operator updated successfully.',
                'data'    => $operator
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Operator not found.',
                'data'    => []
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update operator.',
                'data'    => []
            ], 500);
        }
    }

    // Delete operator
    public function destroy($id)
    {
        try {
            $operator = BbpsOperatorMaster::findOrFail($id);
            $operator->delete();

            return response()->json([
                'success' => true,
                'message' => 'Operator deleted successfully.',
                'data'    => null
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Operator not found.',
                'data'    => []
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete operator.',
                'data'    => []
            ], 500);
        }
    }
}