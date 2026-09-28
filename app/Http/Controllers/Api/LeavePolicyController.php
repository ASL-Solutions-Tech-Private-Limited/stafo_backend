<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\LeavePolicy;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class LeavePolicyController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/leave-policy",
     *      operationId="getLeavePolicies",
     *      tags={"Company Policies"},
     *      security={{"sanctum":{}}},
     *      summary="List company leave policies",
     *      @OA\Response(response=200, description="Leave policies fetched successfully")
     * )
     */
    public function index()
    {
        try {
            $company_id = Auth::id(); // Get the authenticated user's company_id
            $leavepolicies = LeavePolicy::where('company_id', $company_id)->get(); // Only fetch leavepolicies for the authenticated company

            return response()->json([
                'status' => true,
                'message' => 'Leave Policy fetched successfully',
                'data' => $leavepolicies
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



    /**
     * @OA\Post(
     *      path="/api/leavepolicy-create",
     *      operationId="createLeavePolicies",
     *      tags={"Company Policies"},
     *      security={{"sanctum":{}}},
     *      summary="Create leave policies in bulk",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="leavepolicies",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="title", type="string", example="Casual Leave Rule"),
     *                      @OA\Property(property="description", type="string", example="12 days allowed per year")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=201, description="Leave policies created successfully")
     * )
     */
    public function store(Request $request)
    {
        try {
            // Get the authenticated user's company ID
            $company_id = Auth::id();  // Get the authenticated user's ID, assuming it represents the company_id

            // Validate the incoming data. We expect an array of leavepolicies.
            $validated = $request->validate([
                'leavepolicies' => 'required|array', // Ensure leavepolicies is an array
                'leavepolicies.*.title' => 'required|string|max:255',
                'leavepolicies.*.description' => 'nullable|string',
           ]);

            // Loop through the leavepolicies and add the company_id to each leavepolicy
            $leavepolicies = [];
            foreach ($validated['leavepolicies'] as $leavepolicyData) {
                $leavepolicyData['company_id'] = $company_id;
                $leavepolicies[] = $leavepolicyData;
            }

            // Create leavepolicies in bulk (use insert and fetch their ids afterward)
            LeavePolicy::insert($leavepolicies);

            // Fetch the leavepolicies with the newly inserted data
            $createdLeavepolicies = LeavePolicy::where('company_id', $company_id)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Leave policies created successfully',
                'data' => $createdLeavepolicies
            ], 201);
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

    /**
     * @OA\Put(
     *      path="/api/leavepolicy-update/{id}",
     *      operationId="updateLeavePolicy",
     *      tags={"Company Policies"},
     *      security={{"sanctum":{}}},
     *      summary="Update a leave policy by ID",
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="title", type="string", example="Updated Title"),
     *              @OA\Property(property="description", type="string", example="Updated Description")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Leave policy updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $company_id = Auth::id();
            $leavepolicy = LeavePolicy::where('company_id', $company_id)->find($id);
            if (!$leavepolicy) {
                return response()->json([
                    'status' => false,
                    'message' => 'Leave policy not found for the authenticated company',
                    'data' => []
                ], 404);
            }

            // Validate the incoming data
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ]);


            $leavepolicy->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Leave policy updated successfully',
                'data' => $leavepolicy
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


    /**
     * @OA\Delete(
     *      path="/api/leavepolicy-delete/{id}",
     *      operationId="deleteLeavePolicy",
     *      tags={"Company Policies"},
     *      security={{"sanctum":{}}},
     *      summary="Delete a leave policy by ID",
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *      @OA\Response(response=200, description="Leave policy deleted successfully")
     * )
     */
    public function destroy($id)
    {
        try {
            $leavepolicy = LeavePolicy::findOrFail($id);

            // Delete the leavepolicy
            $leavepolicy->delete();

            return response()->json([
                'status' => true,
                'message' => 'Leave policy deleted successfully'
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