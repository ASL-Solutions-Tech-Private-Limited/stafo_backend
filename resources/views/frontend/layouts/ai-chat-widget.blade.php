<!-- Stafo AI Chat Widget ("Nisha") -->
<style>
  /* ===== Stafo AI Chat Widget Styles ===== */
  #nisha-chat-container * {
    box-sizing: border-box;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  }

  /* Floating Launcher Button */
  #nisha-chat-launcher {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff5722 0%, #f4511e 100%);
    box-shadow: 0 8px 25px rgba(244, 81, 30, 0.45);
    border: none;
    cursor: pointer;
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    outline: none;
  }

  #nisha-chat-launcher:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 12px 30px rgba(244, 81, 30, 0.55);
  }

  #nisha-chat-launcher:active {
    transform: scale(0.95);
  }

  #nisha-chat-launcher svg {
    color: #ffffff;
    transition: transform 0.25s ease;
  }

  #nisha-chat-launcher .icon-closed {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #nisha-chat-launcher .icon-open {
    display: none;
    align-items: center;
    justify-content: center;
  }

  #nisha-chat-launcher.is-active .icon-closed {
    display: none;
  }

  #nisha-chat-launcher.is-active .icon-open {
    display: flex;
  }

  /* Chat Window */
  #nisha-chat-window {
    position: fixed;
    bottom: 96px;
    right: 24px;
    width: 380px;
    height: 560px;
    max-width: calc(100vw - 32px);
    max-height: calc(100vh - 120px);
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 16px 50px rgba(0, 0, 0, 0.22);
    display: none;
    flex-direction: column;
    z-index: 999998;
    overflow: hidden;
    animation: nishaFadeInUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid rgba(0, 0, 0, 0.08);
    transition: width 0.3s ease, height 0.3s ease;
  }

  #nisha-chat-window.is-expanded {
    width: 480px;
    height: 680px;
  }

  @keyframes nishaFadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px) scale(0.96);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  /* Header */
  .nisha-header {
    background: linear-gradient(135deg, #ff5722 0%, #f4511e 100%);
    padding: 13px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #ffffff;
    user-select: none;
  }

  .nisha-header-profile {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .nisha-avatar-wrapper {
    position: relative;
    width: 44px;
    height: 44px;
    flex-shrink: 0;
  }

  .nisha-avatar-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: contain;
    background: #ffffff;
    padding: 4px;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
  }

  .nisha-online-status {
    position: absolute;
    bottom: 0px;
    right: 0px;
    width: 12px;
    height: 12px;
    background-color: #10b981;
    border: 2px solid #ffffff;
    border-radius: 50%;
  }

  .nisha-header-title {
    font-size: 1.12rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    margin: 0;
  }

  .nisha-header-sub {
    font-size: 0.76rem;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
  }

  .nisha-header-actions {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .nisha-btn-icon {
    background: transparent;
    border: none;
    color: #ffffff;
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, opacity 0.2s, transform 0.15s;
    opacity: 0.92;
  }

  .nisha-btn-icon:hover {
    background: rgba(255, 255, 255, 0.22);
    opacity: 1;
    transform: scale(1.08);
  }

  /* Chat Messages Stream */
  .nisha-messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #fbfcfe;
    display: flex;
    flex-direction: column;
    gap: 12px;
    scroll-behavior: smooth;
  }

  .nisha-messages-container::-webkit-scrollbar {
    width: 6px;
  }

  .nisha-messages-container::-webkit-scrollbar-track {
    background: transparent;
  }

  .nisha-messages-container::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 4px;
  }

  /* Message Rows */
  .nisha-msg-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    max-width: 88%;
  }

  .nisha-msg-row.bot {
    align-self: flex-start;
  }

  .nisha-msg-row.user {
    align-self: flex-end;
    flex-direction: row-reverse;
  }

  .nisha-msg-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: contain;
    background: #ffffff;
    padding: 3px;
    flex-shrink: 0;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  }

  .nisha-msg-bubbles {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .nisha-bubble {
    padding: 10px 14px;
    font-size: 13.5px;
    line-height: 1.5;
    word-break: break-word;
  }

  .nisha-msg-row.bot .nisha-bubble {
    background: #f1f3f6;
    color: #1e293b;
    border-radius: 16px 16px 16px 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  }

  .nisha-msg-row.user .nisha-bubble {
    background: #ff5722;
    color: #ffffff;
    border-radius: 16px 16px 4px 16px;
    box-shadow: 0 2px 5px rgba(244, 81, 30, 0.25);
  }

  .nisha-bubble p {
    margin: 0 0 6px 0;
  }

  .nisha-bubble p:last-child {
    margin-bottom: 0;
  }

  .nisha-bubble a {
    color: #f4511e;
    font-weight: 600;
    text-decoration: underline;
  }

  .nisha-msg-row.user .nisha-bubble a {
    color: #ffffff;
  }

  /* Option Pills Container */
  .nisha-pills-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 4px;
  }

  .nisha-pill-btn {
    background: #ffffff;
    border: 1.5px solid #94a3b8;
    color: #334155;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    outline: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  }

  .nisha-pill-btn:hover {
    border-color: #ff5722;
    color: #ff5722;
    background: #fff5f2;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(244, 81, 30, 0.15);
  }

  .nisha-pill-btn.pill-link {
    border-color: #ea580c;
    background: #ffedd5;
    color: #9a3412;
    font-weight: 600;
  }

  .nisha-pill-btn.pill-link:hover {
    background: #fed7aa;
    color: #7c2d12;
  }

  /* Typing Indicator */
  .nisha-typing-row {
    display: none;
    align-items: center;
    gap: 10px;
    align-self: flex-start;
  }

  .nisha-typing-bubble {
    background: #f1f3f6;
    padding: 10px 16px;
    border-radius: 16px 16px 16px 4px;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .nisha-dot {
    width: 6px;
    height: 6px;
    background: #94a3b8;
    border-radius: 50%;
    animation: nishaBlink 1.4s infinite both;
  }

  .nisha-dot:nth-child(2) {
    animation-delay: 0.2s;
  }

  .nisha-dot:nth-child(3) {
    animation-delay: 0.4s;
  }

  @keyframes nishaBlink {
    0%, 80%, 100% {
      opacity: 0.3;
      transform: scale(0.8);
    }
    40% {
      opacity: 1;
      transform: scale(1.1);
    }
  }

  /* Image Attachment preview */
  .nisha-attachment-preview {
    max-width: 200px;
    max-height: 150px;
    border-radius: 8px;
    margin-top: 6px;
    object-fit: cover;
    border: 1px solid rgba(0, 0, 0, 0.1);
    cursor: pointer;
  }

  /* Footer Input Area */
  .nisha-footer {
    padding: 12px 16px 14px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
  }

  .nisha-input-capsule {
    display: flex;
    align-items: center;
    border: 1.5px solid #cbd5e1;
    border-radius: 28px;
    padding: 4px 8px 4px 16px;
    background: #ffffff;
    transition: border-color 0.2s, box-shadow 0.2s;
  }

  .nisha-input-capsule:focus-within {
    border-color: #ff5722;
    box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.12);
  }

  .nisha-input-field {
    flex: 1;
    border: none;
    outline: none;
    font-size: 13.5px;
    color: #1e293b;
    background: transparent;
    padding: 6px 4px;
  }

  .nisha-input-field::placeholder {
    color: #94a3b8;
  }

  .nisha-action-btns {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .nisha-action-btn {
    background: transparent;
    border: none;
    color: #64748b;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    outline: none;
    padding: 0;
  }

  .nisha-action-btn:hover {
    color: #ff5722;
    background: #fff5f2;
  }

  .nisha-action-btn.send-btn {
    color: #64748b;
  }

  .nisha-action-btn.send-btn.has-text {
    color: #ff5722;
  }

  .nisha-action-btn.send-btn:hover {
    color: #ff5722;
    transform: scale(1.1);
  }

  /* Responsive Design for Mobile Devices */
  @media (max-width: 480px) {
    #nisha-chat-window {
      right: 12px;
      bottom: 84px;
      width: calc(100vw - 24px);
      height: calc(100vh - 100px);
      border-radius: 16px;
    }

    #nisha-chat-launcher {
      right: 16px;
      bottom: 16px;
      width: 54px;
      height: 54px;
    }
  }
