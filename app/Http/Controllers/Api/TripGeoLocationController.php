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


    /**
     * @OA\Post(
     *     path="/api/trips-geolocation/create",
     *     summary="Store trip geo location coordinates",
     *     tags={"Trips"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"trip_id","latitude","longitude"},
     *             @OA\Property(property="trip_id", type="integer", example=1),
     *             @OA\Property(property="latitude", type="number", example=28.6139),
     *             @OA\Property(property="longitude", type="number", example=77.2090)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Location stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Trip geo location submitted successfully.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/trips-geolocation/get",
     *     summary="Get trip geo location track",
     *     tags={"Trips"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"trip_id"},
     *             @OA\Property(property="trip_id", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date", example="2026-09-26")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Locations fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Trip geo location fetched successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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
