<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Lead;
use App\Models\LeadFollowup;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LeadController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }
    public function index(Request $request)
    {

        try {
            $companyId = $request->company_id;
            $employeeId = $request->employee_id;
            $leads = Lead::with(['company','employee'])->when($request->company_id, fn($q) => $q->where('company_id', $companyId))->when($request->employee_id, fn($q) => $q->where('employee_id', $employeeId))->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Record fetched successfully.',
                'data' => $leads
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the branches.',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    // Create a new branch
    public function store(Request $request)
    {
        try {

            // Create the branch
            $lead = Lead::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Record added successfully.',
                'data' => $lead
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the branch.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update an existing branch
    public function update(Request $request, $id)
    {
        try {
            
            $lead = Lead::find($id);

            if (!$lead) {
                throw new ModelNotFoundException("Record not found.");
            }
            $lead->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully.',
                'data' => $lead
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the branch.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a branch
    public function destroy($id)
    {
        try {
            $lead = Lead::find($id);

            if (!$lead) {
                throw new ModelNotFoundException("Lead not found.");
            }
            $lead->delete();

            return response()->json([
                'message' => 'Record deleted successfully.',
                'status' => true // Indicating successful operation
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => false
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the record.',
                'error' => $e->getMessage(),
                'status' => false // Indicating failure due to general error
            ], 500);
        }
    }

    public function followupStore(Request $request)
    {
        try {

            // Create the branch
            $lead = LeadFollowup::create($request->all());
            Lead::where('id', $request->lead_id)->update(['next_date' => $request->next_date]);
            return response()->json([
                'message' => 'Record added successfully.',
                'data' => $lead
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => true,
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the branch.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function followupList(Request $request)
    {

        try {
            $leadId = $request->lead_id;
            $leads = LeadFollowup::with(['company','employee'])->where('lead_id', $leadId)->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Record fetched successfully.',
                'data' => $leads
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the branches.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function dashboard(Request $request)
    {
        try {
            $companyId = $request->company_id;
            $employeeId = $request->employee_id;
            $leads = Lead::when($request->company_id, fn($q) => $q->where('company_id', $companyId))->when($request->employee_id, fn($q) => $q->where('employee_id', $employeeId))->get();
            
            $totalLeads = $leads->count();  
            $totalFollowups = LeadFollowup::when($request->company_id, fn($q) => $q->where('company_id', $companyId))->when($request->employee_id, fn($q) => $q->where('employee_id', $employeeId))->count();
            $totalFollowupsToday = Lead::with(['company','employee'])->when($request->company_id, fn($q) => $q->where('company_id', $companyId))->when($request->employee_id, fn($q) => $q->where('employee_id', $employeeId))->whereDate('next_date', now()->format('Y-m-d'))->get();
           
            
            return response()->json([
                'success' => true,
                'message' => 'Record fetched successfully.',
                'data' => [
                    'total_leads' => $totalLeads,
                    'total_followups' => $totalFollowups,
                    'total_followups_today' => $totalFollowupsToday->count(),
                    'total_followups_today_list' => $totalFollowupsToday
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the branches.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}