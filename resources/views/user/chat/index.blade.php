@extends('user.layouts.app')
@section('title', 'HRMS Support Desk | STAFO')

@section('css')
<style>
    :root {
        --hrms-chat-bg: #f8fafc;
        --hrms-chat-card: #ffffff;
        --hrms-chat-sent-bg: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        --hrms-chat-sent-text: #ffffff;
        --hrms-chat-recv-bg: #ffffff;
        --hrms-chat-recv-text: #0f172a;
        --hrms-chat-border: #e2e8f0;
        --hrms-chat-header-bg: #ffffff;
        --hrms-chat-meta: #64748b;
        --hrms-chat-seen-blue: #38bdf8;
    }

    [data-theme="dark"] {
        --hrms-chat-bg: #0b1120;
        --hrms-chat-card: #111827;
        --hrms-chat-sent-bg: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        --hrms-chat-sent-text: #ffffff;
        --hrms-chat-recv-bg: #1e293b;
        --hrms-chat-recv-text: #f8fafc;
        --hrms-chat-border: #1e293b;
        --hrms-chat-header-bg: #111827;
        --hrms-chat-meta: #94a3b8;
        --hrms-chat-seen-blue: #38bdf8;
    }

    .hrms-chat-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .hrms-chat-page-header h4 {
        margin: 0;
        font-weight: 800;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.35rem;
    }

    .hrms-chat-container {
        width: 100%;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
        border: 1px solid var(--hrms-chat-border);
        background: var(--hrms-chat-card);
        display: flex;
        flex-direction: column;
        height: calc(100vh - 185px);
        min-height: 600px;
    }

    /* Header Bar */
    .hrms-chat-head {
        background: var(--hrms-chat-header-bg);
        padding: 14px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--hrms-chat-border);
        z-index: 10;
        flex-wrap: wrap;
        gap: 10px;
    }

    .head-support-profile {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .head-avatar-box {
        position: relative;
        width: 44px;
        height: 44px;
    }

    .head-avatar-icon {
        width: 100%;
        height: 100%;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
    }

    .head-status-dot {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background-color: #94a3b8;
        border: 2px solid var(--hrms-chat-header-bg);
        transition: background-color 0.3s;
    }

    .head-status-dot.online {
        background-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
    }

    .head-info-titles h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: inherit;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .head-info-titles p {
        margin: 0;
        font-size: 0.8rem;
        color: var(--hrms-chat-meta);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .head-info-titles p.online-text {
        color: #10b981;
        font-weight: 600;
    }

    .live-status-pill {
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

    .live-status-pill.disconnected {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.2);
    }

    /* Quick suggestions bar */
    .quick-chips-row {
        background: var(--hrms-chat-card);
        padding: 8px 24px;
        border-bottom: 1px solid var(--hrms-chat-border);
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none;
    }
    .quick-chips-row::-webkit-scrollbar { display: none; }

    .quick-chip {
        font-size: 0.78rem;
        padding: 4px 12px;
        border-radius: 20px;
        background: var(--hrms-chat-bg);
        border: 1px solid var(--hrms-chat-border);
        color: var(--hrms-chat-meta);
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .quick-chip:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        transform: translateY(-1px);
    }

    /* Messages Stream */
    .hrms-chat-stream {
        flex: 1;
        overflow-y: auto;
        padding: 24px 28px;
        background-color: var(--hrms-chat-bg);
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
        background: var(--hrms-chat-border);
        z-index: 1;
    }

    .date-divider-pill {
        position: relative;
        z-index: 2;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: var(--hrms-chat-card);
        border: 1px solid var(--hrms-chat-border);
        color: var(--hrms-chat-meta);
        padding: 3px 14px;
        border-radius: 12px;
    }

    /* Message Bubbles */
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

    .msg-sender-avatar {
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

    .msg-sender-avatar.support-icon {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: #ffffff;
    }

    .chat-bubble-box {
        max-width: 70%;
        min-width: 120px;
        display: flex;
        flex-direction: column;
    }

    .bubble-author-label {
        font-size: 0.72rem;
        font-weight: 700;
        margin-bottom: 3px;
        color: var(--hrms-chat-meta);
    }

    .chat-msg-row.sent .bubble-author-label {
        text-align: right;
    }

    .bubble-body {
        padding: 10px 16px;
        border-radius: 16px;
        position: relative;
        word-break: break-word;
        line-height: 1.5;
        font-size: 0.92rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        animation: bubbleFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes bubbleFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-msg-row.sent .bubble-body {
        background: var(--hrms-chat-sent-bg);
        color: var(--hrms-chat-sent-text);
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .chat-msg-row.received .bubble-body {
        background: var(--hrms-chat-recv-bg);
        color: var(--hrms-chat-recv-text);
        border: 1px solid var(--hrms-chat-border);
        border-bottom-left-radius: 4px;
    }

    .bubble-text {
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

    .bubble-footer-meta {
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

    .chat-msg-row.received .bubble-footer-meta {
        color: var(--hrms-chat-meta);
        opacity: 1;
    }

    .seen-ticks {
        font-size: 0.78rem;
        letter-spacing: -2px;
    }

    .seen-ticks.blue {
        color: var(--hrms-chat-seen-blue);
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
    .typing-indicator-wrap {
        display: none;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: var(--hrms-chat-recv-bg);
        border: 1px solid var(--hrms-chat-border);
        border-radius: 12px;
        width: fit-content;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        margin-left: 42px;
    }

    .typing-label {
        font-size: 0.75rem;
        color: var(--hrms-chat-meta);
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

    @keyframes typingDotJump {
        0%, 80%, 100% { transform: translateY(0); opacity: 0.4; }
        40% { transform: translateY(-4px); opacity: 1; }
    }

    /* Input Dock */
    .hrms-chat-dock {
        background: var(--hrms-chat-header-bg);
        padding: 12px 24px;
        border-top: 1px solid var(--hrms-chat-border);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* Selected attachment preview */
    .attachment-preview-bar {
        display: none;
        align-items: center;
        gap: 12px;
        padding: 8px 14px;
        background: var(--hrms-chat-bg);
        border: 1px dashed var(--hrms-chat-border);
        border-radius: 12px;
    }

    .preview-thumb-img {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid var(--hrms-chat-border);
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
        color: var(--hrms-chat-meta);
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
        background: var(--hrms-chat-bg);
        border: 1px solid var(--hrms-chat-border);
        color: var(--hrms-chat-meta);
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

    .dock-input-wrap {
        flex: 1;
        position: relative;
    }

    .dock-input-field {
        width: 100%;
        border: 1px solid var(--hrms-chat-border);
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 0.94rem;
        background: var(--hrms-chat-bg);
        color: inherit;
        outline: none;
        transition: all 0.2s;
    }

    .dock-input-field:focus {
        border-color: #2563eb;
        background: var(--hrms-chat-card);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .dock-send-btn {
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

    .dock-send-btn:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-1px);
    }

    /* Closed Chat Banner */
    .chat-closed-dock {
        background: #f8fafc;
        border-top: 1px solid var(--hrms-chat-border);
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    [data-theme="dark"] .chat-closed-dock {
        background: #1e293b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-3">
    <!-- Breadcrumb & Page Header -->
    <div class="hrms-chat-page-header">
        <div>
            <h4>
                <i class="fa-solid fa-headset text-primary"></i>
                Support & Operations Desk
            </h4>
            <p class="text-muted small mb-0">Direct priority line to Stafo Super Admin, Payroll Specialists & Technical Support</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-3 py-2 rounded-pill">
                <i class="fa-solid fa-shield-halved me-1"></i> Dedicated SLA: &lt; 15 mins
            </span>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Exit to Dashboard">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Main Chat Workspace Card -->
    <div class="hrms-chat-container">
        <!-- Chat Head -->
        <div class="hrms-chat-head">
            <div class="head-support-profile">
                <div class="head-avatar-box">
                    <div class="head-avatar-icon">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <span class="head-status-dot" id="headerStatusDot"></span>
                </div>
                <div class="head-info-titles">
                    <h5>
                        Stafo Super Admin Desk
                        <i class="fa-solid fa-circle-check text-primary" title="Verified Support Specialist" style="font-size: 0.85rem;"></i>
                    </h5>
                    <p id="headerStatusText">
                        <i class="fa-solid fa-clock" style="font-size: 0.7rem;"></i> Connecting to support line...
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Status Badge -->
                <span id="chatLifecycleBadge" class="badge {{ ($chat_status ?? 'open') === 'closed' ? 'bg-secondary' : 'bg-success' }} px-3 py-2 rounded-pill">
                    <i class="fa-solid {{ ($chat_status ?? 'open') === 'closed' ? 'fa-lock' : 'fa-circle-check' }} me-1"></i>
                    <span id="chatLifecycleText">{{ ($chat_status ?? 'open') === 'closed' ? 'Resolved / Closed' : 'Active Session' }}</span>
                </span>

                <!-- Close / Resolve Button -->
                <button type="button" id="closeChatActionBtn" class="btn btn-sm btn-outline-danger rounded-pill px-3 {{ ($chat_status ?? 'open') === 'closed' ? 'd-none' : '' }}" onclick="promptCloseChat()">
                    <i class="fa-solid fa-circle-check me-1"></i> Close Chat
                </button>

                <span class="live-status-pill" id="wsBadge">
                    <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> <span id="wsText">Live Gateway</span>
                </span>
            </div>
        </div>

        <!-- Quick HRMS Inquiry Chips -->
        <div class="quick-chips-row" id="quickChipsRow" style="{{ ($chat_status ?? 'open') === 'closed' ? 'display: none !important;' : '' }}">
            <span class="text-muted small me-1"><i class="fa-solid fa-bolt text-warning"></i> Quick Topics:</span>
            <button type="button" class="quick-chip" onclick="applyQuickTopic('Need help with monthly payroll & salary calculation')">
                💰 Salary Calculation
            </button>
            <button type="button" class="quick-chip" onclick="applyQuickTopic('Biometric attendance device sync issue')">
                ⏱️ Attendance Device
            </button>
            <button type="button" class="quick-chip" onclick="applyQuickTopic('Query regarding Employee PF / ESI compliance')">
                📄 PF / ESI Compliance
            </button>
            <button type="button" class="quick-chip" onclick="applyQuickTopic('Requesting custom package upgrade or renewal')">
                ⭐ Subscription Plan
            </button>
            <button type="button" class="quick-chip" onclick="applyQuickTopic('Help required for bulk employee Excel import')">
                👥 Bulk Employee Import
            </button>
        </div>

        <!-- Chat Stream -->
        <div class="hrms-chat-stream" id="chatStreamBox">
            @php $lastDate = null; @endphp

            @if(count($chats) > 0)
                @foreach($chats as $chat)
                    @php
                        $msgDate = \Carbon\Carbon::parse($chat->created_at)->format('d M Y');
                        $msgTime = \Carbon\Carbon::parse($chat->created_at)->format('h:i A');
                        $isMe = ($chat->message_by === 'company');
                        $isSystem = ($chat->message_by === 'system');
                        $isSeen = $chat->is_seen_admin ? true : false;
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
                                <div class="msg-sender-avatar support-icon" title="Stafo Support">
                                    <i class="fa-solid fa-headset"></i>
                                </div>
                            @endif

                            <div class="chat-bubble-box">
                                <span class="bubble-author-label">{{ $isMe ? 'You' : 'Stafo Admin' }}</span>
                                <div class="bubble-body">
                                    @if(!empty($chat->attachment))
                                        <div class="chat-attachment-container">
                                            <img src="{{ asset($chat->attachment) }}" class="chat-attachment-img" alt="Screenshot" onclick="openLightbox('{{ asset($chat->attachment) }}')">
                                        </div>
                                    @endif

                                    @if(!empty($chat->message))
                                        <p class="bubble-text">{{ $chat->message }}</p>
                                    @endif

                                    <div class="bubble-footer-meta">
                                        <span>{{ $msgTime }}</span>
                                        @if($isMe)
                                            <span class="seen-ticks {{ $isSeen ? 'blue' : '' }}" title="{{ $isSeen ? 'Seen by Support' : 'Sent' }}">
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
                <div class="empty-chat-hero" id="emptyPlaceholder">
                    <i class="fa-regular fa-comments"></i>
                    <h5 class="fw-bold">How can we assist you today?</h5>
                    <p class="small">Our HRMS operations and technical specialists are here to resolve your queries regarding payroll, attendance, shifts, leave rules, and employee records.</p>
                </div>
            @endif

            <!-- Typing indicator -->
            <div class="typing-indicator-wrap" id="typingBubble">
                <span class="typing-label">Support specialist is typing</span>
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        </div>

        <!-- Chat Dock: When Active -->
        <div class="hrms-chat-dock" id="activeChatDock" style="{{ ($chat_status ?? 'open') === 'closed' ? 'display: none !important;' : '' }}">
            <!-- Attachment preview strip -->
            <div class="attachment-preview-bar" id="attachmentPreviewBar">
                <img src="" id="previewThumbImg" class="preview-thumb-img" alt="Thumbnail">
                <div class="preview-file-info">
                    <p class="preview-file-name" id="previewFileName">screenshot.png</p>
                    <p class="preview-file-size" id="previewFileSize">150 KB</p>
                </div>
                <button type="button" class="remove-attachment-btn" onclick="clearSelectedAttachment()" title="Remove Attachment">
                    <i class="fa-solid fa-circle-xmark fa-lg"></i>
                </button>
            </div>

            <div class="d-flex align-items-center gap-2 w-100">
                <!-- Hidden file input -->
                <input type="file" id="chatFileInput" accept="image/*,.pdf" style="display: none;">

                <!-- Attachment trigger button -->
                <button type="button" class="attach-trigger-btn" id="attachBtn" title="Upload Screenshot / Image">
                    <i class="fa-solid fa-paperclip"></i>
                </button>

                <!-- Input field -->
                <div class="dock-input-wrap">
                    <input type="text" id="chatInput" class="dock-input-field" placeholder="Type your query or paste screenshot (Ctrl+V)... (Press Enter to send)" autocomplete="off">
                </div>

                <!-- Send button -->
                <button class="dock-send-btn" id="sendBtn" title="Send Message">
                    <span>Send</span>
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </div>

        <!-- Chat Dock: When Closed -->
        <div class="chat-closed-dock" id="closedChatDock" style="{{ ($chat_status ?? 'open') === 'closed' ? '' : 'display: none !important;' }}">
            <div class="d-flex align-items-center gap-3">
                <i class="fa-solid fa-circle-check text-success fa-2x"></i>
                <div>
                    <h6 class="fw-bold mb-0">This support session has been resolved & closed.</h6>
                    <p class="text-muted small mb-0">Need more assistance? You can reopen this chat anytime to continue.</p>
                </div>
            </div>
            <button type="button" class="btn btn-primary rounded-pill px-4" onclick="reopenChat()">
                <i class="fa-solid fa-lock-open me-2"></i> Reopen Support Chat
            </button>
        </div>
    </div>
</div>

<!-- Image Lightbox Modal -->
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 position-relative text-center">
                <button type="button" class="btn btn-dark position-absolute top-0 end-0 m-2 rounded-circle shadow" data-bs-dismiss="modal" aria-label="Close" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="" id="lightboxModalImg" class="img-fluid rounded-3 shadow-lg" style="max-height: 85vh; object-fit: contain; background: #000;">
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="/socket.io/socket.io.js"></script>
<script>
    if (typeof io === 'undefined') {
        document.write('<script src="https://cdn.socket.io/4.8.1/socket.io.min.js"><\/script>');
    }
</script>

<script>
    const companyId = {{ (int)$company_id }};
    const csrfToken = '{{ csrf_token() }}';
    const saveUrl = '{{ route("savechat") }}';
    const toggleStatusUrl = '{{ route("chat.toggleStatus") }}';
    const uploadAttachmentUrl = '{{ route("chat.uploadAttachment") }}';

    let currentStatus = '{{ $chat_status ?? "open" }}';
    let socket = null;
    let isTyping = false;
    let typingTimeout = null;
    let selectedFile = null;

    const chatStream = document.getElementById('chatStreamBox');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    const attachBtn = document.getElementById('attachBtn');
    const chatFileInput = document.getElementById('chatFileInput');
    const attachmentPreviewBar = document.getElementById('attachmentPreviewBar');
    const previewThumbImg = document.getElementById('previewThumbImg');
    const previewFileName = document.getElementById('previewFileName');
    const previewFileSize = document.getElementById('previewFileSize');

    const typingBubble = document.getElementById('typingBubble');
    const headerStatusDot = document.getElementById('headerStatusDot');
    const headerStatusText = document.getElementById('headerStatusText');
    const wsBadge = document.getElementById('wsBadge');
    const wsText = document.getElementById('wsText');
    const emptyPlaceholder = document.getElementById('emptyPlaceholder');

    const activeDock = document.getElementById('activeChatDock');
    const closedDock = document.getElementById('closedChatDock');
    const closeActionBtn = document.getElementById('closeChatActionBtn');
    const quickChips = document.getElementById('quickChipsRow');
    const lifecycleBadge = document.getElementById('chatLifecycleBadge');

    function applyQuickTopic(text) {
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
        const modalImg = document.getElementById('lightboxModalImg');
        if (modalImg) modalImg.src = url;
        const modalEl = document.getElementById('imageLightboxModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    // Attachment file selection & preview
    attachBtn.addEventListener('click', () => {
        chatFileInput.click();
    });

    chatFileInput.addEventListener('change', (e) => {
        if (e.target.files && e.target.files[0]) {
            handleSelectedFile(e.target.files[0]);
        }
    });

    // Clipboard Paste (Ctrl+V) for screenshots
    document.addEventListener('paste', (e) => {
        if (currentStatus === 'closed') return;
        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (let item of items) {
            if (item.type.indexOf('image') !== -1) {
                const blob = item.getAsFile();
                handleSelectedFile(blob);
                e.preventDefault();
                break;
            }
        }
    });

    function handleSelectedFile(file) {
        selectedFile = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            previewThumbImg.src = e.target.result;
            previewFileName.textContent = file.name || 'screenshot.png';
            previewFileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
            attachmentPreviewBar.style.display = 'flex';
            chatInput.focus();
        };
        reader.readAsDataURL(file);
    }

    function clearSelectedAttachment() {
        selectedFile = null;
        chatFileInput.value = '';
        attachmentPreviewBar.style.display = 'none';
        previewThumbImg.src = '';
    }

    function updateLifecycleUI(status) {
        currentStatus = status;
        if (status === 'closed') {
            activeDock.style.setProperty('display', 'none', 'important');
            closedDock.style.removeProperty('display');
            closeActionBtn.classList.add('d-none');
            quickChips.style.setProperty('display', 'none', 'important');

            lifecycleBadge.className = 'badge bg-secondary px-3 py-2 rounded-pill';
            lifecycleBadge.innerHTML = '<i class="fa-solid fa-lock me-1"></i> <span>Resolved / Closed</span>';
        } else {
            closedDock.style.setProperty('display', 'none', 'important');
            activeDock.style.removeProperty('display');
            closeActionBtn.classList.remove('d-none');
            quickChips.style.removeProperty('display');

            lifecycleBadge.className = 'badge bg-success px-3 py-2 rounded-pill';
            lifecycleBadge.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> <span>Active Session</span>';
            chatInput.focus();
        }
    }

    function promptCloseChat() {
        if (confirm('Are you sure you want to mark this support conversation as Resolved & Closed?')) {
            performStatusUpdate('closed', 'Issue marked resolved by Company');
        }
    }

    function reopenChat() {
        performStatusUpdate('open', '');
    }

    function performStatusUpdate(newStatus, reason) {
        if (socket && socket.connected) {
            socket.emit('update_chat_status', {
                company_id: companyId,
                status: newStatus,
                role: 'company',
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
        .catch(e => console.error('[Status Update Error]', e));
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

        const isSent = (messageBy === 'company');
        const row = document.createElement('div');
        row.className = 'chat-msg-row ' + (isSent ? 'sent' : 'received');
        if (msgId) row.setAttribute('data-id', msgId);

        const displayTime = timeStr || formatTime(new Date());
        let checkHtml = '';
        if (isSent) {
            checkHtml = `<span class="seen-ticks ${isSeen ? 'blue' : ''}" title="${isSeen ? 'Seen by Support' : 'Sent'}">${isSeen ? '✓✓' : '✓'}</span>`;
        }

        let avatarHtml = '';
        if (!isSent) {
            avatarHtml = `
                <div class="msg-sender-avatar support-icon" title="Stafo Support">
                    <i class="fa-solid fa-headset"></i>
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
            msgTextHtml = `<p class="bubble-text">${cleanText}</p>`;
        }

        row.innerHTML = `
            ${avatarHtml}
            <div class="chat-bubble-box">
                <span class="bubble-author-label">${isSent ? 'You' : 'Stafo Admin'}</span>
                <div class="bubble-body">
                    ${attachmentHtml}
                    ${msgTextHtml}
                    <div class="bubble-footer-meta">
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
                    role: 'company'
                });
            });

            socket.on('disconnect', () => {
                wsBadge.classList.add('disconnected');
                wsText.textContent = 'Reconnecting...';
                headerStatusDot.classList.remove('online');
                headerStatusText.innerHTML = '<i class="fa-solid fa-clock" style="font-size: 0.7rem;"></i> Offline';
                headerStatusText.classList.remove('online-text');
            });

            socket.on('presence_update', (data) => {
                if (data && data.admin_online) {
                    headerStatusDot.classList.add('online');
                    headerStatusText.innerHTML = '<i class="fa-solid fa-circle text-success" style="font-size: 0.6rem;"></i> Specialist Active Now';
                    headerStatusText.classList.add('online-text');
                } else {
                    headerStatusDot.classList.remove('online');
                    headerStatusText.innerHTML = '<i class="fa-solid fa-clock" style="font-size: 0.7rem;"></i> Avg response under 10m';
                    headerStatusText.classList.remove('online-text');
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
                    if (msg.message_by === 'admin') {
                        typingBubble.style.display = 'none';
                        appendMessageBubble(msg.message, 'admin', false, formatTime(new Date(msg.created_at || Date.now())), msg.id, msg.attachment);

                        socket.emit('mark_seen', {
                            company_id: companyId,
                            role: 'company'
                        });
                    } else if (msg.message_by === 'system') {
                        appendSystemNotice(msg.message, formatTime(new Date(msg.created_at || Date.now())));
                    }
                }
            });

            socket.on('user_typing', (data) => {
                if (data && data.role === 'admin') {
                    if (data.is_typing) {
                        typingBubble.style.display = 'inline-flex';
                        scrollToBottom(true);
                    } else {
                        typingBubble.style.display = 'none';
                    }
                }
            });

            socket.on('messages_seen', (data) => {
                if (data && data.seen_by === 'admin') {
                    document.querySelectorAll('.chat-msg-row.sent .seen-ticks').forEach(icon => {
                        icon.classList.add('blue');
                        icon.textContent = '✓✓';
                    });
                }
            });

        } catch (err) {
            console.error('[WebSocket Init Error]', err);
            wsBadge.classList.add('disconnected');
            wsText.textContent = 'HTTP Mode';
        }
    }

    function sendMessage() {
        const text = chatInput.value.trim();
        const fileToUpload = selectedFile;

        if (!text && !fileToUpload) return;

        chatInput.value = '';
        clearSelectedAttachment();

        if (fileToUpload) {
            // Upload file with FormData
            const formData = new FormData();
            formData.append('file', fileToUpload);
            formData.append('message', text);
            formData.append('_token', csrfToken);

            // Optimistic display with local preview
            const localPreviewUrl = URL.createObjectURL(fileToUpload);
            appendMessageBubble(text, 'company', false, formatTime(new Date()), null, localPreviewUrl);

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
        appendMessageBubble(text, 'company', false, formatTime(new Date()));

        if (socket && socket.connected) {
            socket.emit('stop_typing', { company_id: companyId, role: 'company' });
            isTyping = false;

            socket.emit('send_message', {
                company_id: companyId,
                message: text,
                message_by: 'company',
                sender: companyId,
                receiver: 1
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
                socket.emit('typing', { company_id: companyId, role: 'company' });
            }
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                isTyping = false;
                socket.emit('stop_typing', { company_id: companyId, role: 'company' });
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
