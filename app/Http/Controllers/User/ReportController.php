<?php

namespace App\Http\Controllers\User;

use App\Exports\LeaveExport;
use Illuminate\Http\Request;
use App\Exports\EmployeeExport;
use App\Exports\EmployeePDFExport;
use App\Exports\AttendanceExport;
use App\Exports\AttendancePDFExport;
use App\Exports\LeavePDFExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Department;
use App\Models\Branch;

class ReportController extends Controller
{

    /**
     * Display the listing of the resource.
     */
    public function index()
    {
        $companyId = Auth::id();
        $departments = Department::where('company_id', $companyId)->get();
        $branches = Branch::where('company_id', $companyId)->get();

        return view('user.report.index', compact('departments', 'branches'));
    }

    /**
     * Export Employee Report
     */
    public function exportEmployee(Request $request)
    {
        // $month = $request->month;
        // $year = $request->year;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $format = $request->format;
        $departmentId = $request->department;
        $branchId = $request->branch;


        $companyId = Auth::id();

        //  dd($companyId);

        if ($format === 'excel') {
            $export = new EmployeeExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export(); // Export the Excel file
        } elseif ($format === 'pdf') {
            $export = new EmployeePdfExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export();
        } else {
            return redirect()->back()->with('error', 'Invalid format selected');
        }
    }
    /**
     * Export Attendance Report
     */
    public function exportAttendance(Request $request)
    {

        // $month = $request->month;
        // $year = $request->year;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;
        $departmentId = $request->department;
        $branchId = $request->branch;


        $companyId = Auth::id();


        if ($format === 'excel') {
            $export = new AttendanceExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export();
        } elseif ($format === 'pdf') {
            $export = new AttendancePDFExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export();
        } else {
            return redirect()->back()->with('error', 'Invalid format selected');
        }
    }

    /**
     * Export Leave Report
     */
    public function exportLeave(Request $request)
    {

        // dd($request->all());
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $format = $request->format;
        $departmentId = $request->department;
        $branchId = $request->branch;

        $companyId = Auth::id();

        if ($format === 'excel') {
            $export = new LeaveExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export();
        } elseif ($format === 'pdf') {
            $export = new LeavePDFExport($startDate, $endDate, $companyId, $departmentId, $branchId);
            return $export->export();
        } else {
            return redirect()->back()->with('error', 'Invalid format selected');
        }
    }
}