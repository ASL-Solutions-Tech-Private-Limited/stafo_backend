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
    /**
     * @OA\Post(
     *     path="/api/bbps-operators/list",
     *     summary="List BBPS operators by category",
     *     tags={"BBPS"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"category"},
     *             @OA\Property(property="category", type="string", example="Electricity")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operator list fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Operator list fetched successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/bbps-operators/create",
     *     summary="Create a new BBPS operator",
     *     tags={"BBPS"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","operator_code","category"},
     *             @OA\Property(property="name", type="string", example="BSPHCL"),
     *             @OA\Property(property="operator_code", type="string", example="BSPH001"),
     *             @OA\Property(property="category", type="string", example="Electricity"),
     *             @OA\Property(property="status", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Operator created successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/bbps-operators/details/{operator_code}",
     *     summary="Get BBPS operator details and parameters",
     *     tags={"BBPS"},
     *     @OA\Parameter(
     *         name="operator_code",
     *         in="path",
     *         required=true,
     *         description="BBPS Operator Code",
     *         @OA\Schema(type="string", example="BSPH001")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operator details fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="refid", type="string", example="ABC12345"),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="mdmRequestNew", type="object")
     *         )
     *     )
     * )
     */
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



    /**
     * @OA\Post(
     *     path="/api/bbps-operators/update/{id}",
     *     summary="Update a BBPS operator",
     *     tags={"BBPS"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Operator ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="BSPHCL Updated"),
     *             @OA\Property(property="status", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Operator updated successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/bbps-operators/delete/{id}",
     *     summary="Delete a BBPS operator",
     *     tags={"BBPS"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Operator ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Operator deleted successfully.")
     *         )
     *     )
     * )
     */
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