</style>

<div id="nisha-chat-container">
  <!-- Chat Window -->
  <div id="nisha-chat-window">
    <!-- Header -->
    <div class="nisha-header">
      <div class="nisha-header-profile">
        <div class="nisha-avatar-wrapper">
          <!-- Stafo Logo with Clean White Background & Online Indicator -->
          <img src="{{ asset('assets/images/icon/c_logo.png') }}" alt="STAFO Logo" class="nisha-avatar-img">
          <span class="nisha-online-status" title="Online"></span>
        </div>
        <div>
          <h4 class="nisha-header-title">Chaity</h4>
        </div>
      </div>
      <div class="nisha-header-actions">
        <!-- Expand Button (Pure SVG) -->
        <button type="button" class="nisha-btn-icon" id="nisha-expand-btn" title="Expand / Minimize" aria-label="Expand or Minimize">
          <svg id="nisha-expand-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 3 21 3 21 9"></polyline>
            <polyline points="9 21 3 21 3 15"></polyline>
            <line x1="21" y1="3" x2="14" y2="10"></line>
            <line x1="3" y1="21" x2="10" y2="14"></line>
          </svg>
        </button>
        <!-- Close Window Button (Pure SVG) -->
        <button type="button" class="nisha-btn-icon" id="nisha-close-btn" title="Close Chat" aria-label="Close Chat">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
    </div>

    <!-- Messages Container -->
    <div class="nisha-messages-container" id="nisha-messages">
      <!-- Initial Greeting from Nisha with Stafo Logo Avatar -->
      <div class="nisha-msg-row bot">
        <img src="{{ asset('assets/images/icon/c_logo.png') }}" class="nisha-msg-avatar" alt="STAFO">
        <div class="nisha-msg-bubbles">
          <div class="nisha-bubble">
            Hi there. How can I help you today?
          </div>
          <div class="nisha-bubble">
            Are you looking for ?
          </div>
          <!-- Quick Action Pills -->
          <div class="nisha-pills-row">
            <button type="button" class="nisha-pill-btn" onclick="sendNishaPill('Product Demo')">Product Demo</button>
            <button type="button" class="nisha-pill-btn" onclick="sendNishaPill('Job')">Job</button>
            <button type="button" class="nisha-pill-btn" onclick="sendNishaPill('Partnership')">Partnership</button>
            <button type="button" class="nisha-pill-btn" onclick="sendNishaPill('Support')">Support</button>
          </div>
        </div>
      </div>

      <!-- Typing Indicator -->
      <div class="nisha-typing-row" id="nisha-typing">
        <img src="{{ asset('assets/images/icon/c_logo.png') }}" class="nisha-msg-avatar" alt="STAFO">
        <div class="nisha-typing-bubble">
          <span class="nisha-dot"></span>
          <span class="nisha-dot"></span>
          <span class="nisha-dot"></span>
        </div>
      </div>
    </div>

    <!-- Input Footer -->
    <div class="nisha-footer">
      <div class="nisha-input-capsule">
        <input type="text" id="nisha-user-input" class="nisha-input-field" placeholder="Type a message..." autocomplete="off">
        <div class="nisha-action-btns">
          <!-- Attachment Clip (Pure SVG) -->
          <button type="button" class="nisha-action-btn" id="nisha-attach-btn" title="Attach screenshot or document" aria-label="Attach File">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
            </svg>
          </button>
          <input type="file" id="nisha-file-input" style="display:none" accept="image/*,.pdf,.doc,.docx">

          <!-- Send Plane (Pure SVG) -->
          <button type="button" class="nisha-action-btn send-btn" id="nisha-send-btn" title="Send message" aria-label="Send Message">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="22" y1="2" x2="11" y2="13"></line>
              <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Floating Launcher Button -->
  <button id="nisha-chat-launcher" type="button" aria-label="Open Stafo Support Chat">
    <!-- Closed Icon: Double speech bubble -->
    <span class="icon-closed">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        <path d="M8 9h8"></path>
        <path d="M8 13h6"></path>
      </svg>
    </span>
    <!-- Open Icon: White 'X' -->
    <span class="icon-open">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </span>
  </button>
