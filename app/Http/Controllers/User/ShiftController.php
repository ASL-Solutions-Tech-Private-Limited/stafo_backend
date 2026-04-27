<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShiftController extends Controller
{

    public function index(Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        try {
            $companyId = Auth::id();
            $query = Shift::where('company_id', $companyId);  // Filter by company_id
            if ($request->has('shift_name') && $request->shift_name !== '') {
                $query->where('shift_name', 'like', '%' . $request->shift_name . '%');
            }

            if ($request->has('start_time') && $request->start_time !== '') {
                $query->where('start_time', 'like', '%' . $request->start_time . '%');
            }

            if ($request->has('end_time') && $request->end_time !== '') {
                $query->where('end_time', 'like', '%' . $request->end_time . '%');
            }
            $shifts = $query->orderBy('created_at', 'desc')->get();
            return view('user.shifts.index', compact('shifts'));
        } catch (Exception $e) {
            Log::error('Error fetching shifts: ' . $e->getMessage());
            return redirect()->route('shifts.index')->with('error', 'An error occurred while fetching shifts.');
        }
    }

    public function create()
    {
        return view('user.shifts.create');
    }


    public function store(Request $request)
    {
        try {
            // Validate the input data
            $request->validate([
                'shift_name' => 'required|string|max:255',
                'start_time' => 'required|date_format:h:i A',
                'end_time' => 'required|date_format:h:i A',
            ]);

            // Add company_id from the authenticated user
            $requestData = $request->all();
            $requestData['company_id'] = auth()->id();  // Automatically set the company_id

            // Create the new shift entry
            Shift::create($requestData);

            // Redirect back with a success message
            return redirect()->route('shifts.index')->with('success', 'Shift created successfully.');
        } catch (ValidationException $e) {
            // Handle validation errors
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            // Log the error and show an error message
            Log::error('Error creating shift: ' . $e->getMessage());
            return redirect()->route('shifts.index')->with('error', 'An error occurred while creating the shift.');
        }
    }


    public function edit(Shift $shift)
    {
        return view('user.shifts.edit', compact('shift'));
    }
    public function update(Request $request, Shift $shift)
    {
        try {
            $request->validate([
                'shift_name' => 'required|string|max:255',
                'start_time' => 'required|date_format:h:i A',
                'end_time' => 'required|date_format:h:i A',
            ]);

            $shift->update([
                'shift_name' => $request->input('shift_name'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
            ]);

            return redirect()->route('shifts.index')->with('success', 'Shift updated successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            return redirect()->route('shifts.index')->with('error', 'Shift not found.');
        } catch (Exception $e) {
            Log::error('Error updating shift: ' . $e->getMessage());
            return redirect()->route('shifts.index')->with('error', 'An error occurred while updating the shift.');
        }
    }

    public function destroy(Shift $shift)
    {
        try {
            $shift->delete();
            return redirect()->route('shifts.index')->with('success', 'Shift deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('shifts.index')->with('error', 'Shift not found.');
        } catch (Exception $e) {
            Log::error('Error deleting shift: ' . $e->getMessage());
            return redirect()->route('shifts.index')->with('error', 'An error occurred while deleting the shift.');
        }
    }


    public function show(Shift $shift)
    {
        try {
            return view('user.shifts.show', compact('shift'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('shifts.index')->with('error', 'Shift not found.');
        } catch (Exception $e) {
            Log::error('Error fetching shift details: ' . $e->getMessage());
            return redirect()->route('shifts.index')->with('error', 'An error occurred while fetching the shift details.');
        }
    }
}