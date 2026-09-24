<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\CompanyDetail;
use Illuminate\Support\Facades\Auth;


use App\Models\Ticket;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $companyCount = CompanyDetail::count();
        $employeeCount = Employee::count();
        $ticketCount = class_exists('App\Models\Ticket') ? Ticket::count() : 0;
        $todayAttendanceCount = class_exists('App\Models\Attendance') ? Attendance::whereDate('created_at', date('Y-m-d'))->count() : 0;
        $recentCompanies = CompanyDetail::orderBy('id', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'companyCount',
            'employeeCount',
            'ticketCount',
            'todayAttendanceCount',
            'recentCompanies'
        ));
    }
}