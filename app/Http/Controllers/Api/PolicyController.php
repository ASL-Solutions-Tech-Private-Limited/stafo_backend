<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Policy;
use Illuminate\Auth\AuthenticationException;
use Exception;

class PolicyController extends Controller
{


    /**
     * @OA\Get(
     *     path="/api/policy",
     *     summary="Get company policies",
     *     tags={"Policy"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         required=true,
     *         description="Company ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Policies retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Policies retrieved successfully."),
     *             @OA\Property(property="file_path", type="string", example="https://stafo.in/uploads/policies"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $companyId = $request->company_id;
            $policies = Policy::where('company_id', $companyId)->get();
            return response()->json([
                'success' => true,
                'message' => 'Policies retrieved successfully.',
                'file_path' => asset('uploads/policies'),
                'data' => $policies
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the policies.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/policy-create",
     *     summary="Create / upload a policy",
     *     tags={"Policy"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string", example="Leave Policy 2026"),
     *                 @OA\Property(property="description", type="string", example="Guidelines for leaves"),
     *                 @OA\Property(property="file", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Policy uploaded successfully")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            //'company_id' => 'required|exists:companies,id',
            //'title' => 'required|string|max:255',
            //'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf',
        ]);
        $companyId = Auth::id();

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . 'policy.' . $file->getClientOriginalExtension();

                $file->move(public_path('uploads/policies'), $filename);

                $policy = Policy::create([
                    'company_id' => $companyId,
                    'title' => $request->title,
                    'description' => $request->description,
                    'file' => $filename,
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Policy uploaded successfully',
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'No policy document was uploaded.',
            ], 200);
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while uploading the document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}