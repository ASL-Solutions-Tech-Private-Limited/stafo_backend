@extends('admin.layouts.layout')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">

    <!-- Welcome Hero Banner -->
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0284c7 100%);">
        <div class="card-body p-4 p-md-5 text-white position-relative" style="z-index: 2;">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-15 mb-3">
                        <i class="fa-solid fa-crown text-warning" style="font-size: 0.8rem;"></i>
                        <span class="small fw-semibold">Master Administration Hub</span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                    <p class="text-white-50 mb-0" style="max-width: 600px; font-size: 0.95rem;">
                        Monitor your enterprise client ecosystem, employee records, attendance logs, and platform operations in real-time.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="d-inline-flex flex-column align-items-lg-end gap-2">
                        <span class="badge bg-success bg-opacity-25 text-white border border-success border-opacity-25 px-3 py-2 rounded-pill">
                            <i class="fa-solid fa-circle text-success me-1 fa-beat" style="font-size: 7px;"></i> All Systems Operational
                        </span>
                        <div class="text-white-50 small">{{ \Carbon\Carbon::now()->format('l, F j, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Decorative Ambient Rings -->
        <div style="position: absolute; right: -60px; bottom: -60px; width: 260px; height: 260px; border-radius: 50%; background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, transparent 70%); pointer-events: none;"></div>
    </div>

    <!-- 4 KPI Metrics Grid -->
    <div class="row g-3 g-md-4 mb-4">
        <!-- 1. Registered Companies -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="admin-stat-icon-wrapper admin-stat-gradient-blue">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 small fw-semibold">
                        <i class="fa-solid fa-arrow-trend-up me-1"></i> Active
                    </span>
                </div>
                <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Registered Companies</div>
                <h3 class="fw-bold text-dark mt-1 mb-2">{{ number_format($companyCount) }}</h3>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <span class="small text-muted">Client Organizations</span>
                    <a href="{{ route('company.details.list') }}" class="small fw-semibold text-primary text-decoration-none">
                        Manage <i class="fa-solid fa-chevron-right fs-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Total Employees -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="admin-stat-icon-wrapper admin-stat-gradient-emerald">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 small fw-semibold">
                        Staff Members
                    </span>
                </div>
                <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Total Employees</div>
                <h3 class="fw-bold text-dark mt-1 mb-2">{{ number_format($employeeCount) }}</h3>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <span class="small text-muted">Across all branches</span>
                    <a href="{{ route('employees.list') }}" class="small fw-semibold text-success text-decoration-none">
                        View List <i class="fa-solid fa-chevron-right fs-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. Today's Attendance -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="admin-stat-icon-wrapper admin-stat-gradient-purple">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </div>
                    <span class="badge bg-purple bg-opacity-10 text-dark rounded-pill px-2.5 py-1 small fw-semibold" style="background: rgba(147, 51, 234, 0.1); color: #7e22ce;">
                        Today
                    </span>
                </div>
                <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Attendance Punches</div>
                <h3 class="fw-bold text-dark mt-1 mb-2">{{ number_format($todayAttendanceCount) }}</h3>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <span class="small text-muted">Recorded logs</span>
                    <a href="{{ route('attendances.index') }}" class="small fw-semibold text-decoration-none" style="color: #7e22ce;">
                        Records <i class="fa-solid fa-chevron-right fs-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4. Support Tickets -->
        <div class="col-sm-6 col-xl-3">
            <div class="admin-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="admin-stat-icon-wrapper admin-stat-gradient-amber">
                        <i class="fa-solid fa-ticket-simple"></i>
                    </div>
                    <span class="badge bg-warning bg-opacity-15 text-dark rounded-pill px-2.5 py-1 small fw-semibold">
                        Support
                    </span>
                </div>
                <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Tickets &amp; Requests</div>
                <h3 class="fw-bold text-dark mt-1 mb-2">{{ number_format($ticketCount) }}</h3>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <span class="small text-muted">Customer queries</span>
                    <a href="{{ route('admin.tickets_list') }}" class="small fw-semibold text-warning text-dark text-decoration-none">
                        Review <i class="fa-solid fa-chevron-right fs-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts Grid -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-bolt text-warning me-2"></i>Quick Actions
                </h5>
                <span class="text-muted small">Frequent admin operations</span>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('company.details.list') }}" class="admin-quick-action h-100">
                <i class="fa-solid fa-building-circle-check text-primary"></i>
                <div>
                    <div class="fw-bold small">Companies</div>
                    <span class="text-muted" style="font-size: 0.7rem;">Directory</span>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('employees.list') }}" class="admin-quick-action h-100">
                <i class="fa-solid fa-user-plus text-success"></i>
                <div>
                    <div class="fw-bold small">Staff</div>
                    <span class="text-muted" style="font-size: 0.7rem;">Employees</span>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('admin.generateSalary') }}" class="admin-quick-action h-100">
                <i class="fa-solid fa-calculator text-indigo" style="color: #6366f1;"></i>
                <div>
                    <div class="fw-bold small">Payroll</div>
                    <span class="text-muted" style="font-size: 0.7rem;">Generate</span>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('admin.lead') }}" class="admin-quick-action h-100">
                <i class="fa-solid fa-chart-line text-info"></i>
                <div>
                    <div class="fw-bold small">CRM Leads</div>
                    <span class="text-muted" style="font-size: 0.7rem;">Pipeline</span>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('packages.index') }}" class="admin-quick-action h-100">
                <i class="fa-solid fa-boxes-stacked text-warning"></i>
                <div>
                    <div class="fw-bold small">Packages</div>
                    <span class="text-muted" style="font-size: 0.7rem;">Pricing</span>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('site_settings.index') }}" class="admin-quick-action h-100">
                <i class="fa-solid fa-gear text-secondary"></i>
                <div>
                    <div class="fw-bold small">Settings</div>
                    <span class="text-muted" style="font-size: 0.7rem;">Platform</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Content Section: Recent Companies & Platform Overview -->
    <div class="row g-4">
        <!-- Recent Registered Companies -->
        <div class="col-lg-8">
            <div class="card admin-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header admin-card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-building me-2 text-primary"></i>Recently Registered Companies
                        </h6>
                        <small class="text-muted">Latest organizations that created a tenant account</small>
                    </div>
                    <a href="{{ route('company.details.list') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        View All <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-4 py-3">Company</th>
                                    <th class="py-3">Contact Email</th>
                                    <th class="py-3">Registered On</th>
                                    <th class="text-end pe-4 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCompanies as $comp)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.8rem;">
                                                    {{ strtoupper(substr($comp->company_name ?? 'C', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $comp->company_name }}</div>
                                                    <small class="text-muted">ID: #{{ $comp->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="text-dark">{{ $comp->email ?? 'N/A' }}</span>
                                        </td>
                                        <td class="py-3 text-muted small">
                                            {{ $comp->created_at ? $comp->created_at->format('d M Y, h:i A') : 'N/A' }}
                                        </td>
                                        <td class="text-end pe-4 py-3">
                                            <a href="{{ route('company.details.list') }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-primary" title="View Details">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            No recent companies found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- System & Security Overview Card -->
        <div class="col-lg-4">
            <div class="card admin-card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header admin-card-header bg-white py-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-server me-2 text-success"></i>System Health &amp; Environment
                    </h6>
                    <small class="text-muted">Platform diagnostic status</small>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-database text-primary"></i>
                            <span class="small fw-semibold text-dark">Database Connection</span>
                        </div>
                        <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-2 py-1 small">
                            Connected
                        </span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-brands fa-php text-indigo" style="color: #6366f1;"></i>
                            <span class="small fw-semibold text-dark">PHP Engine</span>
                        </div>
                        <span class="small text-muted fw-bold">v{{ phpversion() }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-brands fa-laravel text-danger"></i>
                            <span class="small fw-semibold text-dark">Framework</span>
                        </div>
                        <span class="small text-muted fw-bold">Laravel {{ app()->version() }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-lock text-success"></i>
                            <span class="small fw-semibold text-dark">HTTPS / SSL</span>
                        </div>
                        <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-2 py-1 small">
                            Active
                        </span>
                    </div>

                    <div class="p-3 rounded-3 border border-primary-subtle bg-primary bg-opacity-10 text-primary">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-shield-halved fs-5"></i>
                            <strong class="small">Super Admin Privileges</strong>
                        </div>
                        <p class="mb-0 text-secondary" style="font-size: 0.775rem;">
                            You have full root access to system settings, company tenancies, payroll calculations, and master configurations.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection