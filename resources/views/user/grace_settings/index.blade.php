@extends('user.layouts.app')

@section('title', 'Salary & Grace Settings | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Salary & Grace Settings</h3>
                <p class="text-muted small mb-0">Manage global attendance grace periods, salary calculation thresholds, and deduction rules</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 70px;" class="text-center">S.No</th>
                        <th style="width: 350px;">Setting Name</th>
                        <th>Configured Value</th>
                        <th style="width: 120px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($settings as $index => $setting)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $settings->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-sliders"></i>
                                    </div>
                                    <span class="fw-bold text-dark">{{ $setting->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge-stafo badge-stafo-info fs-6 px-3 py-1">
                                    {{ $setting->value }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('grace_settings.edit', $setting->id) }}" 
                                       class="btn btn-sm btn-outline-warning p-0" 
                                       style="width: 32px; height: 32px; border-radius: 8px;"
                                       title="Edit Setting">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-sliders fs-2 mb-2 d-block opacity-50"></i>
                                No settings configured yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($settings, 'links') && $settings->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                <small class="text-muted">Showing {{ $settings->firstItem() }} to {{ $settings->lastItem() }} of {{ $settings->total() }} settings</small>
                <div>{{ $settings->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif

    </div>
</div>

@endsection

@section('scripts')
<script>
    function confirmDelete(event, settingId) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${settingId}`).submit();
            }
        });
    }
</script>
@endsection
