<!-- Desktop Sticky Left Sidebar (>= 992px) -->
<aside class="desktop-sidebar d-none d-lg-flex">
    <div class="sidebar-nav-container">
        
        <div id="desktopSidebarNav">
            <!-- Section 1: Overview -->
            <div class="sidebar-section-title">Overview</div>

            <a href="{{ route('user.dashboard') }}" class="sidebar-nav-item {{ request()->is('company/dashboard', 'dashboard') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="fa-solid fa-chart-pie nav-icon"></i>
                    <span>Dashboard</span>
                </div>
            </a>

            <!-- Section 2: People & Organization -->
            <div class="sidebar-section-title">Employees & Org</div>

            <!-- Employees Dropdown -->
            <a href="#" class="sidebar-nav-item {{ request()->is('company/employee*', 'employee*', 'company/employees*', 'employees*', 'company/departments*', 'departments*', 'company/branche*', 'branche*', 'company/branch*', 'branch*', 'company/designations*', 'designations*', 'company/company-roles*', 'company-roles*') ? 'active' : 'collapsed' }}" 
               data-bs-toggle="collapse" data-bs-target="#desktopEmployeeMenu">
                <div class="nav-link-content">
                    <i class="fa-solid fa-users nav-icon"></i>
                    <span>Employee Directory</span>
                </div>
                <i class="fa-solid fa-chevron-right chevron-arrow"></i>
            </a>
            <div id="desktopEmployeeMenu" class="collapse {{ request()->is('company/employee*', 'employee*', 'company/employees*', 'employees*', 'company/departments*', 'departments*', 'company/branche*', 'branche*', 'company/branch*', 'branch*', 'company/designations*', 'designations*', 'company/company-roles*', 'company-roles*') ? 'show' : '' }}" data-bs-parent="#desktopSidebarNav">
                <a href="{{ route('employee.index') }}" class="sidebar-sub-item {{ (request()->is('company/employee', 'employee', 'company/employees*', 'employees*') && !request()->is('company/employee/location*')) ? 'active' : '' }}">
                    <span class="sub-dot"></span> All Employees
                </a>

                <a href="{{ route('departments.index') }}" class="sidebar-sub-item {{ request()->is('company/departments*', 'departments*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Departments
                </a>
               
                
                <a href="{{ route('branche.index') }}" class="sidebar-sub-item {{ request()->is('company/branche*', 'branche*', 'company/branch*', 'branch*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Branches
                </a>
                 <a href="{{ route('designations.index') }}" class="sidebar-sub-item {{ request()->is('company/designations*', 'designations*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Designations
                </a>

                  <a href="{{ route('employee.location') }}" class="sidebar-sub-item {{ request()->is('company/employee/location*', 'employee/location*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Location Tracking
                </a>

                <a href="{{ route('company-roles.index') }}" class="sidebar-sub-item {{ request()->is('company/company-roles*', 'company-roles*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Roles & Permissions
                </a>
            </div>

            <!-- Section 3: Time & Attendance Management -->
            <div class="sidebar-section-title">Time & Attendance</div>

            <!-- Attendance Dropdown -->
            <a href="#" class="sidebar-nav-item {{ request()->is('company/attendance*', 'attendance*', 'company/shifts*', 'company/shift*', 'shifts*', 'shift*', 'company/deviceList*', 'company/device-list*', 'deviceList*') ? 'active' : 'collapsed' }}" 
               data-bs-toggle="collapse" data-bs-target="#desktopAttendanceMenu">
                <div class="nav-link-content">
                    <i class="fa-solid fa-user-clock nav-icon"></i>
                    <span>Attendance & Shifts</span>
                </div>
                <i class="fa-solid fa-chevron-right chevron-arrow"></i>
            </a>
            <div id="desktopAttendanceMenu" class="collapse {{ request()->is('company/attendance*', 'attendance*', 'company/shifts*', 'company/shift*', 'shifts*', 'shift*', 'company/deviceList*', 'company/device-list*', 'deviceList*') ? 'show' : '' }}" data-bs-parent="#desktopSidebarNav">
                <a href="{{ route('attendance.index') }}" class="sidebar-sub-item {{ request()->is('company/attendance*', 'attendance*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Daily Attendance
                </a>
                <a href="{{ route('shifts.index') }}" class="sidebar-sub-item {{ request()->is('company/shifts*', 'company/shift*', 'shifts*', 'shift*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Shift Timings
                </a>
                <a href="{{ route('deviceList') }}" class="sidebar-sub-item {{ request()->is('company/device-list*', 'company/deviceList*', 'deviceList*') ? 'active' : '' }}">
                    <span class="sub-dot"></span>  Device Request
                </a>
            </div>

            <!-- Leaves & Holidays Dropdown -->
            <a href="#" class="sidebar-nav-item {{ request()->is('company/leave-list*', 'leave-list*', 'company/leavetypes*', 'leavetypes*', 'company/compoffleaves*', 'compoffleaves*', 'company/holiday*', 'holiday*') ? 'active' : 'collapsed' }}" 
               data-bs-toggle="collapse" data-bs-target="#desktopLeaveMenu">
                <div class="nav-link-content">
                    <i class="fa-solid fa-calendar-check nav-icon"></i>
                    <span>Leaves & Holidays</span>
                </div>
                <i class="fa-solid fa-chevron-right chevron-arrow"></i>
            </a>
            <div id="desktopLeaveMenu" class="collapse {{ request()->is('company/leave-list*', 'leave-list*', 'company/leavetypes*', 'leavetypes*', 'company/compoffleaves*', 'compoffleaves*', 'company/holiday*', 'holiday*') ? 'show' : '' }}" data-bs-parent="#desktopSidebarNav">
                <a href="{{ route('leaveList') }}" class="sidebar-sub-item {{ request()->is('company/leave-list*', 'leave-list*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Leave Requests
                </a>
                <a href="{{ route('holiday.index') }}" class="sidebar-sub-item {{ request()->is('company/holiday*', 'holiday*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Company Holidays
                </a>
                <a href="{{ route('leavetypes.index') }}" class="sidebar-sub-item {{ request()->is('company/leavetypes*', 'leavetypes*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Leave Types Policy
                </a>
                <a href="{{ route('compoffleaves.index') }}" class="sidebar-sub-item {{ request()->is('company/compoffleaves*', 'compoffleaves*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Comp-Off Leaves
                </a>
            </div>

            <!-- Section 4: Payroll & Compensation -->
            <div class="sidebar-section-title">Payroll & Compensation</div>

            <a href="#" class="sidebar-nav-item {{ request()->is('company/monthly-salary-list*', 'monthly-salary-list*', 'company/generate-salary*', 'generate-salary*', 'company/salarytype*', 'salarytype*', 'company/grace-settings*', 'grace-settings*') ? 'active' : 'collapsed' }}" 
               data-bs-toggle="collapse" data-bs-target="#desktopSalaryMenu">
                <div class="nav-link-content">
                    <i class="fa-solid fa-file-invoice-dollar nav-icon"></i>
                    <span>Payroll & Salary</span>
                </div>
                <i class="fa-solid fa-chevron-right chevron-arrow"></i>
            </a>
            <div id="desktopSalaryMenu" class="collapse {{ request()->is('company/monthly-salary-list*', 'monthly-salary-list*', 'company/generate-salary*', 'generate-salary*', 'company/salarytype*', 'salarytype*', 'company/grace-settings*', 'grace-settings*') ? 'show' : '' }}" data-bs-parent="#desktopSidebarNav">
                <a href="{{ route('generateSalary') }}" class="sidebar-sub-item {{ request()->is('company/generate-salary*', 'generate-salary*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Run / Generate Salary
                </a>
                <a href="{{ route('employeeSalaryList') }}" class="sidebar-sub-item {{ request()->is('company/monthly-salary-list*', 'monthly-salary-list*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Monthly Salary Records
                </a>
                <a href="{{ route('salarytype.index') }}" class="sidebar-sub-item {{ request()->is('company/salarytype*', 'salarytype*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Salary Components
                </a>
                <a href="{{ route('grace_settings.index') }}" class="sidebar-sub-item {{ request()->is('company/grace-settings*', 'grace-settings*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Grace & Late Settings
                </a>
            </div>

            <!-- Section 5: Performance & Appraisals -->
            <div class="sidebar-section-title">Performance & Growth</div>

            <a href="#" class="sidebar-nav-item {{ request()->is('company/performance-type*', 'performance-type*', 'company/rank-list*', 'rank-list*') ? 'active' : 'collapsed' }}" 
               data-bs-toggle="collapse" data-bs-target="#desktopPerformanceMenu">
                <div class="nav-link-content">
                    <i class="fa-solid fa-chart-line nav-icon"></i>
                    <span>Performance & KPIs</span>
                </div>
                <i class="fa-solid fa-chevron-right chevron-arrow"></i>
            </a>
            <div id="desktopPerformanceMenu" class="collapse {{ request()->is('company/performance-type*', 'performance-type*', 'company/rank-list*', 'rank-list*') ? 'show' : '' }}" data-bs-parent="#desktopSidebarNav">
                <a href="{{ route('performancetypeList') }}" class="sidebar-sub-item {{ request()->is('company/performance-type*', 'performance-type*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Performance KPI Types
                </a>
                <a href="{{ route('employeeRankList') }}" class="sidebar-sub-item {{ request()->is('company/rank-list*', 'rank-list*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Employee Leaderboard
                </a>
            </div>

            <!-- Section 6: Operations & Workspace -->
            <div class="sidebar-section-title">Operations & Workspace</div>

            <a href="#" class="sidebar-nav-item {{ request()->is('company/expense*', 'expense*') ? 'active' : 'collapsed' }}" 
               data-bs-toggle="collapse" data-bs-target="#desktopExpenseMenu">
                <div class="nav-link-content">
                    <i class="fa-solid fa-receipt nav-icon"></i>
                    <span>Expense Claims</span>
                </div>
                <i class="fa-solid fa-chevron-right chevron-arrow"></i>
            </a>
            <div id="desktopExpenseMenu" class="collapse {{ request()->is('company/expense*', 'expense*') ? 'show' : '' }}" data-bs-parent="#desktopSidebarNav">
                <a href="{{ route('expenseList') }}" class="sidebar-sub-item {{ request()->is('company/expense/list*', 'expense/list*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Expense Claims List
                </a>
                <a href="{{ route('expenseformList') }}" class="sidebar-sub-item {{ request()->is('company/expense/formlist*', 'expense/formlist*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Expense Form Setup
                </a>
            </div>

            <a href="{{ route('taskList') }}" class="sidebar-nav-item {{ request()->is('company/task*', 'taskList*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="fa-solid fa-list-check nav-icon"></i>
                    <span>Tasks & To-Dos</span>
                </div>
            </a>

            <a href="{{ route('chat.index') }}" class="sidebar-nav-item {{ request()->is('company/chat*', 'chat*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="fa-solid fa-comments nav-icon"></i>
                    <span>Team Chat</span>
                </div>
            </a>

            <a href="{{ route('leadList') }}" class="sidebar-nav-item {{ request()->is('company/lead*', 'leadList*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="fa-solid fa-bullhorn nav-icon"></i>
                    <span>CRM Leads</span>
                </div>
            </a>

            <!-- Section 7: Reports & Analytics -->
            <div class="sidebar-section-title">Reports & Analytics</div>

            <a href="{{ route('download-report.index') }}" class="sidebar-nav-item {{ request()->is('company/download-report*', 'download-report*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="fa-solid fa-file-arrow-down nav-icon"></i>
                    <span>Download Reports</span>
                </div>
            </a>

            <!-- Section 8: Company Administration -->
            <div class="sidebar-section-title">Administration</div>

            <a href="#" class="sidebar-nav-item {{ request()->is('company/profile/edit*', 'company/company-documents*', 'company-documents*', 'company/document-verification*', 'document-verification*', 'company/referrals*', 'companies/referrals*') ? 'active' : 'collapsed' }}" 
               data-bs-toggle="collapse" data-bs-target="#desktopProfileMenu">
                <div class="nav-link-content">
                    <i class="fa-solid fa-building-user nav-icon"></i>
                    <span>Company Settings</span>
                </div>
                <i class="fa-solid fa-chevron-right chevron-arrow"></i>
            </a>
            <div id="desktopProfileMenu" class="collapse {{ request()->is('company/profile/edit*', 'company/company-documents*', 'company-documents*', 'company/document-verification*', 'document-verification*', 'company/referrals*', 'companies/referrals*') ? 'show' : '' }}" data-bs-parent="#desktopSidebarNav">
                <a href="{{ route('company.profile.edit') }}" class="sidebar-sub-item {{ request()->is('company/profile/edit*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Company Profile
                </a>
                <a href="{{ route('company-documents.index') }}" class="sidebar-sub-item {{ request()->is('company/company-documents*', 'company-documents*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Upload Documents
                </a>
                <a href="{{ route('companydocumentVerification') }}" class="sidebar-sub-item {{ request()->is('company/document-verification*', 'document-verification*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Document Verification
                </a>
                <a href="{{ route('referralList') }}" class="sidebar-sub-item {{ request()->is('company/referrals*', 'companies/referrals*') ? 'active' : '' }}">
                    <span class="sub-dot"></span> Refer & Earn
                </a>
            </div>

            <!-- Section 9: Help & Support -->
            <div class="sidebar-section-title">Help & Support</div>

            <a href="{{ route('company.tickets') }}" class="sidebar-nav-item {{ request()->is('company/company-tickets*', 'company.tickets*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="fa-solid fa-ticket-simple nav-icon"></i>
                    <span>Support Tickets</span>
                </div>
            </a>

            <a href="{{ route('company.feedback') }}" class="sidebar-nav-item {{ request()->is('company/company-feedback*', 'company.feedback*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="fa-solid fa-paper-plane nav-icon"></i>
                    <span>Send Feedback</span>
                </div>
            </a>

            <a href="{{ route('company.helpList') }}" class="sidebar-nav-item {{ request()->is('company/company-help*', 'company-help*') ? 'active' : '' }}">
                <div class="nav-link-content">
                    <i class="fa-solid fa-circle-question nav-icon"></i>
                    <span>Knowledge Base</span>
                </div>
            </a>

        </div>
    </div>
