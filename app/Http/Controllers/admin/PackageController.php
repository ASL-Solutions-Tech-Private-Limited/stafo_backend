<?php

namespace App\Http\Controllers\admin;

use App\Models\Package;
use App\Models\Features;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;


class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('created_at', 'desc')->paginate(10);  // Added pagination

        return view('admin.packages.index', compact('packages'));
    }

    // Show form to create a new package
    public function create()
    {
        $features = Features::all();
        return view('admin.packages.create', compact('features'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'package_name' => 'required|string|max:255',
            'description' => 'nullable|string',            
            'days' => 'nullable|numeric',
            'status' => 'required|string|in:active,inactive',
            'features' => 'nullable|array',
            'feature_value' => 'nullable|array',
        ]);

        // Create the package
        $package = Package::create([
            'package_name' => $request->package_name,
            'description' => $request->description,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'monthly_price' => $request->monthly_price,
            'quarterly_price' => $request->quarterly_price,
            'halfyearly_price' => $request->halfyearly_price,
            'yearly_price' => $request->yearly_price,
            'monthly_discount_price' => $request->monthly_discount_price,
            'quarterly_discount_price' => $request->quarterly_discount_price,
            'halfyearly_discount_price' => $request->halfyearly_discount_price,
            'yearly_discount_price' => $request->yearly_discount_price,
            'days' => $request->days,
            'status' => $request->status,
        ]);

        // Attach features to the package with feature values
        if ($request->has('features')) {
            foreach ($request->features as $featureId) {
                $featureValue = $request->feature_value[$featureId] ?? null;
                $package->features()->attach($featureId, [
                    'feature_value' => $featureValue,
                    'status' => 1,
                ]);
            }
        }

        return redirect()->route('packages.index')->with('success', 'Package created successfully!');
    }

    // Show single package details
    public function show(Package $package)
    {
        return view('admin.packages.show', compact('package'));
    }



    public function edit(Package $package)
    {
        $features = Features::all();
        return view('admin.packages.edit', compact('package', 'features'));
    }



    public function update(Request $request, Package $package)
    {
        // Validate the incoming request
        $request->validate([
            'package_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'days' => 'nullable|numeric',
            'status' => 'required|string|in:active,inactive',
            'features' => 'nullable|array',  // Validate if features is an array (optional)
            'feature_value' => 'nullable|array',  // Validate if feature_values is an array (optional)
        ]);

        // Update the package details
        $package->update([
            'package_name' => $request->package_name,
            'description' => $request->description,
            'monthly_price' => $request->monthly_price,
            'quarterly_price' => $request->quarterly_price,
            'halfyearly_price' => $request->halfyearly_price,
            'yearly_price' => $request->yearly_price,
            'monthly_discount_price' => $request->monthly_discount_price,
            'quarterly_discount_price' => $request->quarterly_discount_price,
            'halfyearly_discount_price' => $request->halfyearly_discount_price,
            'yearly_discount_price' => $request->yearly_discount_price,
            'days' => $request->days,
            'status' => $request->status,

        ]);

        // Check if features are provided in the request
        if ($request->has('features')) {
            // Loop through the selected features
            foreach ($request->features as $featureId) {
                $featureValue = $request->feature_value[$featureId] ?? null;

                // Check if the feature is already linked in the pivot table
                if ($package->features->contains($featureId)) {
                    // If the feature is already linked, update the pivot table
                    $package->features()->updateExistingPivot($featureId, [
                        'feature_value' => $featureValue,
                        'status' => 1, // Assuming 'status' means active
                    ]);
                } else {
                    // If the feature is not linked, attach it to the package
                    $package->features()->attach($featureId, [
                        'feature_value' => $featureValue,
                        'status' => 1, // Assuming 'status' means active
                    ]);
                }
            }
        }

        // Show success message using Alert
        Alert::success('Success', 'Package details updated successfully.');

        // Redirect back to the packages index page with success message
        return redirect()->route('packages.index')->with('success', 'Package updated successfully!');
    }




    // Delete package
    public function destroy(Package $package)
    {
        $package->delete();
        Alert::success('Success', 'Package  deleted successfully.');
        return redirect()->route('packages.index')->with('success', 'Package deleted successfully!');
    }
}