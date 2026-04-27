<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CompanyDetail;
use RealRashid\SweetAlert\Facades\Alert;


use Illuminate\Http\Request;

class BranchController extends Controller
{

    public function index(Request $request)
    {
        $query = Branch::query();
        $companies = CompanyDetail::all(); // Fetch all companies

        // Apply filtering based on the request parameters
        if ($request->has('company_id') && $request->get('company_id') != '') {
            $query->where('company_id', $request->get('company_id'));
        }

        if ($request->has('branch_id') && $request->get('branch_id') != '') {
            $query->where('id', $request->get('branch_id'));
        }

        // Paginate results, 10 branches per page
        $branches = $query->orderBy('created_at', 'desc')->paginate(10);


        return view('admin.branches.index', compact('branches', 'companies'));
    }



    public function create()
    {
        $companies = CompanyDetail::all(); // Fetch all companies
        return view('admin.branches.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:company_details,id',
            'branch_name' => 'required|string|max:255',
            'branch_address' => 'required|string',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'radar' => 'nullable|string|max:255',


        ]);

        Branch::create([
            'company_id' => $request->company_id,
            'branch_name' => $request->branch_name,
            'branch_address' => $request->branch_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radar' => $request->radar,
            'status' => 1, // Default to active
        ]);
        Alert::success('Success', 'Branch created successfully.');

        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }
    public function show($id)
    {
        $branch = Branch::with('company')->findOrFail($id); // Fetch branch with company details
        return view('admin.branches.show', compact('branch'));
    }
    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        $companies = CompanyDetail::all(); // Fetch all companies for dropdown
        return view('admin.branches.edit', compact('branch', 'companies'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'company_id' => 'required|exists:company_details,id',
            'branch_name' => 'required|string|max:255',
            'branch_address' => 'required|string',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
            'radar' => 'nullable|string|max:255',
        ]);

        $branch = Branch::findOrFail($id);
        $branch->update([
            'company_id' => $request->company_id,
            'branch_name' => $request->branch_name,
            'branch_address' => $request->branch_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radar' => $request->radar,
        ]);
        Alert::success('Success', 'Branch Details  updated successfully.');

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }
    public function destroy($id)
    {
        $branch = Branch::findOrFail($id); // Find the branch by ID
        $branch->delete(); // Delete the branch

        Alert::success('Success', 'Branch  deleted successfully.');

        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully.');
    }

    public function getBranchesByCompany($companyId)
    {
        // Fetch branches based on company ID
        $branches = Branch::where('company_id', $companyId)->get();

        return response()->json([
            'branches' => $branches
        ]);
    }
}