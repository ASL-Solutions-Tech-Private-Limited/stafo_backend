<?php

namespace App\Http\Controllers\Location;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    public function getStates($countryId)
    {
        $states = Cache::remember("web_states_{$countryId}", 86400 * 7, function () use ($countryId) {
            return State::where('country_id', $countryId)->get(['id', 'name']);
        });

        return response()->json($states);
    }

    public function getCities($stateId)
    {
        $cities = Cache::remember("web_cities_{$stateId}", 86400 * 7, function () use ($stateId) {
            return City::where('state_id', $stateId)->get(['id', 'name']);
        });

        return response()->json($cities);
    }
}