<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class BusinessTypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/business-types",
     *     summary="Get all business types",
     *     tags={"Company"},
     *     @OA\Response(
     *         response=200,
     *         description="Business types retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            // Fetch all business types from the database
            $businessTypes = BusinessType::all();

            // Return a success response with the business types
            return response()->json([
                'success' => true,
                'data' => $businessTypes
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Return a failure response if an error occurs
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve business types.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/business-types",
     *     summary="Create a new business type",
     *     tags={"Company"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"business_name","status"},
     *             @OA\Property(property="business_name", type="string", example="IT & Software"),
     *             @OA\Property(property="status", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Business type created successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'business_name' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Create the business type record
            $businessType = BusinessType::create([
                'business_name' => $request->business_name,
                'status' => $request->status,
            ]);

            // Return success response with created business type
            return response()->json([
                'success' => true,
                'message' => 'Business type created successfully.',
                'data' => $businessType
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Return a failure response if an error occurs
            return response()->json([
                'success' => false,
                'message' => 'Failed to create business type.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}