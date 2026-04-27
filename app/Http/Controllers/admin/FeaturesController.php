<?php

namespace App\Http\Controllers\admin;

use App\Models\Features;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeaturesController extends Controller
{
    public function index()
    {
        $features = Features::orderBy('created_at', 'desc')->paginate(10); // Paginate the features
        return view('admin.features.index', compact('features'));
    }

    public function create()
    {
        return view('admin.features.create');
    }



    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|boolean',
        ]);
        if ($request->hasFile('icon')) {
            $iconName = $request->file('icon')->getClientOriginalName();
            $request->file('icon')->storeAs('icons', $iconName, 'public');
        } else {
            $iconName = null;
        }
        // Create the feature and save it in the database, storing only the icon's name
        Features::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'icon' => $iconName, // Store only the image name
            'status' => $validated['status'],
        ]);

        // Redirect the user back to the features list with a success message
        return redirect()->route('features.index')->with('success', 'Feature created successfully!');
    }

    public function show($id)
    {
        $feature = Features::findOrFail($id);
        return view('admin.features.show', compact('feature'));
    }

    public function edit($id)
    {
        $feature = Features::findOrFail($id);
        return view('admin.features.edit', compact('feature'));
    }

    // FeaturesController.php
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|boolean',
        ]);
        $feature = Features::findOrFail($id);
        if ($request->hasFile('icon')) {
            // Delete the old icon if it exists
            if ($feature->icon) {
                $oldIconPath = public_path('storage/icons/' . $feature->icon);
                if (file_exists($oldIconPath)) {
                    unlink($oldIconPath);
                }
            }

            // Get the new icon name and store the file
            $iconName = $request->file('icon')->getClientOriginalName();
            $request->file('icon')->storeAs('icons', $iconName, 'public');
        } else {
            // Keep the old icon if no new file is uploaded
            $iconName = $feature->icon;
        }

        // Update the feature in the database
        $feature->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'icon' => $iconName,
            'status' => $validated['status'],
        ]);

        // Redirect back to the features list with a success message
        return redirect()->route('features.index')->with('success', 'Feature updated successfully!');
    }


    public function destroy($id)
    {
        // Find the feature by its ID
        $feature = Features::findOrFail($id);

        // Delete the associated icon file if it exists
        if ($feature->icon) {
            $iconPath = public_path('storage/icons/' . $feature->icon);
            if (file_exists($iconPath)) {
                unlink($iconPath); // Delete the file
            }
        }

        // Delete the feature from the database
        $feature->delete();

        // Redirect back to the features list with a success message
        return redirect()->route('features.index')->with('success', 'Feature deleted successfully!');
    }

    public function toggleStatus($id)
    {
        // Find the feature by ID
        $feature = Features::findOrFail($id);

        // Toggle the status: if it's 1 (active), set it to 0 (inactive), and vice versa
        $feature->status = !$feature->status;
        $feature->save();

        // Return a success response
        return response()->json([
            'status' => $feature->status == 1 ? 'Active' : 'Inactive',
            'success' => true,
        ]);
    }
}