<!-- Sidebar Start -->
<div class="sidebar pe-3 pb-3">
    <nav class="navbar bg-light navbar-light">
        <!-- Logo -->
        <div class="mb-4 img-nav text-center">
            <img src="{{ asset('main/images/logo.png') }}" alt="STAFO logo" height="100" width="100">
        </div>

        <!-- User Info -->
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle" src="{{ asset('webadmin/img/user.jpg') }}" alt=""
                    style="width: 40px; height: 40px;">
                <div
                    class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                </div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                <span>Admin</span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <div class="navbar-nav w-100">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="nav-item nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="fa fa-tachometer-alt me-2"></i>Dashboard
            </a>

            <!-- Company & Employee Dropdown -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-users me-2"></i>Company & Staff
                </a>
                <div class="dropdown-menu bg-transparent border-0 submenu">
                    <a href="{{ route('company.details.list') }}" class="dropdown-item nav-link">
                        <i class="fa fa-building me-2"></i>Company
                    </a>
                    <a href="{{ route('employees.list') }}" class="dropdown-item nav-link">
                        <i class="fa fa-user-tie me-2"></i>Employee
                    </a>
                    <a href="{{ route('document_list') }}" class="dropdown-item nav-link">
                        <i class="fa fa-file-alt me-2"></i>Document
                    </a>
                    <a href="{{ route('branches.index') }}" class="dropdown-item nav-link">
                        <i class="bi bi-shop-window me-2"></i>Branches
                    </a>
                    <a href="{{ route('attendances.index') }}" class="dropdown-item nav-link">
                        <i class="bi bi-person-check me-2"></i>Attendance
                    </a>
                    <a href="{{ route('company.list') }}" class="dropdown-item nav-link">
                        <i class="fa fa-industry me-2"></i>Company Type
                    </a>
                    <a href="{{ route('businessTypes.index') }}" class="dropdown-item nav-link">
                        <i class="bi bi-building me-2"></i>Business Types
                    </a>
                </div>
            </div>

            <!-- Contact Us Dropdown -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-comments me-2"></i>Contact Us
                </a>
                <div class="dropdown-menu bg-transparent border-0 submenu">
                    <a href="{{ route('admin.contact_form_submissions') }}" class="dropdown-item nav-link">
                        <i class="fa fa-envelope me-2"></i>Contact Form
                    </a>
                    <a href="{{ route('request-callback') }}" class="dropdown-item nav-link">
                        <i class="fa fa-phone me-2"></i>Callback Request
                    </a>
                    <a href="{{ route('admin.feedback_list') }}" class="dropdown-item nav-link">
                        <i class="fa fa-comment me-2"></i>Feedback
                    </a>
                </div>
            </div>

            <!-- Package Dropdown -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-box-open me-2"></i>Package
                </a>
                <div class="dropdown-menu bg-transparent border-0 submenu">
                    <a href="{{ route('packages.index') }}" class="dropdown-item nav-link">
                        <i class="fa fa-box me-2"></i>Package
                    </a>
                    <a href="{{ route('features.index') }}" class="dropdown-item nav-link">
                        <i class="fa fa-star me-2"></i>Package Features
                    </a>
                </div>
            </div>

            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-box-open me-2"></i>Salary
                </a>
                <div class="dropdown-menu bg-transparent border-0 submenu">
                    <a href="{{ route('admin.generateSalary') }}" class="dropdown-item nav-link">
                        <i class="fa fa-box me-2"></i>Generate Salary
                    </a>
                    <a href="{{ route('admin.employeeSalaryList') }}" class="dropdown-item nav-link">
                        <i class="fa fa-star me-2"></i>Monthly Salary
                    </a>
                </div>
            </div>

            <!-- Master Dropdown -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-layer-group me-2"></i>Master
                </a>
                <div class="dropdown-menu bg-transparent border-0 submenu">
                    <a href="{{ route('cms.list') }}" class="dropdown-item nav-link">
                        <i class="fa fa-file me-2"></i>CMS
                    </a>
                    <a href="{{ route('faq.index') }}" class="dropdown-item nav-link">
                        <i class="fa fa-question-circle me-2"></i>FAQ
                    </a>
                    <a href="{{ route('admin.tickets_list') }}" class="dropdown-item nav-link">
                        <i class="fa fa-ticket-alt me-2"></i>Ticket
                    </a>
                    <a href="{{ route('admin.help.index') }}" class="dropdown-item nav-link">
                        <i class="fa fa-info-circle me-2"></i>Help
                    </a>
                    <a href="{{ route('site_settings.index') }}" class="dropdown-item nav-link">
                        <i class="fa fa-cogs me-2"></i>Site Settings
                    </a>
                    <a href="{{ route('appbanner.index') }}" class="dropdown-item nav-link">
                        <i class="fa fa-image me-2"></i>App Banner
                    </a>
                    <a href="{{ route('homebanner.index') }}" class="dropdown-item nav-link">
                        <i class="fa fa-image me-2"></i>Home Banner
                    </a>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-box-open me-2"></i>Expense
                </a>
                <div class="dropdown-menu bg-transparent border-0 submenu">
                    <a href="{{ route('admin.expenseList') }}" class="dropdown-item nav-link">
                        <i class="fa fa-box me-2"></i>Expense List
                    </a>
                    <a href="{{ route('admin.expenseformList') }}" class="dropdown-item nav-link">
                        <i class="fa fa-star me-2"></i>Expense Form
                    </a>
                </div>
            </div>

            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-box-open me-2"></i>Blog
                </a>
                <div class="dropdown-menu bg-transparent border-0 submenu">
                    <a href="{{ route('admin.blog.index') }}" class="dropdown-item nav-link">
                        <i class="fa fa-box me-2"></i>List
                    </a>
                    <a href="{{ route('admin.blog_categories.index') }}" class="dropdown-item nav-link">
                        <i class="fa fa-star me-2"></i>Categories
                    </a>
                    <a href="{{ route('admin.blog_tag.index') }}" class="dropdown-item nav-link">
                        <i class="fa fa-star me-2"></i>Tags
                    </a>
                </div>
            </div>

            <a href="{{ route('chatList') }}" class="dropdown-item nav-link">
                <i class="fa fa-message me-2"></i>Chat List
            </a>
            <a href="{{ route('notification.send') }}" class="dropdown-item nav-link">
                <i class="fa fa-bell me-2"></i>Notification
            </a>
            <a href="{{ route('referralcode') }}" class="dropdown-item nav-link">
                <i class="fa fa-user-plus me-2"></i>Referral Code List
            </a>
            <a href="{{ route('admin.lead') }}" class="dropdown-item nav-link">
                <i class="fa fa-user-plus me-2"></i>CRM
            </a>
            <a href="{{ route('admin.taskList') }}" class="dropdown-item nav-link">
                <i class="fa fa-user-plus me-2"></i>Task
            </a>
            <a href="{{ route('admin.vehicles.index') }}" class="dropdown-item nav-link">
                <i class="fa fa-user-plus me-2"></i>Vehicle
            </a>
            <a href="{{ route('admin.trips.index') }}" class="dropdown-item nav-link">
                <i class="fa fa-user-plus me-2"></i>Trip
            </a>
        </div>
    </nav>
</div>
<!-- Sidebar End -->

<!-- Sidebar Dropdown Transition (Optional) -->
<style>
    .dropdown-menu {
        transition: all 0.3s ease-in-out;
    }

    .submenu {
        padding-left: 20px; font-size: 14px;
    }

    .submenu .fa {

        height: 30px !important;
        width: 30px !important;
    }
</style>
