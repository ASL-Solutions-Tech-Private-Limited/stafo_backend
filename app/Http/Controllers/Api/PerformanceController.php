<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerformanceType;
use App\Models\EmployeePerformances;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    public function list(Request $request)
    {
        try {
            //$company_id = $request->company_id;
            $companyId = Auth::id();
            $performancetypes = PerformanceType::where('company_id', $companyId)->get();

            return response()->json([
                'success' => true,
                'message' => 'Record Display successfully.',
                'data' => $performancetypes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching records.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $userId = Auth::id();
            $performance = PerformanceType::create([
                'company_id' => $userId,
                'name' => $request->name,
                'description' => $request->description,
                'status' => '1',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Record added successfully.',
                'data' => $performance
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching records.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
        $Performancetypes = PerformanceType::findOrFail($request->id);
        $Performancetypes->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully.',
                'data' => $Performancetypes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching records.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $Performancetypes = PerformanceType::findOrFail($id);
            $Performancetypes->delete(); 

            return response()->json([
                'success' => true,
                'message' => 'Record added successfully.',
                
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching .',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveEmployeePerformance(Request $request)
    {
        try {
            
            $performances = $request->input('performances', []);

            foreach($performances as $perform){
                
                $performance = new EmployeePerformances();
                $performance->company_id = Auth::id();
                $performance->employee_id = $request->emp_id;
                $performance->month = $request->month;
                $performance->year = $request->year;
                $performance->performance_type_id  = $perform['type_id'];
                $performance->marks = $perform['points'];
                $performance->save();
                
            }
            

            return response()->json([
                'success' => true,
                'message' => 'Record added successfully.',
                
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching .',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function rankList(Request $request)
    {
        try {
        $month = $request->input('month',date('m'));
        $year = $request->input('year',date('Y'));
        $employee_id = $request->input('employee_id','');
        $companyId = Auth::id();        
        $employees = Employee::where('company_id', $companyId)->select('id','name')->get();
        $employeeQuery = EmployeePerformances::with(['employee' => function($query) {
            $query->select('id', 'emp_id', 'name','email','phone'); // Only select these columns from the Post model
        }])->select(  'employee_id', 'month',         
            \DB::raw('SUM(marks) as total_marks') 
        )->groupBy('month','year','employee_id')->where('company_id', $companyId)->where('month',$month)->where('year',$year);
        if($employee_id != ''){
            $employeeQuery->where('employee_id', $employee_id);
        }
        $employeeLists = $employeeQuery->orderBy('total_marks', 'DESC')->get();

            return response()->json([
                'success' => true,
                'message' => 'Record listed successfully.',
                'ranklist' => $employeeLists,                
                'month' => $month,
                'year' => $year,
                'employee_id' => $employee_id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching .',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function rankDetails(Request $request)
    {
        try {
            
        $month = $request->input('month',date('m'));
        $year = $request->input('year',date('Y'));
        $emp_id = $request->input('employee_id');
        $companyId = Auth::id();        
        $employeePerformances = EmployeePerformances::with(['performancetype' => function($query) {
            $query->select('id', 'name','description'); // Only select these columns from the Post model
        }])->where('company_id', $companyId)->where('employee_id',$emp_id)->where('month',$month)->where('year',$year)->get(); 
            

            return response()->json([
                'success' => true,
                'message' => 'Record show successfully.',
                'data' => $employeePerformances,
                'month' => $month,
                'year' => $year,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching .',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}