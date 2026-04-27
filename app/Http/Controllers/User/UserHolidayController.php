<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserHolidayController extends Controller
{
    public function index()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $companyId = Auth::id();
        $holidayes = Holiday::where('company_id', $companyId)->orderBy('created_at', 'desc')->get();

        return view('user.holiday.index', compact('holidayes'));
    }

    public function create()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        return view('user.holiday.create'); // Return the view to create a new holiday
    }


    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        $userId = Auth::id();
      
        Holiday::create([
            'company_id' => $userId,
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        return redirect()->route('holiday.index')->with('success', 'Holiday created successfully.');
    }


    public function edit($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $holiday = Holiday::findOrFail($id); // Fetch the holiday to edit
        return view('user.holiday.edit', compact('holiday')); // Return the edit view
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $userId = Auth::id();
        $status = $request->status === 'active' ? 1 : 0;
        $holiday = Holiday::findOrFail($id);
        $holiday->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // Redirect back with success message
        return redirect()->route('holiday.index')->with('success', 'Holiday updated successfully.');
    }


    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id); // Find the holiday to delete
        $holiday->delete(); // Delete the holiday

        return redirect()->route('holiday.index')->with('success', 'Holiday deleted successfully.');
    }
}