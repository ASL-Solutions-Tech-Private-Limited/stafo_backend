<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/countries",
     *     summary="Get all countries",
     *     tags={"Location"},
     *     @OA\Response(
     *         response=200,
     *         description="Countries retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Countries retrieved successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getCountries()
    {
        try {
            $countries = Country::all();

            if ($countries->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No countries found.',
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Countries retrieved successfully.',
                'data' => $countries
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching countries.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/countries/{countryId}/states",
     *     summary="Get states by country ID",
     *     tags={"Location"},
     *     @OA\Parameter(
     *         name="countryId",
     *         in="path",
     *         required=true,
     *         description="Country ID",
     *         @OA\Schema(type="integer", example=101)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="States retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="States retrieved successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getStates($countryId)
    {
        try {
            $country = Country::findOrFail($countryId);
            $states = State::where('country_id', $countryId)->get();

            // if ($states->isEmpty()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'No states found for this country.',
            //     ], 404);
            // }

            return response()->json([
                'success' => true,
                'message' => 'States retrieved successfully.',
                'data' => $states,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found.',
                'error' => $e->getMessage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching states.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/states/{stateId}/cities",
     *     summary="Get cities by state ID",
     *     tags={"Location"},
     *     @OA\Parameter(
     *         name="stateId",
     *         in="path",
     *         required=true,
     *         description="State ID",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cities retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cities retrieved successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getCities($stateId)
    {
        try {
            $state = State::findOrFail($stateId);
            $cities = City::where('state_id', $stateId)->get();

            // if ($cities->isEmpty()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'No cities found for this state.',
            //     ], 404);
            // }

            return response()->json([
                'success' => true,
                'message' => 'Cities retrieved successfully.',
                'data' => $cities,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'State not found.',
                'error' => $e->getMessage(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching cities.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}