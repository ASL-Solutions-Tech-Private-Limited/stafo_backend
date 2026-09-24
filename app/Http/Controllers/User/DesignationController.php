<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\DesignationPermission;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class DesignationController extends Controller
{
    /**
     * Display a listing of designations for the authenticated company.
     */
    public function index(Request $request)
    {
        if (Auth::user()->is_verified == 'No') {
            return view('user.verify_check');
        }

        $companyId = Auth::id();
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Designation::withCount('employees')
            ->with('permissions')
            ->where('company_id', $companyId);

        if (!empty($search)) {
            $query->where('name', 'like', '%' . trim($search) . '%');
        }

        if ($status !== null && $status !== '') {
            $query->where('status', (int)$status);
        }

        $designations = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        $totalCount = Designation::where('company_id', $companyId)->count();
        $activeCount = Designation::where('company_id', $companyId)->where('status', 1)->count();

        return view('user.designations.index', compact('designations', 'search', 'status', 'totalCount', 'activeCount'));
    }

    /**
     * Show form for creating a new designation.
     */
    public function create()
    {
        if (Auth::user()->is_verified == 'No') {
            return view('user.verify_check');
        }

        return view('user.designations.create');
    }

    /**
     * Store a new designation for this company.
     */
    public function store(Request $request)
    {
        $companyId = Auth::id();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                function ($attribute, $value, $fail) use ($companyId) {
                    if (Designation::where('company_id', $companyId)->where('name', trim($value))->exists()) {
                        $fail('A designation with this name already exists in your company.');
                    }
                },
            ],
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:0,1',
        ]);

        Designation::create([
            'company_id' => $companyId,
            'name' => trim($request->input('name')),
            'description' => $request->input('description'),
            'status' => (int)$request->input('status'),
        ]);

        Alert::success('Success', 'Designation added successfully!');
        return redirect()->route('designations.index')->with('success', 'Designation created successfully.');
    }

    /**
     * Show form to edit an existing designation.
     */
    public function edit($id)
    {
        if (Auth::user()->is_verified == 'No') {
            return view('user.verify_check');
        }

        $companyId = Auth::id();
        $designation = Designation::where('company_id', $companyId)->findOrFail($id);

        return view('user.designations.edit', compact('designation'));
    }

    /**
     * Update the specified designation.
     */
    public function update(Request $request, $id)
    {
        $companyId = Auth::id();
        $designation = Designation::where('company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                function ($attribute, $value, $fail) use ($companyId, $id) {
                    if (Designation::where('company_id', $companyId)->where('name', trim($value))->where('id', '!=', $id)->exists()) {
                        $fail('Another designation with this name already exists in your company.');
                    }
                },
            ],
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:0,1',
        ]);

        $oldName = $designation->name;
        $newName = trim($request->input('name'));

        $designation->update([
            'name' => $newName,
            'description' => $request->input('description'),
            'status' => (int)$request->input('status'),
        ]);

        // Keep employee position text in sync if name changed
        if ($oldName !== $newName) {
            Employee::where('company_id', $companyId)
                ->where('designation_id', $designation->id)
                ->update(['position' => $newName]);
        }

        Alert::success('Updated', 'Designation updated successfully!');
        return redirect()->route('designations.index')->with('success', 'Designation updated successfully.');
    }

    /**
     * Remove the specified designation.
     */
    public function destroy($id)
    {
        $companyId = Auth::id();
        $designation = Designation::where('company_id', $companyId)->findOrFail($id);

        // Detach from employees before deleting
        Employee::where('company_id', $companyId)
            ->where('designation_id', $designation->id)
            ->update(['designation_id' => null]);

        $designation->delete();

        Alert::success('Deleted', 'Designation removed successfully.');
        return redirect()->route('designations.index')->with('success', 'Designation deleted successfully.');
    }

    /**
     * Quick toggle active/inactive status.
     */
    public function toggleStatus($id)
    {
        $companyId = Auth::id();
        $designation = Designation::where('company_id', $companyId)->findOrFail($id);

        $designation->status = $designation->status ? 0 : 1;
        $designation->save();

        return response()->json([
            'status' => true,
            'new_status' => $designation->status,
            'message' => 'Status updated to ' . ($designation->status ? 'Active' : 'Inactive') . '.'
        ]);
    }
}
