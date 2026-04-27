<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalryTypePackage;
use Illuminate\Support\Facades\Auth;

class SalryTypePackageController extends Controller
{
    public function index()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        
        $companyId = Auth::id();
        $packages = SalryTypePackage::where('company_id', $companyId)
            // ->withCount('salaryTypes')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('user.salry_type_package.index', compact('packages'));
    }

    public function create()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        
        return view('user.salry_type_package.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:255|unique:salry_type_packages,package_name,NULL,id,company_id,' . Auth::id(),
            'package_description' => 'nullable|string',
            'package_type' => 'required|in:Basic,Standard,Premium,Custom',
            'status' => 'nullable|in:Active,Inactive',
        ]);

        $companyId = Auth::id();

        SalryTypePackage::create([
            'company_id' => $companyId,
            'package_name' => $request->package_name,
            'package_description' => $request->package_description,
            'package_type' => $request->package_type,
            'status' => $request->status ?? 'Active',
        ]);

       

        return redirect()->route('salary-package-type.index')->with('success', 'Package created successfully.');
    }

    public function show($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        
        $package = SalryTypePackage::where('company_id', Auth::id())
            ->with('salaryTypes')
            ->findOrFail($id);
            
        return view('user.salry_type_package.show', compact('package'));
    }

    public function edit($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        
        $package = SalryTypePackage::where('company_id', Auth::id())->findOrFail($id);
        
        return view('user.salry_type_package.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'package_name' => 'required|string|max:255|unique:salry_type_packages,package_name,' . $id . ',id,company_id,' . Auth::id(),
            'package_description' => 'nullable|string',
            'package_type' => 'required|in:Basic,Standard,Premium,Custom',
            'status' => 'required|in:Active,Inactive',
        ]);

        $package = SalryTypePackage::where('company_id', Auth::id())->findOrFail($id);
        
        $package->update([
            'package_name' => $request->package_name,
            'package_description' => $request->package_description,
            'package_type' => $request->package_type,
            'status' => $request->status,
        ]);

        return redirect()->route('salary-package-type.show', $package->id)
            ->with('success', 'Package updated successfully.');
    }

    public function destroy($id)
    {
        $package = SalryTypePackage::where('company_id', Auth::id())->findOrFail($id);
        
        if ($package->salaryTypes()->count() > 0) {
            return redirect()->route('salary-package-type.index')
                ->with('error', 'Cannot delete package. Please delete associated salary types first.');
        }
        
        $packageName = $package->package_name;
        $package->delete();
        
        return redirect()->route('salary-package-type.index')
            ->with('success', 'Package "' . $packageName . '" deleted successfully.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $package = SalryTypePackage::where('company_id', Auth::id())->findOrFail($id);
        $package->status = $package->status === 'Active' ? 'Inactive' : 'Active';
        $package->save();
        
        return response()->json([
            'success' => true,
            'status' => $package->status,
            'message' => 'Package status updated successfully.'
        ]);
    }
}