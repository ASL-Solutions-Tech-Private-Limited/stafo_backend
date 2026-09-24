<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Models\EmployeeLeave;
use App\Models\Department;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $company_id = Auth::id();
        $today = date('Y-m-d');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Fetch data from models
        $employeeCount = Employee::where('company_id', $company_id)->count();
        $presentCount = Attendance::where('date', $today)->where('company_id', $company_id)->count();
        
        // Today's half-day count if any
        $halfDayCount = Attendance::where('date', $today)
            ->where('company_id', $company_id)
            ->where('halfday', 1)
            ->count();

        // Today's leaves (approved leaves active today)
        $todayLeavesCount = EmployeeLeave::where('company_id', $company_id)
            ->where('status', 'approved')
            ->where('from_date', '<=', $today)
            ->where('to_date', '>=', $today)
            ->count();

        // Full present count (present minus half day)
        $fullPresentCount = max(0, $presentCount - $halfDayCount);

        // Absent calculation: remaining employees not present and not on approved leave
        $absentCount = $employeeCount - $presentCount - $todayLeavesCount;
        if ($absentCount < 0) {
            $absentCount = max(0, $employeeCount - $presentCount);
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

        $todayCarbon = Carbon::now()->startOfDay();

        // Calculate Work Anniversary & Days remaining/since joining for all company employees
        $allAnniversaries = Employee::select('id', 'emp_id', 'date_of_joining', 'name', 'email', 'phone', 'image', 'department_id')
            ->with(['department:id,name'])
            ->where('company_id', $company_id)
            ->whereNotNull('date_of_joining')
            ->get()
            ->map(function ($emp) use ($todayCarbon) {
                $doj = Carbon::parse($emp->date_of_joining)->startOfDay();
                $thisYearAnniversary = Carbon::create($todayCarbon->year, $doj->month, $doj->day)->startOfDay();

                if ($thisYearAnniversary->lessThan($todayCarbon)) {
                    $nextAnniversary = Carbon::create($todayCarbon->year + 1, $doj->month, $doj->day)->startOfDay();
                } else {
                    $nextAnniversary = $thisYearAnniversary;
                }

                $daysUntil = (int) $todayCarbon->diffInDays($nextAnniversary, false);
                $yearsCompleting = max(1, $nextAnniversary->year - $doj->year);
                $daysSinceJoining = (int) $doj->diffInDays($todayCarbon);
                $yearsCompleted = (int) $doj->diffInYears($todayCarbon);

                $emp->days_until_anniversary = $daysUntil;
                $emp->next_anniversary_date = $nextAnniversary;
                $emp->years_completing = $yearsCompleting;
                $emp->days_since_joining = $daysSinceJoining;
                $emp->years_completed = $yearsCompleted;
                $emp->is_anniversary_today = ($daysUntil === 0);
                $emp->is_this_month = ($nextAnniversary->month === $todayCarbon->month && $nextAnniversary->year === $todayCarbon->year);

                return $emp;
            })
            ->sortBy('days_until_anniversary')
            ->values();

        // Employees with missing DOJ for full roster visibility
        $pendingDojEmployees = Employee::select('id', 'emp_id', 'name', 'email', 'phone', 'image', 'department_id')
            ->with(['department:id,name'])
            ->where('company_id', $company_id)
            ->whereNull('date_of_joining')
            ->get();

        $anniversary = $allAnniversaries;

        // Department breakdown for HRMS Pie / Donut Chart
        $departmentStats = Employee::where('employees.company_id', $company_id)
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->selectRaw('COALESCE(departments.name, "General Operations") as dept_name, count(employees.id) as total')
            ->groupBy('dept_name')
            ->orderByDesc('total')
            ->pluck('total', 'dept_name')
            ->toArray();

        // Leave type breakdown for HRMS Pie / Donut Chart
        $leaveTypeStats = EmployeeLeave::where('company_id', $company_id)
            ->where('status', 'approved')
            ->whereYear('from_date', $currentYear)
            ->selectRaw('COALESCE(NULLIF(leave_type, ""), "Casual Leave") as type_name, count(id) as total')
            ->groupBy('type_name')
            ->orderByDesc('total')
            ->pluck('total', 'type_name')
            ->toArray();

        // Upcoming holidays for dashboard ticker (fetch up to 8 so all upcoming festive holidays like Maha Dashmi are included)
        $upcomingHolidays = \App\Models\Holiday::where('company_id', $company_id)
            ->where('start_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->take(8)
            ->get();

        // Return the view with the data
        return view('dashboard', compact(
            'employeeCount',
            'presentCount',
            'absentCount',
            'halfDayCount',
            'todayLeavesCount',
            'fullPresentCount',
            'employeesOnLeave',
            'birthday',
            'anniversary',
            'allAnniversaries',
            'pendingDojEmployees',
            'departmentStats',
            'leaveTypeStats',
            'upcomingHolidays'
        ));
    }
}