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
        $is_verified = Auth::user()->is_verified ?? 'Yes';
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }

        try {
            $companyId = Auth::id();
            $query = Shift::where('company_id', $companyId);

            if ($request->filled('shift_name')) {
                $query->where('shift_name', 'like', '%' . $request->shift_name . '%');
            }

            if ($request->filled('start_time')) {
                $query->where('start_time', 'like', '%' . $request->start_time . '%');
            }

            if ($request->filled('end_time')) {
                $query->where('end_time', 'like', '%' . $request->end_time . '%');
            }

            $shifts = $query->orderBy('created_at', 'desc')->get();

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => true,
                    'message' => 'Shifts retrieved successfully.',
                    'data' => $shifts,
                ]);
            }

            return view('user.shifts.index', compact('shifts'));
        } catch (Exception $e) {
            Log::error('Error fetching shifts: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status' => false,
                    'message' => 'An error occurred while fetching shifts.',
                    'error' => $e->getMessage(),
                ], 500);
            }
            return redirect()->route('shifts.index')->with('error', 'An error occurred while fetching shifts.');
        }
    }

    public function create()
    {
        return redirect()->route('shifts.index', ['action' => 'create']);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'shift_name' => 'required|string|max:255',
                'start_time' => 'required|string',
                'end_time'   => 'required|string',
            ]);

            $companyId = Auth::id();

            // Normalize working days boolean inputs (checked = 1/true, unchecked = 0/false)
            $shift = Shift::create([
                'company_id' => $companyId,
                'shift_name' => $request->shift_name,
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
                'sunday'     => filter_var($request->input('sunday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'monday'     => filter_var($request->input('monday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'tuesday'    => filter_var($request->input('tuesday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'wednesday'  => filter_var($request->input('wednesday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'thursday'   => filter_var($request->input('thursday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'friday'     => filter_var($request->input('friday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'saturday'   => filter_var($request->input('saturday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'status'  => true,
                    'message' => 'Shift created successfully.',
                    'data'    => $shift,
                ], 201);
            }

            return redirect()->route('shifts.index')->with('success', 'Shift created successfully.');
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'Validation error.',
                    'errors'  => $e->errors(),
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('Error creating shift: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'An error occurred while creating the shift.',
                    'error'   => $e->getMessage(),
                ], 500);
            }
            return redirect()->route('shifts.index')->with('error', 'An error occurred while creating the shift.');
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $companyId = Auth::id();
            $shift = Shift::where('company_id', $companyId)->findOrFail($id);

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'status'  => true,
                    'message' => 'Shift retrieved successfully.',
                    'data'    => $shift,
                ]);
            }

            return view('user.shifts.show', compact('shift'));
        } catch (ModelNotFoundException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'Shift not found.',
                ], 404);
            }
            return redirect()->route('shifts.index')->with('error', 'Shift not found.');
        } catch (Exception $e) {
            Log::error('Error fetching shift details: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'An error occurred while fetching shift details.',
                ], 500);
            }
            return redirect()->route('shifts.index')->with('error', 'An error occurred while fetching the shift details.');
        }
    }

    public function edit($id)
    {
        return redirect()->route('shifts.index', ['action' => 'edit', 'id' => $id]);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'shift_name' => 'required|string|max:255',
                'start_time' => 'required|string',
                'end_time'   => 'required|string',
            ]);

            $companyId = Auth::id();
            $shift = Shift::where('company_id', $companyId)->findOrFail($id);

            // Update shift with normalized working days
            $shift->update([
                'shift_name' => $request->shift_name,
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
                'sunday'     => filter_var($request->input('sunday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'monday'     => filter_var($request->input('monday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'tuesday'    => filter_var($request->input('tuesday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'wednesday'  => filter_var($request->input('wednesday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'thursday'   => filter_var($request->input('thursday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'friday'     => filter_var($request->input('friday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
                'saturday'   => filter_var($request->input('saturday', false), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'status'  => true,
                    'message' => 'Shift updated successfully.',
                    'data'    => $shift,
                ]);
            }

            return redirect()->route('shifts.index')->with('success', 'Shift updated successfully.');
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'Validation error.',
                    'errors'  => $e->errors(),
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'Shift not found.',
                ], 404);
            }
            return redirect()->route('shifts.index')->with('error', 'Shift not found.');
        } catch (Exception $e) {
            Log::error('Error updating shift: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'An error occurred while updating the shift.',
                    'error'   => $e->getMessage(),
                ], 500);
            }
            return redirect()->route('shifts.index')->with('error', 'An error occurred while updating the shift.');
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $companyId = Auth::id();
            $shift = Shift::where('company_id', $companyId)->findOrFail($id);
            $shift->delete();

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'status'  => true,
                    'message' => 'Shift deleted successfully.',
                ]);
            }

            return redirect()->route('shifts.index')->with('success', 'Shift deleted successfully.');
        } catch (ModelNotFoundException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'Shift not found.',
                ], 404);
            }
            return redirect()->route('shifts.index')->with('error', 'Shift not found.');
        } catch (Exception $e) {
            Log::error('Error deleting shift: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'An error occurred while deleting the shift.',
                ], 500);
            }
            return redirect()->route('shifts.index')->with('error', 'An error occurred while deleting the shift.');
        }
    }
}