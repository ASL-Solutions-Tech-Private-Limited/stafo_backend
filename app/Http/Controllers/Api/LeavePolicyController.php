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

    // Edit a leavepolicy

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


    // Delete a leavepolicy
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