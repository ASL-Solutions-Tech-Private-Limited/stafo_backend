<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Vehicle;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Validation\Rule;

class VehicleApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   
//      public function index()
//     {
//     try {
//         $companyId = Auth::id();

//         $vehicles = Vehicle::where('company_id', $companyId)->get()->map(function ($vehicle) {
//             return [
//                 'id' => $vehicle->id,
//                 'vehicle_no' => $vehicle->vehicle_no,
//                 'vehicle_type' => $vehicle->vehicle_type,
//                 'fuel' => $vehicle->fuel,
//                 'load_capacity' => $vehicle->load_capacity,
//                 'speedometer' => $vehicle->speedometer,
//                 'rc_upload_path' => $vehicle->rc_upload_path 
//                     ? asset('uploads/rc_files/' . $vehicle->rc_upload_path) 
//                     : null,
//                 'rc_number' => $vehicle->rc_number,
//                 'status' => $vehicle->status,
//                 'km_travelled' => $vehicle->km_travelled,
//                 'company_id' => $vehicle->company_id,
//             ];
//         });

//         return response()->json([
//             'status' => true,
//             'message' => 'Vehicle list fetched successfully',
//             'vehicles' => $vehicles
//         ], 200);

//     } catch (Exception $e) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Something went wrong',
//             'error' => $e->getMessage()
//         ], 500);
//     }
//  }


