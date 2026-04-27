<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class HolidayController extends Controller
{

    // List all holidays
    // public function index()
    // {
    //     try {
    //         $holidays = Holiday::all();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Holidays fetched successfully',
    //             'data' => $holidays
    //         ], 200);
    //     } catch (QueryException $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Database Error',
    //             'data' => $e->getMessage()
    //         ], 500);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Unexpected Error',
    //             'data' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function index(Request $request)
    {
        try {
            if ($request->has('company_id')) {
                $company_id = $request->input('company_id');
            } else {
                $company_id = Auth::id(); // Get the authenticated user's company_id
            }
            
            $holidays = Holiday::where('company_id', $company_id)->get(); // Only fetch holidays for the authenticated company

            return response()->json([
                'status' => true,
                'message' => 'Holidays fetched successfully',
                'data' => $holidays
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }



    public function store(Request $request)
    {
        try {
            // Get the authenticated user's company ID
            $company_id = Auth::id();  // Get the authenticated user's ID, assuming it represents the company_id

            // Validate the incoming data. We expect an array of holidays.
            $validated = $request->validate([
                'holidays' => 'required|array', // Ensure holidays is an array
                'holidays.*.title' => 'required|string|max:255',
                'holidays.*.description' => 'nullable|string',
                'holidays.*.start_date' => 'required|date',
                'holidays.*.end_date' => 'required|date|after_or_equal:holidays.*.start_date', // Ensure end date is after start date
            ]);

            // Loop through the holidays and add the company_id to each holiday
            $holidays = [];
            foreach ($validated['holidays'] as $holidayData) {
                $holidayData['company_id'] = $company_id;
                $holidays[] = $holidayData;
            }

            // Create holidays in bulk (use insert and fetch their ids afterward)
            Holiday::insert($holidays);

            // Fetch the holidays with the newly inserted data
            $createdHolidays = Holiday::where('company_id', $company_id)
                ->whereIn('start_date', collect($holidays)->pluck('start_date'))
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Holidays created successfully',
                'data' => $createdHolidays
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }






    // Edit a holiday

    public function update(Request $request, $id)
    {
        try {
            $company_id = Auth::id();
            $holiday = Holiday::where('company_id', $company_id)->find($id);
            if (!$holiday) {
                return response()->json([
                    'status' => false,
                    'message' => 'Holiday not found for the authenticated company',
                    'data' => []
                ], 404);
            }

            // Validate the incoming data
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);


            $holiday->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Holiday updated successfully',
                'data' => $holiday
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }





    // Get all holidays for a specific company
    public function holidayGetById(Request $request)
    {
        try {
            // Get the authenticated user's company ID
            $company_id = Auth::id();

            // Fetch holidays based on the company_id
            $holidays = Holiday::where('company_id', $company_id)->get();

            if ($holidays->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No holidays found for this company',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Holidays fetched successfully',
                'data' => $holidays
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }



    // Delete a holiday
    public function destroy($id)
    {
        try {
            $holiday = Holiday::findOrFail($id);

            // Delete the holiday
            $holiday->delete();

            return response()->json([
                'status' => true,
                'message' => 'Holiday deleted successfully'
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error',
                'data' => $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unexpected Error',
                'data' => $e->getMessage()
            ], 500);
        }
    }
}