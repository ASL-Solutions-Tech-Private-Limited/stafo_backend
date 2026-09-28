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
    /**
     * @OA\Get(
     *     path="/api/lead/list",
     *     summary="Get lead list",
     *     tags={"Leads"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         required=false,
     *         description="Company ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="employee_id",
     *         in="query",
     *         required=false,
     *         description="Employee ID",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Record fetched successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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





    /**
     * @OA\Post(
     *     path="/api/lead/create",
     *     summary="Create a new lead",
     *     tags={"Leads"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id","name"},
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="employee_id", type="integer", example=5),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="9876543210"),
     *             @OA\Property(property="status", type="string", example="New"),
     *             @OA\Property(property="next_date", type="string", format="date", example="2026-09-30")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Record added successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/lead/update/{id}",
     *     summary="Update a lead",
     *     tags={"Leads"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Lead ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="9876543210"),
     *             @OA\Property(property="status", type="string", example="In Progress"),
     *             @OA\Property(property="next_date", type="string", format="date", example="2026-10-05")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Record updated successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/lead/delete/{id}",
     *     summary="Delete a lead",
     *     tags={"Leads"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Lead ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Record deleted successfully."),
     *             @OA\Property(property="status", type="boolean", example=true)
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/lead/followup-create",
     *     summary="Create a lead follow-up",
     *     tags={"Leads"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"lead_id","next_date"},
     *             @OA\Property(property="lead_id", type="integer", example=1),
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="employee_id", type="integer", example=5),
     *             @OA\Property(property="next_date", type="string", format="date", example="2026-10-01"),
     *             @OA\Property(property="remarks", type="string", example="Client asked to call back next week")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Record added successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/lead/followup-list",
     *     summary="Get lead follow-ups list",
     *     tags={"Leads"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="lead_id",
     *         in="query",
     *         required=true,
     *         description="Lead ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Record fetched successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/lead/dashboard",
     *     summary="Get lead dashboard statistics",
     *     tags={"Leads"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         required=false,
     *         description="Company ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="employee_id",
     *         in="query",
     *         required=false,
     *         description="Employee ID",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Record fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_leads", type="integer", example=15),
     *                 @OA\Property(property="total_followups", type="integer", example=42),
     *                 @OA\Property(property="total_followups_today", type="integer", example=3),
     *                 @OA\Property(property="total_followups_today_list", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
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