public function index()
{
    try {
        $user = Auth::user();
        $userId = $user->id;
        $userType = class_basename($user); // 'CompanyDetail' or 'Employee'

        if ($userType === 'CompanyDetail') {
            // Company: fetch all vehicles belonging to the company
            $vehicles = Vehicle::where('company_id', $userId)->get();
        } elseif ($userType === 'Employee') {
            // Driver: fetch vehicles assigned via trips
            // $vehicleIds = Trip::where('driver_id', $userId)
            //     ->pluck('vehicle_id')
            //     ->unique()
            //     ->filter()
            //     ->toArray();

            // $vehicles = Vehicle::whereIn('id', $vehicleIds)->get();
            $company_id = $user->company_id; // Assuming Employee has a company_id field
            $vehicles = Vehicle::where('company_id', $company_id)->get();
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user type',
            ], 403);
        }

        // Map & format the vehicle data
        $formatted = $vehicles->map(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'vehicle_no' => $vehicle->vehicle_no,
                'vehicle_type' => $vehicle->vehicle_type,
                'fuel' => $vehicle->fuel,
                'load_capacity' => $vehicle->load_capacity,
                'speedometer' => $vehicle->speedometer,
                'rc_upload_path' => $vehicle->rc_upload_path 
                    ? asset('uploads/rc_files/' . $vehicle->rc_upload_path) 
                    : null,
                'rc_number' => $vehicle->rc_number,
                'status' => $vehicle->status,
                'km_travelled' => $vehicle->km_travelled,
                'company_id' => $vehicle->company_id,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Vehicle list fetched successfully',
            'vehicles' => $formatted,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Store a newly created resource in storage.
     */
      
     public function store(Request $request)
     { 
    try {
        // Validate input
        $validator = Validator::make($request->all(), [
            'vehicle_no' => [
                'required',
                'string',
                'unique:vehicles,vehicle_no',
                'regex:/^[A-Z]{2}[0-9]{1,2}[A-Z]{1,2}[0-9]{4}$/i',
            ],
            'vehicle_type' => 'required|string',
            'fuel' => 'required|string',
            'load_capacity' => 'nullable|numeric',
            'speedometer' => 'nullable|integer',
            'rc_upload_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'rc_number'=>'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
            'km_travelled' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 200); 
        }

        $company_id = Auth::id();

        // Handle RC file upload
        $rcFilePath = null;
        if ($request->hasFile('rc_upload_path')) {
            $file = $request->file('rc_upload_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('uploads/rc_files');
            $file->move($destinationPath, $fileName);
            $rcFilePath = $fileName;
        }

        // Create the vehicle
        $vehicle = Vehicle::create([
            'vehicle_no' => $request->vehicle_no,
            'vehicle_type' => $request->vehicle_type,
            'fuel' => $request->fuel,
            'load_capacity' => $request->load_capacity,
            'speedometer' => $request->speedometer ?? 0,
            'rc_upload_path' => $rcFilePath,
            'rc_number'=>$request->rc_number,
            'status' => $request->status,
            'km_travelled' => $request->km_travelled ?? 0,
            'company_id' => $company_id,
        ]);

         // Remove created_at and updated_at
        $filteredVehicle = collect($vehicle)->except(['created_at', 'updated_at','employee_id']);
        return response()->json([
            'status' => true,
            'message' => 'Vehicle added successfully',
            'vehicle' => $filteredVehicle
        ], 201);

    } catch (Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
   
        public function show(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'id' => 'required|exists:vehicles,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation error',
                        'errors' => $validator->errors()
                    ], 200); // Force 200 status
                }

                $vehicle = Vehicle::find($request->id);

                if (!$vehicle) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Vehicle not found'
                    ], 200); // Even here, return 200 to match your design
                }

                $data = [
                    'id' => $vehicle->id,
                    'vehicle_no' => $vehicle->vehicle_no,
                    'vehicle_type' => $vehicle->vehicle_type,
                    'fuel' => $vehicle->fuel,
                    'load_capacity' => $vehicle->load_capacity,
                    'speedometer' => $vehicle->speedometer,
                    'rc_upload_path' => $vehicle->rc_upload_path
                        ? asset('uploads/rc_files/' . $vehicle->rc_upload_path)
                        : null,
                        'rc_number'=>$vehicle->rc_number,
                    'status' => $vehicle->status,
                    'km_travelled' => $vehicle->km_travelled,
                    'company_id' => $vehicle->company_id,
                ];

                return response()->json([
                    'status' => true,
                    'message' => 'Vehicle details fetched successfully',
                    'vehicle' => $data
                ], 200);

            } catch (Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong',
                    'error' => $e->getMessage()
                ], 500);
            }
        }


    /**
     * Update the specified resource in storage.
     */
  
        public function update(Request $request, $id)
        {
            try {
                // Validation
                $validator = Validator::make($request->all(), [
                    'vehicle_no' => [
                        'nullable',
                        'string',
                        Rule::unique('vehicles', 'vehicle_no')->ignore($id),
                        'regex:/^[A-Z]{2}[0-9]{1,2}[A-Z]{1,2}[0-9]{4}$/i',
                    ],
                    'vehicle_type' => 'nullable|string',
                    'fuel' => 'nullable|string',
                    'load_capacity' => 'nullable|numeric',
                    'speedometer' => 'nullable|integer',
                    'rc_upload_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                    'rc_number'=>'nullable|string',
                    'status' => 'nullable|in:active,inactive,maintenance',
                    'km_travelled' => 'nullable|integer',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation error',
                        'errors' => $validator->errors(),
                    ], 200);
                }

                $companyId = Auth::id(); 

                $vehicle = Vehicle::where('id', $id)
                                ->where('company_id', $companyId)
                                ->first();

                if (!$vehicle) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Vehicle not found'
                    ], 201);
                }

                // Handle RC upload
                if ($request->hasFile('rc_upload_path')) {
                    $file = $request->file('rc_upload_path');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $destinationPath = public_path('uploads/rc_files');
                    $file->move($destinationPath, $fileName);
                    $vehicle->rc_upload_path = $fileName;
                }

                 if ($request->has('rc_number')) {
                    $vehicle->rc_number = $request->rc_number;
                }
                // Only update if present in request
                if ($request->has('vehicle_no')) {
                    $vehicle->vehicle_no = $request->vehicle_no;
                }
                if ($request->has('vehicle_type')) {
                    $vehicle->vehicle_type = $request->vehicle_type;
                }
                if ($request->has('fuel')) {
                    $vehicle->fuel = $request->fuel;
                }
                if ($request->has('load_capacity')) {
                    $vehicle->load_capacity = $request->load_capacity;
                }
                if ($request->has('speedometer')) {
                    $vehicle->speedometer = $request->speedometer;
                }
                if ($request->has('status')) {
                    $vehicle->status = $request->status;
                }
                if ($request->has('km_travelled')) {
                    $vehicle->km_travelled = $request->km_travelled;
                }

                // Save only if something changed
                if ($vehicle->isDirty()) {
                    $vehicle->save();
                }

                $filteredVehicle = collect($vehicle)->except(['created_at', 'updated_at','employee_id']);

                return response()->json([
                    'status' => true,
                    'message' => 'Vehicle updated successfully',
                    'vehicle' => $filteredVehicle
                ], 200);

            } catch (Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong',
                    'error' => $e->getMessage()
                ], 500);
            }
        }


    /**
     * Remove the specified resource from storage.
     */
  
     public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'vehicle_id' => 'required|exists:vehicles,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 200);
            }

            $companyId = Auth::id(); // Or however you're identifying the company

            // Find vehicle by ID and company_id
            $vehicle = Vehicle::where('id', $request->vehicle_id)
                            ->where('company_id', $companyId)
                            ->first();

            if (!$vehicle) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vehicle not found or does not belong to your company'
                ], 200);
            }

            // Delete RC file if exists
            if ($vehicle->rc_upload_path) {
                $filePath = public_path('uploads/rc_files/' . $vehicle->rc_upload_path);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // Delete vehicle record
            $vehicle->delete();

            return response()->json([
                'status' => true,
                'message' => 'Vehicle deleted successfully'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function statusChange(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'status' => 'required'
            ]);

            $employee = Vehicle::find($request->id);
            $employee->status = $request->status;
            $employee->save();
            return response()->json([
                'status' => true,
                'message' => 'Status changed successfully.',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
