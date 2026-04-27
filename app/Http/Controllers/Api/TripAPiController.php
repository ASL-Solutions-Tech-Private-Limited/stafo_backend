<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\CustomerInfo;
use Illuminate\Validation\Rule;
use App\Models\Employee;
use App\Models\TripLog;
use App\Models\TripExpense;
use Illuminate\Support\Facades\File;



class TripAPiController extends Controller
{



    public function index()
    {
        try {
            $user = Auth::user();
            $userType = class_basename($user);

            if ($userType === 'CompanyDetail') {
                $trips = Trip::with(['customerInfo:id,customer_name,phone'])
                    ->where('company_id', $user->id)
                    ->latest()
                    ->get()
                    ->makeHidden(['created_at', 'updated_at']);
            } elseif ($userType === 'Employee') {
                $trips = Trip::with(['customerInfo:id,customer_name,email,phone,address'])
                    ->where('employee_id', $user->id)
                    ->latest()
                    ->get()
                    ->makeHidden(['created_at', 'updated_at']);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized user type',
                ], 200);
            }

            // Add additional information to each trip
            foreach ($trips as $trip) {
                $trip->duration = $this->calculateTripDuration($trip);

                // Set odometer_start
                $trip->odometer_start = $trip->status === 'pending'
                    ? 0
                    : $this->getOdometerReading($trip, 'start');

                // Set odometer_latest
                if ($trip->status === 'pending') {
                    $trip->odometer_latest = 0;
                } elseif ($trip->status === 'ongoing') {
                    $trip->odometer_latest = $this->getOdometerReading($trip, 'latest');
                }

                $trip->total_expenses = $this->getTotalExpenses($trip, $trip->driver_id);
            }


            return response()->json([
                'status' => true,
                'message' => 'Trip list fetched successfully',
                'trips' => $trips
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }







    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                // Customer Info
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'customer_address' => 'nullable|string',

                // Trip Info
                'title' => 'required|string|max:255',
                'start_time' => 'required|date',
                'end_time' => 'nullable|date|after:start_time',
                'notes' => 'nullable|string',
                'status' => 'required|in:pending,completed,cancelled',

                // Locations
                'start_latitude' => 'required|numeric|between:-90,90',
                'start_longitude' => 'required|numeric|between:-180,180',
                'end_latitude' => 'required|numeric|between:-90,90',
                'end_longitude' => 'required|numeric|between:-180,180',
                'from_address' => 'required|string|max:255',
                'to_address' => 'required|string|max:255',

                // Driver & Vehicle
                'driver_id' => 'nullable|exists:employees,id',
                'vehicle_id' => 'nullable|exists:vehicles,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 200);
            }

            // Auth
            $user = Auth::user();
            $userType = class_basename($user); // CompanyDetail or Employee

            $company_id = null;
            $employee_id = null;

            if ($userType === 'CompanyDetail') {
                $company_id = $user->id;
            } elseif ($userType === 'Employee') {
                $company_id = $user->company_id;
                $employee_id = $user->id;

                // Force assign employee as driver
                // $request->merge(['driver_id' => $employee_id]);
            }

