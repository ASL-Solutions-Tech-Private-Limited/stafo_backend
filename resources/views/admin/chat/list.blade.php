@extends('admin.layouts.layout')
@section('title', 'Client Support Inboxes')

@section('css')
<style>
    .inbox-page-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .inbox-page-head h4 {
        margin: 0;
        font-weight: 800;
        font-size: 1.35rem;
        letter-spacing: -0.02em;
    }

    /* Stats Row */
    .inbox-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .inbox-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    [data-theme="dark"] .inbox-stat-card {
        background: #1e293b;
        border-color: #334155;
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-blue {
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
    }

    .stat-emerald {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .stat-amber {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .stat-card-info h6 {
        margin: 0 0 2px 0;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }

    .stat-card-info p {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    /* Conversation Container Card */
    .inbox-main-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    [data-theme="dark"] .inbox-main-card {
        background: #1e293b;
        border-color: #334155;
    }

    .inbox-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        background: #ffffff;
    }

    [data-theme="dark"] .inbox-card-header {
        background: #1e293b;
        border-color: #334155;
    }

    .search-input-box {
        position: relative;
        min-width: 280px;
    }

    .search-input-box input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        font-size: 0.88rem;
        outline: none;
        transition: all 0.2s;
        background: #f8fafc;
    }

    [data-theme="dark"] .search-input-box input {
        background: #0f172a;
        border-color: #334155;
        color: #f1f5f9;
    }

    .search-input-box input:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .search-input-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .conv-list-ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .conv-row-link {
        display: flex;
        align-items: center;
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        text-decoration: none;
        color: inherit;
        transition: all 0.15s ease;
        position: relative;
    }

    [data-theme="dark"] .conv-row-link {
        border-color: #263349;
    }

    .conv-row-link:hover {
        background-color: #f8fafc;
        transform: translateX(3px);
    }

    [data-theme="dark"] .conv-row-link:hover {
        background-color: #263349;
    }

    .conv-logo-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        position: relative;
        flex-shrink: 0;
        margin-right: 18px;
    }

    .conv-logo-box img {
        width: 100%;
        height: 100%;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
    }

    .conv-logo-fallback {
        width: 100%;
        height: 100%;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
    }

    .conv-main-details {
        flex: 1;
        min-width: 0;
    }

    .conv-header-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    .conv-company-name {
        font-weight: 700;
        font-size: 0.98rem;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conv-timestamp {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 500;
        white-space: nowrap;
    }

    .conv-snippet-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .conv-preview-text {
        font-size: 0.85rem;
        color: #64748b;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
    }

    [data-theme="dark"] .conv-preview-text {
        color: #94a3b8;
    }

    .conv-preview-text.unread-bold {
        color: #0f172a;
        font-weight: 700;
    }

    [data-theme="dark"] .conv-preview-text.unread-bold {
        color: #f8fafc;
    }

    .unread-counter-badge {
        background: #2563eb;
        color: #ffffff;
        font-size: 0.72rem;
        font-weight: 700;
        min-width: 22px;
        height: 22px;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-3 px-lg-4">
    <!-- Header -->
    <div class="inbox-page-head">
        <div>
            <h4>
                <i class="fa-solid fa-headset text-primary me-2"></i>
                Client Support Inboxes
            </h4>
            <p class="text-muted small mb-0">Unified real-time desk for all registered companies</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">
                <i class="fa-solid fa-circle text-success me-1" style="font-size: 0.55rem;"></i> Real-time WebSocket Gateway
            </span>
        </div>
    </div>

    <!-- Stats Ribbon -->
    @php
        $totalCompanies = count($companies);
        $totalUnread = 0;
        foreach($companies as $c) {
            $totalUnread += ($c->unread_count ?? 0);
        }
    @endphp
    <div class="inbox-stats-grid">
        <div class="inbox-stat-card">
            <div class="stat-card-icon stat-blue">
                <i class="fa-solid fa-building"></i>
            </div>
            <div class="stat-card-info">
                <h6>Active Inboxes</h6>
                <p>{{ $totalCompanies }}</p>
            </div>
        </div>

        <div class="inbox-stat-card">
            <div class="stat-card-icon stat-amber">
                <i class="fa-solid fa-envelope-open-text"></i>
            </div>
            <div class="stat-card-info">
                <h6>Pending Inquiries</h6>
                <p id="totalUnreadStat">{{ $totalUnread }}</p>
            </div>
        </div>

        <div class="inbox-stat-card">
            <div class="stat-card-icon stat-emerald">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div class="stat-card-info">
                <h6>Support SLA</h6>
                <p>&lt; 15 mins</p>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="inbox-main-card">
        <div class="inbox-card-header">
            <div>
                <h5 class="fw-bold mb-0">Conversations</h5>
                <span class="text-muted small">Sorted by most recent activity</span>
            </div>
            <div class="search-input-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="companySearchInput" placeholder="Filter company by name or ID..." autocomplete="off">
            </div>
        </div>

        <ul class="conv-list-ul" id="inboxList">
            @forelse($companies as $comp)
                @php
                    $unread = $comp->unread_count ?? 0;
                    $timeFormatted = '';
                    if ($comp->latest_time) {
                        $cDate = \Carbon\Carbon::parse($comp->latest_time);
                        if ($cDate->isToday()) {
                            $timeFormatted = $cDate->format('h:i A');
                        } elseif ($cDate->isYesterday()) {
                            $timeFormatted = 'Yesterday';
                        } else {
                            $timeFormatted = $cDate->format('d/m/Y');
                        }
                    }
                @endphp

                <li>
                    <a href="{{ route('adminchat', $comp->id) }}" class="conv-row-link" data-company-id="{{ $comp->id }}" data-search="{{ strtolower($comp->company_name) }} #{{ $comp->id }}">
                        <div class="conv-logo-box">
                            @if($comp->image_name)
                                <img src="{{ asset('uploads/compnay_logo/' . $comp->image_name) }}" alt="Logo" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'conv-logo-fallback\'>{{ substr($comp->company_name, 0, 1) }}</div>';">
                            @else
                                <div class="conv-logo-fallback">
                                    {{ substr($comp->company_name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <div class="conv-main-details">
                            <div class="conv-header-line">
                                <h6 class="conv-company-name">
                                    {{ $comp->company_name }}
                                    <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.72rem; font-weight: normal;">#{{ $comp->id }}</span>
                                </h6>
                                <span class="conv-timestamp" id="time-{{ $comp->id }}">{{ $timeFormatted }}</span>
                            </div>

                            <div class="conv-snippet-line">
                                <p class="conv-preview-text {{ $unread > 0 ? 'unread-bold' : '' }}" id="snippet-{{ $comp->id }}">
                                    @if($comp->latest_by === 'admin')
                                        <span class="text-primary"><i class="fa-solid fa-reply me-1"></i>You: </span>
                                    @endif
                                    {{ $comp->latest_message ?: 'No messages yet' }}
                                </p>
                                @if($unread > 0)
                                    <span class="unread-counter-badge" id="badge-{{ $comp->id }}">{{ $unread }}</span>
                                @else
                                    <span class="unread-counter-badge d-none" id="badge-{{ $comp->id }}">0</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="p-5 text-center text-muted">
                    <i class="fa-regular fa-comment-dots fa-3x mb-3 text-secondary opacity-50"></i>
                    <h5>No conversations recorded yet</h5>
                    <p class="small">Company inquiries sent through the HRMS Support Desk will automatically show up here.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

@section('scripts')
<script src="/socket.io/socket.io.js"></script>
<script>
    if (typeof io === 'undefined') {
        document.write('<script src="https://cdn.socket.io/4.8.1/socket.io.min.js"><\/script>');
    }
</script>

<script>
    // Live Search Filter
    document.getElementById('companySearchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const links = document.querySelectorAll('.conv-row-link');
        links.forEach(link => {
            const searchData = link.getAttribute('data-search') || '';
            if (searchData.includes(query)) {
                link.parentElement.style.display = '';
            } else {
                link.parentElement.style.display = 'none';
            }
        });
    });

    // Real-time inbox update on incoming company messages
    (function() {
        try {
            const socket = io('/', {
                path: '/socket.io',
                transports: ['websocket', 'polling']
            });

            socket.on('chat_list_activity', (data) => {
                if (!data || !data.company_id) return;

                const cId = data.company_id;
                const snippet = document.getElementById('snippet-' + cId);
                const timeEl = document.getElementById('time-' + cId);
                const badge = document.getElementById('badge-' + cId);

                if (snippet) {
                    if (data.message_by === 'admin') {
                        snippet.innerHTML = '<span class="text-primary"><i class="fa-solid fa-reply me-1"></i>You: </span>' + document.createTextNode(data.last_message).textContent;
                    } else {
                        snippet.textContent = data.last_message;
                        snippet.classList.add('unread-bold');
                    }
                }

                if (timeEl) {
                    const now = new Date();
                    let hours = now.getHours();
                    let minutes = now.getMinutes();
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12;
                    minutes = minutes < 10 ? '0' + minutes : minutes;
                    timeEl.textContent = hours + ':' + minutes + ' ' + ampm;
                }

                if (badge && data.message_by === 'company') {
                    let currentCount = parseInt(badge.textContent) || 0;
                    currentCount++;
                    badge.textContent = currentCount;
                    badge.classList.remove('d-none');

                    const totalStat = document.getElementById('totalUnreadStat');
                    if (totalStat) {
                        let total = parseInt(totalStat.textContent) || 0;
                        totalStat.textContent = total + 1;
                    }
                }

                // Move updated conversation to top of list
                const item = document.querySelector(`.conv-row-link[data-company-id="${cId}"]`);
                if (item && item.parentElement) {
                    const list = document.getElementById('inboxList');
                    list.insertBefore(item.parentElement, list.firstChild);
                }
            });
        } catch (e) {
            console.error('[Inbox Socket Error]', e);
        }
    })();
</script>
@endsection