</aside>

<!-- Mobile Offcanvas Sidebar Drawer (< 992px) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="userSidebarOffcanvas" aria-labelledby="userSidebarOffcanvasLabel" style="background-color: var(--stafo-sidebar-bg); width: 280px; border-right: 1px solid var(--stafo-sidebar-border);">
    <div class="offcanvas-header border-bottom border-secondary border-opacity-25 pb-3">
        <a href="{{ route('user.dashboard') }}" class="navbar-brand">
            <img src="{{ asset('main/images/logo.png') }}" alt="STAFO logo" height="32">
        </a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="sidebar-nav-container">
            <div id="mobileSidebarNav">
                <!-- Section 1: Overview -->
                <div class="sidebar-section-title">Overview</div>

                <a href="{{ route('user.dashboard') }}" class="sidebar-nav-item {{ request()->is('company/dashboard', 'dashboard') ? 'active' : '' }}">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-chart-pie nav-icon"></i>
                        <span>Dashboard</span>
                    </div>
                </a>

                <!-- Section 2: Employees & Organization -->
                <div class="sidebar-section-title">Employees & Org</div>

                <a href="#" class="sidebar-nav-item {{ request()->is('company/employee*', 'company/employees*', 'employee*', 'company/departments*', 'departments*', 'company/branche*', 'branche*', 'company/branch*', 'branch*', 'company/designations*', 'designations*', 'company/company-roles*', 'company-roles*') ? 'active' : 'collapsed' }}" 
                   data-bs-toggle="collapse" data-bs-target="#mobileEmployeeMenu">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-users nav-icon"></i>
                        <span>Employees & Org</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-arrow"></i>
                </a>
                <div id="mobileEmployeeMenu" class="collapse {{ request()->is('company/employee*', 'company/employees*', 'employee*', 'company/departments*', 'departments*', 'company/branche*', 'branche*', 'company/branch*', 'branch*', 'company/designations*', 'designations*', 'company/company-roles*', 'company-roles*') ? 'show' : '' }}" data-bs-parent="#mobileSidebarNav">
                    <a href="{{ route('employee.index') }}" class="sidebar-sub-item {{ (request()->is('company/employee', 'company/employees*', 'employee*') && !request()->is('company/employee/location*')) ? 'active' : '' }}">
                        <span class="sub-dot"></span> All Employees
                    </a>
                    <a href="{{ route('employee.location') }}" class="sidebar-sub-item {{ request()->is('company/employee/location*', 'employee/location*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Location Tracking
                    </a>
                    <a href="{{ route('departments.index') }}" class="sidebar-sub-item {{ request()->is('company/departments*', 'departments*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Departments
                    </a>
                    <a href="{{ route('designations.index') }}" class="sidebar-sub-item {{ request()->is('company/designations*', 'designations*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Designations
                    </a>
                    <a href="{{ route('company-roles.index') }}" class="sidebar-sub-item {{ request()->is('company/company-roles*', 'company-roles*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Roles & Permissions
                    </a>
                    <a href="{{ route('branche.index') }}" class="sidebar-sub-item {{ request()->is('company/branche*', 'branche*', 'company/branch*', 'branch*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Branches
                    </a>
                </div>

                <!-- Section 3: Time & Attendance Management -->
                <div class="sidebar-section-title">Time & Attendance</div>

                <!-- Attendance Dropdown -->
                <a href="#" class="sidebar-nav-item {{ request()->is('company/attendance*', 'attendance*', 'company/shifts*', 'company/shift*', 'shifts*', 'shift*', 'company/deviceList*', 'company/device-list*', 'deviceList*') ? 'active' : 'collapsed' }}" 
                   data-bs-toggle="collapse" data-bs-target="#mobileAttendanceMenu">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-user-clock nav-icon"></i>
                        <span>Attendance & Shifts</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-arrow"></i>
                </a>
                <div id="mobileAttendanceMenu" class="collapse {{ request()->is('company/attendance*', 'attendance*', 'company/shifts*', 'company/shift*', 'shifts*', 'shift*', 'company/deviceList*', 'company/device-list*', 'deviceList*') ? 'show' : '' }}" data-bs-parent="#mobileSidebarNav">
                    <a href="{{ route('attendance.index') }}" class="sidebar-sub-item {{ request()->is('company/attendance*', 'attendance*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Daily Attendance
                    </a>
                    <a href="{{ route('shifts.index') }}" class="sidebar-sub-item {{ request()->is('company/shifts*', 'company/shift*', 'shifts*', 'shift*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Shift Timings
                    </a>
                    <a href="{{ route('deviceList') }}" class="sidebar-sub-item {{ request()->is('company/device-list*', 'company/deviceList*', 'deviceList*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Biometric Devices
                    </a>
                </div>

                <!-- Leaves & Holidays Dropdown -->
                <a href="#" class="sidebar-nav-item {{ request()->is('company/leave-list*', 'leave-list*', 'company/leavetypes*', 'leavetypes*', 'company/compoffleaves*', 'compoffleaves*', 'company/holiday*', 'holiday*') ? 'active' : 'collapsed' }}" 
                   data-bs-toggle="collapse" data-bs-target="#mobileLeaveMenu">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-calendar-check nav-icon"></i>
                        <span>Leaves & Holidays</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-arrow"></i>
                </a>
                <div id="mobileLeaveMenu" class="collapse {{ request()->is('company/leave-list*', 'leave-list*', 'company/leavetypes*', 'leavetypes*', 'company/compoffleaves*', 'compoffleaves*', 'company/holiday*', 'holiday*') ? 'show' : '' }}" data-bs-parent="#mobileSidebarNav">
                    <a href="{{ route('leaveList') }}" class="sidebar-sub-item {{ request()->is('company/leave-list*', 'leave-list*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Leave Requests
                    </a>
                    <a href="{{ route('holiday.index') }}" class="sidebar-sub-item {{ request()->is('company/holiday*', 'holiday*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Company Holidays
                    </a>
                    <a href="{{ route('leavetypes.index') }}" class="sidebar-sub-item {{ request()->is('company/leavetypes*', 'leavetypes*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Leave Types Policy
                    </a>
                    <a href="{{ route('compoffleaves.index') }}" class="sidebar-sub-item {{ request()->is('company/compoffleaves*', 'compoffleaves*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Comp-Off Leaves
                    </a>
                </div>

                <!-- Section 4: Payroll & Compensation -->
                <div class="sidebar-section-title">Payroll & Compensation</div>

                <a href="#" class="sidebar-nav-item {{ request()->is('company/monthly-salary-list*', 'monthly-salary-list*', 'company/generate-salary*', 'generate-salary*', 'company/salarytype*', 'salarytype*', 'company/grace-settings*', 'grace-settings*') ? 'active' : 'collapsed' }}" 
                   data-bs-toggle="collapse" data-bs-target="#mobileSalaryMenu">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-file-invoice-dollar nav-icon"></i>
                        <span>Payroll & Salary</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-arrow"></i>
                </a>
                <div id="mobileSalaryMenu" class="collapse {{ request()->is('company/monthly-salary-list*', 'monthly-salary-list*', 'company/generate-salary*', 'generate-salary*', 'company/salarytype*', 'salarytype*', 'company/grace-settings*', 'grace-settings*') ? 'show' : '' }}" data-bs-parent="#mobileSidebarNav">
                    <a href="{{ route('generateSalary') }}" class="sidebar-sub-item {{ request()->is('company/generate-salary*', 'generate-salary*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Run / Generate Salary
                    </a>
                    <a href="{{ route('employeeSalaryList') }}" class="sidebar-sub-item {{ request()->is('company/monthly-salary-list*', 'monthly-salary-list*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Monthly Salary Records
                    </a>
                    <a href="{{ route('salarytype.index') }}" class="sidebar-sub-item {{ request()->is('company/salarytype*', 'salarytype*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Salary Components
                    </a>
                    <a href="{{ route('grace_settings.index') }}" class="sidebar-sub-item {{ request()->is('company/grace-settings*', 'grace-settings*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Grace & Late Settings
                    </a>
                </div>

                <!-- Section 5: Performance & Appraisals -->
                <div class="sidebar-section-title">Performance & Growth</div>

                <a href="#" class="sidebar-nav-item {{ request()->is('company/performance-type*', 'performance-type*', 'company/rank-list*', 'rank-list*') ? 'active' : 'collapsed' }}" 
                   data-bs-toggle="collapse" data-bs-target="#mobilePerformanceMenu">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-chart-line nav-icon"></i>
                        <span>Performance & KPIs</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-arrow"></i>
                </a>
                <div id="mobilePerformanceMenu" class="collapse {{ request()->is('company/performance-type*', 'performance-type*', 'company/rank-list*', 'rank-list*') ? 'show' : '' }}" data-bs-parent="#mobileSidebarNav">
                    <a href="{{ route('performancetypeList') }}" class="sidebar-sub-item {{ request()->is('company/performance-type*', 'performance-type*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Performance KPI Types
                    </a>
                    <a href="{{ route('employeeRankList') }}" class="sidebar-sub-item {{ request()->is('company/rank-list*', 'rank-list*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Employee Leaderboard
                    </a>
                </div>

                <!-- Section 6: Operations & Workspace -->
                <div class="sidebar-section-title">Operations & Workspace</div>

                <a href="#" class="sidebar-nav-item {{ request()->is('company/expense*', 'expense*') ? 'active' : 'collapsed' }}" 
                   data-bs-toggle="collapse" data-bs-target="#mobileExpenseMenu">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-receipt nav-icon"></i>
                        <span>Expense Claims</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-arrow"></i>
                </a>
                <div id="mobileExpenseMenu" class="collapse {{ request()->is('company/expense*', 'expense*') ? 'show' : '' }}" data-bs-parent="#mobileSidebarNav">
                    <a href="{{ route('expenseList') }}" class="sidebar-sub-item {{ request()->is('company/expense/list*', 'expense/list*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Expense Claims List
                    </a>
                    <a href="{{ route('expenseformList') }}" class="sidebar-sub-item {{ request()->is('company/expense/formlist*', 'expense/formlist*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Expense Form Setup
                    </a>
                </div>

                <a href="{{ route('taskList') }}" class="sidebar-nav-item {{ request()->is('company/task*', 'taskList*') ? 'active' : '' }}">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-list-check nav-icon"></i>
                        <span>Tasks & To-Dos</span>
                    </div>
                </a>

                <a href="{{ route('chat.index') }}" class="sidebar-nav-item {{ request()->is('company/chat*', 'chat*') ? 'active' : '' }}">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-comments nav-icon"></i>
                        <span>Team Chat</span>
                    </div>
                </a>

                <a href="{{ route('leadList') }}" class="sidebar-nav-item {{ request()->is('company/lead*', 'leadList*') ? 'active' : '' }}">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-bullhorn nav-icon"></i>
                        <span>CRM Leads</span>
                    </div>
                </a>

                <!-- Section 7: Reports & Analytics -->
                <div class="sidebar-section-title">Reports & Analytics</div>

                <a href="{{ route('download-report.index') }}" class="sidebar-nav-item {{ request()->is('company/download-report*', 'download-report*') ? 'active' : '' }}">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-file-arrow-down nav-icon"></i>
                        <span>Download Reports</span>
                    </div>
                </a>

                <!-- Section 8: Company Administration -->
                <div class="sidebar-section-title">Administration</div>

                <a href="#" class="sidebar-nav-item {{ request()->is('company/profile/edit*', 'company/company-documents*', 'company-documents*', 'company/document-verification*', 'document-verification*', 'company/referrals*', 'companies/referrals*') ? 'active' : 'collapsed' }}" 
                   data-bs-toggle="collapse" data-bs-target="#mobileProfileMenu">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-building-user nav-icon"></i>
                        <span>Company Settings</span>
                    </div>
                    <i class="fa-solid fa-chevron-right chevron-arrow"></i>
                </a>
                <div id="mobileProfileMenu" class="collapse {{ request()->is('company/profile/edit*', 'company/company-documents*', 'company-documents*', 'company/document-verification*', 'document-verification*', 'company/referrals*', 'companies/referrals*') ? 'show' : '' }}" data-bs-parent="#mobileSidebarNav">
                    <a href="{{ route('company.profile.edit') }}" class="sidebar-sub-item {{ request()->is('company/profile/edit*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Company Profile
                    </a>
                    <a href="{{ route('company-documents.index') }}" class="sidebar-sub-item {{ request()->is('company/company-documents*', 'company-documents*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Upload Documents
                    </a>
                    <a href="{{ route('companydocumentVerification') }}" class="sidebar-sub-item {{ request()->is('company/document-verification*', 'document-verification*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Document Verification
                    </a>
                    <a href="{{ route('referralList') }}" class="sidebar-sub-item {{ request()->is('company/referrals*', 'companies/referrals*') ? 'active' : '' }}">
                        <span class="sub-dot"></span> Refer & Earn
                    </a>
                </div>

                <!-- Section 9: Help & Support -->
                <div class="sidebar-section-title">Help & Support</div>

                <a href="{{ route('company.tickets') }}" class="sidebar-nav-item {{ request()->is('company/company-tickets*', 'company.tickets*') ? 'active' : '' }}">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-ticket-simple nav-icon"></i>
                        <span>Support Tickets</span>
                    </div>
                </a>

                <a href="{{ route('company.feedback') }}" class="sidebar-nav-item {{ request()->is('company/company-feedback*', 'company.feedback*') ? 'active' : '' }}">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-paper-plane nav-icon"></i>
                        <span>Send Feedback</span>
                    </div>
                </a>

                <a href="{{ route('company.helpList') }}" class="sidebar-nav-item {{ request()->is('company/company-help*', 'company-help*') ? 'active' : '' }}">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-circle-question nav-icon"></i>
                        <span>Knowledge Base</span>
                    </div>
                </a>

                <a href="{{ route('logout') }}" class="sidebar-nav-item text-danger mt-3">
                    <div class="nav-link-content">
                        <i class="fa-solid fa-arrow-right-from-bracket nav-icon text-danger"></i>
                        <span>Log Out</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
