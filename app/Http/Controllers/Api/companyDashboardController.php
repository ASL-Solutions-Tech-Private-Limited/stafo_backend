<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\AppBanner;
use App\Models\CompanyDetail;
use App\Models\EmployeeLeave;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\PackageFeature;

class companyDashboardController extends Controller
{
    public function dashboard()
    {
        $company_id = Auth::id();
        // dd($company_id);
        $today = date('Y-m-d');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $employeeCount = Employee::where('company_id', $company_id)->count();
        $presentCount = Attendance::where('company_id', $company_id)->where('date', $today)->count();
        $employeesOnLeave = EmployeeLeave::select('id', 'employee_id', 'from_date', 'to_date', 'reason', 'leave_type')->with(['employeeBasicInfo'])->where('company_id', $company_id)->where('status', 'approved')->whereMonth('from_date', $currentMonth)->whereYear('from_date', $currentYear)->where('from_date','>=', $today)->get();
        $birthday = Employee::select('id','emp_id','date_of_birth','name','email','phone','image')->where('company_id', $company_id)->whereMonth('date_of_birth', date('m'))->where('date_of_birth','>=', $today)->get();
        $annyversary = Employee::select('id','emp_id','date_of_joining','name','email','phone','image')->where('company_id', $company_id)->whereMonth('date_of_joining', date('m'))->get();
        $companyInfo = CompanyDetail::select('id','company_name','company_code','is_verified','package_id','subscription_start','subscription_end', 'max_employee_add')->where('id', $company_id)->first();
        $packagefeature = PackageFeature::with('features')->where('package_id', $companyInfo->package_id)->get();
        $maxEmployeeAdd = $this->sitesetting(5);
        return response()->json([
            'status' => true,
            'message' => 'Record fetched successfully',
            'employeeCount' => $employeeCount,
            'presentCount' => $presentCount,
            'employeesOnLeave' => $employeesOnLeave,
            'birthday' => $birthday,
            'annyversary' => $annyversary,
            'company_id' => $company_id,
            'companyInfo' => $companyInfo,
            'maxEmployeeAdd' => $maxEmployeeAdd,
            'packagefeature' => $packagefeature,
            'package_id' => $companyInfo->package_id,
        ], 200);
    }


    public function employeesOnLeave()
    {
        $company_id = Auth::id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $employeesOnLeave = EmployeeLeave::select('id', 'employee_id', 'from_date', 'to_date', 'reason', 'leave_type')->with(['employeeBasicInfo'])->where('company_id', $company_id)->where('status', 'approved')->whereMonth('from_date', $currentMonth)->whereYear('from_date', $currentYear)->get();
        return response()->json(['leave' => $employeesOnLeave]);
    }

    public function appbanner(){
       $appbanner =  AppBanner::where('type', '1')->where('status', '1')->get();
       $data = [];
       if($appbanner){
        $data['banner'] = $appbanner;
        $data['path'] = asset('uploads/appbanner/');
       }
       return response()->json([
            'status' => true,        
            'data' => $data
        ], 200);
    }

    public function referralList(){
        $company_id = Auth::id();
        //$company_id = '18';
        $company = CompanyDetail::findOrFail($company_id);  
        $rcount = $company->referrals->count();
        $rlist = $company->referrals()->select('id','company_name','company_code','email','mobile_no')->get();
      
        $referral = $company->referrals->count();
        return response()->json([
            'status' => true,
            'message' => 'Record fetched successfully',
            'referral_code' => $company->referral_code,
            'referral_count' => $rcount,
            'referral_list' => $rlist,

        ], 200);
    }
}