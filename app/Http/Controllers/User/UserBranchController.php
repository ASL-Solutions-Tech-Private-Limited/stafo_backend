<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBranchController extends Controller
{
    public function index()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $companyId = Auth::id();
        $branches = Branch::where('company_id', $companyId)->orderBy('created_at', 'desc')->get();

        return view('user.branch.index', compact('branches'));
    }

    public function create()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        return view('user.branch.create'); // Return the view to create a new branch
    }


    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_address' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);
        $userId = Auth::id();
        $status = $request->status === 'active' ? 1 : 0;
        Branch::create([
            'company_id' => $userId,
            'branch_name' => $request->branch_name,
            'branch_address' => $request->branch_address,
            'status' =>  $status,
        ]);
        return redirect()->route('branche.index')->with('success', 'Branch created successfully.');
    }


    public function edit($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $branch = Branch::findOrFail($id); // Fetch the branch to edit
        return view('user.branch.edit', compact('branch')); // Return the edit view
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_address' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $userId = Auth::id();
        $status = $request->status === 'active' ? 1 : 0;
        $branch = Branch::findOrFail($id);
        $branch->update([
            'company_id' => $userId,
            'branch_name' => $request->branch_name,
            'branch_address' => $request->branch_address,
            'status' => $status,
        ]);

        // Redirect back with success message
        return redirect()->route('branche.index')->with('success', 'Branch updated successfully.');
    }


    public function destroy($id)
    {
        $branch = Branch::findOrFail($id); // Find the branch to delete
        $branch->delete(); // Delete the branch

        return redirect()->route('branche.index')->with('success', 'Branch deleted successfully.');
    }
}