@extends('user.layouts.app')
@section('title', 'Edit Role & Permissions | STAFO HRMS')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                <div>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-pen-to-square text-primary me-2"></i>Edit Access Role: {{ $role->name }}
                    </h3>
                    <p class="text-muted small mb-0">Modify role title, status, and module permissions</p>
                </div>
                <a href="{{ route('company-roles.index') }}" class="btn btn-outline-secondary px-3 py-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Roles
                </a>
            </div>

            <!-- Form -->
            <form action="{{ route('company-roles.update', $role->id) }}" method="POST" id="roleForm">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="row g-4 mb-4 pb-4 border-bottom">
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label fw-bold text-dark">
                            Role Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                               value="{{ old('name', $role->name) }}" 
                               placeholder="e.g. Field Executive, Team Lead, HR Manager" 
                               required 
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold text-dark d-block">Role Status</label>
                        <div class="d-flex align-items-center gap-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_active" value="1" {{ old('status', $role->status) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label text-dark fw-semibold" for="status_active">
                                    <i class="fa-solid fa-circle-check text-success me-1"></i> Active
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive" value="0" {{ old('status', $role->status) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label text-muted" for="status_inactive">
                                    <i class="fa-solid fa-circle-pause text-secondary me-1"></i> Inactive
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-bold text-dark">
                            Role Description <span class="text-muted small">(Optional)</span>
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="2" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="Describe the authority and organizational scope of this role...">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Permissions Matrix Section -->
                <div class="mb-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                        <div>
                            <h4 class="fw-bold text-dark mb-1">
                                <i class="fa-solid fa-key text-warning me-2"></i>Module Permissions Matrix
                            </h4>
                            <p class="text-muted small mb-0">Select features and actions that employees with this role are permitted to perform</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary px-3" onclick="toggleAllPermissions(true)">
                                <i class="fa-solid fa-check-double me-1"></i> Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="toggleAllPermissions(false)">
                                <i class="fa-solid fa-xmark me-1"></i> Deselect All
                            </button>
                        </div>
                    </div>

                    <div class="row g-4">
                        @foreach($catalog as $moduleKey => $module)
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card h-100 border rounded-4 shadow-xs bg-light bg-opacity-50">
                                    <div class="card-header bg-white border-bottom py-3 px-3.5 d-flex align-items-center justify-content-between rounded-top-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="{{ $module['icon'] }} fs-5"></i>
                                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $module['name'] }}</span>
                                        </div>
                                        <button type="button" 
                                                class="btn btn-link btn-sm text-decoration-none p-0 text-primary small fw-semibold" 
                                                onclick="toggleModulePermissions('{{ $moduleKey }}')">
                                            Toggle
                                        </button>
                                    </div>
                                    <div class="card-body p-3.5">
                                        <div class="d-flex flex-column gap-2.5">
                                            @foreach($module['permissions'] as $permKey => $permLabel)
                                                @php
                                                    $isChecked = old('permissions') !== null 
                                                        ? in_array($permKey, old('permissions', []))
                                                        : in_array($permKey, $assignedKeys);
                                                @endphp
                                                <div class="form-check custom-permission-checkbox">
                                                    <input class="form-check-input perm-checkbox perm-mod-{{ $moduleKey }}" 
                                                           type="checkbox" 
                                                           name="permissions[]" 
                                                           id="perm_{{ str_replace('.', '_', $permKey) }}" 
                                                           value="{{ $permKey }}"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    <label class="form-check-label text-dark small cursor-pointer" for="perm_{{ str_replace('.', '_', $permKey) }}">
                                                        {{ $permLabel }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Bar -->
                <div class="d-flex align-items-center gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4 py-2.5 fw-semibold">
                        <i class="fa-solid fa-circle-check me-1"></i> Update Role & Permissions
                    </button>
                    <a href="{{ route('company-roles.index') }}" class="btn btn-outline-secondary px-4 py-2.5">
                        Cancel
                    </a>
                </div>
            </form>

        </div>
    </div>

    <script>
        function toggleAllPermissions(check) {
            document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = check);
        }

        function toggleModulePermissions(moduleKey) {
            const checkboxes = document.querySelectorAll('.perm-mod-' + moduleKey);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }
    </script>
@endsection