</div>

<script>
  (function() {
    const launcher = document.getElementById('nisha-chat-launcher');
    const chatWindow = document.getElementById('nisha-chat-window');
    const closeBtn = document.getElementById('nisha-close-btn');
    const expandBtn = document.getElementById('nisha-expand-btn');
    const inputField = document.getElementById('nisha-user-input');
    const sendBtn = document.getElementById('nisha-send-btn');
    const attachBtn = document.getElementById('nisha-attach-btn');
    const fileInput = document.getElementById('nisha-file-input');
    const messagesContainer = document.getElementById('nisha-messages');
    const typingIndicator = document.getElementById('nisha-typing');
    const avatarUrl = "{{ asset('assets/images/icon/c_logo.png') }}";
    const apiEndpoint = "{{ route('website.aiChat') }}";
    const uploadEndpoint = "{{ route('website.aiChat.upload') }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    const STORAGE_KEY_OPEN = 'stafo_nisha_chat_open';
    const STORAGE_KEY_HISTORY = 'stafo_nisha_chat_history_v1';

    // 1. Restore State from Session Storage
    function loadSavedChat() {
      try {
        const isOpen = sessionStorage.getItem(STORAGE_KEY_OPEN) === 'true';
        if (isOpen) {
          openChat();
        }

        const history = JSON.parse(sessionStorage.getItem(STORAGE_KEY_HISTORY) || '[]');
        if (history.length > 0) {
          // Re-render saved messages after greeting
          history.forEach(item => {
            renderMessage(item.sender, item.text, item.quick_actions, false, item.attachmentUrl);
          });
          scrollToBottom();
        }
      } catch (e) {
        console.error('Nisha Chat storage load error', e);
      }
    }

    function saveMessageToStorage(sender, text, quickActions = [], attachmentUrl = null) {
      try {
        const history = JSON.parse(sessionStorage.getItem(STORAGE_KEY_HISTORY) || '[]');
        history.push({ sender, text, quick_actions: quickActions, attachmentUrl, time: Date.now() });
        sessionStorage.setItem(STORAGE_KEY_HISTORY, JSON.stringify(history));
      } catch (e) {
        console.error('Nisha Chat storage save error', e);
      }
    }

    // Toggle Chat Window
    function openChat() {
      chatWindow.style.display = 'flex';
      launcher.classList.add('is-active');
      sessionStorage.setItem(STORAGE_KEY_OPEN, 'true');
      scrollToBottom();
      setTimeout(() => inputField.focus(), 150);
    }

    function closeChat() {
      chatWindow.style.display = 'none';
      launcher.classList.remove('is-active');
      sessionStorage.setItem(STORAGE_KEY_OPEN, 'false');
    }

    launcher.addEventListener('click', function() {
      if (chatWindow.style.display === 'flex') {
        closeChat();
      } else {
        openChat();
      }
    });

    closeBtn.addEventListener('click', closeChat);

    // Expand / Minimize Chat with Pure SVG Icon Toggle
    expandBtn.addEventListener('click', function() {
      chatWindow.classList.toggle('is-expanded');
      const isExpanded = chatWindow.classList.contains('is-expanded');
      if (isExpanded) {
        expandBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 14 10 14 10 20"></polyline><polyline points="20 10 14 10 14 4"></polyline><line x1="14" y1="10" x2="21" y2="3"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>';
        expandBtn.title = "Minimize";
      } else {
        expandBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>';
        expandBtn.title = "Expand";
      }
    });

    // Auto-highlight send button when typing
    inputField.addEventListener('input', function() {
      if (this.value.trim().length > 0) {
        sendBtn.classList.add('has-text');
      } else {
        sendBtn.classList.remove('has-text');
      }
    });

    // Enter Key to Send
    inputField.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        sendUserMessage();
      }
    });

    sendBtn.addEventListener('click', function(e) {
      e.preventDefault();
      sendUserMessage();
    });

    // Attachment Button
    attachBtn.addEventListener('click', function() {
      fileInput.click();
    });

    fileInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        uploadFile(this.files[0]);
      }
    });

    // Paste screenshot directly into input
    window.addEventListener('paste', function(e) {
      if (chatWindow.style.display !== 'flex') return;
      const items = (e.clipboardData || e.originalEvent.clipboardData).items;
      for (let index in items) {
        const item = items[index];
        if (item.kind === 'file' && item.type.includes('image/')) {
          const blob = item.getAsFile();
          uploadFile(blob);
        }
      }
    });

    function uploadFile(file) {
      const formData = new FormData();
      formData.append('attachment', file);
      formData.append('_token', csrfToken);

      renderMessage('user', '📎 Uploaded Attachment', [], true, URL.createObjectURL(file));
      showTyping(true);

      fetch(uploadEndpoint, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        showTyping(false);
        if (data.status === 'success') {
          renderMessage('bot', data.reply, [
            { label: 'Product Demo', action: 'Product Demo', type: 'pill' },
            { label: 'Call Support (+91 6292252470)', url: 'tel:+916292252470', type: 'link' }
          ], true);
        } else {
          renderMessage('bot', "Could not upload file. Please try again or email us at support@stafo.com.", [], true);
        }
      })
      .catch(err => {
        showTyping(false);
        renderMessage('bot', "Upload error. Please check your internet connection.", [], true);
      });

      fileInput.value = '';
    }

    // Send Message Handler
    function sendUserMessage() {
      const text = inputField.value.trim();
      if (!text) return;

      inputField.value = '';
      sendBtn.classList.remove('has-text');

      // 1. Render user message
      renderMessage('user', text, [], true);

      // 2. Show Typing Indicator
      showTyping(true);

      // 3. Send to Backend
      fetch(apiEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ message: text })
      })
      .then(res => res.json())
      .then(data => {
        // Natural slight delay for realism
        setTimeout(() => {
          showTyping(false);
          if (data.status === 'success') {
            renderMessage('bot', data.reply, data.quick_actions || [], true);
          } else {
            renderMessage('bot', data.reply || "I am here to assist with Stafo HRMS support. How can I help?", [], true);
          }
        }, 400);
      })
      .catch(err => {
        console.error('Chat error:', err);
        showTyping(false);
        renderMessage('bot', "I'm currently having trouble connecting to the support server. Please reach us directly at **+91 6292252470** or **stafo.sales@stafo.in**.", [
          { label: 'Call Support (+91 6292252470)', url: 'tel:+916292252470', type: 'link' }
        ], true);
      });
    }

    // Global Pill Click Handler
    window.sendNishaPill = function(actionText) {
      inputField.value = actionText;
      sendUserMessage();
    };

    function showTyping(show) {
      if (show) {
        typingIndicator.style.display = 'flex';
        scrollToBottom();
      } else {
        typingIndicator.style.display = 'none';
      }
    }

    function scrollToBottom() {
      setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      }, 50);
    }

    // Format Markdown-like text (bold, links, linebreaks)
    function formatMessageText(text) {
      let formatted = text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

      // Bold **text**
      formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

      // Markdown links [Text](URL)
      formatted = formatted.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>');

      // New lines
      formatted = formatted.replace(/\n/g, '<br>');

      return formatted;
    }

    function renderMessage(sender, text, quickActions = [], persist = true, attachmentUrl = null) {
      const msgRow = document.createElement('div');
      msgRow.className = `nisha-msg-row ${sender}`;

      if (sender === 'bot') {
        const avatar = document.createElement('img');
        avatar.className = 'nisha-msg-avatar';
        avatar.src = avatarUrl;
        avatar.alt = 'STAFO';
        msgRow.appendChild(avatar);
      }

      const bubblesContainer = document.createElement('div');
      bubblesContainer.className = 'nisha-msg-bubbles';

      const bubble = document.createElement('div');
      bubble.className = 'nisha-bubble';
      bubble.innerHTML = formatMessageText(text);

      if (attachmentUrl) {
        const img = document.createElement('img');
        img.className = 'nisha-attachment-preview';
        img.src = attachmentUrl;
        img.alt = 'Attachment';
        img.onclick = () => window.open(attachmentUrl, '_blank');
        bubble.appendChild(document.createElement('br'));
        bubble.appendChild(img);
      }

      bubblesContainer.appendChild(bubble);

      // Render Quick Actions Pills if present
      if (quickActions && quickActions.length > 0) {
        const pillsRow = document.createElement('div');
        pillsRow.className = 'nisha-pills-row';

        const extSvg = '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; display: inline-block; vertical-align: middle;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>';

        quickActions.forEach(action => {
          if (action.type === 'link' || action.url) {
            const link = document.createElement('a');
            link.className = 'nisha-pill-btn pill-link';
            link.href = action.url;
            link.target = action.url.startsWith('tel:') || action.url.startsWith('mailto:') ? '_self' : '_blank';
            link.innerHTML = `${extSvg}<span>${action.label}</span>`;
            pillsRow.appendChild(link);
          } else {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'nisha-pill-btn';
            btn.textContent = action.label;
            btn.onclick = () => {
              window.sendNishaPill(action.action || action.label);
            };
            pillsRow.appendChild(btn);
          }
        });

        bubblesContainer.appendChild(pillsRow);
      }

      msgRow.appendChild(bubblesContainer);

      // Insert before typing indicator
      messagesContainer.insertBefore(msgRow, typingIndicator);

      if (persist) {
        saveMessageToStorage(sender, text, quickActions, attachmentUrl);
      }

      scrollToBottom();
    }

    // Initialize
    loadSavedChat();
  })();
</script>
