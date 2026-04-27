<?php

namespace App\Http\Controllers\User;

use App\Models\CompoffLeave;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CompoffLeaveController extends Controller
{
    // Display list of compoffleaves
    public function index(Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $name = $request->input('title');
        $status = $request->input('status');

        $compoffleaves = CompoffLeave::query();

        if ($name) {
            $compoffleaves->where('titlename', 'like', '%' . $name . '%');
        }

        if ($status !== null) {
            $compoffleaves->where('status', $status);
        }

        $companyId = Auth::id();
        $compoffleaves->where('company_id', $companyId);

      
        $compoffleaves = $compoffleaves->orderBy('created_at', 'desc')->paginate(10);

        return view('user.compoffleaves.index', compact('compoffleaves', 'name', 'status'));
    }

    // Show the create compoffleave form
    public function create()
    {
        $employees = Employee::where('company_id', Auth::id())->get();
        return view('user.compoffleaves.create', compact('employees'));
    }

    // Store a new compoffleave
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        // Get company_id from authenticated user
        $companyId = Auth::id();
        $fileName = null; // Initialize fileName variable
        if ($request->hasFile('filename')) {
            $file = $request->file('filename');
            $fileName = time() . '_' . $file->getClientOriginalName();

            $filePath = public_path('uploads/compoff/' . $fileName);

            $file->move(public_path('uploads/compoff'), $fileName);
        }
        // Create compoffleave with the validated data and company_id
        CompoffLeave::create([
            'employee_id' => $request->input('employee_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'date' => $request->input('date'),
            'company_id' => $companyId,
            'filename' => $fileName,
        ]);

        

        // Redirect back with success message
        return redirect()->route('compoffleaves.index')->with('success', 'Compoff Leave added successfully!');
    }

    // Show the details of a compoffleave
    public function show($id)
    {
        // Get company_id from authenticated user and ensure compoffleave belongs to that company
        $companyId = Auth::id();
        $compoffleave = CompoffLeave::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        return view('user.compoffleaves.show', compact('compoffleave'));
    }

    // Show the edit form for a compoffleave
    public function edit($id)
    {
        // Get company_id from authenticated user and ensure compoffleave belongs to that company
        $companyId = Auth::id();
        $compoffleave = CompoffLeave::where('id', $id)->where('company_id', $companyId)->firstOrFail();
         $employees = Employee::where('company_id', Auth::id())->get();
        return view('user.compoffleaves.edit', compact('compoffleave','employees'));
    }

    // Update an existing compoffleave
    public function update(Request $request, $id)
    {
        // Get company_id from authenticated user and ensure compoffleave belongs to that company
        $companyId = Auth::id();
        $compoffleave = CompoffLeave::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $compoffleave->employee_id = $request->input('employee_id');
        $compoffleave->description = $request->input('description');
        $compoffleave->title = $request->input('title');
        $compoffleave->status = $request->input('status');
        $compoffleave->date = $request->input('date');

        if ($request->hasFile('filename')) {
            if($compoffleave->receipt_file){
                $oldDocumentPath = public_path('uploads/compoff/' . $compoffleave->receipt_file);
                if (file_exists($oldDocumentPath)) {
                    unlink($oldDocumentPath);
                }
            }
            
            $file = $request->file('filename');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/compoff'), $fileName);

            // Update the document path in the database
            $compoffleave->filename = $fileName;
        }
        $compoffleave->save();
        
        // Update compoffleave with validated data
        $compoffleave->update([
            'employee_id' => $request->input('employee_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        // Redirect back with success message
        return redirect()->route('compoffleaves.index')->with('success', 'Compoff Leave updated successfully!');
    }

    // Delete a compoffleave
    public function destroy($id)
    {
        $companyId = Auth::id();
        $compoffleave = CompoffLeave::where('id', $id)->where('company_id', $companyId)->firstOrFail();
        if($compoffleave->receipt_file){
                $oldDocumentPath = public_path('uploads/receipt/' . $compoffleave->receipt_file);
                if (file_exists($oldDocumentPath)) {
                    unlink($oldDocumentPath);
                }
            }
        $compoffleave->delete();

        return redirect()->route('compoffleaves.index')->with('success', 'Compoff Leave deleted successfully!');
    }

    // Toggle the status of a compoffleave
    public function statuschange(Request $request)
    {
        $companyId = Auth::id();
        $id = $request->input('id');
        $status = $request->input('status');
        $compoffleave = CompoffLeave::where('id', $id)->where('company_id', $companyId)->firstOrFail();
        $compoffleave->status = $status;
        $compoffleave->save();
        if($status == 'Approved'){
            Employee::where('id', $compoffleave->employee_id)->update(['casual_leave' => 1]);
        }

        return redirect()->route('compoffleaves.index')->with('success', 'Compoff Leave '.$status.' successfully!');
    }
}