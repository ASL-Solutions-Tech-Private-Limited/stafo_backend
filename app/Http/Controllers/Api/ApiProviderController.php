<?php

namespace App\Http\Controllers\Api;

use App\Library\CommonFunction;
use Exception;
use App\Models\ApiProvider;
use Illuminate\Http\Request;
use App\Models\BbpsOperatorMaster;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApiProviderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/api-providers/list",
     *     summary="List all API Providers",
     *     tags={"API Provider"},
     *     @OA\Response(
     *         response=200,
     *         description="API Provider list fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="API Provider list fetched successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $data = ApiProvider::with('operator')->get();

            return response()->json([
                'success' => true,
                'message' => 'API Provider list fetched successfully.',
                'data'    => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch API Provider list.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/api-providers/create",
     *     summary="Create new API Provider",
     *     tags={"API Provider"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"api_id","operator_id","api_code","api_provider_code"},
     *             @OA\Property(property="api_id", type="string", example="1"),
     *             @OA\Property(property="operator_id", type="integer", example=1),
     *             @OA\Property(property="api_code", type="string", example="APICODE01"),
     *             @OA\Property(property="api_provider_code", type="string", example="PROV01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="API Provider created successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            // Manually check if operator_id exists
            $operatorExists = BbpsOperatorMaster::where('id', $request->operator_id)->exists();

            if (!$operatorExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid operator_id. Operator does not exist.',
                    'data'    => []
                ], 201);
            }

            // Create API Provider
            $provider = ApiProvider::create([
                'api_id'            => $request->api_id,
                'operator_id'       => $request->operator_id,
                'api_code'          => $request->api_code,
                'api_provider_code' => $request->api_provider_code
            ]);

            return response()->json([
                'success' => true,
                'message' => 'API Provider created successfully.',
                'data'    => $provider
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create API Provider.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/api-providers/details/{id}",
     *     summary="Get single API Provider details",
     *     tags={"API Provider"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="API Provider ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="API Provider fetched successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {

            $provider = ApiProvider::with('operator')->findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'API Provider fetched successfully.',
                'data' => $provider
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'API Provider not found.',
                'data' => []
            ], 201);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/api-providers/update/{id}",
     *     summary="Update an API Provider",
     *     tags={"API Provider"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="API Provider ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="api_id", type="string", example="1"),
     *             @OA\Property(property="operator_id", type="integer", example=1),
     *             @OA\Property(property="api_code", type="string", example="APICODE02"),
     *             @OA\Property(property="api_provider_code", type="string", example="PROV02")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="API Provider updated successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $provider = ApiProvider::findOrFail($id);

            // Optional: check if operator_id exists before updating
            if ($request->has('operator_id')) {
                $operatorExists = BbpsOperatorMaster::where('id', $request->operator_id)->exists();
                if (!$operatorExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid operator_id. Operator does not exist.',
                        'data'    => []
                    ], 404);
                }
            }

            $provider->update([
                'api_id'            => $request->api_id,
                'operator_id'       => $request->operator_id,
                'api_code'          => $request->api_code,
                'api_provider_code' => $request->api_provider_code,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'API Provider updated successfully.',
                'data'    => $provider
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'API Provider not found.',
                'data'    => []
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update API Provider.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/api-providers/delete/{id}",
     *     summary="Delete an API Provider",
     *     tags={"API Provider"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="API Provider ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="API Provider deleted successfully.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $provider = ApiProvider::findOrFail($id);
            $provider->delete();

            return response()->json([
                'success' => true,
                'message' => 'API Provider deleted successfully.',
                'data' => null
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'API Provider not found.',
                'data' => []
            ], 201);
        }
    }
}