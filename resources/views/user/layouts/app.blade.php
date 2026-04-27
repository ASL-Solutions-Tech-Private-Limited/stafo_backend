<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="STAFO is an advanced HRMS software with AI-driven payroll, attendance tracking, and leave management. Automate HR processes seamlessly. Try now!">
    <meta property="og:title" content="STAFO - Best HR Management System | Payroll, Attendance">
    <meta property="og:description"
        content="STAFO is an advanced HRMS software with AI-driven payroll, attendance tracking, and leave management. Automate HR processes seamlessly. Try now!">

    <title>@yield('title', 'Employee Dashboard')</title>
    <link rel="shortcut icon" href="{{ asset('main/images/favicon_io (1)/favicon-32x32.png') }}" type="image/x-icon">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="{{ asset('main/css/bootstrap.min-5.3.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('main/css/dashboard.css') }}">
    @yield('css')
</head>

<body>
    <div class="container-fluid stiky-added">
        <div class="row">
            <nav class="navbar navbar-expand-lg bg-theme py-0">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">
                        <img src="{{ asset('main/images/logo.png') }}" alt="STAFO logo">
                    </a>
                    <button class="btn btn-outline-light hide-mob" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#staticBackdrop" aria-controls="staticBackdrop">
                        <i class="fa-solid fa-bars align-middle"></i>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-0">
                            <li class="nav-item dropdown user">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    @if (Auth::user()->image_name && file_exists(public_path('uploads/compnay_logo/' . Auth::user()->image_name)))
                                        <img src="{{ asset('uploads/compnay_logo/' . Auth::user()->image_name) }}"
                                            alt="Company Logo" class="ms-2" width="30px" height="30px" />
                                    
                                    @endif
                                    <span class="text-capitalize fw-bold">{{ Auth::user()->company_name }}</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item text-end"><a href="{{ route('logout') }}"
                                            class="text-danger fw-bold">Log Out</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="offcanvas offcanvas-start hide-mob" data-bs-backdrop="static" tabindex="-1"
                    id="staticBackdrop" aria-labelledby="staticBackdropLabel">
                    <div class="offcanvas-header justify-content-end">
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <nav class="d-md-block sidebar overflow-y-scroll">
                            <img src="{{ asset('main/images/logo.png') }}" alt="STAFO logo">
                            <a href="{{ route('user.dashboard') }}"
                                class="{{ request()->is('dashboard') ? 'active' : '' }}"><i
                                    class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#masterSection"><i
                                    class="fas fa-cogs"></i> Employee <i class="fa-solid fa-caret-right"></i></a>
                            <div id="masterSection"
                                class="submenu collapse {{ request()->is('shift*') || request()->is('branch*') || request()->is('departments*') || request()->is('employee*') || request()->is('attendance*') || request()->is('leave-list*') ? 'show' : '' }}">
                                <a href="{{ route('employee.index') }}"
                                    class="{{ request()->is('employee*') ? 'active' : '' }}"><i
                                        class="fas fa-users"></i> Employees List </a>
                                <a href="{{ route('branche.index') }}"
                                    class="{{ request()->is('branch*') ? 'active' : '' }}"><i
                                        class="fas fa-building"></i> Branch</a>
                                <a href="{{ route('departments.index') }}"
                                    class="{{ request()->is('departments*') ? 'active' : '' }}"><i
                                        class="fas fa-building"></i> Departments</a>
                                <a href="{{ route('shifts.index') }}"
                                    class="{{ request()->is('shift*') ? 'active' : '' }}"><i class="fas fa-clock"></i>
                                    Shift</a>
                                <a href="{{ route('attendance.index') }}"
                                    class="{{ request()->is('attendance*') ? 'active' : '' }}"><i
                                        class="fas fa-building"></i> Attendance</a>
                                <a href="{{ route('leaveList') }}"
                                    class="{{ request()->is('leave-list*') ? 'active' : '' }}"><i
                                        class="fas fa-building"></i> Leave List</a>
                            </div>

                            <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#profileSection"><i
                                    class="fas fa-user-cog"></i> Profile <i class="fa-solid fa-caret-right"></i></a>
                            <div id="profileSection"
                                class="collapse {{ request()->is('company/profile/edit') || request()->is('company-documents*') || request()->is('document-verification*') ? 'show' : '' }}">
                                <a href="{{ route('company.profile.edit') }}"
                                    class="{{ request()->is('company/profile/edit') ? 'active' : '' }}"><i
                                        class="fas fa-edit"></i> Edit Info</a>
                                <a href="{{ route('company-documents.index') }}"
                                    class="{{ request()->is('company-documents*') ? 'active' : '' }} d-flex"><i
                                        class="fas fa-upload"></i> Documents upload</a>
                                <a href="{{ route('companydocumentVerification') }}"
                                    class="{{ request()->is('document-verification*') ? 'active' : '' }} d-flex"><i
                                        class="fas fa-check-circle"></i> Documents verify</a>
                            </div>

                            <a href="{{ route('company.feedback') }}"
                                class="{{ request()->is('company.feedback*') ? 'active' : '' }}"><i
                                    class="fas fa-file-alt"></i> Feedback</a>

                            <a href="{{ route('company.tickets') }}"
                                class="{{ request()->is('company.tickets*') ? 'active' : '' }}">
                                <i class="fas fa-ticket-alt"></i> Ticket
                            </a>

                            <a href="{{ route('download-report.index') }}"
                                class="{{ request()->is('download-report*') ? 'active' : '' }}">
                                <i class="fas fa-file-pdf"></i> Report
                            </a>
                            <a href="{{ route('company.helpList') }}"
                                class="{{ request()->is('company-help*') ? 'active' : '' }}">
                                <i class="fa fa-user me-2"></i> Help
                            </a>


                        </nav>
                    </div>

                </div>
            </nav>
        </div>
    </div>

    <div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col-md-3 col-lg-2 px-0">
                <nav class="d-md-block sidebar overflow-y-scroll">
                    <div class="search-bar mb-3">
                        <input type="text" id="searchInput" onkeyup="searchFunction()" class="form-control"
                            placeholder="Search Modules...">
                    </div>
                    <a href="{{ route('user.dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}"><i
                            class="fas fa-tachometer-alt"></i> Dashboard</a>

                    <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#masterSection"><i
                            class="fas fa-cogs"></i> Employee <i class="fa-solid fa-caret-right"></i></a>
                    <div id="masterSection"
                        class="submenu collapse {{ request()->is('shift*') || request()->is('branch*') || request()->is('departments*') || request()->is('employee*') || request()->is('attendance*') || request()->is('leave-list*') ? 'show' : '' }}">
                        <a href="{{ route('employee.index') }}"
                            class="{{ request()->is('employee*') ? 'active' : '' }}"><i class="fas fa-users"></i>
                            Employees List</a>
                        <a href="{{ route('branche.index') }}" class="{{ request()->is('branch*') ? 'active' : '' }}"><i
                                class="fas fa-building"></i>
                            Branch</a>
                        <a href="{{ route('departments.index') }}"
                            class="{{ request()->is('departments*') ? 'active' : '' }}"><i class="fas fa-building"></i>
                            Departments</a>
                        <a href="{{ route('shifts.index') }}" class="{{ request()->is('shift*') ? 'active' : '' }}"><i
                                class="fas fa-clock"></i>
                            Shift</a>
                        <a href="{{ route('attendance.index') }}"
                            class="{{ request()->is('attendance*') ? 'active' : '' }}"><i class="fas fa-building"></i>
                            Attendance</a>
                        <a href="{{ route('leaveList') }}" class="{{ request()->is('leave-list*') ? 'active' : '' }}"><i
                                class="fas fa-building"></i> Leave List</a> 
                        <a href="{{ route('leavetypes.index') }}"
                            class="{{ request()->is('leavetypes*') ? 'active' : '' }}"><i class="fas fa-building"></i>
                            Leave Types</a>
                        <!-- <a href="{{ route('reimbursements.index') }}"
                            class="{{ request()->is('reimbursements*') ? 'active' : '' }}"><i class="fas fa-building"></i>
                            Reimbursements</a> -->
                        <a href="{{ route('compoffleaves.index') }}"
                            class="{{ request()->is('compoffleaves*') ? 'active' : '' }}"><i class="fas fa-building"></i>
                            Compoff Leave</a>
                        <a href="{{ route('deviceList') }}"
                            class="{{ request()->is('deviceList*') ? 'active' : '' }}"><i class="fas fa-building"></i>
                            Device List</a>
                    </div>

                    <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#profileSection"><i
                            class="fas fa-user-cog"></i> Profile <i class="fa-solid fa-caret-right"></i></a>
                    <div id="profileSection"
                        class="submenu collapse {{ request()->is('company/profile/edit') || request()->is('company-documents*') || request()->is('document-verification*') ? 'show' : '' }}">
                        <a href="{{ route('company.profile.edit') }}"
                            class="{{ request()->is('company/profile/edit') ? 'active' : '' }}"><i
                                class="fas fa-edit"></i> Edit Info</a>
                        <a href="{{ route('company-documents.index') }}"
                            class="{{ request()->is('company-documents*') ? 'active' : '' }} d-flex"><i
                                class="fas fa-upload"></i> Documents upload</a>
                        <a href="{{ route('companydocumentVerification') }}"
                            class="{{ request()->is('document-verification*') ? 'active' : '' }} d-flex"><i
                                class="fas fa-check-circle"></i> Documents verify</a>
                        <a href="{{ route('referralList') }}"
                            class="{{ request()->is('companies/referrals') ? 'active' : '' }} d-flex"><i
                                class="fas fa-check-circle"></i>Referrals</a>
                    </div>

                    <a href="{{ route('company.feedback') }}"
                        class="{{ request()->is('company.feedback*') ? 'active' : '' }}"><i class="fas fa-file-alt"></i>
                        Feedback</a>

                    <a href="{{ route('company.tickets') }}"
                        class="{{ request()->is('company.tickets*') ? 'active' : '' }}">
                        <i class="fas fa-ticket-alt"></i> Ticket
                    </a>


                    <a href="{{ route('download-report.index') }}"
                        class="{{ request()->is('download-report*') ? 'active' : '' }}">
                        <i class="fas fa-file-pdf"></i> Report
                    </a>
                    <a href="{{ route('performancetypeList') }}"
                        class="{{ request()->is('performance-type*') ? 'active' : '' }}">
                        <i class="fas fa-line-chart"></i> Performance Type
                    </a>
                    <a href="{{ route('employeeRankList') }}" class="{{ request()->is('rank-list') ? 'active' : '' }}">
                        <i class="fas fa-trophy"></i> Rank List
                    </a>
                    <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#salarySection"><i
                            class="fas fa-inr"></i> Salary <i class="fa-solid fa-caret-right"></i></a>
                    <div id="salarySection"
                        class="submenu collapse {{ request()->is('monthly-salary-list') || request()->is('generate-salary') ||  request()->is('salary-package-type*') || request()->is('salarytype*') || request()->is('grace-settings*') ? 'show' : '' }}">
                        <a href="{{ route('salarytype.index') }}"
                            class="{{ request()->is('salarytype') ? 'active' : '' }}"><i
                                class="fas fa-newspaper"></i> Salary Type</a>

                        <a href="{{ route('salary-package-type.index') }}"
                            class="{{ request()->is('salary-package-type') ? 'active' : '' }} d-flex"><i
                                class="fas fa-ticket"></i>Package</a>       


                        <a href="{{ route('generateSalary') }}"
                            class="{{ request()->is('generate-salary') ? 'active' : '' }} d-flex"><i
                                class="fas fa-ils"></i> Generate Salary</a>
                        <a href="{{ route('employeeSalaryList') }}"
                            class="{{ request()->is('monthly-salary-list') ? 'active' : '' }} d-flex"><i
                                class="fas fa-ticket"></i> Monthly Salary</a>
                        <a href="{{ route('grace_settings.index') }}"
                            class="{{ request()->is('grace-settings') ? 'active' : '' }} d-flex"><i
                                class="fas fa-ticket"></i> Salary settings</a>

                       
                    </div>
                    <a href="{{ route('company.helpList') }}"
                                class="{{ request()->is('company-help*') ? 'active' : '' }}">
                                <i class="fa fa-user me-2"></i> Help
                    </a>
                    <a href="{{ route('holiday.index') }}"
                                class="{{ request()->is('holiday*') ? 'active' : '' }}">
                                <i class="fa fa-sleigh me-2"></i> Holiday
                    </a>
                    <a href="{{ route('chat.index') }}"
                        class="{{ request()->is('chat.index') ? 'active' : '' }}">
                        <i class="fa fa-comment me-2"></i> Chat
                    </a>

                    <a href="{{ route('leadList') }}"
                        class="{{ request()->is('leadList') ? 'active' : '' }}">
                        <i class="fa fa-message me-2"></i> CRM
                    </a>
                    
                    <a href="{{ route('taskList') }}"
                        class="{{ request()->is('taskList') ? 'active' : '' }}">
                        <i class="fa fa-tasks me-2"></i> Task
                    </a>                    

                    <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#expenseSection"><i
                            class="fas fa-inr"></i> Expense <i class="fa-solid fa-caret-right"></i></a>
                    <div id="expenseSection"
                        class="submenu collapse {{ request()->is('expense*') ? 'show' : '' }}">
                        <a href="{{ route('expenseList') }}"
                            class="{{ request()->is('expense/list') ? 'active' : '' }}"><i
                                class="fas fa-newspaper"></i> Expense List</a>
                        <a href="{{ route('expenseformList') }}"
                            class="{{ request()->is('expense/formlist') ? 'active' : '' }} d-flex"><i
                                class="fas fa-ils"></i> Expense Form</a>
                        
                    </div>

                    <a href="#" class="collapsed" data-bs-toggle="collapse" data-bs-target="#manageTrip">
                        <i class="fas fa-inr"></i> Manage Trip <i class="fa-solid fa-caret-right"></i>
                    </a>
                    <div id="manageTrip"
                        class="submenu collapse {{ request()->is('vehicles/*') || request()->is('trips/*') ? 'show' : '' }}">
                        
                        <a href="{{ route('vehicles.index') }}"
                        class="{{ request()->is('vehicles/*') ? 'active' : '' }}">
                            <i class="fas fa-newspaper"></i> Vehicles
                        </a>

                        <a href="{{ route('trips.index') }}"
                        class="{{ request()->is('trips/*') ? 'active' : '' }} d-flex">
                            <i class="fas fa-ils"></i> Trip
                        </a>

                    </div>
                    
                </nav>
            </div>

            <div class="col-md-9 col-lg-10 px-md-4 py-4">
                <main class="main-content ps-0">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('sweetalert::alert')
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="{{ asset('main/js/bootstrap.bundle.min5.3.js') }}"></script>

    <script>
        function searchFunction() {
            let input, filter, sidebarLinks, collapsibleSections, i, txtValue, section, sectionLinks;
            input = document.getElementById('searchInput');
            filter = input.value.toLowerCase();
            sidebarLinks = document.querySelectorAll('.sidebar a');
            collapsibleSections = document.querySelectorAll('.collapse');

            for (i = 0; i < sidebarLinks.length; i++) {
                txtValue = sidebarLinks[i].textContent || sidebarLinks[i].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    sidebarLinks[i].style.display = "";
                } else {
                    sidebarLinks[i].style.display = "none";
                }
            }

            for (i = 0; i < collapsibleSections.length; i++) {
                section = collapsibleSections[i];
                sectionLinks = section.querySelectorAll('a');
                let sectionMatches = false;

                for (let j = 0; j < sectionLinks.length; j++) {
                    txtValue = sectionLinks[j].textContent || sectionLinks[j].innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        sectionMatches = true;
                        sectionLinks[j].style.display = "";
                    } else {
                        sectionLinks[j].style.display = "none";
                    }
                }

                if (sectionMatches) {
                    section.classList.add('show');
                } else {
                    section.classList.remove('show');
                }
            }
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.querySelector(".col-md-3");
            const mainContent = document.querySelector(".col-md-9");

            function updateLayout() {
                if (window.innerWidth < 992) {
                    sidebar?.classList.add("d-none");
                    mainContent.classList.remove("col-md-9");
                    mainContent.classList.add("col-md-12");
                } else {
                    sidebar?.classList.remove("d-none");
                    mainContent.classList.remove("col-md-12");
                    mainContent.classList.add("col-md-9");
                }
            }

            updateLayout();
            window.addEventListener("resize", updateLayout);
        });
    </script>

    @yield('js')
</body>

</html>