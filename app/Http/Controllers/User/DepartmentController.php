<?php

namespace App\Http\Controllers\User;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    // Display list of departments
    public function index(Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $name = $request->input('name');
        $status = $request->input('status');

        $departments = Department::query();

        if ($name) {
            $departments->where('name', 'like', '%' . $name . '%');
        }

        if ($status !== null) {
            $departments->where('status', $status);
        }

        $companyId = Auth::id();
        $departments->where('company_id', $companyId);

        // Order by name and paginate
        $departments->where('status', 1); // This line filters only active departments.


        $departments = $departments->orderBy('created_at', 'desc')->paginate(10);

        return view('user.departments.index', compact('departments', 'name', 'status'));
    }

    // Show the create department form
    public function create()
    {
        return view('user.departments.create');
    }

    // Store a new department
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

        // Create department with the validated data and company_id
        Department::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'company_id' => $companyId,
        ]);

        // Redirect back with success message
        return redirect()->route('departments.index')->with('success', 'Department added successfully!');
    }

    // Show the details of a department
    public function show($id)
    {
        // Get company_id from authenticated user and ensure department belongs to that company
        $companyId = Auth::id();
        $department = Department::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        return view('user.departments.show', compact('department'));
    }

    // Show the edit form for a department
    public function edit($id)
    {
        // Get company_id from authenticated user and ensure department belongs to that company
        $companyId = Auth::id();
        $department = Department::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        return view('user.departments.edit', compact('department'));
    }

    // Update an existing department
    public function update(Request $request, $id)
    {
        // Get company_id from authenticated user and ensure department belongs to that company
        $companyId = Auth::id();
        $department = Department::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        // Update department with validated data
        $department->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        // Redirect back with success message
        return redirect()->route('departments.index')->with('success', 'Department updated successfully!');
    }

    // Delete a department
    public function destroy($id)
    {
        $companyId = Auth::id();
        $department = Department::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Department deleted successfully!');
    }

    // Toggle the status of a department
    public function toggleStatus($id)
    {
        $companyId = Auth::id();
        $department = Department::where('id', $id)->where('company_id', $companyId)->firstOrFail();
        $department->status = !$department->status;
        $department->save();

        return response()->json(['status' => $department->status]);
    }
}