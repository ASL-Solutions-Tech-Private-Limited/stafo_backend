<!-- Modern Super Admin Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <!-- Sidebar Header / Brand Logo -->
    <div class="admin-sidebar-header">
        <a href="{{ route('dashboard') }}" class="admin-sidebar-brand">
            <img src="{{ asset('main/images/logo.png') }}" alt="STAFO Logo">
            <span class="admin-sidebar-brand-text">STAFO</span>
            <span class="admin-sidebar-badge">ADMIN</span>
        </a>
        <button type="button" class="btn-close btn-close-white d-lg-none" id="closeSidebarBtn" aria-label="Close Sidebar"></button>
    </div>

    <!-- Navigation Scroll Area -->
    <div class="admin-sidebar-body">
        <div class="navbar-nav w-100">

            <!-- Section: Core -->
            <div class="admin-menu-heading">Main</div>

            <!-- Dashboard -->
            <div class="admin-nav-item">
                <a href="{{ route('dashboard') }}" class="admin-nav-link {{ request()->routeIs('dashboard') || request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>

            <!-- Admin Profile -->
            <div class="admin-nav-item">
                <a href="{{ route('admin.profile') }}" class="admin-nav-link {{ request()->routeIs('admin.profile') || request()->is('admin/profile*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield nav-icon"></i>
                    <span class="nav-text">Admin Profile</span>
                </a>
            </div>

            <!-- Section: Management -->
            <div class="admin-menu-heading">Management</div>

            <!-- 1. Company & Staff -->
            @php
                $isCompanyActive = request()->is('admin/company*') || request()->is('admin/employee*') || request()->is('admin/document*') || request()->is('admin/branches*') || request()->is('admin/attendances*') || request()->is('admin/businessTypes*');
            @endphp
            <div class="admin-nav-item admin-nav-dropdown {{ $isCompanyActive ? 'is-open' : '' }}">
                <a href="#collapseCompanyStaff" class="admin-nav-link {{ $isCompanyActive ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isCompanyActive ? 'true' : 'false' }}" aria-controls="collapseCompanyStaff">
                    <div class="admin-nav-link-content">
                        <i class="fa-solid fa-building-user nav-icon"></i>
                        <span class="nav-text">Company &amp; Staff</span>
                    </div>
                    <i class="fa-solid fa-chevron-right admin-dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $isCompanyActive ? 'show' : '' }}" id="collapseCompanyStaff">
                    <ul class="admin-submenu">
                        <li>
                            <a href="{{ route('company.details.list') }}" class="admin-sub-item {{ request()->is('admin/company-details*') ? 'active' : '' }}">
                                <i class="fa-solid fa-building width-16"></i> Companies
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('employees.list') }}" class="admin-sub-item {{ request()->is('admin/employee*') ? 'active' : '' }}">
                                <i class="fa-solid fa-user-tie width-16"></i> Employees
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('document_list') }}" class="admin-sub-item {{ request()->is('admin/document*') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-shield width-16"></i> Documents
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('branches.index') }}" class="admin-sub-item {{ request()->is('admin/branches*') ? 'active' : '' }}">
                                <i class="fa-solid fa-store width-16"></i> Branches
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('attendances.index') }}" class="admin-sub-item {{ request()->is('admin/attendances*') ? 'active' : '' }}">
                                <i class="fa-solid fa-clipboard-user width-16"></i> Attendance
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('company.list') }}" class="admin-sub-item {{ request()->is('admin/company-types*') ? 'active' : '' }}">
                                <i class="fa-solid fa-industry width-16"></i> Company Types
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('businessTypes.index') }}" class="admin-sub-item {{ request()->is('admin/businessTypes*') ? 'active' : '' }}">
                                <i class="fa-solid fa-briefcase width-16"></i> Business Types
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 2. HR & Payroll -->
            @php
                $isSalaryActive = request()->is('admin/salary*') || request()->is('admin/generateSalary*') || request()->is('admin/employeeSalaryList*');
            @endphp
            <div class="admin-nav-item admin-nav-dropdown {{ $isSalaryActive ? 'is-open' : '' }}">
                <a href="#collapseSalary" class="admin-nav-link {{ $isSalaryActive ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isSalaryActive ? 'true' : 'false' }}" aria-controls="collapseSalary">
                    <div class="admin-nav-link-content">
                        <i class="fa-solid fa-money-check-dollar nav-icon"></i>
                        <span class="nav-text">HR &amp; Payroll</span>
                    </div>
                    <i class="fa-solid fa-chevron-right admin-dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $isSalaryActive ? 'show' : '' }}" id="collapseSalary">
                    <ul class="admin-submenu">
                        <li>
                            <a href="{{ route('admin.generateSalary') }}" class="admin-sub-item {{ request()->is('admin/generateSalary*') ? 'active' : '' }}">
                                <i class="fa-solid fa-calculator width-16"></i> Generate Salary
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.employeeSalaryList') }}" class="admin-sub-item {{ request()->is('admin/employeeSalaryList*') ? 'active' : '' }}">
                                <i class="fa-solid fa-receipt width-16"></i> Monthly Salary
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 3. CRM & Communication -->
            @php
                $isCrmActive = request()->is('admin/lead*') || request()->is('admin/task*') || request()->is('admin/chat*') || request()->is('admin/notification*') || request()->is('admin/contact-form*') || request()->is('admin/callback-requests*') || request()->is('admin/feedback*') || request()->is('admin/tickets*');
            @endphp
            <div class="admin-nav-item admin-nav-dropdown {{ $isCrmActive ? 'is-open' : '' }}">
                <a href="#collapseCRM" class="admin-nav-link {{ $isCrmActive ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isCrmActive ? 'true' : 'false' }}" aria-controls="collapseCRM">
                    <div class="admin-nav-link-content">
                        <i class="fa-solid fa-comments-dollar nav-icon"></i>
                        <span class="nav-text">CRM &amp; Inquiries</span>
                    </div>
                    <i class="fa-solid fa-chevron-right admin-dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $isCrmActive ? 'show' : '' }}" id="collapseCRM">
                    <ul class="admin-submenu">
                        <li>
                            <a href="{{ route('admin.lead') }}" class="admin-sub-item {{ request()->is('admin/lead*') ? 'active' : '' }}">
                                <i class="fa-solid fa-filter-circle-dollar width-16"></i> CRM Leads
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.taskList') }}" class="admin-sub-item {{ request()->is('admin/task*') ? 'active' : '' }}">
                                <i class="fa-solid fa-list-check width-16"></i> Tasks
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('chatList') }}" class="admin-sub-item {{ request()->is('admin/chat*') ? 'active' : '' }}">
                                <i class="fa-solid fa-comments width-16"></i> Support Chat
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('notification.send') }}" class="admin-sub-item {{ request()->is('admin/notification*') ? 'active' : '' }}">
                                <i class="fa-solid fa-paper-plane width-16"></i> Send Push Notifications
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.contact_form_submissions') }}" class="admin-sub-item {{ request()->is('admin/contact-form*') ? 'active' : '' }}">
                                <i class="fa-solid fa-envelope-open-text width-16"></i> Contact Forms
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('request-callback') }}" class="admin-sub-item {{ request()->is('admin/callback-requests*') ? 'active' : '' }}">
                                <i class="fa-solid fa-phone-volume width-16"></i> Callback Requests
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.tickets_list') }}" class="admin-sub-item {{ request()->is('admin/tickets*') ? 'active' : '' }}">
                                <i class="fa-solid fa-ticket-simple width-16"></i> Support Tickets
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.feedback_list') }}" class="admin-sub-item {{ request()->is('admin/feedback*') ? 'active' : '' }}">
                                <i class="fa-solid fa-star-half-stroke width-16"></i> User Feedback
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 4. Fleet & Logistics -->
            @php
                $isFleetActive = request()->is('admin/vehicles*') || request()->is('admin/trips*');
            @endphp
            <div class="admin-nav-item admin-nav-dropdown {{ $isFleetActive ? 'is-open' : '' }}">
                <a href="#collapseFleet" class="admin-nav-link {{ $isFleetActive ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isFleetActive ? 'true' : 'false' }}" aria-controls="collapseFleet">
                    <div class="admin-nav-link-content">
                        <i class="fa-solid fa-truck-moving nav-icon"></i>
                        <span class="nav-text">Fleet &amp; Logistics</span>
                    </div>
                    <i class="fa-solid fa-chevron-right admin-dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $isFleetActive ? 'show' : '' }}" id="collapseFleet">
                    <ul class="admin-submenu">
                        <li>
                            <a href="{{ route('admin.vehicles.index') }}" class="admin-sub-item {{ request()->is('admin/vehicles*') ? 'active' : '' }}">
                                <i class="fa-solid fa-van-shuttle width-16"></i> Vehicles
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.trips.index') }}" class="admin-sub-item {{ request()->is('admin/trips*') ? 'active' : '' }}">
                                <i class="fa-solid fa-route width-16"></i> Trips
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 5. Expense Tracking -->
            @php
                $isExpenseActive = request()->is('admin/expense*');
            @endphp
            <div class="admin-nav-item admin-nav-dropdown {{ $isExpenseActive ? 'is-open' : '' }}">
                <a href="#collapseExpense" class="admin-nav-link {{ $isExpenseActive ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isExpenseActive ? 'true' : 'false' }}" aria-controls="collapseExpense">
                    <div class="admin-nav-link-content">
                        <i class="fa-solid fa-wallet nav-icon"></i>
                        <span class="nav-text">Expenses</span>
                    </div>
                    <i class="fa-solid fa-chevron-right admin-dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $isExpenseActive ? 'show' : '' }}" id="collapseExpense">
                    <ul class="admin-submenu">
                        <li>
                            <a href="{{ route('admin.expenseList') }}" class="admin-sub-item {{ request()->is('admin/expenseList*') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-invoice-dollar width-16"></i> Expense List
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.expenseformList') }}" class="admin-sub-item {{ request()->is('admin/expenseformList*') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-signature width-16"></i> Expense Forms
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Section: System & Marketing -->
            <div class="admin-menu-heading">Platform</div>

            <!-- 6. Subscription Packages -->
            @php
                $isPkgActive = request()->is('admin/packages*') || request()->is('admin/features*');
            @endphp
            <div class="admin-nav-item admin-nav-dropdown {{ $isPkgActive ? 'is-open' : '' }}">
                <a href="#collapsePackage" class="admin-nav-link {{ $isPkgActive ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isPkgActive ? 'true' : 'false' }}" aria-controls="collapsePackage">
                    <div class="admin-nav-link-content">
                        <i class="fa-solid fa-layer-group nav-icon"></i>
                        <span class="nav-text">SaaS Packages</span>
                    </div>
                    <i class="fa-solid fa-chevron-right admin-dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $isPkgActive ? 'show' : '' }}" id="collapsePackage">
                    <ul class="admin-submenu">
                        <li>
                            <a href="{{ route('packages.index') }}" class="admin-sub-item {{ request()->is('admin/packages*') ? 'active' : '' }}">
                                <i class="fa-solid fa-boxes-stacked width-16"></i> Packages
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('features.index') }}" class="admin-sub-item {{ request()->is('admin/features*') ? 'active' : '' }}">
                                <i class="fa-solid fa-sparkles width-16"></i> Package Features
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 7. Blog & Articles -->
            @php
                $isBlogActive = request()->is('admin/blog*');
            @endphp
            <div class="admin-nav-item admin-nav-dropdown {{ $isBlogActive ? 'is-open' : '' }}">
                <a href="#collapseBlog" class="admin-nav-link {{ $isBlogActive ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isBlogActive ? 'true' : 'false' }}" aria-controls="collapseBlog">
                    <div class="admin-nav-link-content">
                        <i class="fa-solid fa-newspaper nav-icon"></i>
                        <span class="nav-text">Blog &amp; Articles</span>
                    </div>
                    <i class="fa-solid fa-chevron-right admin-dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $isBlogActive ? 'show' : '' }}" id="collapseBlog">
                    <ul class="admin-submenu">
                        <li>
                            <a href="{{ route('admin.blog.index') }}" class="admin-sub-item {{ request()->is('admin/blog') ? 'active' : '' }}">
                                <i class="fa-solid fa-pen-to-square width-16"></i> All Articles
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.blog_categories.index') }}" class="admin-sub-item {{ request()->is('admin/blog_categories*') ? 'active' : '' }}">
                                <i class="fa-solid fa-tags width-16"></i> Categories
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.blog_tag.index') }}" class="admin-sub-item {{ request()->is('admin/blog_tag*') ? 'active' : '' }}">
                                <i class="fa-solid fa-hashtag width-16"></i> Tags
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 8. Master CMS & Settings -->
            @php
                $isMasterActive = request()->is('admin/cms*') || request()->is('admin/faq*') || request()->is('admin/help*') || request()->is('admin/site_settings*') || request()->is('admin/appbanner*') || request()->is('admin/homebanner*') || request()->is('admin/referralcode*');
            @endphp
            <div class="admin-nav-item admin-nav-dropdown {{ $isMasterActive ? 'is-open' : '' }}">
                <a href="#collapseMaster" class="admin-nav-link {{ $isMasterActive ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}" aria-controls="collapseMaster">
                    <div class="admin-nav-link-content">
                        <i class="fa-solid fa-sliders nav-icon"></i>
                        <span class="nav-text">Master Settings</span>
                    </div>
                    <i class="fa-solid fa-chevron-right admin-dropdown-arrow"></i>
                </a>
                <div class="collapse {{ $isMasterActive ? 'show' : '' }}" id="collapseMaster">
                    <ul class="admin-submenu">
                        <li>
                            <a href="{{ route('cms.list') }}" class="admin-sub-item {{ request()->is('admin/cms*') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-code width-16"></i> CMS Pages
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faq.index') }}" class="admin-sub-item {{ request()->is('admin/faq*') ? 'active' : '' }}">
                                <i class="fa-solid fa-circle-question width-16"></i> FAQs
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.help.index') }}" class="admin-sub-item {{ request()->is('admin/help*') ? 'active' : '' }}">
                                <i class="fa-solid fa-circle-info width-16"></i> Help Contents
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('appbanner.index') }}" class="admin-sub-item {{ request()->is('admin/appbanner*') ? 'active' : '' }}">
                                <i class="fa-solid fa-mobile-screen width-16"></i> App Banners
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('homebanner.index') }}" class="admin-sub-item {{ request()->is('admin/homebanner*') ? 'active' : '' }}">
                                <i class="fa-solid fa-images width-16"></i> Home Banners
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('referralcode') }}" class="admin-sub-item {{ request()->is('admin/referralcode*') ? 'active' : '' }}">
                                <i class="fa-solid fa-ticket width-16"></i> Referral Codes
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('site_settings.index') }}" class="admin-sub-item {{ request()->is('admin/site_settings*') ? 'active' : '' }}">
                                <i class="fa-solid fa-gear width-16"></i> Global Settings
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <!-- Sidebar User Footer -->
    <div class="admin-sidebar-footer">
        <div class="admin-user-pill">
            <a href="{{ route('admin.profile') }}" class="d-flex align-items-center flex-grow-1 overflow-hidden text-decoration-none" title="View & Edit Admin Profile">
                @if(Auth::user()->image && file_exists(public_path('uploads/' . Auth::user()->image)))
                    <img class="admin-avatar" src="{{ asset('uploads/' . Auth::user()->image) }}" alt="{{ Auth::user()->name }}">
                @else
                    <div class="admin-avatar-initials">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                    </div>
                @endif
                <div class="admin-user-info flex-grow-1 overflow-hidden ms-2">
                    <div class="text-white fw-bold small text-truncate">{{ Auth::user()->name }}</div>
                    <div class="text-white-50" style="font-size: 0.7rem;">Super Admin</div>
                </div>
            </a>
            <a href="{{ route('admin_logout') }}" class="text-danger p-1 ms-1" title="Sign Out" onclick="return confirm('Are you sure you want to sign out?')">
                <i class="fa-solid fa-power-off"></i>
            </a>
        </div>
    </div>
</aside>

<!-- Backdrop overlay for mobile drawer -->
<div class="admin-sidebar-backdrop" id="adminSidebarBackdrop"></div>
