<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TripGeoLocation;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\Trip;


class TripGeoLocationController extends Controller
{


public function storeTripGeoLocation(Request $request)
{
    try {
        $request->validate([
            'trip_id'   => 'required|exists:trips,id',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();
        $userType = class_basename($user); 
        $trip = null;
        $companyId = null;
        $employeeId = null;

        if ($userType === 'CompanyDetail') {
            $companyId = $user->id;
            $trip = Trip::where('id', $request->trip_id)
                        ->where('company_id', $companyId)
                        ->first();
        } elseif ($userType === 'Employee') {
            $companyId = $user->company_id;
            $employeeId = $user->id;
            $trip = Trip::where('id', $request->trip_id)
                        ->where(['company_id' => $companyId, 'driver_id' => $employeeId])
                        ->first();
        }

        if (!$trip) {
            return response()->json([
                'status' => false,
                'message' => 'Trip not found or unauthorized access.'
            ], 200);
        }

        TripGeoLocation::create([
            'trip_id'    => $trip->id,
            'company_id' => $companyId,
            'driver_id'=> $employeeId, // null for company
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Trip geo location submitted successfully.',
        ], 201);

    } catch (ValidationException $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Validation error.',
            'errors'  => $e->errors()
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'An error occurred while saving trip location.',
            'error'   => $e->getMessage()
        ], 500);
    }
}

public function getTripGeoLocation(Request $request)
{
    try {
        $request->validate([
            'trip_id' => 'required|exists:trips,id'
        ]);

        $user = Auth::user();
        $userType = class_basename($user);

        $trip = null;
        $companyId = null;
        $employeeId = null;

        if ($userType === 'CompanyDetail') {
            $companyId = $user->id;
            $trip = Trip::where('id', $request->trip_id)
                        ->where('company_id', $companyId)
                        ->first();
        } elseif ($userType === 'Employee') {
            $companyId = $user->company_id;
            $employeeId = $user->id;
            $trip = Trip::where('id', $request->trip_id)
                        ->where(['company_id' => $companyId, 'driver_id' => $employeeId])
                        ->first();
        }

        if (!$trip) {
            return response()->json([
                'status'  => false,
                'message' => 'Trip not found or unauthorized access.'
            ], 200);
        }

        $query = TripGeoLocation::where('trip_id', $trip->id);

        if ($userType === 'Employee') {
            $query->where('driver_id', $employeeId);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $locations = $query->orderBy('created_at')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Trip geo location fetched successfully.',
            'data'    => $locations
        ], 200);

    } catch (ValidationException $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Validation error.',
            'errors'  => $e->errors()
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'An error occurred while fetching the request.',
            'error'   => $e->getMessage()
        ], 500);
    }
}



}
