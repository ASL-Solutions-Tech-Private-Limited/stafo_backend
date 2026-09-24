<!-- Modern Admin Header (Topbar) -->
<header class="admin-header">
    <div class="admin-header-left">
        <!-- Sidebar Toggle Button -->
        <button type="button" class="admin-toggler-btn" id="sidebarToggleBtn" aria-label="Toggle Sidebar Navigation">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>

        <!-- Current Area Title / Quick Breadcrumb -->
        <div class="d-none d-sm-block">
            <h1 class="admin-header-title fs-6">
                <span>Super Admin Workspace</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.7rem;">
                    <i class="fa-regular fa-calendar me-1"></i>{{ \Carbon\Carbon::now()->format('D, d M Y') }}
                </span>
            </h1>
        </div>
    </div>

    <div class="admin-header-right">
        <!-- Quick Link: Visit Front Website -->
        <a href="{{ url('/') }}" target="_blank" class="admin-icon-btn d-none d-md-inline-flex" title="Visit Public Website">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>

        <!-- Quick Notifications -->
        <a href="{{ route('admin.tickets_list') }}" class="admin-icon-btn position-relative" title="Support Tickets">
            <i class="fa-regular fa-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 8px; height: 8px;"></span>
        </a>

        <!-- User Profile Dropdown Pill -->
        <div class="dropdown">
            <a href="#" class="admin-profile-btn" data-bs-toggle="dropdown" aria-expanded="false">
                @if(Auth::user()->image && file_exists(public_path('uploads/' . Auth::user()->image)))
                    <img class="admin-avatar" src="{{ asset('uploads/' . Auth::user()->image) }}" alt="{{ Auth::user()->name }}">
                @else
                    <div class="admin-avatar-initials">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                    </div>
                @endif
                <div class="d-none d-lg-block text-start lh-1">
                    <div class="fw-bold text-dark small">{{ Auth::user()->name }}</div>
                    <span class="text-muted" style="font-size: 0.68rem;">Super Admin</span>
                </div>
                <i class="fa-solid fa-chevron-down text-muted ms-1" style="font-size: 0.65rem;"></i>
            </a>

            <!-- Modern Dropdown Menu -->
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" style="min-width: 220px;">
                <li class="px-3 py-2 border-bottom mb-1">
                    <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                    <div class="text-muted small text-truncate" style="font-size: 0.75rem;">{{ Auth::user()->email ?? 'superadmin@stafo.in' }}</div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-0.5 mt-1" style="font-size: 0.65rem;">
                        <i class="fa-solid fa-shield-halved me-1"></i>Master Admin
                    </span>
                </li>
                <li>
                    <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                        <i class="fa-solid fa-gauge-high text-primary width-16"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('admin.profile') }}">
                        <i class="fa-solid fa-user-gear text-info width-16"></i> My Profile 
                    </a>
                </li>
                <li>
                    <a class="dropdown-item rounded-3 py-2 small d-flex align-items-center gap-2" href="{{ route('site_settings.index') }}">
                        <i class="fa-solid fa-sliders text-secondary width-16"></i> System Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1 opacity-50"></li>
                <li>
                    <a class="dropdown-item rounded-3 py-2 small text-danger fw-semibold d-flex align-items-center gap-2" href="{{ route('admin_logout') }}">
                        <i class="fa-solid fa-right-from-bracket width-16"></i> Sign Out
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>