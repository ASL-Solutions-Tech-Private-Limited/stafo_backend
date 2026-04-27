<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessType;

class BusinessTypeController extends Controller
{


    public function index(Request $request)
    {
        $businessTypes = BusinessType::query()
            ->when($request->business_name, function ($query) use ($request) {
                $query->where('business_name', 'like', '%' . $request->business_name . '%');
            })
            ->when($request->status !== null, function ($query) use ($request) {
                // Check if the status is either 0 or 1 and filter accordingly
                if ($request->status !== '') {
                    $query->where('status', $request->status);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->except('page')); // Keep filters intact during pagination

        return view('admin.businessTypes.index', compact('businessTypes'));
    }



    // Show the form for creating a new business type
    public function create()
    {
        return view('admin.businessTypes.create');
    }

    // Store a newly created business type in the database
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        BusinessType::create($request->all());

        return redirect()->route('businessTypes.index')
            ->with('success', 'Business Type created successfully.');
    }

    // Display the specified business type
    // public function show($id)
    // {
    //     $businessType = BusinessType::findOrFail($id);
    //     return view('admin.businessTypes.show', compact('businessType'));
    // }

    // Show the form for editing the specified business type
    public function edit($id)
    {
        $businessType = BusinessType::findOrFail($id);
        return view('admin.businessTypes.edit', compact('businessType'));
    }

    // Update the specified business type in the database
    public function update(Request $request, $id)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $businessType = BusinessType::findOrFail($id);
        $businessType->update([
            'business_name' => $request->business_name,
            'status' => $request->status,
        ]);

        return redirect()->route('businessTypes.index')->with('success', 'Business Type updated successfully.');
    }


    // Remove the specified business type from the database
    public function destroy($id)
    {
        $businessType = BusinessType::findOrFail($id);
        $businessType->delete();

        return redirect()->route('businessTypes.index')
            ->with('success', 'Business Type deleted successfully.');
    }
}