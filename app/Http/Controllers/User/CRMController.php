<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Employee;
use App\Models\LeadFollowup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CRMController extends Controller
{
    public function leadList()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $companyId = Auth::id();
        $leads = Lead::where('company_id', $companyId)->get();
        return view('user.crm.index', compact('leads')); // Return the view with all salarytypes
    }

    public function leadCreate()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $employees = Employee::where('company_id', Auth::id())->get(); // Fetch all employees for the dropdown
        return view('user.crm.create', compact('employees')); // Return the view to create a new lead
    }


    public function leadStore(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'employee_id' => 'required',
            'name' => 'required',
        ]);
        $userId = Auth::id();
        Lead::create([
            'company_id' => $userId,
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'email' => $request->email,
            'phone' => $request->phone,
            'notes' => $request->notes,
            'status' => 'New',
            'lead_from' => $request->lead_from,
            'next_date' => $request->next_date,
        ]);
        return redirect()->route('leadList')->with('success', 'Record created successfully.');
    }


    public function leadEdit($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $lead = Lead::findOrFail($id); // Fetch the salarytype to edit
        $employees = Employee::where('company_id', Auth::id())->get();
        return view('user.crm.edit', compact('lead','employees')); // Return the edit view
    }

    public function leadUpdate(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'employee_id' => 'required',
            'name' => 'required',
        ]);

        $userId = Auth::id();
        $lead = Lead::findOrFail($id);
        $lead->update([
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'email' => $request->email,
            'phone' => $request->phone,
            'notes' => $request->notes,
            'lead_from' => $request->lead_from,
            'next_date' => $request->next_date,
        ]);

        // Redirect back with success message
        return redirect()->route('leadList')->with('success', 'Record updated successfully.');
    }


    public function destroy($id)
    {
        $salarytype = Lead::findOrFail($id); // Find the salarytype to delete
        $salarytype->delete(); // Delete the salarytype

        return redirect()->route('leadList')->with('success', 'Record deleted successfully.');
    }

    public function leadFollowup($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $lead = Lead::findOrFail($id); // Fetch the salarytype to edit
        $followups = LeadFollowup::where('lead_id', $id)->get();
        return view('user.crm.followup', compact('lead','followups')); // Return the edit view
    }

    public function leadFollowupCreate($lead_id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }        
        return view('user.crm.followup-create', compact('lead_id')); // Return the view to create a new lead
    }


    public function leadFollowupStore($lead_id,Request $request)
    {
        // Validate the incoming request
        $lead = Lead::findOrFail($lead_id); 
        $userId = Auth::id();
        LeadFollowup::create([
            'company_id' => $lead->company_id,
            'employee_id' => $lead->employee_id,
            'lead_id' => $lead_id,
            'type' => $request->type,
            'next_date' => $request->next_date,
            'status' => $request->status,
            'remarks' => $request->remarks
        ]);
        return redirect()->route('leadFollowup',$lead_id)->with('success', 'Record created successfully.');
    }

    public function followupEdit($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $followup = LeadFollowup::findOrFail($id); // Fetch the salarytype to edit
        return view('user.crm.followup-edit', compact('followup')); // Return the edit view
    }

    public function followupUpdate(Request $request, $id)
    {
        
        $userId = Auth::id();
        $LeadFollowup = LeadFollowup::findOrFail($id);
        $LeadFollowup->update([
            'type' => $request->type,
            'next_date' => $request->next_date,
            'status' => $request->status,
            'remarks' => $request->remarks
        ]);

        // Redirect back with success message
        return redirect()->route('leadFollowup',$LeadFollowup->lead_id)->with('success', 'Record updated successfully.');
    }

    
}