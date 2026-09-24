@extends('user.layouts.app')
@section('title', 'Company Roles & Access Permissions | STAFO HRMS')

@section('content')
<div class="container-fluid px-0 px-md-2">

    <!-- Top Hero Header -->
    <div class="dashboard-hero-banner mb-4">
        <div class="row align-items-center g-3">
            <div class="col-12 col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill hero-pill-badge mb-2">
                    <i class="fa-solid fa-shield-halved text-warning"></i>
                    <span class="fw-bold">Designation-Based Access Control</span>
                </div>
                <h2 class="text-white mb-2 fw-bold">
                    Company Roles & Permissions
                </h2>
                <p class="text-white-50 mb-0" style="font-size: 0.92rem; max-width: 620px;">
                    In your organization, <strong>Designation is the Role</strong>. Assign designations to staff members and configure administrative privileges for each designation.
                </p>
            </div>
            <div class="col-12 col-lg-5 text-lg-end">
                <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                    <a href="{{ route('designations.create') }}" class="btn btn-hero-manage px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-primary"></i>
                        <span>Add New Designation</span>
                    </a>
                    <a href="{{ route('designations.index') }}" class="btn btn-outline-light px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="border-radius: 10px;">
                        <i class="fa-solid fa-id-badge"></i>
                        <span>Designations Directory</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Mini Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="kpi-card kpi-primary">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="kpi-title">Active Designations</span>
                    <div class="kpi-icon-wrapper">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </div>
                </div>
                <h3 class="kpi-value mb-1">{{ $totalRoles }}</h3>
                <span class="text-muted small">Designations acting as company roles</span>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="kpi-card kpi-success">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="kpi-title">Assigned Staff</span>
                    <div class="kpi-icon-wrapper">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
                <h3 class="kpi-value text-success mb-1">{{ $assignedStaffCount }}</h3>
                <span class="text-muted small">Employees with assigned designation roles</span>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="kpi-card kpi-purple">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="kpi-title">Unassigned Staff</span>
                    <div class="kpi-icon-wrapper">
                        <i class="fa-solid fa-user-clock"></i>
                    </div>
                </div>
                <h3 class="kpi-value kpi-purple-val mb-1">{{ $standardStaffCount }}</h3>
                <span class="text-muted small">Employees on default portal access</span>
            </div>
        </div>
    </div>

    <!-- Explanatory Banner Note -->
    <div class="alert alert-info border-0 shadow-xs rounded-3 mb-4 d-flex align-items-start gap-3 p-3" style="background: rgba(14, 165, 233, 0.08); border-left: 4px solid #0ea5e9 !important;">
        <div class="p-1 text-info fs-5 mt-0.5">
            <i class="fa-solid fa-circle-info"></i>
        </div>
        <div class="small text-dark lh-sm">
            <strong class="d-block mb-1 text-dark">Designation is the Employee's Role:</strong>
            <span class="text-muted">
                Each employee's assigned designation directly determines their role and permissions. 
                All employees automatically have default self-service access to punch attendance, apply for leaves, view payslips, and access their profile. 
                Configure permissions below to give specific designations administrative management powers (such as approving leaves, calculating payroll, or managing attendance).
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
            <div class="p-2 rounded-circle bg-success text-white me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="fw-semibold text-dark">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
            <div class="p-2 rounded-circle bg-danger text-white me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="fa-solid fa-exclamation"></i>
            </div>
            <div class="fw-semibold text-dark">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- SECTION 1: Designations (Roles) Grid -->
    <div class="stafo-card mb-4">
        <div class="stafo-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="stafo-card-title">
                    <i class="fa-solid fa-id-badge text-primary me-2"></i> Company Designations & Roles
                </h5>
                <span class="text-muted small">Designations acting as roles with configured administrative privileges</span>
            </div>
            <a href="{{ route('designations.create') }}" class="btn btn-sm btn-primary px-3 rounded-pill fw-semibold shadow-xs">
                <i class="fa-solid fa-plus me-1"></i> Add New Designation
            </a>
        </div>

        <div class="p-3 p-md-4">
            <div class="row g-3">
                @forelse ($roles as $role)
                    @php
                        $rolePermCount = $role->permissions->count();
                        $assignedCount = $role->employees_count;
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="role-card h-100 p-3.5 rounded-3 border d-flex flex-column justify-content-between transition-all" id="role-card-{{ $role->id }}">
                            <div>
                                <!-- Header: Role/Designation Name, Status & Options -->
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="role-icon-box shadow-2xs">
                                            <i class="fa-solid fa-user-shield text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $role->name }}</h6>
                                            <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                                @if ($role->status)
                                                    <span class="badge badge-stafo badge-stafo-success" style="font-size: 10px;">Active</span>
                                                @else
                                                    <span class="badge badge-stafo badge-stafo-danger" style="font-size: 10px;">Inactive</span>
                                                @endif
                                                <span class="text-muted small">•</span>
                                                <span class="text-muted small fw-medium" style="font-size: 11px;">
                                                    <i class="fa-solid fa-users text-primary me-1"></i> {{ $assignedCount }} Staff Assigned
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border-0 p-1 rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Options">
                                            <i class="fa-solid fa-ellipsis-vertical text-muted"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm py-1">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('designations.edit', $role->id) }}">
                                                    <i class="fa-solid fa-pen-to-square text-primary"></i>
                                                    <span>Edit Designation</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Description -->
                                <p class="text-muted small mb-3 lh-sm" style="font-size: 12px; min-height: 36px;">
                                    {{ $role->description ?: 'No description specified for this designation.' }}
                                </p>

                                <!-- Permission Summary Pill -->
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-2 role-meta-strip mb-3">
                                    <span class="text-muted small fw-medium">Administrative Access:</span>
                                    <div id="rolePermBadgeWrap_{{ $role->id }}">
                                        @if ($rolePermCount > 0)
                                            <span class="badge badge-stafo badge-stafo-info fw-bold">
                                                <i class="fa-solid fa-key me-1"></i> {{ $rolePermCount }} of {{ $totalCatalogPerms }} Active
                                            </span>
                                        @else
                                            <span class="badge badge-stafo badge-stafo-subtle fw-medium">
                                                Standard Access Only
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Footer: Manage Permissions Button -->
                            <div class="pt-2 border-top d-flex align-items-center justify-content-between">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold w-100" onclick="openEditRoleModal({{ $role->id }})">
                                    <i class="fa-solid fa-sliders me-1.5"></i> Configure Permissions
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-id-badge fs-1 text-muted opacity-50 mb-3 d-block"></i>
                            <h6 class="fw-bold text-dark">No designations found</h6>
                            <p class="small mb-3">Create your first company designation to assign roles & privileges.</p>
                            <a href="{{ route('designations.create') }}" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-plus me-1"></i> Add New Designation
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- SECTION 2: Staff Role Assignment Directory (Designation = Role) -->
    <div class="stafo-card mb-4">
        <div class="stafo-card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 py-3">
            <div>
                <h5 class="stafo-card-title mb-0">
                    <i class="fa-solid fa-users text-primary me-2"></i> Staff Role Assignment Directory
                </h5>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap flex-sm-nowrap">
                <!-- Modern Integrated Search Box -->
                <div class="position-relative search-input-container" style="min-width: 220px; max-width: 280px; width: 100%;">
                    <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.85rem; pointer-events: none;"></i>
                    <input type="text" 
                           id="employeeDirectorySearch" 
                           class="form-control form-control-sm ps-5 rounded-pill border shadow-xs" 
                           placeholder="Search staff by name or ID..." 
                           value="{{ request('search') }}" 
                           oninput="debounceFetchEmployeeTable()" 
                           style="height: 38px; font-size: 0.84rem;">
                </div>

                <!-- Modern Role Filter Dropdown -->
                <div class="role-filter-container" style="min-width: 180px;">
                    <select id="roleFilterSelect" 
                            class="form-select form-select-sm rounded-pill border shadow-xs" 
                            style="height: 38px; font-size: 0.84rem;" 
                            onchange="fetchEmployeeTable(1)">
                        <option value="">All Roles (Designations)</option>
                        <option value="__none__" {{ request('role_filter') === '__none__' ? 'selected' : '' }}>Standard Access Only</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->id }}" {{ request('role_filter') == $r->id ? 'selected' : '' }}>
                                {{ $r->name }} ({{ $r->employees_count }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Paginated Employee Directory Table (Backend-Managed, AJAX No Reload) -->
        <div id="employeeTableWrapper">
            @include('user.roles.partials.employee_table')
        </div>
    </div>

</div>

<!-- ========================================================
     MODAL: CONFIGURE DESIGNATION PERMISSIONS (INSTANT LIVE SYNC)
     ======================================================== -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <div class="modal-header border-bottom py-2.5 px-3 px-md-4">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fa-solid fa-shield-halved fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="roleModalLabel">Configure Role Permissions</h5>
                        <small class="text-muted" id="roleModalSub">Instant live auto-save: Check to assign, uncheck to unassign</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-3 p-md-4">
                <input type="hidden" id="roleIdInput" value="">

                <!-- Top Strip: Designation Title & Live Sync Status (Short & Compact) -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-2.5 px-3 rounded-3 mb-3 bg-light border">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Designation (Role):</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fw-bold fs-6 d-inline-flex align-items-center gap-1.5" id="modalRoleNameBadge">
                            <i class="fa-solid fa-id-card"></i>
                            <span id="modalRoleNameText">Loading...</span>
                        </span>
                    </div>

                    <!-- Live Auto-Save Indicator -->
                    <div id="modalLiveSyncStatus" class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill small bg-success-subtle text-success border border-success-subtle fw-semibold" style="font-size: 0.78rem;">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span id="modalLiveSyncText">Auto-saved</span>
                    </div>
                </div>

                <!-- Permissions Control Bar (Count + Select All / Clear All) -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-2 px-3 rounded-3 mb-3" style="background: rgba(99, 102, 241, 0.08); border: 1px solid rgba(99, 102, 241, 0.18);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-key text-primary"></i>
                        <span class="fw-bold text-dark small">Management Rights:</span>
                        <span class="badge bg-primary text-white px-2.5 py-1 rounded-pill small" id="modalActivePermCountBadge">
                            <span id="modalSelectedPermCount">0</span> / {{ $totalCatalogPerms }} Assigned
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <button type="button" class="btn btn-sm btn-outline-primary px-2.5 py-1 rounded-pill fw-semibold" onclick="modalToggleAllLive(true)" title="Assign All Permissions">
                            <i class="fa-solid fa-check-double me-1"></i> Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 py-1 rounded-pill fw-semibold" onclick="modalToggleAllLive(false)" title="Unassign All Permissions">
                            <i class="fa-solid fa-xmark me-1"></i> Clear All
                        </button>
                    </div>
                </div>

                <!-- Permissions Matrix Table (Exact Match to Screenshot: # | INTERFACE NAME | INSERT | EDIT | DELETE | VIEW | ALL) -->
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0 permissions-matrix-table">
                        <thead class="bg-light">
                            <tr class="text-muted small">
                                <th class="text-center py-2.5 px-3" style="width: 50px;">#</th>
                                <th class="py-2.5 px-3 text-uppercase fw-bold" style="min-width: 220px;">INTERFACE NAME</th>
                                <th class="text-center py-2.5 px-2" style="width: 110px;">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="text-uppercase fw-bold text-dark" style="font-size: 0.76rem;">INSERT</span>
                                        <input type="checkbox" id="colToggle_create" class="form-check-input cursor-pointer" onchange="modalToggleColumnLive('create', this.checked)" title="Toggle all INSERT rights">
                                    </div>
                                </th>
                                <th class="text-center py-2.5 px-2" style="width: 110px;">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="text-uppercase fw-bold text-dark" style="font-size: 0.76rem;">EDIT</span>
                                        <input type="checkbox" id="colToggle_edit" class="form-check-input cursor-pointer" onchange="modalToggleColumnLive('edit', this.checked)" title="Toggle all EDIT rights">
                                    </div>
                                </th>
                                <th class="text-center py-2.5 px-2" style="width: 110px;">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="text-uppercase fw-bold text-dark" style="font-size: 0.76rem;">DELETE</span>
                                        <input type="checkbox" id="colToggle_delete" class="form-check-input cursor-pointer" onchange="modalToggleColumnLive('delete', this.checked)" title="Toggle all DELETE rights">
                                    </div>
                                </th>
                                <th class="text-center py-2.5 px-2" style="width: 110px;">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="text-uppercase fw-bold text-dark" style="font-size: 0.76rem;">VIEW</span>
                                        <input type="checkbox" id="colToggle_view" class="form-check-input cursor-pointer" onchange="modalToggleColumnLive('view', this.checked)" title="Toggle all VIEW rights">
                                    </div>
                                </th>
                                <th class="text-center py-2.5 px-2" style="width: 80px;">
                                    <span class="text-uppercase fw-bold text-dark" style="font-size: 0.76rem;">ALL</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($catalog as $modKey => $module)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                                    <td class="px-3">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="p-1.5 rounded-2 bg-light border d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                <i class="{{ $module['icon'] }}" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark lh-sm" style="font-size: 0.88rem;">{{ $module['name'] }}</span>
                                                <span class="text-muted font-monospace" style="font-size: 10px;">{{ $modKey }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input modal-perm-cb mod-cb-{{ $modKey }} col-cb-create cursor-pointer" 
                                               type="checkbox" 
                                               value="{{ $modKey }}.create" 
                                               id="perm_{{ $modKey }}_create"
                                               onchange="modalToggleSinglePermLive('{{ $modKey }}.create', this)"
                                               title="Insert / Add {{ $module['name'] }}">
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input modal-perm-cb mod-cb-{{ $modKey }} col-cb-edit cursor-pointer" 
                                               type="checkbox" 
                                               value="{{ $modKey }}.edit" 
                                               id="perm_{{ $modKey }}_edit"
                                               onchange="modalToggleSinglePermLive('{{ $modKey }}.edit', this)"
                                               title="Edit {{ $module['name'] }}">
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input modal-perm-cb mod-cb-{{ $modKey }} col-cb-delete cursor-pointer" 
                                               type="checkbox" 
                                               value="{{ $modKey }}.delete" 
                                               id="perm_{{ $modKey }}_delete"
                                               onchange="modalToggleSinglePermLive('{{ $modKey }}.delete', this)"
                                               title="Delete {{ $module['name'] }}">
                                    </td>
                                    <td class="text-center">
                                        <input class="form-check-input modal-perm-cb mod-cb-{{ $modKey }} col-cb-view cursor-pointer" 
                                               type="checkbox" 
                                               value="{{ $modKey }}.view" 
                                               id="perm_{{ $modKey }}_view"
                                               onchange="modalToggleSinglePermLive('{{ $modKey }}.view', this)"
                                               title="View {{ $module['name'] }}">
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block mb-0">
                                            <input class="form-check-input mod-switch custom-switch cursor-pointer" 
                                                   type="checkbox" 
                                                   role="switch" 
                                                   id="modToggle_{{ $modKey }}" 
                                                   title="Toggle all 4 permissions for {{ $module['name'] }}"
                                                   onchange="modalToggleModuleLive('{{ $modKey }}', this.checked)">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer border-top py-2.5 px-4 d-flex align-items-center justify-content-between">
                <span class="text-muted small d-inline-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-bolt text-warning"></i>
                    <span>Checked permissions update automatically in real-time</span>
                </span>
                <button type="button" class="btn btn-primary px-4 py-1.5 fw-semibold rounded-pill" data-bs-dismiss="modal">
                    <i class="fa-solid fa-check me-1"></i> Done
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Extra Styles -->
<style>
    .role-card {
        background-color: var(--stafo-card-bg, #ffffff);
        border-color: var(--stafo-card-border, #e2e8f0);
    }
    .role-card:hover {
        border-color: #cbd5e1;
        box-shadow: var(--stafo-shadow-md);
        transform: translateY(-2px);
    }
    .role-icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(99, 102, 241, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .role-meta-strip {
        background: var(--stafo-body-bg, #f8fafc);
        border: 1px solid var(--stafo-card-border, #f1f5f9);
    }
    .avatar-circle-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        color: #ffffff;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        flex-shrink: 0;
    }
    .permission-group-card {
        background: var(--stafo-body-bg, #f8fafc);
        border-color: var(--stafo-card-border, #e2e8f0) !important;
    }
    .perm-check-item {
        transition: background-color 0.15s ease-in-out;
    }
    .perm-check-item:hover {
        background-color: rgba(99, 102, 241, 0.07);
    }
    [data-theme="dark"] .perm-check-item:hover,
    [data-bs-theme="dark"] .perm-check-item:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .custom-switch {
        width: 2.2em !important;
        height: 1.15em !important;
        cursor: pointer;
    }
    .shadow-2xs {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .badge-stafo-subtle {
        background-color: rgba(100, 116, 139, 0.12) !important;
        color: #64748b !important;
        border: 1px solid rgba(100, 116, 139, 0.25) !important;
    }
    [data-theme="dark"] .badge-stafo-subtle {
        background-color: rgba(148, 163, 184, 0.15) !important;
        color: #cbd5e1 !important;
        border-color: rgba(148, 163, 184, 0.3) !important;
    }
    [data-theme="dark"] .role-card {
        background-color: #111c30 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .role-meta-strip {
        background: #16243d !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .permission-group-card {
        background: #16243d !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .role-assignment-dropdown {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
    }
    .pagination {
        margin-bottom: 0;
        gap: 3px;
    }
    .pagination .page-item .page-link {
        border-radius: 8px;
        padding: 5px 11px;
        font-size: 0.83rem;
        font-weight: 500;
        color: var(--stafo-text, #475569);
        border: 1px solid var(--stafo-card-border, #e2e8f0);
        background-color: var(--stafo-card-bg, #ffffff);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        transition: all 0.15s ease;
    }
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%) !important;
        border-color: #4f46e5 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 5px rgba(79, 70, 229, 0.35);
    }
    .pagination .page-item.disabled .page-link {
        opacity: 0.45;
        background-color: transparent !important;
    }
    [data-theme="dark"] .pagination .page-link,
    [data-bs-theme="dark"] .pagination .page-link {
        background-color: #16243d !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #cbd5e1 !important;
    }
    .search-input-container .form-control {
        border-color: #e2e8f0;
        background-color: #ffffff;
        color: #1e293b;
        transition: all 0.2s ease;
    }
    .search-input-container .form-control:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12) !important;
        background-color: #ffffff;
    }
    .role-filter-container .form-select {
        border-color: #e2e8f0;
        background-color: #ffffff;
        color: #1e293b;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .role-filter-container .form-select:focus {
        border-color: #4f46e5 !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12) !important;
    }
    [data-theme="dark"] .search-input-container .form-control,
    [data-bs-theme="dark"] .search-input-container .form-control,
    [data-theme="dark"] .role-filter-container .form-select,
    [data-bs-theme="dark"] .role-filter-container .form-select {
        background-color: #16243d !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
        color: #f8fafc !important;
    }
    .permissions-matrix-table {
        font-size: 0.88rem;
    }
    .permissions-matrix-table thead th {
        font-weight: 700;
        letter-spacing: 0.04em;
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .permissions-matrix-table .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        border-color: #cbd5e1;
    }
    .permissions-matrix-table .form-check-input:checked {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    [data-theme="dark"] .permissions-matrix-table,
    [data-bs-theme="dark"] .permissions-matrix-table {
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    [data-theme="dark"] .permissions-matrix-table thead th,
    [data-bs-theme="dark"] .permissions-matrix-table thead th {
        background-color: #16243d !important;
        border-bottom: 2px solid rgba(255, 255, 255, 0.1) !important;
        color: #e2e8f0 !important;
    }
    [data-theme="dark"] .permissions-matrix-table tbody tr:hover,
    [data-bs-theme="dark"] .permissions-matrix-table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.03) !important;
    }
</style>

<!-- Dynamic Scripts for Roles & Permissions -->
<script>
    const assignRoleUrl = "{{ route('company-roles.assignEmployeeRole') }}";
    const storeRoleUrl = "{{ route('company-roles.store') }}";
    const rolesBaseUrl = "{{ url('company/company-roles') }}";
    const autoOpenDesigId = "{{ $autoOpenDesignationId ?? '' }}";

    let roleModalInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('roleModal');
        if (modalEl) {
            roleModalInstance = new bootstrap.Modal(modalEl);
        }

        if (autoOpenDesigId) {
            openEditRoleModal(autoOpenDesigId);
        }
    });

    const totalCatalogPerms = {{ $totalCatalogPerms }};

    // 1. Open / Redirect to Create New Designation Page
    function openCreateRoleModal() {
        window.location.href = "{{ route('designations.create') }}";
    }

    // 2. Open Modal to Configure Permissions
    function openEditRoleModal(roleId) {
        document.getElementById('roleIdInput').value = roleId;
        document.getElementById('modalRoleNameText').textContent = 'Loading...';
        setLiveSyncStatus('loading', 'Loading permissions...');

        // Clear all checkboxes & switches before fetching
        document.querySelectorAll('.modal-perm-cb').forEach(cb => cb.checked = false);
        document.querySelectorAll('.mod-switch').forEach(sw => sw.checked = false);
        modalUpdatePermCount();

        if (roleModalInstance) roleModalInstance.show();

        // Fetch existing role data via AJAX
        fetch(`${rolesBaseUrl}/${roleId}/role-data`)
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    document.getElementById('modalRoleNameText').textContent = data.role.name;

                    const permissions = new Set(data.role.permissions || []);
                    document.querySelectorAll('.modal-perm-cb').forEach(cb => {
                        cb.checked = permissions.has(cb.value);
                    });

                    modalUpdatePermCount();
                    setLiveSyncStatus('saved', 'Auto-saved');
                } else {
                    setLiveSyncStatus('error', 'Failed to load');
                }
            })
            .catch(err => {
                console.error(err);
                setLiveSyncStatus('error', 'Connection error');
            });
    }

    // Helper: update live status indicator in modal
    function setLiveSyncStatus(state, text) {
        const el = document.getElementById('modalLiveSyncStatus');
        if (!el) return;

        if (state === 'loading') {
            el.className = 'd-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill small bg-light text-muted border fw-semibold';
            el.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span id="modalLiveSyncText">${text}</span>`;
        } else if (state === 'saving') {
            el.className = 'd-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill small bg-warning-subtle text-warning border border-warning-subtle fw-semibold';
            el.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span id="modalLiveSyncText">${text}</span>`;
        } else if (state === 'saved') {
            el.className = 'd-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill small bg-success-subtle text-success border border-success-subtle fw-semibold';
            el.innerHTML = `<i class="fa-solid fa-cloud-arrow-up"></i> <span id="modalLiveSyncText">${text}</span>`;
        } else if (state === 'error') {
            el.className = 'd-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill small bg-danger-subtle text-danger border border-danger-subtle fw-semibold';
            el.innerHTML = `<i class="fa-solid fa-circle-exclamation"></i> <span id="modalLiveSyncText">${text}</span>`;
        }
    }

    // 3. Instant Live Toggle Single Permission (Check => Assign, Uncheck => Unassign)
    function modalToggleSinglePermLive(permKey, cbEl) {
        const roleId = document.getElementById('roleIdInput').value;
        if (!roleId) return;

        const isChecked = cbEl.checked;
        modalUpdatePermCount();
        setLiveSyncStatus('saving', isChecked ? 'Assigning...' : 'Unassigning...');

        fetch(`${rolesBaseUrl}/${roleId}/toggle-permission`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                permission_key: permKey,
                state: isChecked
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                setLiveSyncStatus('saved', isChecked ? 'Assigned' : 'Unassigned');
                updateRoleCardBadge(roleId, data.active_count);
            } else {
                cbEl.checked = !isChecked; // revert
                modalUpdatePermCount();
                setLiveSyncStatus('error', 'Error updating');
            }
        })
        .catch(err => {
            console.error(err);
            cbEl.checked = !isChecked; // revert
            modalUpdatePermCount();
            setLiveSyncStatus('error', 'Network error');
        });
    }

    // 4. Instant Live Toggle Module Row
    function modalToggleModuleLive(modKey, check) {
        const roleId = document.getElementById('roleIdInput').value;
        if (!roleId) return;

        document.querySelectorAll(`.mod-cb-${modKey}`).forEach(cb => cb.checked = check);
        modalUpdatePermCount();
        syncAllCurrentCheckedPerms(roleId, check ? 'Module rights assigned' : 'Module rights unassigned');
    }

    // 4b. Instant Live Toggle Action Column (INSERT, EDIT, DELETE, VIEW across all modules)
    function modalToggleColumnLive(colType, check) {
        const roleId = document.getElementById('roleIdInput').value;
        if (!roleId) return;

        document.querySelectorAll(`.col-cb-${colType}`).forEach(cb => cb.checked = check);
        modalUpdatePermCount();
        syncAllCurrentCheckedPerms(roleId, check ? `All ${colType.toUpperCase()} rights assigned` : `All ${colType.toUpperCase()} rights unassigned`);
    }

    // 5. Instant Live Toggle All (Select All / Clear All)
    function modalToggleAllLive(check) {
        const roleId = document.getElementById('roleIdInput').value;
        if (!roleId) return;

        document.querySelectorAll('.modal-perm-cb').forEach(cb => cb.checked = check);
        document.querySelectorAll('.mod-switch').forEach(sw => sw.checked = check);
        ['create', 'edit', 'delete', 'view'].forEach(col => {
            const colToggle = document.getElementById(`colToggle_${col}`);
            if (colToggle) colToggle.checked = check;
        });
        modalUpdatePermCount();
        syncAllCurrentCheckedPerms(roleId, check ? 'All permissions assigned' : 'All permissions cleared');
    }

    // Helper: Bulk sync all checked permissions
    function syncAllCurrentCheckedPerms(roleId, successMsg) {
        const checkedValues = Array.from(document.querySelectorAll('.modal-perm-cb:checked')).map(cb => cb.value);
        setLiveSyncStatus('saving', 'Saving changes...');

        fetch(`${rolesBaseUrl}/${roleId}/sync-permissions`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                permissions: checkedValues
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                setLiveSyncStatus('saved', successMsg || 'Auto-saved');
                updateRoleCardBadge(roleId, data.active_count);
            } else {
                setLiveSyncStatus('error', 'Sync failed');
            }
        })
        .catch(err => {
            console.error(err);
            setLiveSyncStatus('error', 'Network error');
        });
    }

    // Helper: Update permission badge on the role card
    function updateRoleCardBadge(roleId, activeCount) {
        const wrap = document.getElementById('rolePermBadgeWrap_' + roleId);
        if (!wrap) return;

        if (activeCount > 0) {
            wrap.innerHTML = `
                <span class="badge badge-stafo badge-stafo-info fw-bold animate__animated animate__fadeIn">
                    <i class="fa-solid fa-key me-1"></i> ${activeCount} of ${totalCatalogPerms} Active
                </span>
            `;
        } else {
            wrap.innerHTML = `
                <span class="badge badge-stafo badge-stafo-subtle fw-medium animate__animated animate__fadeIn">
                    Standard Access Only
                </span>
            `;
        }
    }

    // 6. Update Modal Checked Permissions Count, Module switches, and Column switches
    function modalUpdatePermCount() {
        const checkedCount = document.querySelectorAll('.modal-perm-cb:checked').length;
        const countEl = document.getElementById('modalSelectedPermCount');
        if (countEl) countEl.textContent = checkedCount;

        // Sync module row switches
        @foreach ($catalog as $modKey => $module)
            const modCbs_{{ $modKey }} = document.querySelectorAll('.mod-cb-{{ $modKey }}');
            const modChecked_{{ $modKey }} = document.querySelectorAll('.mod-cb-{{ $modKey }}:checked');
            const modSw_{{ $modKey }} = document.getElementById('modToggle_{{ $modKey }}');
            if (modSw_{{ $modKey }} && modCbs_{{ $modKey }}.length > 0) {
                modSw_{{ $modKey }}.checked = (modCbs_{{ $modKey }}.length === modChecked_{{ $modKey }}.length);
            }
        @endforeach

        // Sync column header checkboxes (INSERT, EDIT, DELETE, VIEW)
        ['create', 'edit', 'delete', 'view'].forEach(col => {
            const colCbs = document.querySelectorAll(`.col-cb-${col}`);
            const colChecked = document.querySelectorAll(`.col-cb-${col}:checked`);
            const colToggle = document.getElementById(`colToggle_${col}`);
            if (colToggle && colCbs.length > 0) {
                colToggle.checked = (colCbs.length === colChecked.length);
            }
        });
    }

    // 7. Instant 1-Click Designation Assignment in Table
    function assignDesignationDirect(empId, desigId, selectEl) {
        const spinner = document.getElementById('spinner_' + empId);
        if (spinner) spinner.classList.remove('d-none');
        selectEl.disabled = true;

        fetch(assignRoleUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                employee_id: empId,
                designation_id: desigId || null
            })
        })
        .then(res => res.json())
        .then(data => {
            selectEl.disabled = false;
            if (spinner) spinner.classList.add('d-none');

            if (data.status) {
                const badgeContainer = document.getElementById('accessBadge_' + empId);
                const row = document.querySelector(`tr[data-emp-id="${empId}"]`);

                if (data.is_administrative) {
                    badgeContainer.innerHTML = `
                        <span class="badge badge-stafo badge-stafo-success animate__animated animate__fadeIn">
                            <i class="fa-solid fa-shield-check me-1"></i> ${data.role_name}
                        </span>
                    `;
                    if (row) row.setAttribute('data-role', data.role_name);
                } else {
                    badgeContainer.innerHTML = `
                        <span class="badge badge-stafo badge-stafo-subtle animate__animated animate__fadeIn">
                            <i class="fa-solid fa-user me-1"></i> Standard Access
                        </span>
                    `;
                    if (row) row.setAttribute('data-role', 'No Designation');
                }
            } else {
                alert(data.message || 'Error updating employee designation.');
            }
        })
        .catch(err => {
            selectEl.disabled = false;
            if (spinner) spinner.classList.add('d-none');
            console.error(err);
            alert('Failed to update employee designation.');
        });
    }

    // 8. Toggle Active / Inactive Status
    function toggleRoleStatus(roleId) {
        fetch(`${rolesBaseUrl}/${roleId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                window.location.reload();
            }
        })
        .catch(err => console.error(err));
    }

    // 9. Backend-Driven AJAX Pagination & Filtering (Zero Page Reload)
    let searchDebounceTimer = null;

    function debounceFetchEmployeeTable() {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            fetchEmployeeTable(1);
        }, 350);
    }

    function fetchEmployeeTable(page = 1) {
        const searchInput = document.getElementById('employeeDirectorySearch');
        const roleFilter = document.getElementById('roleFilterSelect');
        const search = searchInput ? searchInput.value.trim() : '';
        const role = roleFilter ? roleFilter.value : '';

        const overlay = document.getElementById('tableLoadingOverlay');
        if (overlay) {
            overlay.classList.remove('d-none');
            overlay.classList.add('d-flex');
        }

        const params = new URLSearchParams();
        params.append('page', page);
        if (search) params.append('search', search);
        if (role) params.append('role_filter', role);

        const fetchUrl = `${window.location.pathname}?${params.toString()}`;

        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(html => {
            const container = document.getElementById('employeeTableWrapper');
            if (container) {
                container.innerHTML = html;
            }
            if (window.history && window.history.pushState) {
                window.history.pushState({ page, search, role }, '', fetchUrl);
            }
        })
        .catch(err => {
            console.error('Error fetching employee page:', err);
        })
        .finally(() => {
            const newOverlay = document.getElementById('tableLoadingOverlay');
            if (newOverlay) {
                newOverlay.classList.remove('d-flex');
                newOverlay.classList.add('d-none');
            }
        });
    }

    // Intercept click on pagination links to prevent page refresh
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('#employeeTableWrapper .pagination a');
        if (paginationLink) {
            e.preventDefault();
            const href = paginationLink.getAttribute('href');
            if (href && href !== '#' && !paginationLink.closest('.disabled')) {
                const url = new URL(href, window.location.origin);
                const page = url.searchParams.get('page') || 1;
                fetchEmployeeTable(page);
            }
        }
    });

    // Handle browser back/forward buttons seamlessly
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.page) {
            fetchEmployeeTable(e.state.page);
        }
    });
</script>
@endsection
