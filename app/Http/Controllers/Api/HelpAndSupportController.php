<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HelpAndSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class HelpAndSupportController extends Controller
{
    // Store Help and Support request
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
        ]);

        // Get the authenticated user's company_id
        $company_id = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 200);
        }

        // Create the help and support record with the company_id
        $helpAndSupport = HelpAndSupport::create([
            'company_id' => $company_id,
            'title' => $request->title,
            'description' => $request->description,
            'contact_number' => $request->contact_number,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Help and support request created successfully',
            'data' => $helpAndSupport,
        ], 201);
    }

    // Get all Help and Support requests for the authenticated company
    public function index()
    {
        $company_id = Auth::id();

        // Fetch all help and support requests for the authenticated company
        $helpAndSupport = HelpAndSupport::where('company_id', $company_id)->get();

        return response()->json([
            'status' => true,
            'data' => $helpAndSupport,
        ], 200);
    }

    // Get a specific Help and Support request by ID
    public function show($id)
    {
        $company_id = Auth::id();

        $helpAndSupport = HelpAndSupport::where('company_id', $company_id)->find($id);

        if (!$helpAndSupport) {
            return response()->json([
                'status' => false,
                'message' => 'Help and support request not found.',
            ], 200);
        }

        return response()->json([
            'status' => true,
            'data' => $helpAndSupport,
        ], 200);
    }

    // Update Help and Support request
    public function update(Request $request, $id)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
        ]);

        $company_id = Auth::id();

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 200);
        }

        // Fetch the help and support request for the company
        $helpAndSupport = HelpAndSupport::where('company_id', $company_id)->find($id);

        if (!$helpAndSupport) {
            return response()->json([
                'status' => false,
                'message' => 'Help and support request not found.',
            ], 200);
        }

        // Update the help and support record
        $helpAndSupport->update([
            'title' => $request->title ?? $helpAndSupport->title,
            'description' => $request->description ?? $helpAndSupport->description,
            'contact_number' => $request->contact_number ?? $helpAndSupport->contact_number,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Help and support request updated successfully',
            'data' => $helpAndSupport,
        ], 200);
    }

    // Delete Help and Support request
    public function destroy($id)
    {
        $company_id = Auth::id();

        $helpAndSupport = HelpAndSupport::where('company_id', $company_id)->find($id);

        if (!$helpAndSupport) {
            return response()->json([
                'status' => false,
                'message' => 'Help and support request not found.',
            ], 200);
        }

        $helpAndSupport->delete();

        return response()->json([
            'status' => true,
            'message' => 'Help and support request deleted successfully',
        ], 200);
    }
}