<?php

namespace App\Http\Controllers\User;

use App\Models\Leavetype;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LeavetypeController extends Controller
{
    // Display list of leavetypes
    public function index(Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $name = $request->input('name');
        $status = $request->input('status');

        $leavetypes = Leavetype::query();

        if ($name) {
            $leavetypes->where('name', 'like', '%' . $name . '%');
        }

        if ($status !== null) {
            $leavetypes->where('status', $status);
        }

        $companyId = Auth::id();
        $leavetypes->where('company_id', $companyId);

        $leavetypes = $leavetypes->orderBy('created_at', 'desc')->paginate(10);

        return view('user.leavetypes.index', compact('leavetypes', 'name', 'status'));
    }

    // Show the create leavetype form
    public function create()
    {
        return view('user.leavetypes.create');
    }

    // Store a new leavetype
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        // Get company_id from authenticated user
        $companyId = Auth::id();

        // Create leavetype with the validated data and company_id
        Leavetype::create([
            'name' => $request->input('name'),
            'no_of_days' => $request->input('no_of_days', 0), // Default to 0 if not provided
            'description' => $request->input('description'),
            'is_paid' => $request->input('is_paid'),
            'status' => $request->input('status'),
            'company_id' => $companyId,
        ]);

        // Redirect back with success message
        return redirect()->route('leavetypes.index')->with('success', 'Leavetype added successfully!');
    }

    // Show the details of a leavetype
    public function show($id)
    {
        // Get company_id from authenticated user and ensure leavetype belongs to that company
        $companyId = Auth::id();
        $leavetype = Leavetype::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        return view('user.leavetypes.show', compact('leavetype'));
    }

    // Show the edit form for a leavetype
    public function edit($id)
    {
        // Get company_id from authenticated user and ensure leavetype belongs to that company
        $companyId = Auth::id();
        $leavetype = Leavetype::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        return view('user.leavetypes.edit', compact('leavetype'));
    }

    // Update an existing leavetype
    public function update(Request $request, $id)
    {
        // Get company_id from authenticated user and ensure leavetype belongs to that company
        $companyId = Auth::id();
        $leavetype = Leavetype::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        // Update leavetype with validated data
        $leavetype->update([
            'name' => $request->input('name'),
            'no_of_days' => $request->input('no_of_days', 0), // Default to 0 if not provided
            'description' => $request->input('description'),
            'is_paid' => $request->input('is_paid'),
            'status' => $request->input('status'),
        ]);

        // Redirect back with success message
        return redirect()->route('leavetypes.index')->with('success', 'Leavetype updated successfully!');
    }

    // Delete a leavetype
    public function destroy($id)
    {
        $companyId = Auth::id();
        $leavetype = Leavetype::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        $leavetype->delete();

        return redirect()->route('leavetypes.index')->with('success', 'Leavetype deleted successfully!');
    }

    // Toggle the status of a leavetype
    public function toggleStatus($id)
    {
        $companyId = Auth::id();
        $leavetype = Leavetype::where('id', $id)->where('company_id', $companyId)->firstOrFail();
        $leavetype->status = !$leavetype->status;
        $leavetype->save();

        return response()->json(['status' => $leavetype->status]);
    }
}