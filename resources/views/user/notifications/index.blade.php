@extends('user.layouts.app')

@section('title', 'Notifications & Alerts | STAFO HRMS')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fa-solid fa-bell text-warning me-2"></i>Notifications & Live Activity
            </h4>
            <span class="text-muted small">Real-time alerts and activity updates received from your employees</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($unreadCount > 0)
                <button type="button" class="btn btn-outline-primary btn-sm px-3 rounded-pill" onclick="markAllNotificationsRead()">
                    <i class="fa-solid fa-check-double me-1"></i> Mark All as Read
                </button>
            @endif
            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('user.notifications.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by message or employee..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-filter text-muted"></i></span>
                        <select name="status" class="form-select border-start-0" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread Only ({{ $unreadCount }})</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-3 rounded-3">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('user.notifications.index') }}" class="btn btn-sm btn-outline-secondary px-3 rounded-3">
                            <i class="fa-solid fa-rotate-left me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Notifications List Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-list-check text-primary"></i>
                <h6 class="fw-bold mb-0 text-dark">Activity Stream</h6>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                {{ $notifications->total() }} Total
            </span>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notif)
                    @php
                        $msg = strtolower($notif->message ?? '');
                        $iconClass = 'fa-solid fa-bell text-primary bg-primary bg-opacity-10';
                        $targetUrl = route('user.dashboard');
                        $actionLabel = 'View';

                        if (str_contains($msg, 'punch-out') || str_contains($msg, 'regularisation') || str_contains($msg, 'regularization')) {
                            $iconClass = 'fa-solid fa-business-time text-warning bg-warning bg-opacity-10';
                            $targetUrl = route('user.attendanceRequests.index');
                            $actionLabel = 'Review Request';
                        } elseif (str_contains($msg, 'punch in') || str_contains($msg, 'punched in')) {
                            $iconClass = 'fa-solid fa-arrow-right-to-bracket text-success bg-success bg-opacity-10';
                            $targetUrl = route('attendance.index');
                            $actionLabel = 'Attendance';
                        } elseif (str_contains($msg, 'punch out') || str_contains($msg, 'punched out')) {
                            $iconClass = 'fa-solid fa-arrow-right-from-bracket text-danger bg-danger bg-opacity-10';
                            $targetUrl = route('attendance.index');
                            $actionLabel = 'Attendance';
                        } elseif (str_contains($msg, 'leave')) {
                            $iconClass = 'fa-solid fa-calendar-minus text-warning bg-warning bg-opacity-10';
                            $targetUrl = route('leaveList');
                            $actionLabel = 'Leave Requests';
                        } elseif (str_contains($msg, 'location') || str_contains($msg, 'tracking') || str_contains($msg, 'geo')) {
                            $iconClass = 'fa-solid fa-location-dot text-info bg-info bg-opacity-10';
                            $targetUrl = route('employee.location');
                            $actionLabel = 'Tracking';
                        } elseif (str_contains($msg, 'expense') || str_contains($msg, 'claim')) {
                            $iconClass = 'fa-solid fa-receipt text-success bg-success bg-opacity-10';
                            $targetUrl = route('expenseList');
                            $actionLabel = 'Expense';
                        }
                    @endphp
                    <div class="list-group-item p-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 {{ $notif->status == 'unread' ? 'bg-light bg-opacity-50' : '' }}">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $iconClass }}" style="width: 44px; height: 44px; font-size: 1.1rem;">
                                <i class="{{ explode(' ', $iconClass)[0] }} {{ explode(' ', $iconClass)[1] }}"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $notif->message }}</span>
                                    @if($notif->status == 'unread')
                                        <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">NEW</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-3 text-muted small" style="font-size: 0.78rem;">
                                    @if($notif->employee)
                                        <span><i class="fa-solid fa-user me-1 text-secondary"></i> {{ $notif->employee->name }} ({{ $notif->employee->emp_id ?? 'ID:'.$notif->employee->id }})</span>
                                    @endif
                                    <span><i class="fa-regular fa-clock me-1 text-secondary"></i> {{ $notif->created_at ? $notif->created_at->format('d M Y, h:i A') : '—' }} ({{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }})</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-auto ms-sm-0">
                            <a href="{{ $targetUrl }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                                {{ $actionLabel }} <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                            </a>
                            @if($notif->status == 'unread')
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1" title="Mark as read" onclick="markSingleNotificationRead({{ $notif->id }}, this)">
                                    <i class="fa-solid fa-check text-success"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <div class="mb-3">
                            <div class="avatar-lg rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                                <i class="fa-solid fa-bell-slash fa-2x text-secondary opacity-50"></i>
                            </div>
                        </div>
                        <h6 class="fw-bold text-secondary">No Notifications Found</h6>
                        <p class="small text-muted mb-0">Whenever employees perform activities like punching, requesting regularization, or submitting leaves, alerts will appear here in real-time.</p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function markAllNotificationsRead() {
        fetch("{{ route('user.notifications.markRead', 'all') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                window.location.reload();
            }
        });
    }

    function markSingleNotificationRead(id, btn) {
        fetch("{{ url('company/notifications/mark-read') }}/" + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                var item = btn.closest('.list-group-item');
                if (item) {
                    item.classList.remove('bg-light', 'bg-opacity-50');
                    var badge = item.querySelector('.badge.bg-danger');
                    if (badge) badge.remove();
                }
                btn.remove();
            }
        });
    }
</script>
@endsection
