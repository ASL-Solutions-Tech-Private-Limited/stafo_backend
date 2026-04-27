<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Employee;
use App\Models\CompanyDetail;
use App\Models\LeadFollowup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCRMController extends Controller
{
    public function lead(){
        $companies = CompanyDetail::select('id','company_name')->get();
        return view('admin.crm.lead', compact('companies'));
    }
    public function leadList($companyId)
    {
        $companies = CompanyDetail::select('id','company_name')->get();
        //$companyId = $request->company_id;
        $leads = Lead::where('company_id', $companyId)->get();
        return view('admin.crm.index', compact('leads','companies','companyId')); // Return the view with all salarytypes
    }

    public function leadCreate($companyId)
    {
        
        $employees = Employee::where('company_id', $companyId)->get(); // Fetch all employees for the dropdown
        return view('admin.crm.create', compact('employees','companyId')); // Return the view to create a new lead
    }


    public function leadStore(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'employee_id' => 'required',
            'name' => 'required',
        ]);
        $company_id = $request->company_id;
        Lead::create([
            'company_id' =>$company_id,
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
        return redirect()->route('admin.leadList',$company_id)->with('success', 'Record created successfully.');
    }


    public function leadEdit($id)
    {
        
        $lead = Lead::findOrFail($id); // Fetch the salarytype to edit
        $company_id = $lead->company_id;
        $employees = Employee::where('company_id', $company_id)->get();
        return view('admin.crm.edit', compact('lead','employees')); // Return the edit view
    }

    public function leadUpdate(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'employee_id' => 'required',
            'name' => 'required',
        ]);

        
        $lead = Lead::findOrFail($id);
        $company_id = $lead->company_id;
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
        return redirect()->route('admin.leadList',$company_id)->with('success', 'Record updated successfully.');
    }


    public function destroy($id)
    {
        $salarytype = Lead::findOrFail($id); // Find the salarytype to delete
        $salarytype->delete(); // Delete the salarytype

        return redirect()->route('admin.leadList')->with('success', 'Record deleted successfully.');
    }

    public function leadFollowup($id)
    {
       
        $lead = Lead::findOrFail($id); // Fetch the salarytype to edit
        $followups = LeadFollowup::where('lead_id', $id)->get();
        return view('admin.crm.followup', compact('lead','followups')); // Return the edit view
    }

    public function leadFollowupCreate($lead_id)
    {
             
        return view('admin.crm.followup-create', compact('lead_id')); // Return the view to create a new lead
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
        return redirect()->route('admin.leadFollowup',$lead_id)->with('success', 'Record created successfully.');
    }

    public function followupEdit($id)
    {
        $followup = LeadFollowup::findOrFail($id); // Fetch the salarytype to edit
        return view('admin.crm.followup-edit', compact('followup')); // Return the edit view
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
        return redirect()->route('admin.leadFollowup',$LeadFollowup->lead_id)->with('success', 'Record updated successfully.');
    }

    
}