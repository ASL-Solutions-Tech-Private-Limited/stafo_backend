<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\EmployeeLeave;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //

    // public function index()
    // {
    //     $userId = Auth::id();

    //     return view('dashboard', compact('userId'));
    // }

    public function index()
    {
        $company_id = Auth::id();
        $today = date('Y-m-d');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Fetch data from models
        $employeeCount = Employee::where('company_id', $company_id)->count();
        $presentCount = Attendance::where('date', $today)->where('company_id', $company_id)->count();
        $absentCount = $employeeCount - $presentCount;
        if ($absentCount < 0) {
            $absentCount = 0;
        }
        $employeesOnLeave = EmployeeLeave::select('id', 'employee_id', 'from_date', 'to_date', 'reason', 'leave_type')
            ->with(['employeeBasicInfo'])
            ->where('company_id', $company_id)
            ->where('status', 'approved')
            ->whereMonth('from_date', $currentMonth)
            ->whereYear('from_date', $currentYear)
            ->get();
        $birthday = Employee::select('id', 'emp_id', 'date_of_birth', 'name', 'email', 'phone', 'image')
            ->where('company_id', $company_id)
            ->whereMonth('date_of_birth', date('m'))
            ->get();
        $anniversary = Employee::select('id', 'emp_id', 'date_of_joining', 'name', 'email', 'phone', 'image')
            ->where('company_id', $company_id)
            ->whereMonth('date_of_joining', date('m'))
            ->get();

        // dd($presentCount);

        // Return the view with the data
        return view('dashboard', compact(
            'employeeCount',
            'presentCount',
            'absentCount',
            'employeesOnLeave',
            'birthday',
            'anniversary',
        ));
    }
}