            // Check driver validity
            if ($request->driver_id) {
                $driver = Employee::where('id', $request->driver_id)
                    ->where('company_id', $company_id)
                    ->where('position', 'driver')
                    ->first();

                if (!$driver) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Selected employee is not a valid driver for this company',
                    ], 200);
                }

                if (!$this->isDriverAvailable($request->driver_id, $request->start_time, $request->end_time)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Driver is already assigned to another trip in the given time frame',
                    ], 200);
                }
            }

            // Vehicle availability check
            if ($request->vehicle_id && !$this->isVehicleAvailable($request->vehicle_id, $request->start_time, $request->end_time)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vehicle is already assigned to another trip in the given time frame',
                ], 200);
            }

            // Create or fetch customer
            if (!empty($request->customer_email)) {
                $customer = CustomerInfo::firstOrCreate([
                    'company_id' => $company_id,
                    'customer_name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'address' => $request->customer_address ?? '',
                ]);
            } else {
                $customer = CustomerInfo::create([
                    'company_id' => $company_id,
                    'employee_id' => $employee_id,
                    'customer_name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'address' => $request->customer_address ?? '',
                ]);
            }

            // Calculate distance
            $distance = $this->calculateDistance(
                $request->start_latitude,
                $request->start_longitude,
                $request->end_latitude,
                $request->end_longitude
            );

            // Create Trip
            $trip = Trip::create([
                'company_id' => $company_id,
                'employee_id' => $employee_id,
                'customer_id' => $customer->id,
                'title' => $request->title,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes ?? '',
                'status' => $request->status,
                'start_latitude' => $request->start_latitude,
                'start_longitude' => $request->start_longitude,
                'from_address' => $request->from_address,
                'end_latitude' => $request->end_latitude,
                'end_longitude' => $request->end_longitude,
                'to_address' => $request->to_address,
                'distance' => $distance,
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Trip created successfully',
                'trip' => $trip,
                'customer' => $customer,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                // CustomerInfo fields
                'customer_name' => 'required|string|max:255',
                //'customer_email' => 'nullable|email|max:255',
                'customer_phone' => 'required|string|max:20',
                //   'customer_address' => 'nullable|string',

                // Trip fields
                'title' => 'required|string|max:255',
                'start_time' => 'required|date',
                'end_time' => 'nullable|date|after:start_time',
                'notes' => 'nullable|string',
                'status' => 'required|in:pending,completed,cancelled',

                // Locations
                'start_latitude' => 'required|numeric|between:-90,90',
                'start_longitude' => 'required|numeric|between:-180,180',
                'end_latitude' => 'required|numeric|between:-90,90',
                'end_longitude' => 'required|numeric|between:-180,180',
                'from_address' => 'required|string|max:255',
                'to_address' => 'required|string|max:255',

                // Driver & Vehicle
                'driver_id' => 'nullable|exists:employees,id',
                'vehicle_id' => 'nullable|exists:vehicles,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 200);
            }

            $company_id = Auth::id();

            // Find the trip
            $trip = Trip::where('company_id', $company_id)->findOrFail($id);

            // Check driver
            if ($request->driver_id) {
                $driver = Employee::where('id', $request->driver_id)
                    ->where('company_id', $company_id)
                    ->where('position', 'driver')
                    ->first();

                if (!$driver) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Selected employee is not a driver or does not exist',
                    ], 200);
                }

                // Check driver availability excluding current trip
                $isDriverAvailable = !Trip::where('driver_id', $request->driver_id)
                    ->where('id', '!=', $trip->id)
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                            ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                            ->orWhere(function ($q) use ($request) {
                                $q->where('start_time', '<', $request->start_time)
                                    ->where('end_time', '>', $request->end_time);
                            });
                    })->exists();

                if (!$isDriverAvailable) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Driver is already assigned to another trip in the given time frame',
                    ], 200);
                }
            }

            // Check vehicle availability excluding current trip
            if ($request->vehicle_id) {
                $isVehicleAvailable = !Trip::where('vehicle_id', $request->vehicle_id)
                    ->where('id', '!=', $trip->id)
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                            ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                            ->orWhere(function ($q) use ($request) {
                                $q->where('start_time', '<', $request->start_time)
                                    ->where('end_time', '>', $request->end_time);
                            });
                    })->exists();

                if (!$isVehicleAvailable) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Vehicle is already assigned to another trip in the given time frame',
                    ], 409);
                }
            }

            // Update customer info
            $customer = CustomerInfo::find($trip->customer_id);

            if ($customer) {
                $customer->update([
                    'customer_name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                ]);
            }

            // Recalculate distance
            $distance = $this->calculateDistance(
                $request->start_latitude,
                $request->start_longitude,
                $request->end_latitude,
                $request->end_longitude
            );

            // Update trip
            $trip->update([
                'title' => $request->title,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes ?? '',
                'status' => $request->status,
                'start_latitude' => $request->start_latitude,
                'start_longitude' => $request->start_longitude,
                'from_address' => $request->from_address,
                'end_latitude' => $request->end_latitude,
                'end_longitude' => $request->end_longitude,
                'to_address' => $request->to_address,
                'distance' => $distance,
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Trip updated successfully',
                'trip' => $trip->fresh(),
                'customer' => $customer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // public function show(Request $request)
    // {
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'id' => 'required|integer|exists:trips,id',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Validation error',
    //                 'errors' => $validator->errors()
    //             ], 200);
    //         }

    //         $user = Auth::user();
    //         $userId = $user->id;
    //         //  dd($userId);
    //         $userType = class_basename($user); // 'Company' or 'Driver'

    //         // Fetch trip based on user type
    //         $tripQuery = Trip::with([
    //             'customerInfo:id,customer_name,phone',
    //             'driver:id,name,emp_id,position',
    //             'vehicle:id,vehicle_no,vehicle_type,fuel,load_capacity,speedometer,status,km_travelled'
    //         ])->where('id', $request->id);

    //         if ($userType === 'CompanyDetail') {
    //             $tripQuery->where('company_id', $userId);
    //         } elseif ($userType === 'Employee') {
    //             $tripQuery->where('driver_id', $userId);
    //         } else {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Unauthorized user type',
    //             ], 200);
    //         }

    //         $trip = $tripQuery->first();

    //         if (!$trip) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Trip not found..',
    //             ], 201);
    //         }

    //         // Fetch Trip Logs
    //         $tripLogs = TripLog::where('trip_id', $trip->id)
    //             ->when($userType === 'Company', fn($q) => $q->where('company_id', $userId))
    //             ->select('id', 'action_type', 'latitude', 'longitude', 'user_type', 'odometer', 'image_path', 'timestamp')
    //             ->get();

    //         // Fetch Trip Expenses

    //         $tripExpenses = TripExpense::where('trip_id', $trip->id)
    //             ->when($userType === 'Company', fn($q) => $q->where('company_id', $userId))
    //             ->select('id', 'trip_id', 'company_id', 'expense_type', 'amount', 'note', 'bill_receipt')
    //             ->get();

    //         // Total Expenses
    //         $totalExpenses = $tripExpenses->sum('amount');

    //         // Trip Duration
    //         $duration = null;
    //         if ($trip->start_time && $trip->end_time) {
    //             $start = strtotime($trip->start_time);
    //             $end = strtotime($trip->end_time);
    //             $duration = gmdate('H:i:s', $end - $start);
    //         }

    //         // Distance Calculation
    //         $totalKM = $trip->distance;
    //         if (!$totalKM && $tripLogs->count() >= 2) {
    //             $startOdometer = $tripLogs->first()->odometer;
    //             $endOdometer = $tripLogs->last()->odometer;
    //             if (is_numeric($startOdometer) && is_numeric($endOdometer)) {
    //                 $totalKM = $endOdometer - $startOdometer;
    //             }
    //         }

    //         // Attach additional data
    //         $trip->trip_logs = $tripLogs;
    //         $trip->trip_expenses = $tripExpenses;
    //         $trip->total_expenses = $totalExpenses;
    //         $trip->total_km = $totalKM;
    //         $trip->duration = $duration;

    //         // Hide timestamps
    //         $trip->makeHidden(['created_at', 'updated_at']);
    //         $trip->customerInfo?->makeHidden(['created_at', 'updated_at']);
    //         $trip->driver?->makeHidden(['created_at', 'updated_at']);
    //         $trip->vehicle?->makeHidden(['created_at', 'updated_at']);

    //          $message = $userType === 'CompanyDetail'
    //             ? 'Trip details fetched successfully for Company'
    //             : 'Trip details fetched successfully for Driver';

    //         return response()->json([
    //             'status' => true,
    //             'message' => $message,
    //             'trip' => $trip,
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:trips,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 200);
            }

            $user = Auth::user();
            $userId = $user->id;
            $userType = class_basename($user); // 'CompanyDetail' or 'Employee'

            // Fetch trip
            $tripQuery = Trip::with([
                'customerInfo:id,customer_name,phone',
                'driver:id,name,emp_id,position',
                'vehicle:id,vehicle_no,vehicle_type,fuel,load_capacity,speedometer,status,km_travelled'
            ])->where('id', $request->id);

            if ($userType === 'CompanyDetail') {
                $tripQuery->where('company_id', $userId);
            } elseif ($userType === 'Employee') {
                $tripQuery->where('driver_id', $userId);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized user type',
                ], 200);
            }

            $trip = $tripQuery->first();

            if (!$trip) {
                return response()->json([
                    'status' => false,
                    'message' => 'Trip not found..',
                ], 201);
            }

            // Fetch Trip Logs
            $tripLogs = TripLog::where('trip_id', $trip->id)
                ->when($userType === 'CompanyDetail', fn($q) => $q->where('company_id', $userId))
                ->select('id', 'action_type', 'latitude', 'longitude', 'user_type', 'odometer', 'image_path', 'timestamp')
                ->orderBy('timestamp')
                ->get();

            // dd($tripLogs);


            // Fetch Trip Expenses
            $tripExpenses = TripExpense::where('trip_id', $trip->id)
                ->when($userType === 'CompanyDetail', fn($q) => $q->where('company_id', $userId))
                ->select('id', 'trip_id', 'company_id', 'expense_type', 'amount', 'note', 'bill_receipt')
                ->get();

            $totalExpenses = $tripExpenses->sum('amount');

            // Trip Duration


            // Distance Calculation
            $totalKM = $trip->distance;
            if (!$totalKM && $tripLogs->count() >= 2) {
                $startOdometer = $tripLogs->first()->odometer;
                $endOdometer = $tripLogs->last()->odometer;
                if (is_numeric($startOdometer) && is_numeric($endOdometer)) {
                    $totalKM = $endOdometer - $startOdometer;
                }
            }

            // Halt Calculation
            $halts = [];
            $pauseStart = null;
            $totalHaltDuration = 0;

            foreach ($tripLogs as $log) {
                if ($log->action_type === 'pause') {
                    $pauseStart = $log;
                    // dd($pauseStart);
                }

                if (($log->action_type === 'resume' || $log->action_type === 'end') && $pauseStart) {
                    $haltDuration = strtotime($log->timestamp) - strtotime($pauseStart->timestamp);
                    $totalHaltDuration += $haltDuration;

                    $halts[] = [
                        'start_time' => $pauseStart->timestamp,
                        'end_time' => $log->timestamp,
                        'duration_minutes' => round($haltDuration / 60),
                        'location' => [
                            'lat' => $pauseStart->latitude,
                            'lng' => $pauseStart->longitude,
                        ]
                    ];

                    $pauseStart = null;
                }
            }

            // Trip Duration
            $duration = null;
            if ($trip->start_time && $trip->end_time) {
                $start = strtotime($trip->start_time);
                $end = strtotime($trip->end_time);
                $duration = gmdate('H:i:s', $end - $start);
            }
            // Attach data to trip object
            $trip->trip_logs = $tripLogs;
            $trip->trip_expenses = $tripExpenses;
            $trip->total_expenses = $totalExpenses;
            $trip->total_km = $totalKM;
            $trip->duration = $duration;
            $trip->halts = $halts;
            $trip->total_halt_time = gmdate('H:i:s', $totalHaltDuration);

            // Hide timestamps
            $trip->makeHidden(['created_at', 'updated_at']);
            $trip->customerInfo?->makeHidden(['created_at', 'updated_at']);
            $trip->driver?->makeHidden(['created_at', 'updated_at']);
            $trip->vehicle?->makeHidden(['created_at', 'updated_at']);

            $message = $userType === 'CompanyDetail'
                ? 'Trip details fetched successfully for Company'
                : 'Trip details fetched successfully for Driver';

            return response()->json([
                'status' => true,
                'message' => $message,
                'trip' => $trip,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    public function destroy(Request $request)
    {
        try {
            // Validate trip ID
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:trips,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 200);
            }

            $company_id = Auth::id();

            // Find trip with matching ID and company
            $trip = Trip::where('id', $request->id)
                ->where('company_id', $company_id)
                ->first();

            if (!$trip) {
                return response()->json([
                    'status' => false,
                    'message' => 'Trip not found or unauthorized',
                ], 200);
            }

            // Delete associated customer if exists
            $customer = CustomerInfo::where('id', $trip->customer_id)
                ->where('company_id', $company_id)
                ->first();

            $trip->delete();

            if ($customer) {
                $customer->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'Trip and related customer deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function driverList(Request $request)
    {
        try {
            $company_id = Auth::id();

            $drivers = Employee::select('id', 'emp_id', 'company_id', 'name', 'position')
                ->where('company_id', $company_id)
                ->where('position', 'driver')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Driver list fetched successfully',
                'drivers' => $drivers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // public function dashboard(Request $request)
    // {
    //     try {
    //         $user = Auth::user();

    //         $userType = class_basename($user);
    //         $companyId = ($userType === 'CompanyDetail') ? $user->id : null;
    //         $driverId  = ($userType === 'Employee') ? $user->id : null;

    //         if ($userType === "CompanyDetail") {
    //             // Total counts
    //             $company_id = Auth::id();
    //             $totalTrips = Trip::where('company_id', $company_id)->count();
    //             $totalVehicles = Vehicle::where('company_id', $company_id)->count();
    //             $totalDrivers = Employee::where('company_id', $company_id)
    //                 ->where('position', 'driver')->count();
    //             $totalCustomers = CustomerInfo::where('company_id', $company_id)->count();

    //             // Latest 5 trips with customer info
    //             $Trips = Trip::with(['customerInfo:id,customer_name,phone'])
    //                 ->where('company_id', $company_id)
    //                 ->orderBy('id', 'desc') // Optional: still order latest first
    //                 ->get()
    //                 ->makeHidden(['created_at', 'updated_at']);

    //             // Optional: Latest 5 drivers and vehicles (uncomment if needed)
    //             $drivers = Employee::select('id', 'name', 'emp_id', 'position', 'company_id')
    //                 ->where('company_id', $company_id)
    //                 ->where('position', 'driver')
    //                 ->orderBy('id', 'desc') // Optional: still order latest first
    //                 ->get();

    //             $Vehicles = Vehicle::select('id', 'vehicle_no', 'vehicle_type', 'company_id', 'status')
    //                 ->where('company_id', $company_id)
    //                 ->get();

    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Dashboard data fetched successfully',
    //                 'summary' => [
    //                     'total_trips' => $totalTrips,
    //                     'total_vehicles' => $totalVehicles,
    //                     'total_drivers' => $totalDrivers,
    //                     'total_customers' => $totalCustomers,
    //                 ],
    //                 'trips' => $Trips,
    //                 'drivers' => $drivers,
    //                 'vehicles' => $Vehicles,
    //             ], 200);
    //         } else {
    //             $driver_id = $user->id;
    //             $company_id = $user->company_id;
    //             // dd($company_id);

    //             // Totals relevant to driver’s company and driver
    //             $totalTrips = Trip::where('company_id', $company_id)
    //                 ->where('employee_id', $driver_id)
    //                 ->count();
    //             $totalCustomers = CustomerInfo::where('company_id', $company_id)
    //                 ->where('employee_id', $driver_id)
    //                 ->count();
    //             // Trips assigned to this driver with customer info
    //             $Trips = Trip::with(['customerInfo:id,customer_name,phone'])
    //                 ->where('company_id', $company_id)
    //                 ->where('employee_id', $driver_id)
    //                 ->orderBy('id', 'desc')
    //                 ->get()
    //                 ->makeHidden(['created_at', 'updated_at']);
    //             $tripIds = $Trips->pluck('id')->toArray();

    //             $vehicleIds = $Trips->pluck('vehicle_id')->unique()->toArray();

    //             $Vehicles = Vehicle::select('id', 'vehicle_no', 'vehicle_type', 'company_id', 'status')
    //           ->where('company_id', $company_id)
    //           ->whereIn('id', $vehicleIds)
    //             ->get();

    //             // dd($Trips);
    //            // Add additional information to each trip
    //                 foreach ($Trips as $trip) {
    //                 $trip->duration = $this->calculateTripDuration($trip);

    //             // Set odometer_start
    //                 $trip->odometer_start = $trip->status === 'pending'
    //                 ? 0
    //                 : $this->getOdometerReading($trip, 'start');

    //             // Set odometer_latest
    //                if ($trip->status === 'pending') {
    //                 $trip->odometer_latest = 0;
    //                 } elseif ($trip->status === 'ongoing') {
    //                 $trip->odometer_latest = $this->getOdometerReading($trip, 'latest');
    //                 }
    //             }
             
    //             $totalExpenses = TripExpense::where('company_id', $company_id)
    //                 ->whereIn('trip_id', $tripIds)
    //                 ->where('driver_id', $driver_id)
    //                 ->sum('amount');


    //             return response()->json([
    //                 'status' => true,
    //                 'message' => 'Dashboard data fetched successfully for driver',
    //                 'summary' => [
    //                     'total_trips' => $totalTrips,
    //                     'total_expenses' => $totalExpenses,
    //                     'total_customer' => $totalCustomers
    //                 ],
    //                 'trips' => $Trips,
    //                 'Vehicles'=> $Vehicles
    //             ], 200);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }



    public function dashboard(Request $request)
    {
        try {
            $user = Auth::user();
            $userType = class_basename($user);
            $companyId = ($userType === 'CompanyDetail') ? $user->id : $user->company_id;
            $driverId = ($userType === 'Employee') ? $user->id : null;

            if ($userType === 'CompanyDetail') {
                // Company dashboard
                $summary = [
                    'total_trips'     => Trip::where('company_id', $companyId)->count(),
                    'total_vehicles'  => Vehicle::where('company_id', $companyId)->count(),
                    'total_drivers'   => Employee::where('company_id', $companyId)->where('position', 'driver')->count(),
                    'total_customers' => CustomerInfo::where('company_id', $companyId)->count(),
                    'total_expenses' => TripExpense::where('company_id', $companyId)
                                                ->sum('amount'),
                    'expenses_count' => TripExpense::where('company_id', $companyId)
                                                ->count(),                              


                ];

                $trips = Trip::with(['customerInfo:id,customer_name,phone'])
                    ->where('company_id', $companyId)
                    ->latest()
                    ->get()
                    ->makeHidden(['created_at', 'updated_at']);

                $drivers = Employee::select('id', 'name', 'emp_id', 'position', 'company_id')
                    ->where('company_id', $companyId)
                    ->where('position', 'driver')
                    ->latest()
                    ->get();

                $vehicles = Vehicle::select('id', 'vehicle_no', 'vehicle_type', 'company_id', 'status')
                    ->where('company_id', $companyId)
                    ->get();


                $expense = TripExpense::where('company_id', $companyId)
                  ->select('id', 'trip_id', 'expense_type', 'amount', 'note', 'bill_receipt')
                  ->get();


                return response()->json([
                    'status'  => true,
                    'message' => 'Dashboard data fetched for company successfully',
                    'summary' => $summary,
                    'trips'   => $trips,
                    'drivers' => $drivers,
                    'vehicles'=> $vehicles,
                    'expense'=> $expense
                ]);
            }

            // Driver dashboard
            $summary = [
                'total_trips'    => Trip::where('company_id', $companyId)->where('employee_id', $driverId)->count(),
                'total_expenses' => TripExpense::where('company_id', $companyId)
                                                ->where('driver_id', $driverId)
                                                ->sum('amount'),
               'expenses_count' => TripExpense::where('company_id', $companyId)
                                                ->where('driver_id', $driverId)
                                                ->count(),        

                'total_customer' => CustomerInfo::where('company_id', $companyId)
                                                ->where('employee_id', $driverId)
                                                ->count(),
            ];

            $trips = Trip::with(['customerInfo:id,customer_name,phone'])
                ->where('company_id', $companyId)
                ->where('employee_id', $driverId)
                ->latest()
                ->get()
                ->makeHidden(['created_at', 'updated_at']);

            // Enhance trips with calculated data
            foreach ($trips as $trip) {
                $trip->duration = $this->calculateTripDuration($trip);

                $trip->odometer_start = $trip->status === 'pending'
                    ? 0
                    : $this->getOdometerReading($trip, 'start');

                $trip->odometer_latest = match ($trip->status) {
                    'pending'  => 0,
                    'ongoing'  => $this->getOdometerReading($trip, 'latest'),
                    default    => null,
                };
            }

            // Collect associated vehicle data
            $vehicleIds = $trips->pluck('vehicle_id')->unique()->toArray();
            $vehicles = Vehicle::select('id', 'vehicle_no', 'vehicle_type', 'company_id', 'status')
                ->where('company_id', $companyId)
                ->whereIn('id', $vehicleIds)
                ->get();

            $expense = TripExpense::where('company_id', $companyId)
            ->where('driver_id', $driverId)
        ->select('id', 'trip_id', 'expense_type', 'amount', 'note', 'bill_receipt')
        ->get();


            return response()->json([
                'status'   => true,
                'message'  => 'Dashboard data fetched successfully for driver',
                'summary'  => $summary,
                'trips'    => $trips,
                'vehicles' => $vehicles,
                'expense'=>   $expense
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }





    public function assignDriver(Request $request)
    {
        $company_id = Auth::id();

        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|integer|exists:trips,id',
            'driver_id' => 'required|integer|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 200);
        }

        $trip = Trip::where('company_id', $company_id)->where('id', $request->trip_id)->first();

        if (!$trip) {
            return response()->json([
                'status' => false,
                'message' => 'Trip not found or unauthorized',
            ], 200);
        }

        $trip->driver_id = $request->driver_id;
        $trip->save();

        return response()->json([
            'status' => true,
            'message' => 'Driver assigned successfully',
            'trip' => $trip->only(['id', 'driver_id']),
        ], 200);
    }

    public function assignVehicle(Request $request)
    {
        $company_id = Auth::id();

        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|integer|exists:trips,id',
            'vehicle_id' => 'required|integer|exists:vehicles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 200);
        }

        $trip = Trip::where('company_id', $company_id)->where('id', $request->trip_id)->first();

        if (!$trip) {
            return response()->json([
                'status' => false,
                'message' => 'Trip not found or unauthorized',
            ], 200);
        }

        $trip->vehicle_id = $request->vehicle_id;
        $trip->save();

        return response()->json([
            'status' => true,
            'message' => 'Vehicle assigned successfully',
            'trip' => $trip->only(['id', 'vehicle_id']),
        ], 200);
    }



    public function checkVehicleAvailability(Request $request)
    {
        $company_id = Auth::id();

        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|integer|exists:vehicles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $conflict = Trip::where('company_id', $company_id)
            ->where('vehicle_id', $request->vehicle_id)
            ->where('status', 'ongoing') // ✅ Only check ongoing trips
            ->exists();

        return response()->json([
            'status'    => true,
            'available' => !$conflict,
        ], 200);
    }




    public function checkDriverAvailability(Request $request)
    {
        $company_id = Auth::id();

        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|integer|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 200);
        }

        $conflict = Trip::where('company_id', $company_id)
            ->where('driver_id', $request->driver_id)
            ->where('status', 'ongoing')
            ->exists();

        return response()->json([
            'status'    => true,
            'available' => !$conflict,
        ], 200);
    }


    // Add this method for distance calculation
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // distance in km

        return round($distance, 2); // round to 2 decimals
    }





    private function isVehicleAvailable($vehicleId, $startTime, $endTime)
    {
        return !Trip::where('vehicle_id', $vehicleId)
            ->where('status', 'ongoing') // Only consider ongoing trips
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $startTime)
                            ->where('end_time', '>', $endTime);
                    });
            })->exists();
    }

    private function isDriverAvailable($driverId, $startTime, $endTime)
    {
        return !Trip::where('driver_id', $driverId)
            ->where('status', 'ongoing') // Only consider ongoing trips
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $startTime)
                            ->where('end_time', '>', $endTime);
                    });
            })->exists();
    }



    public function tripAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|numeric',
            'trip_id'   => 'required|exists:trips,id',
            'type'      => 'required|in:start,pause,resume,end',
            'lat'       => 'required|numeric',
            'long'      => 'required|numeric',
            'odometer'  => 'nullable|numeric',
            'image'     => 'required|image|mimes:jpeg,png,jpg|max:2048',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 200);
        }

        $user = Auth::user();

        $userType = class_basename($user);
        $companyId = ($userType === 'CompanyDetail') ? $user->id : null;
        $driverId  = ($userType === 'Employee') ? $user->id : null;


        if ($userType === "CompanyDetail") {
            $company_id = Auth::id();
            $trip = Trip::where('id', $request->trip_id)
                ->where('company_id', $company_id)
                ->first();
        } else {
            $trip = Trip::where('id', $request->trip_id)
                ->where('company_id', $request->company_id)
                ->first();
        }

        if (!$trip) {
            return response()->json([
                'status' => false,
                'message' => 'Trip not found or does not belong to your company.'
            ], 200);
        }

          $type = $request->type;
        if ($type === 'start' && $userType === 'Employee') {
            if ($trip->employee_id !== $user->id || strtolower($user->position) !== 'driver') {
                return response()->json([
                    'status' => true,
                    'message' => 'Trip can only be started by the driver assigned by the company.',
                ], 200);
            }
        }

        // Restrict start and end
        if ($type === 'start' && $trip->status !== 'pending') {
            return response()->json(['status' => true, 'message' => 'Trip has already been started.'], 200);
        }

        if ($type === 'end' && $trip->status === 'completed') {
            return response()->json(['status' => true, 'message' => 'Trip has already been ended.'], 200);
        }

        // Restrict pause/resume based on current status
        if ($type === 'pause' && $trip->status !== 'ongoing') {
            return response()->json(['status' => true, 'message' => 'Trip must be ongoing to pause.'], 200);
        }

        if ($type === 'resume' && $trip->status !== 'pause') {
            return response()->json(['status' => true, 'message' => 'Trip must be paused to resume.'], 200);
        }


        $imagePath = null;
        if ($request->hasFile('image')) {
            $directory = public_path('uploads/trip_logs');
            File::ensureDirectoryExists($directory);
            $imageName = time() . '_' . uniqid() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move($directory, $imageName);
            $imagePath = $imageName;
        }
        TripLog::create([
            'trip_id'     => $trip->id,
            'company_id'  => $trip->company_id ?? null,
            'driver_id'  => $trip->driver_id ?? null,
            'user_type'   => $userType,         // 'CompanyDetail' or 'Employee'
            'action_type' => $type,
            'latitude'    => $request->lat,
            'longitude'   => $request->long,
            'odometer'    => $request->odometer,
            'image_path'  => $imagePath,
            'timestamp'   => now(),
        ]);

        // Update trip based on action
        switch ($type) {
            case 'start':
                $trip->status = 'ongoing';
                $trip->start_time = now();
                $trip->start_latitude = $request->lat;
                $trip->start_longitude = $request->long;
                $trip->trip_started_by = $userType;
                break;

            case 'pause':
                $trip->status = 'pause';
                break;

            case 'resume':
                $trip->status = 'ongoing';
                break;

            case 'end':
                $trip->status = 'completed';
                $trip->end_time = now();
                $trip->end_latitude = $request->lat;
                $trip->end_longitude = $request->long;
                break;
        }

        $trip->save();

        return response()->json([
            'status' => true,
            'message' => "Trip action $type processed successfully.",

        ]);
    }



    private function calculateTripDuration($trip)
    {
        // Fetch the latest trip log for the trip
        $latestLog = TripLog::where('trip_id', $trip->id)
            ->latest('timestamp')
            ->first();

        if (!$latestLog) {
            return null; // No logs found
        }

        // Check if the trip is ongoing
        if ($latestLog->action_type === 'start') {
            // Calculate duration from the start time to the current time
            $startTime = \Carbon\Carbon::parse($latestLog->timestamp);
            $duration = $startTime->diffForHumans(null, true);
            return $duration;
        }

        // If the trip is completed, calculate the duration between start and end times
        $startLog = TripLog::where('trip_id', $trip->id)
            ->where('action_type', 'start')
            ->first();

        if ($startLog) {
            $endLog = TripLog::where('trip_id', $trip->id)
                ->where('action_type', 'end')
                ->first();

            if ($endLog) {
                $startTime = \Carbon\Carbon::parse($startLog->timestamp);
                $endTime = \Carbon\Carbon::parse($endLog->timestamp);
                $duration = $startTime->diffForHumans($endTime, true);
                return $duration;
            }
        }

        return null; // Duration not available
    }

    private function getOdometerReading($trip, $actionType)
    {
        $log = TripLog::where('trip_id', $trip->id)
            ->where('action_type', $actionType)
            ->orderBy('timestamp', $actionType === 'start' ? 'asc' : 'desc')
            ->first();

        return $log ? $log->odometer : 0;
    }


    private function getTotalExpenses($trip, $driver_id)
    {
        $totalExpenses = TripExpense::where('trip_id', $trip->id)
            ->where('driver_id', $driver_id)
            ->sum('amount');

        return $totalExpenses;
    }
}
