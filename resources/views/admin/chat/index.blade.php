@extends('admin.layouts.layout')
@section('title', 'Client Support Console - ' . ($company_name ?? 'Company'))

@section('css')
<style>
    :root {
        --admin-bg-base: #f8fafc;
        --admin-card-base: #ffffff;
        --admin-sent-bubble: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        --admin-sent-text: #ffffff;
        --admin-recv-bubble: #ffffff;
        --admin-recv-text: #0f172a;
        --admin-border-color: #e2e8f0;
        --admin-meta-text: #64748b;
        --admin-seen-blue: #38bdf8;
    }

    [data-theme="dark"] {
        --admin-bg-base: #0b1120;
        --admin-card-base: #111827;
        --admin-sent-bubble: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        --admin-sent-text: #ffffff;
        --admin-recv-bubble: #1e293b;
        --admin-recv-text: #f8fafc;
        --admin-border-color: #1e293b;
        --admin-meta-text: #94a3b8;
        --admin-seen-blue: #38bdf8;
    }

    .admin-chat-page-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .admin-chat-page-head h4 {
        margin: 0;
        font-weight: 800;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.35rem;
    }

    .admin-console-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        height: calc(100vh - 180px);
        min-height: 620px;
    }

    @media (max-width: 1100px) {
        .admin-console-grid {
            grid-template-columns: 1fr;
            height: auto;
        }
        .admin-sidebar-info {
            display: none;
        }
    }

    /* Main Chat Panel */
    .admin-chat-main-card {
        background: var(--admin-card-base);
        border: 1px solid var(--admin-border-color);
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
        height: 100%;
    }

    .admin-chat-topbar {
        background: var(--admin-card-base);
        padding: 14px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--admin-border-color);
        z-index: 5;
        flex-wrap: wrap;
        gap: 10px;
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .back-inbox-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--admin-bg-base);
        border: 1px solid var(--admin-border-color);
        color: var(--admin-meta-text);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s;
    }

    .back-inbox-btn:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .company-avatar-square {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        position: relative;
        flex-shrink: 0;
    }

    .company-avatar-square img {
        width: 100%;
        height: 100%;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid var(--admin-border-color);
    }

    .company-avatar-fallback {
        width: 100%;
        height: 100%;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.15rem;
    }

    .company-status-badge-dot {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #94a3b8;
        border: 2px solid var(--admin-card-base);
        transition: background-color 0.3s;
    }

    .company-status-badge-dot.online {
        background-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
    }

    .company-title-wrap h5 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: inherit;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .company-title-wrap p {
        margin: 0;
        font-size: 0.8rem;
        color: var(--admin-meta-text);
    }

    .company-title-wrap p.online-text {
        color: #10b981;
        font-weight: 600;
    }

    .gateway-pill {
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 20px;
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .gateway-pill.disconnected {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.2);
    }

    /* Quick canned responses */
    .admin-canned-bar {
        background: var(--admin-card-base);
        padding: 8px 22px;
        border-bottom: 1px solid var(--admin-border-color);
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none;
    }
    .admin-canned-bar::-webkit-scrollbar { display: none; }

    .canned-chip {
        font-size: 0.78rem;
        padding: 4px 12px;
        border-radius: 20px;
        background: var(--admin-bg-base);
        border: 1px solid var(--admin-border-color);
        color: var(--admin-meta-text);
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .canned-chip:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        transform: translateY(-1px);
    }

    /* Message Stream */
    .admin-message-stream {
        flex: 1;
        overflow-y: auto;
        padding: 24px 28px;
        background-color: var(--admin-bg-base);
        display: flex;
        flex-direction: column;
        gap: 16px;
        scroll-behavior: smooth;
    }

    .date-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 8px 0;
        position: relative;
    }

    .date-divider::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        height: 1px;
        background: var(--admin-border-color);
        z-index: 1;
    }

    .date-divider-pill {
        position: relative;
        z-index: 2;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: var(--admin-card-base);
        border: 1px solid var(--admin-border-color);
        color: var(--admin-meta-text);
        padding: 3px 14px;
        border-radius: 12px;
    }

    .chat-msg-row {
        display: flex;
        width: 100%;
        gap: 10px;
    }

    .chat-msg-row.sent {
        justify-content: flex-end;
    }

    .chat-msg-row.received {
        justify-content: flex-start;
    }

    .sender-avatar-thumb {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #e2e8f0;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .sender-avatar-thumb.company-thumb {
        background: linear-gradient(135deg, #6366f1, #4338ca);
        color: #ffffff;
    }

    .chat-bubble-container {
        max-width: 70%;
        min-width: 120px;
        display: flex;
        flex-direction: column;
    }

    .author-title {
        font-size: 0.72rem;
        font-weight: 700;
        margin-bottom: 3px;
        color: var(--admin-meta-text);
    }

    .chat-msg-row.sent .author-title {
        text-align: right;
    }

    .bubble-wrapper {
        padding: 10px 16px;
        border-radius: 16px;
        position: relative;
        word-break: break-word;
        line-height: 1.5;
        font-size: 0.92rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        animation: bubbleFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .chat-msg-row.sent .bubble-wrapper {
        background: var(--admin-sent-bubble);
        color: var(--admin-sent-text);
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .chat-msg-row.received .bubble-wrapper {
        background: var(--admin-recv-bubble);
        color: var(--admin-recv-text);
        border: 1px solid var(--admin-border-color);
        border-bottom-left-radius: 4px;
    }

    .bubble-content {
        margin: 0;
        white-space: pre-wrap;
    }

    /* Attachment container */
    .chat-attachment-container {
        margin-bottom: 8px;
        border-radius: 12px;
        overflow: hidden;
        max-width: 320px;
    }

    .chat-attachment-img {
        width: 100%;
        max-height: 240px;
        object-fit: cover;
        border-radius: 12px;
        display: block;
        cursor: pointer;
        transition: transform 0.2s ease, opacity 0.2s ease;
        border: 1px solid rgba(0,0,0,0.1);
    }

    .chat-attachment-img:hover {
        transform: scale(1.02);
        opacity: 0.95;
    }

    .bubble-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 5px;
        margin-top: 4px;
        font-size: 0.68rem;
        color: inherit;
        opacity: 0.8;
        user-select: none;
    }

    .chat-msg-row.received .bubble-meta {
        color: var(--admin-meta-text);
        opacity: 1;
    }

    .seen-icon {
        font-size: 0.78rem;
        letter-spacing: -2px;
    }

    .seen-icon.blue {
        color: var(--admin-seen-blue);
    }

    /* System Notice */
    .system-notice-row {
        display: flex;
        justify-content: center;
        width: 100%;
        margin: 10px 0;
    }

    .system-notice-bubble {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #334155;
        font-size: 0.8rem;
        padding: 8px 18px;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        max-width: 80%;
        text-align: center;
    }

    [data-theme="dark"] .system-notice-bubble {
        background: #1e293b;
        border-color: #334155;
        color: #94a3b8;
    }

    /* Typing indicator */
    .typing-box {
        display: none;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: var(--admin-recv-bubble);
        border: 1px solid var(--admin-border-color);
        border-radius: 12px;
        width: fit-content;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        margin-left: 42px;
    }

    .typing-text {
        font-size: 0.75rem;
        color: var(--admin-meta-text);
        font-weight: 500;
    }

    .typing-dots {
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    .typing-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background-color: #3b82f6;
        animation: typingDotJump 1.4s infinite ease-in-out;
    }

    .typing-dot:nth-child(1) { animation-delay: 0s; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    /* Input Dock */
    .admin-chat-dock {
        background: var(--admin-card-base);
        padding: 12px 22px;
        border-top: 1px solid var(--admin-border-color);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* Attachment preview bar */
    .attachment-preview-bar {
        display: none;
        align-items: center;
        gap: 12px;
        padding: 8px 14px;
        background: var(--admin-bg-base);
        border: 1px dashed var(--admin-border-color);
        border-radius: 12px;
    }

    .preview-thumb-img {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid var(--admin-border-color);
    }

    .preview-file-info {
        flex: 1;
        min-width: 0;
    }

    .preview-file-name {
        font-size: 0.85rem;
        font-weight: 600;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .preview-file-size {
        font-size: 0.72rem;
        color: var(--admin-meta-text);
        margin: 0;
    }

    .remove-attachment-btn {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .attach-trigger-btn {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--admin-bg-base);
        border: 1px solid var(--admin-border-color);
        color: var(--admin-meta-text);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .attach-trigger-btn:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .dock-field-wrap {
        flex: 1;
    }

    .dock-chat-input {
        width: 100%;
        border: 1px solid var(--admin-border-color);
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 0.94rem;
        background: var(--admin-bg-base);
        color: inherit;
        outline: none;
        transition: all 0.2s;
    }

    .dock-chat-input:focus {
        border-color: #2563eb;
        background: var(--admin-card-base);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .dock-send-action {
        height: 44px;
        padding: 0 22px;
        border-radius: 12px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        flex-shrink: 0;
    }

    .dock-send-action:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-1px);
    }

    /* Closed dock */
    .admin-chat-closed-dock {
        background: #f8fafc;
        border-top: 1px solid var(--admin-border-color);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    [data-theme="dark"] .admin-chat-closed-dock {
        background: #1e293b;
    }

    /* Right Sidebar Profile */
    .admin-sidebar-info {
        background: var(--admin-card-base);
        border: 1px solid var(--admin-border-color);
        border-radius: 16px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 18px;
        overflow-y: auto;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
    }

    .sidebar-company-card {
        text-align: center;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--admin-border-color);
    }

    .sidebar-avatar-img {
        width: 72px;
        height: 72px;
        border-radius: 16px;
        object-fit: cover;
        margin: 0 auto 12px auto;
        border: 2px solid var(--admin-border-color);
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .sidebar-avatar-fallback {
        width: 72px;
        height: 72px;
        border-radius: 16px;
        background: linear-gradient(135deg, #2563eb 0%, #4338ca 100%);
        color: #ffffff;
        font-size: 1.8rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px auto;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
    }

    .sidebar-company-card h5 {
        margin: 0 0 4px 0;
        font-size: 1.1rem;
        font-weight: 800;
    }

    .sidebar-meta-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 0.85rem;
        margin-bottom: 10px;
    }

    .sidebar-meta-item i {
        color: var(--admin-meta-text);
        margin-top: 3px;
        width: 16px;
        text-align: center;
    }

    .metric-box {
        background: var(--admin-bg-base);
        border: 1px solid var(--admin-border-color);
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 12px;
    }

    .metric-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: var(--admin-meta-text);
        margin-bottom: 4px;
    }

    .metric-val {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
    }

    .quota-progress {
        height: 6px;
        border-radius: 3px;
        background: #e2e8f0;
        margin-top: 6px;
        overflow: hidden;
    }

    .quota-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #2563eb);
        border-radius: 3px;
    }
</style>
@endsection

@section('content')
@php
    $chatStatus = $company->chat_status ?? 'open';
@endphp
<div class="container-fluid px-3 px-lg-4">
    <!-- Breadcrumb & Header -->
    <div class="admin-chat-page-head">
        <div>
            <h4>
                <i class="fa-solid fa-comments text-primary"></i>
                Client Support Console
            </h4>
            <p class="text-muted small mb-0">Live priority workspace for {{ $company_name }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('chatList') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-list me-1"></i> All Inboxes
            </a>
            @if(isset($company_id))
                <a href="{{ route('company.details.show', $company_id) }}" class="btn btn-primary btn-sm rounded-pill px-3" target="_blank">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Profile
                </a>
            @endif
        </div>
    </div>

    <!-- 2-Column Console Layout -->
    <div class="admin-console-grid">
        <!-- Main Chat Panel -->
        <div class="admin-chat-main-card">
            <!-- Topbar -->
            <div class="admin-chat-topbar">
                <div class="topbar-left">
                    <a href="{{ route('chatList') }}" class="back-inbox-btn" title="Back to Inboxes">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div class="company-avatar-square">
                        @if(isset($company) && $company->image_name)
                            <img src="{{ asset('uploads/compnay_logo/' . $company->image_name) }}" alt="Logo" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'company-avatar-fallback\'>{{ substr($company_name ?? 'C', 0, 1) }}</div>';">
                        @else
                            <div class="company-avatar-fallback">
                                {{ substr($company_name ?? 'C', 0, 1) }}
                            </div>
                        @endif
                        <span class="company-status-badge-dot" id="companyStatusDot"></span>
                    </div>
                    <div class="company-title-wrap">
                        <h5>
                            {{ $company_name }}
                            <span class="badge bg-light text-secondary border fs-8">ID: #{{ $company_id }}</span>
                        </h5>
                        <p id="companyStatusText">
                            <i class="fa-solid fa-clock" style="font-size: 0.7rem;"></i> Checking company status...
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <!-- Chat Status Badge -->
                    <span id="adminLifecycleBadge" class="badge {{ $chatStatus === 'closed' ? 'bg-secondary' : 'bg-success' }} px-3 py-2 rounded-pill">
                        <i class="fa-solid {{ $chatStatus === 'closed' ? 'fa-lock' : 'fa-circle-check' }} me-1"></i>
                        <span id="adminLifecycleText">{{ $chatStatus === 'closed' ? 'Resolved / Closed' : 'Active Session' }}</span>
                    </span>

                    <!-- Close / Resolve Action Button -->
                    <button type="button" id="adminCloseBtn" class="btn btn-sm btn-outline-danger rounded-pill px-3 {{ $chatStatus === 'closed' ? 'd-none' : '' }}" onclick="promptAdminClose()">
                        <i class="fa-solid fa-circle-check me-1"></i> Resolve & Close
                    </button>

                    <button type="button" id="adminReopenBtn" class="btn btn-sm btn-outline-success rounded-pill px-3 {{ $chatStatus === 'closed' ? '' : 'd-none' }}" onclick="adminReopenChat()">
                        <i class="fa-solid fa-lock-open me-1"></i> Reopen
                    </button>

                    <span class="gateway-pill" id="adminWsBadge">
                        <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> <span id="adminWsText">Live Gateway</span>
                    </span>
                </div>
            </div>

            <!-- Canned Responses Bar -->
            <div class="admin-canned-bar" id="adminCannedBar" style="{{ $chatStatus === 'closed' ? 'display: none !important;' : '' }}">
                <span class="text-muted small me-1"><i class="fa-solid fa-bolt text-warning"></i> Quick Replies:</span>
                <button type="button" class="canned-chip" onclick="insertCanned('Hello! We are looking into this right now.')">
                    🔍 Investigating Now
                </button>
                <button type="button" class="canned-chip" onclick="insertCanned('Please share the screenshot or error details for verification.')">
                    📸 Request Screenshot
                </button>
                <button type="button" class="canned-chip" onclick="insertCanned('Your issue has been resolved. Please refresh the page and verify.')">
                    ✅ Issue Resolved
                </button>
                <button type="button" class="canned-chip" onclick="insertCanned('This request has been escalated to our senior technical lead.')">
                    ⚡ Escalated to Lead
                </button>
            </div>

            <!-- Message Stream -->
            <div class="admin-message-stream" id="adminChatStream">
                @php $lastDate = null; @endphp

                @if(count($chats) > 0)
                    @foreach($chats as $chat)
                        @php
                            $msgDate = \Carbon\Carbon::parse($chat->created_at)->format('d M Y');
                            $msgTime = \Carbon\Carbon::parse($chat->created_at)->format('h:i A');
                            $isMe = ($chat->message_by === 'admin');
                            $isSystem = ($chat->message_by === 'system');
                            $isSeen = $chat->is_seen_user ? true : false;
                        @endphp

                        @if($lastDate !== $msgDate)
                            <div class="date-divider">
                                <span class="date-divider-pill">
                                    {{ \Carbon\Carbon::parse($chat->created_at)->isToday() ? 'Today' : (\Carbon\Carbon::parse($chat->created_at)->isYesterday() ? 'Yesterday' : $msgDate) }}
                                </span>
                            </div>
                            @php $lastDate = $msgDate; @endphp
                        @endif

                        @if($isSystem)
                            <div class="system-notice-row" data-id="{{ $chat->id }}">
                                <div class="system-notice-bubble">
                                    <i class="fa-solid fa-shield-halved text-primary"></i>
                                    <span>{{ $chat->message }}</span>
                                    <span class="text-muted ms-1" style="font-size: 0.7rem;">({{ $msgTime }})</span>
                                </div>
                            </div>
                        @else
                            <div class="chat-msg-row {{ $isMe ? 'sent' : 'received' }}" data-id="{{ $chat->id }}">
                                @if(!$isMe)
                                    <div class="sender-avatar-thumb company-thumb" title="{{ $company_name }}">
                                        <i class="fa-solid fa-building"></i>
                                    </div>
                                @endif

                                <div class="chat-bubble-container">
                                    <span class="author-title">{{ $isMe ? 'You (Super Admin)' : $company_name }}</span>
                                    <div class="bubble-wrapper">
                                        @if(!empty($chat->attachment))
                                            <div class="chat-attachment-container">
                                                <img src="{{ asset($chat->attachment) }}" class="chat-attachment-img" alt="Screenshot" onclick="openLightbox('{{ asset($chat->attachment) }}')">
                                            </div>
                                        @endif

                                        @if(!empty($chat->message))
                                            <p class="bubble-content">{{ $chat->message }}</p>
                                        @endif

                                        <div class="bubble-meta">
                                            <span>{{ $msgTime }}</span>
                                            @if($isMe)
                                                <span class="seen-icon {{ $isSeen ? 'blue' : '' }}" title="{{ $isSeen ? 'Seen by Client' : 'Delivered' }}">
                                                    {{ $isSeen ? '✓✓' : '✓' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center my-auto p-4 text-muted" id="emptyChatPlaceholder">
                        <i class="fa-regular fa-paper-plane fa-3x text-primary opacity-50 mb-3"></i>
                        <h5 class="fw-bold">No previous messages with {{ $company_name }}</h5>
                        <p class="small">Send a message to initiate communication or respond to an ongoing client inquiry.</p>
                    </div>
                @endif

                <!-- Typing Bubble -->
                <div class="typing-box" id="adminTypingBubble">
                    <span class="typing-text">{{ $company_name }} is typing</span>
                    <div class="typing-dots">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>

            <!-- Input Dock: When Active -->
            <div class="admin-chat-dock" id="adminActiveDock" style="{{ $chatStatus === 'closed' ? 'display: none !important;' : '' }}">
                <!-- Attachment preview strip -->
                <div class="attachment-preview-bar" id="adminAttachmentPreviewBar">
                    <img src="" id="adminPreviewThumbImg" class="preview-thumb-img" alt="Thumbnail">
                    <div class="preview-file-info">
                        <p class="preview-file-name" id="adminPreviewFileName">screenshot.png</p>
                        <p class="preview-file-size" id="adminPreviewFileSize">150 KB</p>
                    </div>
                    <button type="button" class="remove-attachment-btn" onclick="clearAdminSelectedAttachment()" title="Remove Attachment">
                        <i class="fa-solid fa-circle-xmark fa-lg"></i>
                    </button>
                </div>

                <div class="d-flex align-items-center gap-2 w-100">
                    <input type="file" id="adminChatFileInput" accept="image/*,.pdf" style="display: none;">
                    <button type="button" class="attach-trigger-btn" id="adminAttachBtn" title="Upload Screenshot / Image">
                        <i class="fa-solid fa-paperclip"></i>
                    </button>

                    <div class="dock-field-wrap">
                        <input type="text" id="adminChatInput" class="dock-chat-input" placeholder="Type a response or paste screenshot (Ctrl+V)... (Press Enter to send)" autocomplete="off">
                    </div>
                    <button class="dock-send-action" id="adminSendBtn" title="Send Message">
                        <span>Send</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>

            <!-- Input Dock: When Closed -->
            <div class="admin-chat-closed-dock" id="adminClosedDock" style="{{ $chatStatus === 'closed' ? '' : 'display: none !important;' }}">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa-solid fa-circle-check text-success fa-2x"></i>
                    <div>
                        <h6 class="fw-bold mb-0">This client inquiry has been resolved and closed.</h6>
                        <p class="text-muted small mb-0">To continue assisting this company, click Reopen below.</p>
                    </div>
                </div>
                <button type="button" class="btn btn-success rounded-pill px-4" onclick="adminReopenChat()">
                    <i class="fa-solid fa-lock-open me-2"></i> Reopen Conversation
                </button>
            </div>
        </div>

        <!-- Right Sidebar Info Panel -->
        <div class="admin-sidebar-info">
            <div class="sidebar-company-card">
                @if(isset($company) && $company->image_name)
                    <img src="{{ asset('uploads/compnay_logo/' . $company->image_name) }}" alt="Logo" class="sidebar-avatar-img" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'sidebar-avatar-fallback\'>{{ substr($company_name ?? 'C', 0, 1) }}</div>';">
                @else
                    <div class="sidebar-avatar-fallback">
                        {{ substr($company_name ?? 'C', 0, 1) }}
                    </div>
                @endif
                <h5>{{ $company_name }}</h5>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">
                    ID: #{{ $company_id }}
                </span>
            </div>

            <!-- Subscription & Plan -->
            <div class="metric-box">
                <div class="metric-label">Active Subscription</div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="metric-val text-primary">
                        {{ isset($company->package) ? $company->package->package_name : 'Standard HRMS' }}
                    </p>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                        Active
                    </span>
                </div>
                <div class="small text-muted mt-1">
                    Valid till: <strong>{{ isset($company->subscription_end) ? \Carbon\Carbon::parse($company->subscription_end)->format('d M Y') : 'N/A' }}</strong>
                </div>
            </div>

            <!-- Employee Quota -->
            @php
                $added = $company->employee_added ?? 0;
                $max = $company->max_employee_add ?? 1;
                $pct = min(100, round(($added / max(1, $max)) * 100));
            @endphp
            <div class="metric-box">
                <div class="metric-label">Employee Quota</div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="metric-val">{{ $added }} / {{ $max }}</p>
                    <span class="small text-muted">{{ $pct }}% Used</span>
                </div>
                <div class="quota-progress">
                    <div class="quota-progress-bar" style="width: {{ $pct }}%;"></div>
                </div>
            </div>

            <!-- Contact Details -->
            <div>
                <div class="metric-label mb-2">Company Contact</div>
                <div class="sidebar-meta-item">
                    <i class="fa-solid fa-envelope"></i>
                    <div>
                        <div class="text-muted small">Email</div>
                        <a href="mailto:{{ $company->email ?? $company->company_email ?? '#' }}" class="text-dark fw-semibold text-break">
                            {{ $company->email ?? $company->company_email ?? 'Not available' }}
                        </a>
                    </div>
                </div>

                <div class="sidebar-meta-item">
                    <i class="fa-solid fa-phone"></i>
                    <div>
                        <div class="text-muted small">Phone</div>
                        <a href="tel:{{ $company->mobile_no ?? $company->company_phone ?? '#' }}" class="text-dark fw-semibold">
                            {{ $company->mobile_no ?? $company->company_phone ?? 'Not available' }}
                        </a>
                    </div>
                </div>

                @if(isset($company->city) || isset($company->state))
                    <div class="sidebar-meta-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <div>
                            <div class="text-muted small">Location</div>
                            <span class="fw-semibold text-dark">{{ $company->city ? $company->city . ', ' : '' }}{{ $company->state ?? '' }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action buttons -->
            <div class="mt-auto pt-3 border-top">
                <a href="{{ route('company.details.show', $company_id) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill mb-2" target="_blank">
                    <i class="fa-solid fa-building me-1"></i> Company Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Image Lightbox Modal -->
<div class="modal fade" id="adminImageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 position-relative text-center">
                <button type="button" class="btn btn-dark position-absolute top-0 end-0 m-2 rounded-circle shadow" data-bs-dismiss="modal" aria-label="Close" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="" id="adminLightboxModalImg" class="img-fluid rounded-3 shadow-lg" style="max-height: 85vh; object-fit: contain; background: #000;">
            </div>
        </div>
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
    const companyId = {{ (int)$company_id }};
    const csrfToken = '{{ csrf_token() }}';
    const saveUrl = '{{ url("/admin/save-adminchat/" . $company_id) }}';
    const toggleStatusUrl = '{{ route("adminchat.toggleStatus", $company_id) }}';
    const uploadAttachmentUrl = '{{ route("adminchat.uploadAttachment", $company_id) }}';

    let currentStatus = '{{ $chatStatus ?? "open" }}';
    let socket = null;
    let isTyping = false;
    let typingTimeout = null;
    let selectedFile = null;

    const chatStream = document.getElementById('adminChatStream');
    const chatInput = document.getElementById('adminChatInput');
    const sendBtn = document.getElementById('adminSendBtn');
    const adminAttachBtn = document.getElementById('adminAttachBtn');
    const adminChatFileInput = document.getElementById('adminChatFileInput');
    const adminAttachmentPreviewBar = document.getElementById('adminAttachmentPreviewBar');
    const adminPreviewThumbImg = document.getElementById('adminPreviewThumbImg');
    const adminPreviewFileName = document.getElementById('adminPreviewFileName');
    const adminPreviewFileSize = document.getElementById('adminPreviewFileSize');

    const typingBubble = document.getElementById('adminTypingBubble');
    const statusDot = document.getElementById('companyStatusDot');
    const statusText = document.getElementById('companyStatusText');
    const wsBadge = document.getElementById('adminWsBadge');
    const wsText = document.getElementById('adminWsText');
    const emptyPlaceholder = document.getElementById('emptyChatPlaceholder');

    const adminActiveDock = document.getElementById('adminActiveDock');
    const adminClosedDock = document.getElementById('adminClosedDock');
    const adminCloseBtn = document.getElementById('adminCloseBtn');
    const adminReopenBtn = document.getElementById('adminReopenBtn');
    const adminCannedBar = document.getElementById('adminCannedBar');
    const adminLifecycleBadge = document.getElementById('adminLifecycleBadge');

    function insertCanned(text) {
        if (chatInput) {
            chatInput.value = text;
            chatInput.focus();
        }
    }

    function scrollToBottom(smooth = true) {
        if (chatStream) {
            chatStream.scrollTo({
                top: chatStream.scrollHeight,
                behavior: smooth ? 'smooth' : 'auto'
            });
        }
    }
    setTimeout(() => scrollToBottom(false), 150);

    function formatTime(dateObj = new Date()) {
        let hours = dateObj.getHours();
        let minutes = dateObj.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        return hours + ':' + minutes + ' ' + ampm;
    }

    function openLightbox(url) {
        const modalImg = document.getElementById('adminLightboxModalImg');
        if (modalImg) modalImg.src = url;
        const modalEl = document.getElementById('adminImageLightboxModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    // Attachment events
    adminAttachBtn.addEventListener('click', () => {
        adminChatFileInput.click();
    });

    adminChatFileInput.addEventListener('change', (e) => {
        if (e.target.files && e.target.files[0]) {
            handleAdminSelectedFile(e.target.files[0]);
        }
    });

    document.addEventListener('paste', (e) => {
        if (currentStatus === 'closed') return;
        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (let item of items) {
            if (item.type.indexOf('image') !== -1) {
                const blob = item.getAsFile();
                handleAdminSelectedFile(blob);
                e.preventDefault();
                break;
            }
        }
    });

    function handleAdminSelectedFile(file) {
        selectedFile = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            adminPreviewThumbImg.src = e.target.result;
            adminPreviewFileName.textContent = file.name || 'screenshot.png';
            adminPreviewFileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
            adminAttachmentPreviewBar.style.display = 'flex';
            chatInput.focus();
        };
        reader.readAsDataURL(file);
    }

    function clearAdminSelectedAttachment() {
        selectedFile = null;
        adminChatFileInput.value = '';
        adminAttachmentPreviewBar.style.display = 'none';
        adminPreviewThumbImg.src = '';
    }

    function updateLifecycleUI(status) {
        currentStatus = status;
        if (status === 'closed') {
            adminActiveDock.style.setProperty('display', 'none', 'important');
            adminClosedDock.style.removeProperty('display');
            adminCloseBtn.classList.add('d-none');
            adminReopenBtn.classList.remove('d-none');
            adminCannedBar.style.setProperty('display', 'none', 'important');

            adminLifecycleBadge.className = 'badge bg-secondary px-3 py-2 rounded-pill';
            adminLifecycleBadge.innerHTML = '<i class="fa-solid fa-lock me-1"></i> <span>Resolved / Closed</span>';
        } else {
            adminClosedDock.style.setProperty('display', 'none', 'important');
            adminActiveDock.style.removeProperty('display');
            adminCloseBtn.classList.remove('d-none');
            adminReopenBtn.classList.add('d-none');
            adminCannedBar.style.removeProperty('display');

            adminLifecycleBadge.className = 'badge bg-success px-3 py-2 rounded-pill';
            adminLifecycleBadge.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> <span>Active Session</span>';
            chatInput.focus();
        }
    }

    function promptAdminClose() {
        const reason = prompt('Optional resolution note (e.g. Query Resolved, Information Provided):', 'Query Resolved');
        if (reason !== null) {
            performStatusUpdate('closed', reason || 'Query Resolved');
        }
    }

    function adminReopenChat() {
        performStatusUpdate('open', '');
    }

    function performStatusUpdate(newStatus, reason) {
        if (socket && socket.connected) {
            socket.emit('update_chat_status', {
                company_id: companyId,
                status: newStatus,
                role: 'admin',
                reason: reason
            }, (ack) => {
                updateLifecycleUI(newStatus);
            });
        }

        fetch(toggleStatusUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus, reason: reason })
        })
        .then(res => res.json())
        .then(data => {
            updateLifecycleUI(newStatus);
        })
        .catch(e => console.error('[Admin Status Update Error]', e));
    }

    function appendSystemNotice(text, timeStr = null) {
        const row = document.createElement('div');
        row.className = 'system-notice-row';
        const displayTime = timeStr || formatTime(new Date());
        row.innerHTML = `
            <div class="system-notice-bubble">
                <i class="fa-solid fa-shield-halved text-primary"></i>
                <span>${text}</span>
                <span class="text-muted ms-1" style="font-size: 0.7rem;">(${displayTime})</span>
            </div>
        `;
        chatStream.insertBefore(row, typingBubble);
        scrollToBottom(true);
    }

    function appendMessageBubble(message, messageBy, isSeen = false, timeStr = null, msgId = null, attachmentUrl = null) {
        if (emptyPlaceholder) emptyPlaceholder.style.display = 'none';

        const isSent = (messageBy === 'admin');
        const row = document.createElement('div');
        row.className = 'chat-msg-row ' + (isSent ? 'sent' : 'received');
        if (msgId) row.setAttribute('data-id', msgId);

        const displayTime = timeStr || formatTime(new Date());
        let checkHtml = '';
        if (isSent) {
            checkHtml = `<span class="seen-icon ${isSeen ? 'blue' : ''}" title="${isSeen ? 'Seen by Client' : 'Delivered'}">${isSeen ? '✓✓' : '✓'}</span>`;
        }

        let avatarHtml = '';
        if (!isSent) {
            avatarHtml = `
                <div class="sender-avatar-thumb company-thumb" title="{{ $company_name }}">
                    <i class="fa-solid fa-building"></i>
                </div>
            `;
        }

        let attachmentHtml = '';
        if (attachmentUrl) {
            const cleanUrl = attachmentUrl.startsWith('http') ? attachmentUrl : '/' + attachmentUrl;
            attachmentHtml = `
                <div class="chat-attachment-container">
                    <img src="${cleanUrl}" class="chat-attachment-img" alt="Screenshot" onclick="openLightbox('${cleanUrl}')">
                </div>
            `;
        }

        let msgTextHtml = '';
        if (message && message.trim()) {
            const cleanText = document.createTextNode(message).textContent;
            msgTextHtml = `<p class="bubble-content">${cleanText}</p>`;
        }

        row.innerHTML = `
            ${avatarHtml}
            <div class="chat-bubble-container">
                <span class="author-title">${isSent ? 'You (Super Admin)' : '{{ $company_name }}'}</span>
                <div class="bubble-wrapper">
                    ${attachmentHtml}
                    ${msgTextHtml}
                    <div class="bubble-meta">
                        <span>${displayTime}</span>
                        ${checkHtml}
                    </div>
                </div>
            </div>
        `;

        chatStream.insertBefore(row, typingBubble);
        scrollToBottom(true);
    }

    function initSocket() {
        try {
            socket = io('/', {
                path: '/socket.io',
                transports: ['websocket', 'polling'],
                reconnectionAttempts: 20,
                reconnectionDelay: 1500
            });

            socket.on('connect', () => {
                wsBadge.classList.remove('disconnected');
                wsText.textContent = 'Live Gateway';

                socket.emit('join_room', {
                    company_id: companyId,
                    role: 'admin'
                });
            });

            socket.on('disconnect', () => {
                wsBadge.classList.add('disconnected');
                wsText.textContent = 'Reconnecting...';
                statusDot.classList.remove('online');
                statusText.innerHTML = '<i class="fa-solid fa-clock" style="font-size: 0.7rem;"></i> Offline';
                statusText.classList.remove('online-text');
            });

            socket.on('presence_update', (data) => {
                if (data && data.company_online) {
                    statusDot.classList.add('online');
                    statusText.innerHTML = '<i class="fa-solid fa-circle text-success" style="font-size: 0.6rem;"></i> Active Now';
                    statusText.classList.add('online-text');
                } else {
                    statusDot.classList.remove('online');
                    statusText.innerHTML = '<i class="fa-solid fa-clock" style="font-size: 0.7rem;"></i> Offline';
                    statusText.classList.remove('online-text');
                }
            });

            socket.on('chat_status_updated', (data) => {
                if (data && parseInt(data.company_id) === companyId) {
                    updateLifecycleUI(data.status);
                    if (data.system_message) {
                        appendSystemNotice(data.system_message.message, formatTime(new Date(data.system_message.created_at || Date.now())));
                    }
                }
            });

            socket.on('new_message', (msg) => {
                if (parseInt(msg.company_id) === companyId) {
                    if (msg.message_by === 'company') {
                        typingBubble.style.display = 'none';
                        appendMessageBubble(msg.message, 'company', false, formatTime(new Date(msg.created_at || Date.now())), msg.id, msg.attachment);

                        socket.emit('mark_seen', {
                            company_id: companyId,
                            role: 'admin'
                        });
                    } else if (msg.message_by === 'system') {
                        appendSystemNotice(msg.message, formatTime(new Date(msg.created_at || Date.now())));
                    }
                }
            });

            socket.on('user_typing', (data) => {
                if (data && data.role === 'company') {
                    if (data.is_typing) {
                        typingBubble.style.display = 'inline-flex';
                        scrollToBottom(true);
                    } else {
                        typingBubble.style.display = 'none';
                    }
                }
            });

            socket.on('messages_seen', (data) => {
                if (data && data.seen_by === 'company') {
                    document.querySelectorAll('.chat-msg-row.sent .seen-icon').forEach(icon => {
                        icon.classList.add('blue');
                        icon.textContent = '✓✓';
                    });
                }
            });

        } catch (err) {
            console.error('[WebSocket Admin Init Error]', err);
            wsBadge.classList.add('disconnected');
            wsText.textContent = 'HTTP Mode';
        }
    }

    function sendMessage() {
        const text = chatInput.value.trim();
        const fileToUpload = selectedFile;

        if (!text && !fileToUpload) return;

        chatInput.value = '';
        clearAdminSelectedAttachment();

        if (fileToUpload) {
            const formData = new FormData();
            formData.append('file', fileToUpload);
            formData.append('message', text);
            formData.append('_token', csrfToken);

            const localPreviewUrl = URL.createObjectURL(fileToUpload);
            appendMessageBubble(text, 'admin', false, formatTime(new Date()), null, localPreviewUrl);

            fetch(uploadAttachmentUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'ok') {
                    const messageObj = res.data;
                    messageObj.attachment = res.file_url;

                    if (socket && socket.connected) {
                        socket.emit('broadcast_message', {
                            company_id: companyId,
                            messageObj: messageObj
                        });
                    }
                }
            })
            .catch(err => {
                console.error('[Upload Error]', err);
            });

            return;
        }

        // Regular text message
        appendMessageBubble(text, 'admin', false, formatTime(new Date()));

        if (socket && socket.connected) {
            socket.emit('stop_typing', { company_id: companyId, role: 'admin' });
            isTyping = false;

            socket.emit('send_message', {
                company_id: companyId,
                message: text,
                message_by: 'admin',
                sender: 1,
                receiver: companyId
            }, (ack) => {
                if (!ack || ack.status !== 'ok') {
                    fallbackHttpSave(text);
                }
            });
        } else {
            fallbackHttpSave(text);
        }
    }

    function fallbackHttpSave(text) {
        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: text })
        }).catch(e => console.error('[HTTP Fallback Error]', e));
    }

    chatInput.addEventListener('input', () => {
        if (socket && socket.connected) {
            if (!isTyping) {
                isTyping = true;
                socket.emit('typing', { company_id: companyId, role: 'admin' });
            }
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                isTyping = false;
                socket.emit('stop_typing', { company_id: companyId, role: 'admin' });
            }, 1800);
        }
    });

    chatInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    sendBtn.addEventListener('click', sendMessage);

    initSocket();
</script>
@endsection
