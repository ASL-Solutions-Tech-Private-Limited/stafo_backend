<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\CompanyDetail;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;


class AdminVehicleController extends Controller
{


     /**
     * Display a listing of the vehicles.
     * Developed by: Nikhil Gupta
     */
    public function index(Request $request)
    {
        

        $query = Vehicle::query();

        if ($request->filled('vehicle_no')) {
            $query->where('vehicle_no', 'like', '%' . $request->vehicle_no . '%');
        }

        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        $vehicles = $query->paginate(10)->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }


     /**
     * Show the form for creating a new vehicle.
     * Developed by: Nikhil Gupta
     */
    public function create()
    {
        $companies = CompanyDetail::select('id','company_name')->get();
        return view('admin.vehicles.create', compact('companies'));
    }

    /**
     * Store a newly created vehicle in storage.
     * Developed by: Nikhil Gupta
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_no' => [
                'required',
                'string',
                'unique:vehicles,vehicle_no',
                'regex:/^[A-Z]{2}[0-9]{1,2}[A-Z]{1,2}[0-9]{4}$/i',
            ],
            'company_id' => 'required',
            'vehicle_type' => 'required|string',
            'fuel' => 'required|string',
            'load_capacity' => 'nullable|numeric',
            'speedometer' => 'nullable|integer',
            'rc_upload_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status' => 'required|in:active,inactive,maintenance',
            'km_travelled' => 'nullable|integer',
        ]);

        // Handle RC file upload
        $rcFilePath = null;
        if ($request->hasFile('rc_upload_path')) {
            $file = $request->file('rc_upload_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('uploads/rc_files');
            $file->move($destinationPath, $fileName);
            $rcFilePath =  $fileName; // relative path to save in DB
        }
        // Create new vehicle
        Vehicle::create([
            'vehicle_no' => $request->vehicle_no,
            'vehicle_type' => $request->vehicle_type,
            'fuel' => $request->fuel,
            'load_capacity' => $request->load_capacity,
            'speedometer' => $request->speedometer ?? 0,
            'rc_upload_path' => $rcFilePath,
            'status' => $request->status,
            'km_travelled' => $request->km_travelled ?? 0,
            'company_id' => $request->company_id,
        ]);

        Alert::success('Success', 'Vehicle added successfully!');
        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle added successfully!');
    }

     /**
     * Show the form for editing the specified vehicle.
     * Developed by: Nikhil Gupta
     */
    public function edit($id)
    {
        $vehicle = Vehicle::where('id', $id)
            ->firstOrFail();
        return view('admin.vehicles.edit', compact('vehicle'));
    }


     /**
     * Update the specified vehicle in storage.
     * Developed by: Nikhil Gupta
     */

    public function update(Request $request, $id)
    {
        // Find vehicle by ID
        $vehicle = Vehicle::findOrFail($id);
        $validated = $request->validate([
            'vehicle_no' => [
                'required',
                'string',
                Rule::unique('vehicles', 'vehicle_no')->ignore($vehicle->id),
                'regex:/^[A-Z]{2}[0-9]{1,2}[A-Z]{1,2}[0-9]{4}$/i',
            ],
            'vehicle_type' => 'required|string',
            'fuel' => 'required|string',
            'load_capacity' => 'nullable|numeric',
            'speedometer' => 'nullable|integer',
            'rc_upload_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status' => 'required|in:active,inactive,maintenance',
            'km_travelled' => 'nullable|integer',
        ]);

        if ($request->hasFile('rc_upload_path')) {
            if ($vehicle->rc_upload_path && file_exists(public_path('uploads/rc_files/' . $vehicle->rc_upload_path))) {
                unlink(public_path('uploads/rc_files/' . $vehicle->rc_upload_path));
            }
            $file = $request->file('rc_upload_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/rc_files'), $filename);

            $validated['rc_upload_path'] = $filename;
        } else {
            $validated['rc_upload_path'] = $vehicle->rc_upload_path;
        }
        $vehicle->update($validated);

        Alert::success('Success', 'Vehicle updated successfully!');
        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully!');
    }

    /**
     * Remove the specified vehicle from storage along with RC file.
     * Developed by: Nikhil Gupta
     */
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        if ($vehicle->rc_upload_path) {
            $filePath = public_path('uploads/rc_files/' . $vehicle->rc_upload_path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $vehicle->delete();

        Alert::success('Success', 'Vehicle deleted successfully.');
        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
