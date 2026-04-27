<?php

namespace App\Http\Controllers\User;

use App\Models\Reimbursement;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReimbursementController extends Controller
{
    // Display list of reimbursements
    public function index(Request $request)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $name = $request->input('name');
        $status = $request->input('status');

        $reimbursements = Reimbursement::query();

        if ($name) {
            $reimbursements->where('name', 'like', '%' . $name . '%');
        }

        if ($status !== null) {
            $reimbursements->where('status', $status);
        }

        $companyId = Auth::id();
        $reimbursements->where('company_id', $companyId);

      
        $reimbursements = $reimbursements->orderBy('created_at', 'desc')->paginate(10);

        return view('user.reimbursements.index', compact('reimbursements', 'name', 'status'));
    }

    // Show the create reimbursement form
    public function create()
    {
        $employees = Employee::where('company_id', Auth::id())->get();
        return view('user.reimbursements.create', compact('employees'));
    }

    // Store a new reimbursement
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Get company_id from authenticated user
        $companyId = Auth::id();

        $file = $request->file('receipt_file');
        $fileName = time() . '_' . $file->getClientOriginalName();

        $filePath = public_path('uploads/receipt/' . $fileName);

        $file->move(public_path('uploads/receipt'), $fileName);
        
        // Create reimbursement with the validated data and company_id
        Reimbursement::create([
            'employee_id' => $request->input('employee_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'amount' => $request->input('amount'),
            'date' => $request->input('date'),
            'company_id' => $companyId,
            'receipt_file' => $fileName,
        ]);

        

        // Redirect back with success message
        return redirect()->route('reimbursements.index')->with('success', 'Reimbursement added successfully!');
    }

    // Show the details of a reimbursement
    public function show($id)
    {
        // Get company_id from authenticated user and ensure reimbursement belongs to that company
        $companyId = Auth::id();
        $reimbursement = Reimbursement::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        return view('user.reimbursements.show', compact('reimbursement'));
    }

    // Show the edit form for a reimbursement
    public function edit($id)
    {
        // Get company_id from authenticated user and ensure reimbursement belongs to that company
        $companyId = Auth::id();
        $reimbursement = Reimbursement::where('id', $id)->where('company_id', $companyId)->firstOrFail();
         $employees = Employee::where('company_id', Auth::id())->get();
        return view('user.reimbursements.edit', compact('reimbursement','employees'));
    }

    // Update an existing reimbursement
    public function update(Request $request, $id)
    {
        // Get company_id from authenticated user and ensure reimbursement belongs to that company
        $companyId = Auth::id();
        $reimbursement = Reimbursement::where('id', $id)->where('company_id', $companyId)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $reimbursement->employee_id = $request->input('employee_id');
        $reimbursement->description = $request->input('description');
        $reimbursement->name = $request->input('name');
        $reimbursement->status = $request->input('status');
        $reimbursement->amount = $request->input('amount');
        $reimbursement->date = $request->input('date');

        if ($request->hasFile('receipt_file')) {
            if($reimbursement->receipt_file){
                $oldDocumentPath = public_path('uploads/receipt/' . $reimbursement->receipt_file);
                if (file_exists($oldDocumentPath)) {
                    unlink($oldDocumentPath);
                }
            }
            
            $file = $request->file('receipt_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/receipt'), $fileName);

            // Update the document path in the database
            $reimbursement->receipt_file = $fileName;
        }
        $reimbursement->save();
        
        // Update reimbursement with validated data
        $reimbursement->update([
            'employee_id' => $request->input('employee_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'amount' => $request->input('amount'),
        ]);

        // Redirect back with success message
        return redirect()->route('reimbursements.index')->with('success', 'Reimbursement updated successfully!');
    }

    // Delete a reimbursement
    public function destroy($id)
    {
        $companyId = Auth::id();
        $reimbursement = Reimbursement::where('id', $id)->where('company_id', $companyId)->firstOrFail();
        if($reimbursement->receipt_file){
                $oldDocumentPath = public_path('uploads/receipt/' . $reimbursement->receipt_file);
                if (file_exists($oldDocumentPath)) {
                    unlink($oldDocumentPath);
                }
            }
        $reimbursement->delete();

        return redirect()->route('reimbursements.index')->with('success', 'Reimbursement deleted successfully!');
    }

    // Toggle the status of a reimbursement
    public function statuschange(Request $request)
    {
        $companyId = Auth::id();
        $id = $request->input('id');
        $status = $request->input('status');
        $reimbursement = Reimbursement::where('id', $id)->where('company_id', $companyId)->firstOrFail();
        $reimbursement->status = $status;
        $reimbursement->save();

        return redirect()->route('reimbursements.index')->with('success', 'Reimbursement '.$status.' successfully!');
    }
}