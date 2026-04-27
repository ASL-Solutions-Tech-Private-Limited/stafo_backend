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
    // List all API Providers
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

    // Store new API Provider
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


    // Show single API Provider
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

    // Update API Provider
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


    // Delete API Provider
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