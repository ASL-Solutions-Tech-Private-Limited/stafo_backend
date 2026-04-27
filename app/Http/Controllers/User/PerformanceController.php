<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PerformanceType;
use App\Models\EmployeePerformances;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    public function performancetypeList()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $companyId = Auth::id();
        $performancetypes = PerformanceType::where('company_id', $companyId)->get();
        return view('user.performance.performancetype_list', compact('performancetypes')); 
    }

    public function performancetypeCreate()
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        return view('user.performance.performancetype_create');
    }


    public function performancetypeStore(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string',
        ]);
        $userId = Auth::id();
        PerformanceType::create([
            'company_id' => $userId,
            'name' => $request->name,
            'description' => $request->description,
            'status' => '1',
        ]);
        return redirect()->route('performancetypeList')->with('success', 'Record added successfully.');
    }


    public function performancetypeEdit($id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $Performancetype = PerformanceType::findOrFail($id);
        return view('user.performance.performancetype_edit', compact('Performancetype'));
    }

    public function performancetypeUpdate(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string',
        ]);

        $userId = Auth::id();
        $Performancetypes = PerformanceType::findOrFail($id);
        $Performancetypes->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Redirect back with success message
        return redirect()->route('performancetypeList')->with('success', 'Record updated successfully.');
    }


    public function performancetypeDelete($id)
    {
        $Performancetypes = PerformanceType::findOrFail($id);
        $Performancetypes->delete(); 

        return redirect()->route('performancetypeList')->with('success', 'Record deleted successfully.');
    }

    public function employeePerformanceAdd($emp_id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $companyId = Auth::id();
        $employees = Employee::where('company_id', $companyId)->where('id',$emp_id)->first();
        $Performancetypes = PerformanceType::where('status','1')->where('company_id', $companyId)->get();
        return view('user.performance.employee_performance_add', compact('employees','Performancetypes'));
    }

    public function saveEmployeePerformance($emp_id,Request $request)
    {
        // $request->validate([
        //     'month' => 'required',
        // ]);
        $companyId = Auth::id();
        $performancetypes = PerformanceType::where('status','1')->where('company_id', $companyId)->get();
            
        if(!$performancetypes->isEmpty()){

            foreach($performancetypes as $performancetype){
                
                $performance = new EmployeePerformances();
                $performance->company_id = Auth::id();
                $performance->employee_id = $emp_id;
                $performance->month = $request->month;
                $performance->year = $request->year;
                $performance->performance_type_id  = $performancetype->id;
                $performance->marks = $request->input('performence_type_'.$performancetype->id);
                $performance->save();
                
            }
        }   
        return redirect()->route('employee.index')->with('success', 'Record saved successfully.');
    }

    public function employeePerformance($emp_id)
    {
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $companyId = Auth::id();
        $employee = Employee::where('company_id', $companyId)->select('id','name')->where('id',$emp_id)->first();
        $employeePerformances = EmployeePerformances::where('company_id', $companyId)->where('employee_id',$emp_id)->orderBy('month')->orderBy('year')->get();     
        return view('user.performance.employee_performance', compact('employee','employeePerformances')); 
    }

    public function employeeRankList(Request $request){
        $is_verified = Auth::user()->is_verified;
        if ($is_verified == 'No') {
            return view('user.verify_check');
        }
        $month = $request->input('month',date('m'));
        $year = $request->input('year',date('Y'));
        $employee_id = $request->input('employee_id','');
        $companyId = Auth::id();        
        $employees = Employee::where('company_id', $companyId)->select('id','name')->get();
        $employeeQuery = EmployeePerformances::select(  'employee_id', 'month',         
            \DB::raw('SUM(marks) as total_marks') 
        )->groupBy('month','year','employee_id')->where('company_id', $companyId)->where('month',$month)->where('year',$year);
        if($employee_id != ''){
            $employeeQuery->where('employee_id', $employee_id);
        }
        $employeeLists = $employeeQuery->orderBy('total_marks', 'DESC')->get();
        return view('user.performance.rank_list', compact('employeeLists','employees','month','year','employee_id')); 
    }

    public function employeeRankDetails(Request $request){        
        $month = $request->input('month',date('m'));
        $year = $request->input('year',date('Y'));
        $emp_id = $request->input('employee_id');
        $companyId = Auth::id();        
        $employeePerformances = EmployeePerformances::where('company_id', $companyId)->where('employee_id',$emp_id)->where('month',$month)->where('year',$year)->get();   
        $html = '';
        $html .= '<table class="table table-bordered">';
        foreach($employeePerformances as $performance)
        {
            $html .= '<tr>';
            $html .= '<td>'.$performance->performancetype->name.'</td>';
            $html .= '<td>'.$performance->marks.'</td>';
            $html .= '</tr>';

        }
        $html .= '</table>';
        return response()->json(['html' => $html]);
        
    }

}