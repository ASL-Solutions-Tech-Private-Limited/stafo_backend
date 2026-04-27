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