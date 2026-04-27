<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyType;
use Illuminate\Http\Request;

class CompanyTypeController extends Controller
{
    public function index()
    {

        $companytype = CompanyType::orderBy('created_at', 'desc')->get();
        return view('admin.companytypes.list')->with(['companytype' => $companytype]);
    }

    public function add()
    {
        return view('admin.companytypes.add');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
        ]);

        $companytype = new CompanyType;
        $companytype->company_name = $request->company_name;
        $companytype->save();

        return redirect()->route('company.list')->with('success', 'Company type added successfully!');
    }

    public function edit(Request $request, $id)
    {
        $companytype = CompanyType::find($id);
        return view('admin.companytypes.edit')->with(['companytype' => $companytype]);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $companytype = CompanyType::find($id);
        $companytype->company_name = $request->company_name;
        $companytype->save();
        return redirect()->route('company.list')->with('success', 'Company type updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $companytype = CompanyType::find($id);
        $companytype->delete();
        return redirect()->route('company.list')->with('success', 'Company type deleted successfully!');
    }
}