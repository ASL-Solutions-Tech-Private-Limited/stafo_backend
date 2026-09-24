<!-- Left Sidebar -->
<aside class="sidebar p-3" id="sidebarNav">
    <div class="mb-2 px-1">
        <div class="sidebar-category-label">
            <span class="indicator-pill"></span>
            <span>Employee Menu</span>
        </div>
    </div>

    @php
        $navEmp = Auth::guard('employee')->user();
    @endphp

    <nav class="d-flex flex-column">
        <a href="{{ route('employee.dashboard') }}" class="employee-nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-gauge-high"></i></span>
            <span>Dashboard</span>
        </a>

        @if(!$navEmp || $navEmp->hasPermission('profile.view'))
        <a href="{{ route('employee.profile') }}" class="employee-nav-item {{ request()->routeIs('employee.profile') ? 'active' : '' }}">
            <span class="nav-icon text-primary"><i class="fa-solid fa-id-badge"></i></span>
            <span>My Profile</span>
        </a>
        @endif

        @if(!$navEmp || $navEmp->hasPermission('attendance.view') || $navEmp->hasPermission('attendance.punch'))
        <a href="{{ route('employee.attendance') }}" class="employee-nav-item {{ request()->routeIs('employee.attendance') ? 'active' : '' }}">
            <span class="nav-icon text-success"><i class="fa-solid fa-clipboard-user"></i></span>
            <span>Attendance</span>
        </a>
        @endif

        @if(!$navEmp || $navEmp->hasPermission('leaves.view') || $navEmp->hasPermission('leaves.apply'))
        <a href="{{ route('employee.leaves') }}" class="employee-nav-item {{ request()->routeIs('employee.leaves*') ? 'active' : '' }}">
            <span class="nav-icon text-warning"><i class="fa-solid fa-calendar-days"></i></span>
            <span>Leave Requests</span>
        </a>
        @endif

        @if(!$navEmp || $navEmp->hasPermission('tasks.view') || $navEmp->hasPermission('tasks.update_status'))
        <a href="{{ route('employee.tasks') }}" class="employee-nav-item {{ request()->routeIs('employee.tasks*') ? 'active' : '' }}">
            <span class="nav-icon text-info"><i class="fa-solid fa-list-check"></i></span>
            <span>My Assigned Tasks</span>
        </a>
        @endif

        @if(!$navEmp || $navEmp->hasPermission('payroll.self') || $navEmp->hasPermission('payroll.download') || $navEmp->hasPermission('payroll.view'))
        <a href="{{ route('employee.salarySlips') }}" class="employee-nav-item {{ request()->routeIs('employee.salarySlips*') ? 'active' : '' }}">
            <span class="nav-icon text-success"><i class="fa-solid fa-file-invoice-dollar"></i></span>
            <span>Salary Slips</span>
        </a>
        @endif

        @if(!$navEmp || $navEmp->hasPermission('documents.view'))
        <a href="{{ route('employee.documents') }}" class="employee-nav-item {{ request()->routeIs('employee.documents*') ? 'active' : '' }}">
            <span class="nav-icon text-secondary"><i class="fa-solid fa-folder-open"></i></span>
            <span>Documents & KYC</span>
        </a>
        @endif

        <a href="{{ route('employee.holidays') }}" class="employee-nav-item {{ request()->routeIs('employee.holidays*') ? 'active' : '' }}">
            <span class="nav-icon text-danger"><i class="fa-solid fa-umbrella-beach"></i></span>
            <span>Holiday Calendar</span>
        </a>

        @php
            $canMgmtBranches = $navEmp && $navEmp->hasPermission('branches.view');
            $canMgmtAttendance = $navEmp && $navEmp->hasPermission('attendance.view');
            $canMgmtShifts = $navEmp && $navEmp->hasPermission('shifts.view');
            $canMgmtEmployees = $navEmp && $navEmp->hasPermission('employees.view');
            $canMgmtDepartments = $navEmp && $navEmp->hasPermission('departments.view');
            $canMgmtDesignations = $navEmp && $navEmp->hasPermission('designations.view');
            $canMgmtLeaves = $navEmp && $navEmp->hasPermission('leaves.view');
            $canMgmtPayroll = $navEmp && $navEmp->hasPermission('payroll.view');
            $canMgmtTasks = $navEmp && $navEmp->hasPermission('tasks.view');
            $canMgmtReports = $navEmp && $navEmp->hasPermission('reports.view');

            $hasAnyMgmtRights = $canMgmtBranches || $canMgmtAttendance || $canMgmtShifts || $canMgmtEmployees || $canMgmtDepartments || $canMgmtDesignations || $canMgmtLeaves || $canMgmtPayroll || $canMgmtTasks || $canMgmtReports;

            $navPendingLeaves = 0;
            if ($canMgmtLeaves && $navEmp && !empty($navEmp->company_id)) {
                try {
                    $navPendingLeaves = \App\Models\EmployeeLeave::where('company_id', $navEmp->company_id)->where('status', 'pending')->count();
                } catch (\Exception $e) {
                    $navPendingLeaves = 0;
                }
            }
        @endphp

        @if($hasAnyMgmtRights)
            <div class="mt-3 mb-2 px-1 pt-3 border-top border-secondary border-opacity-10">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="sidebar-category-label px-0">
                        <span class="indicator-pill" style="background: #f59e0b;"></span>
                        <span>Management Rights</span>
                    </div>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.62rem;">
                        {{ $navEmp->designation->name ?? 'Role Access' }}
                    </span>
                </div>
            </div>

            @if($canMgmtBranches)
            <a href="{{ route('employee.management.branches') }}" class="employee-nav-item {{ request()->routeIs('employee.management.branches*') ? 'active' : '' }}">
                <span class="nav-icon text-success"><i class="fa-solid fa-building"></i></span>
                <span>Branch</span>
            </a>
            @endif

            @if($canMgmtAttendance)
            <a href="{{ route('employee.management.attendance') }}" class="employee-nav-item {{ request()->routeIs('employee.management.attendance*') ? 'active' : '' }}">
                <span class="nav-icon text-success"><i class="fa-solid fa-clock-rotate-left"></i></span>
                <span>Emp Attendance</span>
            </a>
            @endif

            @if($canMgmtShifts)
            <a href="{{ route('employee.management.shifts') }}" class="employee-nav-item {{ request()->routeIs('employee.management.shifts*') ? 'active' : '' }}">
                <span class="nav-icon text-primary"><i class="fa-solid fa-business-time"></i></span>
                <span>Shift</span>
            </a>
            @endif

            @if($canMgmtEmployees)
            <a href="{{ route('employee.management.employees') }}" class="employee-nav-item {{ request()->routeIs('employee.management.employees*') ? 'active' : '' }}">
                <span class="nav-icon text-primary"><i class="fa-solid fa-users"></i></span>
                <span>Employee</span>
            </a>
            @endif

            @if($canMgmtDepartments)
            <a href="{{ route('employee.management.departments') }}" class="employee-nav-item {{ request()->routeIs('employee.management.departments*') ? 'active' : '' }}">
                <span class="nav-icon text-info"><i class="fa-solid fa-layer-group"></i></span>
                <span>Department</span>
            </a>
            @endif

            @if($canMgmtDesignations)
            <a href="{{ route('employee.management.designations') }}" class="employee-nav-item {{ request()->routeIs('employee.management.designations*') ? 'active' : '' }}">
                <span class="nav-icon text-warning"><i class="fa-solid fa-id-card"></i></span>
                <span>Designation</span>
            </a>
            @endif

            @if($canMgmtLeaves)
            <a href="{{ route('employee.management.leaves') }}" class="employee-nav-item {{ request()->routeIs('employee.management.leaves*') ? 'active' : '' }} d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="nav-icon" style="color: #8b5cf6;"><i class="fa-solid fa-calendar-check"></i></span>
                    <span>Leave</span>
                </div>
                @if($navPendingLeaves > 0)
                    <span class="badge bg-danger rounded-pill px-2 py-0.5 ms-auto" style="font-size: 0.65rem;">{{ $navPendingLeaves }}</span>
                @endif
            </a>
            @endif

            @if($canMgmtPayroll)
            @php
                $isPayrollActive = request()->routeIs('employee.management.payroll*');
            @endphp
            <a href="#mgmtPayrollMenu" class="employee-nav-item has-submenu {{ $isPayrollActive ? '' : 'collapsed' }} d-flex align-items-center justify-content-between" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isPayrollActive ? 'true' : 'false' }}" aria-controls="mgmtPayrollMenu">
                <div class="d-flex align-items-center gap-2">
                    <span class="nav-icon text-warning"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                    <span>Payroll & Salary</span>
                </div>
                <i class="fa-solid fa-chevron-down submenu-chevron"></i>
            </a>
            <div class="collapse {{ $isPayrollActive ? 'show' : '' }}" id="mgmtPayrollMenu">
                <div class="employee-submenu-box">
                    <a href="{{ route('employee.management.payroll.records') }}" class="employee-sub-item {{ request()->routeIs('employee.management.payroll.records') || request()->fullUrlIs(route('employee.management.payroll')) ? 'active' : '' }}">
                        <span class="sub-icon-pill"><i class="fa-solid fa-receipt"></i></span>
                        <span>Monthly Salary Records</span>
                    </a>
                    @if(Auth::guard('employee')->user()->hasPermission('payroll.create'))
                    <a href="{{ route('employee.management.payroll.generate') }}" class="employee-sub-item {{ request()->routeIs('employee.management.payroll.generate*') ? 'active' : '' }}">
                        <span class="sub-icon-pill"><i class="fa-solid fa-calculator"></i></span>
                        <span>Run / Generate Salary</span>
                    </a>
                    @endif
                    <a href="{{ route('employee.management.payroll.components') }}" class="employee-sub-item {{ request()->routeIs('employee.management.payroll.components*') ? 'active' : '' }}">
                        <span class="sub-icon-pill"><i class="fa-solid fa-sliders"></i></span>
                        <span>Salary Components</span>
                    </a>
                </div>
            </div>
            @endif

            @if($canMgmtTasks)
            <a href="{{ route('employee.management.tasks') }}" class="employee-nav-item {{ request()->routeIs('employee.management.tasks*') ? 'active' : '' }}">
                <span class="nav-icon text-info"><i class="fa-solid fa-list-check"></i></span>
                <span>Team task</span>
            </a>
            @endif

            @if($canMgmtReports)
            <a href="{{ route('employee.management.reports') }}" class="employee-nav-item {{ request()->routeIs('employee.management.reports*') ? 'active' : '' }}">
                <span class="nav-icon text-danger"><i class="fa-solid fa-chart-line"></i></span>
                <span>Download Report</span>
            </a>
            @endif
        @endif

        <hr class="my-3 opacity-25">

        <a href="{{ route('logout') }}" class="employee-nav-item text-danger logout-btn">
            <span class="nav-icon text-danger" style="background: rgba(239, 68, 68, 0.1);"><i class="fa-solid fa-arrow-right-from-bracket"></i></span>
            <span class="fw-semibold">Log Out</span>
        </a>
    </nav>
</aside>
