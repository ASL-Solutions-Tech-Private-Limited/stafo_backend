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