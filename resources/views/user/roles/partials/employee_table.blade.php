<div class="table-responsive position-relative">
    <!-- Table Loading Overlay -->
    <div id="tableLoadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center" style="background: rgba(255, 255, 255, 0.7); z-index: 10; backdrop-filter: blur(2px);">
        <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <table class="table-modern align-middle mb-0" id="employeeDirectoryTable">
        <thead>
            <tr>
                <th class="ps-4" style="width: 50px;">#</th>
                <th>Employee Details</th>
                <th>Department</th>
                <th style="min-width: 250px;">Assigned Designation (Role)</th>
                <th>Privilege Level</th>
                <th class="text-end pe-4">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($employees as $index => $emp)
                @php
                    $empDesig = $emp->designation;
                    $desigName = $empDesig ? $empDesig->name : 'No Designation';
                    $hasSpecialPerms = $empDesig && $empDesig->permissions->isNotEmpty();
                @endphp
                <tr data-emp-id="{{ $emp->id }}" data-role="{{ $desigName }}" class="directory-row">
                    <td class="ps-4 text-muted small">{{ $employees->firstItem() + $index }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="avatar-circle-sm">
                                {{ strtoupper(substr($emp->name, 0, 2)) }}
                            </div>
                            <div>
                                <span class="fw-bold text-dark d-block">{{ $emp->name }}</span>
                                <small class="text-muted font-monospace">ID: {{ $emp->emp_id ?? '#' . $emp->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="fw-medium text-dark d-block" style="font-size: 0.85rem;">
                            {{ $emp->department ? $emp->department->name : 'General Department' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select form-select-sm role-assignment-dropdown shadow-2xs fw-semibold" 
                                    onchange="assignDesignationDirect({{ $emp->id }}, this.value, this)"
                                    id="empRoleSelect_{{ $emp->id }}">
                                <option value="" {{ !$emp->designation_id ? 'selected' : '' }}>
                                    -- No Designation (Standard Access) --
                                </option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id }}" {{ (string)$emp->designation_id === (string)$r->id ? 'selected' : '' }}>
                                        {{ $r->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="status-indicator-spinner d-none" id="spinner_{{ $emp->id }}">
                                <i class="fa-solid fa-circle-notch fa-spin text-primary"></i>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div id="accessBadge_{{ $emp->id }}">
                            @if ($empDesig)
                                @if ($hasSpecialPerms)
                                    <span class="badge badge-stafo badge-stafo-success">
                                        <i class="fa-solid fa-shield-check me-1"></i> {{ $empDesig->name }} ({{ $empDesig->permissions->count() }} Perms)
                                    </span>
                                @else
                                    <span class="badge badge-stafo badge-stafo-info">
                                        <i class="fa-solid fa-id-badge me-1"></i> {{ $empDesig->name }}
                                    </span>
                                @endif
                            @else
                                <span class="badge badge-stafo badge-stafo-subtle">
                                    <i class="fa-solid fa-user me-1"></i> Standard Access
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="text-end pe-4">
                        @if ($emp->status == 1 || $emp->status === 'active' || $emp->status === null)
                            <span class="badge badge-stafo badge-stafo-success">Active</span>
                        @else
                            <span class="badge badge-stafo badge-stafo-danger">Inactive</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-users fs-2 mb-2 d-block text-muted opacity-50"></i>
                        <h6>No employees found</h6>
                        <span class="small">Try adjusting your search query or designation filter.</span>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination & Summary Footer (Managed by Backend) -->
@if ($employees->hasPages() || $employees->total() > 0)
    <div class="p-3 px-4 border-top d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 pagination-container">
        <div class="text-muted small">
            Showing <span class="fw-bold text-dark">{{ $employees->firstItem() ?? 0 }}</span> to <span class="fw-bold text-dark">{{ $employees->lastItem() ?? 0 }}</span> of <span class="fw-bold text-dark">{{ $employees->total() }}</span> staff members
        </div>
        <div class="ajax-pagination-links">
            {{ $employees->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif
