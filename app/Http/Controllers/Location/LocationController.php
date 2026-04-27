<?php

namespace App\Http\Controllers\Location;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function getStates($countryId)
    {

        $states = State::where('country_id', $countryId)->get(['id', 'name']);
        return response()->json($states);
    }

    public function getCities($stateId)
    {
        $cities = City::where('state_id', $stateId)->get(['id', 'name']);
        return response()->json($cities);
    }
}