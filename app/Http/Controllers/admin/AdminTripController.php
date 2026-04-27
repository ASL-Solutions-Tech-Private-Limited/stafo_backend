<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\CustomerInfo;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Models\TripImage;
use App\Models\CompanyDetail;


class AdminTripController extends Controller
{


    public function index()
    {
        

        $trips = Trip::with(['customerInfo'])->paginate(10);

        //    dd($trips);

        return view('admin.trips.index', compact('trips'));
    }

    public function create()
    {
        
        $companies = CompanyDetail::select('id','company_name')->get();
        $vehicles = Vehicle::select('id', 'vehicle_type')->get();
        $drivers = Employee::select('id', 'position')->get();

        return view('admin.trips.create', compact('vehicles', 'drivers','companies'));
    }

    public function edit($id)
    {
        

        $trip = Trip::with('customerInfo')
            ->where('id', $id)
            ->firstOrFail();
        return view('admin.trips.edit', compact('trip'));
    }

    public function show($id)
    {

        $trip = Trip::with('customerInfo')
            ->where('id', $id)
            ->firstOrFail();

        $vehicles = Vehicle::get();
        // dd($vehicles);
        $employees = Employee::where(['position' => 'driver'])->get();



        return view('admin.trips.show', compact('trip', 'vehicles', 'employees'));
    }


    public function DriverAndVechicleAssign(Request $request, $id)
    {
        $trip = Trip::where('id', $id)->firstOrFail();

        // dd($trip);

        $trip->vehicle_id = $request->vehicle_id ?? Null;
        $trip->driver_id = $request->driver_id ?? Null;
        $trip->update();

        return redirect()->route('admin.trips.show', $trip->id)->with('success', 'Driver and Vehicle assigned successfully!');
    }




    public function store(Request $request)
    {
        //  dd($request->all());
        $company_id = Auth::id();

        // Validate all fields, including customer info
        $validated = $request->validate([
            // CustomerInfo fields
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'nullable|string',
            'company_id' => 'required',
            // Trip fields
            'title' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',

            //  START DESTINATION Lat/Long
            'start_latitude' => 'required|numeric|between:-90,90',
            'start_longitude' => 'required|numeric|between:-180,180',

            //  END DESTINATION Lat/Long
            'end_latitude' => 'required|numeric|between:-90,90',
            'end_longitude' => 'required|numeric|between:-180,180',

            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255'



        ]);


        // Create customer info record first
        $customer = CustomerInfo::create([
            'company_id' => $request->company_id,
            'customer_name' => $request->customer_name ?? "",
            'email' => $request->customer_email ?? "",
            'phone' => $request->customer_phone ?? "",
            'address' => $request->customer_address ?? "",
        ]);


        // Calculate distance using Haversine formula
        $distance = $this->calculateDistance(
            $request->start_latitude,
            $request->start_longitude,
            $request->end_latitude,
            $request->end_longitude
        );


        // Create trip record with customer_id and company_id
        $trip = Trip::create([
            'company_id' => $request->company_id,
            'customer_id' => $customer->id,
            'title' => $request->title ?? "",
            'start_time' => $request->start_time ?? "",
            'end_time' => $request->end_time ?? "",
            'notes' => $request->notes ?? "",
            'status' => $request->status ?? "",

            'start_latitude' => $request->start_latitude ?? "",
            'start_longitude' => $request->start_longitude ?? "",
            'from_address' => $request->from_address ?? "",

            'end_latitude' => $request->end_latitude ?? "",
            'end_longitude' => $request->end_longitude ?? "",
            'to_address' => $request->to_address ?? "",
            'distance' => $distance,

        ]);

        Alert::success('Success', 'Trip and Customer Info created successfully!');
        return redirect()->route('admin.trips.index');
    }



    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            // CustomerInfo fields
            'customer_name'   => 'required|string|max:255',
            'customer_email'  => 'nullable|email|max:255',
            'customer_phone'  => 'required|string|max:20',
            'customer_address' => 'nullable|string',

            // Trip fields
            'title'          => 'required|string|max:255',
            'start_time'     => 'required|date',
            'end_time'       => 'nullable|date|after:start_time',
            'notes'          => 'nullable|string',
            'status'         => 'required|in:pending,completed,cancelled',

            // New latitude/longitude fields
            'start_latitude'  => 'required|numeric|between:-90,90',
            'start_longitude' => 'required|numeric|between:-180,180',
            'end_latitude'    => 'required|numeric|between:-90,90',
            'end_longitude'   => 'required|numeric|between:-180,180',

            // New address fields
            'from_address'    => 'nullable|string|max:500',
            'to_address'      => 'nullable|string|max:500',
        ]);


        $trip = Trip::where('id', $id)->firstOrFail();

        // Update customer info (assuming customerInfo relationship exists)
        $trip->customerInfo->update([
            'customer_name' => $request->customer_name,
            'email'        => $request->customer_email,
            'phone'        => $request->customer_phone,
            'address'      => $request->customer_address,
        ]);

        // Calculate distance using Haversine formula
        $distance = $this->calculateDistance(
            $request->start_latitude,
            $request->start_longitude,
            $request->end_latitude,
            $request->end_longitude
        );

        // Update trip info with new fields
        $trip->update([
            'title'          => $request->title,
            'start_time'     => $request->start_time,
            'end_time'       => $request->end_time,
            'notes'          => $request->notes,
            'status'         => $request->status,

            'start_latitude'  => $request->start_latitude,
            'start_longitude' => $request->start_longitude,
            'end_latitude'    => $request->end_latitude,
            'end_longitude'   => $request->end_longitude,

            'from_address'    => $request->from_address,
            'to_address'      => $request->to_address,
            'distance' => $distance,

        ]);

        Alert::success('Success', 'Trip updated successfully!');
        return redirect()->route('admin.trips.index')->with('success', 'Trip updated successfully!');
    }




    public function destroy($id)
    {
        $trip = Trip::with('customerInfo')->findOrFail($id);
        if ($trip->customerInfo) {
            $trip->customerInfo->delete();
        }
        $trip->delete();
        Alert::success('Success', 'Trip and related customer deleted successfully!');
        return redirect()->route('admin.trips.index');
    }
    public function checkVehicleAvailability($tripId, $vehicleId)
    {
        $trip = Trip::findOrFail($tripId);

        $conflict = Trip::where('vehicle_id', $vehicleId)
            ->where('id', '!=', $tripId)
            ->where(function ($query) use ($trip) {
                $query->whereBetween('start_time', [$trip->start_time, $trip->end_time])
                    ->orWhereBetween('end_time', [$trip->start_time, $trip->end_time])
                    ->orWhere(function ($q) use ($trip) {
                        $q->where('start_time', '<', $trip->start_time)
                            ->where('end_time', '>', $trip->end_time);
                    });
            })->exists();

        return response()->json(['available' => !$conflict]);
    }

    public function checkDriverAvailability($tripId, $driverId)
    {
        $trip = Trip::findOrFail($tripId);

        $conflict = Trip::where('driver_id', $driverId)
            ->where('id', '!=', $tripId)
            ->where(function ($query) use ($trip) {
                $query->whereBetween('start_time', [$trip->start_time, $trip->end_time])
                    ->orWhereBetween('end_time', [$trip->start_time, $trip->end_time])
                    ->orWhere(function ($q) use ($trip) {
                        $q->where('start_time', '<', $trip->start_time)
                            ->where('end_time', '>', $trip->end_time);
                    });
            })->exists();

        return response()->json(['available' => !$conflict]);
    }


    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; 
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($lat1) * cos($lat2) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;
        return round($distance, 2);
    }
}
