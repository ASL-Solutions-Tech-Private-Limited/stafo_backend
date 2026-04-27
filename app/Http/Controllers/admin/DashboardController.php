<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\CompanyDetail;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    //

    public function index()
    {
        $companyCount = CompanyDetail::count();
        $employeeCount = Employee::count();
        //dd($employeeCount);
        return view('admin.dashboard', compact('companyCount', 'employeeCount'));
    }
}