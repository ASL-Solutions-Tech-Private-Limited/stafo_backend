@extends('user.layouts.app')

@section('title', 'Add Company Holiday | STAFO HRMS')

@section('content')
@include('user.layouts.alert')

<style>
    /* ========================================================
       Add Holiday Form - STAFO Modern Design
       ======================================================== */
    .holiday-form-hero {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 45%, #0284c7 100%);
        border-radius: 20px;
        padding: 1.5rem 2rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.35);
        margin-bottom: 1.5rem;
    }
    .holiday-form-hero::after {
        content: '';
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .form-card-container {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .form-section-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .holiday-input-group .input-group-text {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
        padding-left: 14px;
        padding-right: 14px;
    }

    .holiday-input-group .form-control {
        border-color: #e2e8f0;
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
        padding: 10px 14px;
        font-size: 0.92rem;
        transition: all 0.2s ease;
    }

    .holiday-input-group .form-control:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    /* Quick Suggestion Pills */
    .holiday-suggestion-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
    }
    .holiday-suggestion-chip:hover {
        background: #d1fae5;
        border-color: #a7f3d0;
        color: #065f46;
        transform: translateY(-1px);
    }

    /* Live Preview Card */
    .holiday-preview-box {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem;
    }

    /* Dark Mode Form Overrides */
    [data-theme="dark"] .holiday-form-hero {
        background: linear-gradient(135deg, #022c22 0%, #064e3b 45%, #0c4a6e 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5) !important;
    }

    [data-theme="dark"] .form-card-container {
        background: #111c30 !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .form-section-header {
        background: #132038 !important;
        border-bottom-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-theme="dark"] .holiday-input-group .input-group-text {
        background-color: #0f172a !important;
        border-color: #24344d !important;
        color: #94a3b8 !important;
    }
    [data-theme="dark"] .holiday-input-group .form-control {
        background-color: #0d1527 !important;
        border-color: #24344d !important;
        color: #f8fafc !important;
    }
    [data-theme="dark"] .holiday-input-group .form-control:focus {
        border-color: #10b981 !important;
        background-color: #111c30 !important;
    }
    [data-theme="dark"] .holiday-suggestion-chip {
        background: #16243f !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
        color: #cbd5e1 !important;
    }
    [data-theme="dark"] .holiday-suggestion-chip:hover {
        background: rgba(16, 185, 129, 0.22) !important;
        border-color: rgba(16, 185, 129, 0.4) !important;
        color: #6ee7b7 !important;
    }
    [data-theme="dark"] .holiday-preview-box {
        background: linear-gradient(135deg, #132038 0%, #0f172a 100%) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
</style>

<div class="container-fluid p-0">

    <!-- Top Banner -->
    <div class="holiday-form-hero">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: rgba(255,255,255,0.2); color: #ffffff; font-size: 0.74rem;">
                        <i class="fa-solid fa-umbrella-beach me-1 text-warning"></i> Company Holiday Manager
                    </span>
                    <span class="text-white-50 small">• Calendar Year {{ date('Y') }}</span>
                </div>
                <h3 class="fw-bold text-white mb-1">Add Company Holiday</h3>
                <p class="text-white small mb-0 opacity-90">
                    Declare a new national, regional, or corporate calendar holiday for your organization.
                </p>
            </div>
            <a href="{{ route('holiday.index') }}" class="btn btn-outline-light btn-sm rounded-3 py-2 px-3 fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Calendar
            </a>
        </div>
    </div>

    <!-- Form & Live Preview Row -->
    <div class="row g-4 mb-4">
        <!-- Left Column: Form Fields -->
        <div class="col-lg-8">
            <div class="form-card-container">
                <div class="form-section-header">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-calendar-plus text-primary"></i>
                        <span>Holiday Details & Dates</span>
                    </h6>
                    <span class="text-muted small">All marked (*) fields are required</span>
                </div>

                <div class="p-4">
                    <form action="{{ route('holiday.store') }}" method="POST" id="holidayForm">
                        @csrf

                        <!-- 1. Holiday Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold text-dark small mb-1.5">
                                Holiday Title <span class="text-danger">*</span>
                            </label>
                            <div class="input-group holiday-input-group">
                                <span class="input-group-text"><i id="titleInputIcon" class="fa-solid fa-champagne-glasses text-primary"></i></span>
                                <input type="text" name="title" id="title" class="form-control form-control-lg" 
                                       value="{{ old('title') }}" placeholder="e.g. New Year's Day, Diwali, Independence Day, Christmas, Eid" 
                                       required autofocus oninput="updateLivePreview()">
                            </div>
                            @error('title')
                                <div class="text-danger small mt-1"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                            @enderror

                            <!-- Popular Festive Holiday Quick Chips -->
                            <div class="mt-2.5">
                                <small class="text-muted fw-semibold d-block mb-1.5" style="font-size: 0.72rem; letter-spacing: 0.3px; text-transform: uppercase;">
                                    <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> Quick Festival Suggestions (Click to autofill):
                                </small>
                                <div class="d-flex flex-wrap gap-1.5">
                                    <span class="holiday-suggestion-chip" onclick="fillTitle('New Year\'s Day', 'New Year Celebration - Welcome to the new calendar year!')">🎉 New Year's Day</span>
                                    <span class="holiday-suggestion-chip" onclick="fillTitle('Republic Day', 'National Holiday - Republic Day of India')">🇮🇳 Republic Day</span>
                                    <span class="holiday-suggestion-chip" onclick="fillTitle('Holi', 'Festival of Colors - Official Company Holiday')">🎨 Holi</span>
                                    <span class="holiday-suggestion-chip" onclick="fillTitle('Eid-ul-Fitr', 'Religious Holiday - Eid Observance')">🌙 Eid-ul-Fitr</span>
                                    <span class="holiday-suggestion-chip" onclick="fillTitle('Independence Day', 'National Holiday - Independence Day of India')">🇮🇳 Independence Day</span>
                                    <span class="holiday-suggestion-chip" onclick="fillTitle('Diwali', 'Festival of Lights - Official Company Holiday')">🪔 Diwali</span>
                                    <span class="holiday-suggestion-chip" onclick="fillTitle('Christmas', 'Christmas Celebration - Official Holiday')">🎄 Christmas</span>
                                    <span class="holiday-suggestion-chip" onclick="fillTitle('Gandhi Jayanti', 'National Observance - Mahatma Gandhi Birthday')">🕊️ Gandhi Jayanti</span>
                                    <span class="holiday-suggestion-chip" onclick="fillTitle('Raksha Bandhan', 'Festive Holiday - Rakhi Observance')">🎁 Raksha Bandhan</span>
                                    <span class="holiday-suggestion-chip" onclick="fillTitle('Maha Dashmi', 'Vijayadashami / Dussehra - Festive Observance')">🏹 Maha Dashmi</span>
                                    <span class="holiday-suggestion-chip" onclick="fillTitle('Dussehra', 'Festive Observance - Vijayadashami')">🏹 Dussehra</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Start Date & End Date -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-bold text-dark small mb-1.5">
                                    Start Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group holiday-input-group">
                                    <span class="input-group-text"><i class="fa-regular fa-calendar text-primary"></i></span>
                                    <input type="date" name="start_date" id="start_date" class="form-control" 
                                           value="{{ old('start_date', date('Y-m-d')) }}" required onchange="onStartDateChange()">
                                </div>
                                @error('start_date')
                                    <div class="text-danger small mt-1"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-bold text-dark small mb-1.5">
                                    End Date <span class="text-danger">*</span>
                                </label>
                                <div class="input-group holiday-input-group">
                                    <span class="input-group-text"><i class="fa-regular fa-calendar-check text-info"></i></span>
                                    <input type="date" name="end_date" id="end_date" class="form-control" 
                                           value="{{ old('end_date', date('Y-m-d')) }}" required onchange="updateLivePreview()">
                                </div>
                                @error('end_date')
                                    <div class="text-danger small mt-1"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- 3. Description & Observance Notes -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold text-dark small mb-1.5">
                                Description & Observance Notes <span class="text-muted fw-normal">(Optional)</span>
                            </label>
                            <div class="input-group holiday-input-group">
                                <span class="input-group-text align-items-start pt-2"><i class="fa-solid fa-align-left text-muted"></i></span>
                                <textarea name="description" id="description" class="form-control" rows="3" 
                                          placeholder="Enter any guidance, office closure details, or festive notes for employees..." 
                                          oninput="updateLivePreview()">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Form Submission Actions -->
                        <div class="d-flex align-items-center justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('holiday.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 fw-semibold">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Save Holiday</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Live Interactive Card Preview -->
        <div class="col-lg-4">
            <div class="form-card-container mb-4">
                <div class="form-section-header">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-eye text-info"></i>
                        <span>Live Preview</span>
                    </h6>
                    <span class="badge bg-success bg-opacity-10 text-success small">Real-time</span>
                </div>

                <div class="p-3">
                    <small class="text-muted d-block mb-3" style="font-size: 0.75rem;">
                        This is how the holiday card and calendar chip will appear on both employee and company panels:
                    </small>

                    <!-- Preview Card -->
                    <div class="holiday-preview-box">
                        <div class="d-flex align-items-center gap-2.5 mb-2.5">
                            <div id="prevIconBox" class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm fs-4" 
                                 style="width: 48px; height: 48px; flex-shrink: 0; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                                <i id="prevIcon" class="fa-solid fa-champagne-glasses"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                    <span id="prevEmoji">🎉</span>
                                    <span id="prevTitle">New Year's Day</span>
                                </h6>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    <span class="badge py-0.5 px-1.5" id="prevCategoryBadge" style="background: rgba(139, 92, 246, 0.15); color: #7c3aed; font-size: 0.65rem;">New Year Celebration</span>
                                    <span class="badge bg-success text-success py-0.5 px-1.5" id="prevDurationBadge" style="font-size: 0.65rem;">1 Day Holiday</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-3 bg-white border mb-2.5" style="border-color: rgba(226, 232, 240, 0.8) !important;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Start Date:</small>
                                <strong class="text-primary small" id="prevStartDate">--</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">End Date:</small>
                                <strong class="text-dark small" id="prevEndDate">--</strong>
                            </div>
                        </div>

                        <div>
                            <small class="text-muted d-block fw-semibold" style="font-size: 0.72rem;">Notes / Details:</small>
                            <p class="text-muted small mb-0 lh-sm" id="prevDesc" style="font-size: 0.8rem;">
                                Official company holiday announced by management.
                            </p>
                        </div>
                    </div>

                    <!-- Calendar Chip Preview -->
                    <div class="mt-3 p-3 rounded-3 bg-light border">
                        <small class="text-muted d-block fw-semibold mb-1.5" style="font-size: 0.72rem; text-transform: uppercase;">
                            Calendar Chip Appearance:
                        </small>
                        <div class="holiday-chip d-inline-flex" id="prevChip" style="font-size: 0.78rem; padding: 5px 10px;">
                            <i id="prevChipIcon" class="fa-solid fa-champagne-glasses me-1"></i> <span id="prevChipText">New Year's Day</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function getFestivalIconData(title) {
        const t = (title || '').toLowerCase();
        if (t.includes('new year')) {
            return { icon: 'fa-solid fa-champagne-glasses', emoji: '🎉', color: '#8b5cf6', bg: 'linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)', category: 'New Year Celebration' };
        }
        if (t.includes('republic') || t.includes('gantantra')) {
            return { icon: 'fa-solid fa-flag', emoji: '🇮🇳', color: '#ea580c', bg: 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)', category: 'National Holiday' };
        }
        if (t.includes('independence') || t.includes('swatantrata')) {
            return { icon: 'fa-solid fa-flag', emoji: '🇮🇳', color: '#ea580c', bg: 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)', category: 'National Holiday' };
        }
        if (t.includes('diwali') || t.includes('deepavali') || t.includes('deepawali')) {
            return { icon: 'fa-solid fa-fire-flame-curved', emoji: '🪔', color: '#d97706', bg: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', category: 'Festival of Lights' };
        }
        if (t.includes('holi') || t.includes('dhuleti') || t.includes('rang')) {
            return { icon: 'fa-solid fa-palette', emoji: '🎨', color: '#db2777', bg: 'linear-gradient(135deg, #f43f5e 0%, #db2777 100%)', category: 'Festival of Colors' };
        }
        if (t.includes('christmas') || t.includes('xmas') || t.includes('x-mas')) {
            return { icon: 'fa-solid fa-tree', emoji: '🎄', color: '#059669', bg: 'linear-gradient(135deg, #10b981 0%, #047857 100%)', category: 'Christmas Celebration' };
        }
        if (t.includes('eid') || t.includes('ramadan') || t.includes('ramzan') || t.includes('bakrid') || t.includes('muharram')) {
            return { icon: 'fa-solid fa-moon', emoji: '🌙', color: '#0d9488', bg: 'linear-gradient(135deg, #14b8a6 0%, #0f766e 100%)', category: 'Islamic Observance' };
        }
        if (t.includes('gandhi')) {
            return { icon: 'fa-solid fa-dove', emoji: '🕊️', color: '#475569', bg: 'linear-gradient(135deg, #64748b 0%, #475569 100%)', category: 'National Observance' };
        }
        if (t.includes('rakhi') || t.includes('raksha') || t.includes('bhai dooj') || t.includes('bhai tika')) {
            return { icon: 'fa-solid fa-gift', emoji: '🎁', color: '#e11d48', bg: 'linear-gradient(135deg, #f43f5e 0%, #be123c 100%)', category: 'Festive Celebration' };
        }
        if (t.includes('dussehra') || t.includes('vijayadashami') || t.includes('dashahara')) {
            return { icon: 'fa-solid fa-shield-halved', emoji: '🏹', color: '#b45309', bg: 'linear-gradient(135deg, #d97706 0%, #92400e 100%)', category: 'Festive Observance' };
        }
        if (t.includes('janmashtami') || t.includes('krishna')) {
            return { icon: 'fa-solid fa-om', emoji: '🦚', color: '#2563eb', bg: 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)', category: 'Festive Observance' };
        }
        if (t.includes('shivratri') || t.includes('shiva')) {
            return { icon: 'fa-solid fa-om', emoji: '🔱', color: '#0284c7', bg: 'linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%)', category: 'Spiritual Observance' };
        }
        if (t.includes('ganesh') || t.includes('vinayaka')) {
            return { icon: 'fa-solid fa-om', emoji: '🐘', color: '#ea580c', bg: 'linear-gradient(135deg, #f97316 0%, #c2410c 100%)', category: 'Festive Observance' };
        }
        if (t.includes('durga') || t.includes('navratri') || t.includes('navaratri') || t.includes('puja') || t.includes('saptami') || t.includes('astami') || t.includes('ashtami') || t.includes('navami') || t.includes('dashami')) {
            return { icon: 'fa-solid fa-bell', emoji: '🌺', color: '#dc2626', bg: 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)', category: 'Festive Observance' };
        }
        if (t.includes('good friday') || t.includes('easter')) {
            return { icon: 'fa-solid fa-cross', emoji: '✝️', color: '#6366f1', bg: 'linear-gradient(135deg, #818cf8 0%, #4f46e5 100%)', category: 'Christian Observance' };
        }
        if (t.includes('buddha') || t.includes('guru') || t.includes('gurpurab') || t.includes('baisakhi') || t.includes('vaisakhi') || t.includes('mahavir')) {
            return { icon: 'fa-solid fa-dharmachakra', emoji: '☸️', color: '#ea580c', bg: 'linear-gradient(135deg, #f97316 0%, #ea580c 100%)', category: 'Spiritual Observance' };
        }
        if (t.includes('sankranti') || t.includes('pongal') || t.includes('lohri') || t.includes('bihu') || t.includes('chhath') || t.includes('chhat')) {
            return { icon: 'fa-solid fa-sun', emoji: '🪁', color: '#f59e0b', bg: 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)', category: 'Harvest Festival' };
        }
        if (t.includes('labour') || t.includes('may day') || t.includes('worker')) {
            return { icon: 'fa-solid fa-hammer', emoji: '⚒️', color: '#0284c7', bg: 'linear-gradient(135deg, #38bdf8 0%, #0284c7 100%)', category: 'International Observance' };
        }
        if (t.includes('women')) {
            return { icon: 'fa-solid fa-venus', emoji: '👩', color: '#db2777', bg: 'linear-gradient(135deg, #f472b6 0%, #db2777 100%)', category: 'Special Observance' };
        }
        if (t.includes('anniversary') || t.includes('birthday') || t.includes('foundation')) {
            return { icon: 'fa-solid fa-cake-candles', emoji: '🎂', color: '#ec4899', bg: 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)', category: 'Corporate Observance' };
        }
        return { icon: 'fa-solid fa-umbrella-beach', emoji: '🏖️', color: '#059669', bg: 'linear-gradient(135deg, #10b981 0%, #059669 100%)', category: 'Company Holiday' };
    }

    function fillTitle(title, desc) {
        document.getElementById('title').value = title;
        if (desc) {
            document.getElementById('description').value = desc;
        }
        updateLivePreview();
    }

    function onStartDateChange() {
        const sDate = document.getElementById('start_date').value;
        const eInput = document.getElementById('end_date');
        // Auto set end date to start date if empty or before start date
        if (sDate && (!eInput.value || eInput.value < sDate)) {
            eInput.value = sDate;
        }
        updateLivePreview();
    }

    function updateLivePreview() {
        const title = document.getElementById('title').value || 'Holiday Title';
        const desc = document.getElementById('description').value || 'Official company holiday announced by management.';
        const sDateStr = document.getElementById('start_date').value;
        const eDateStr = document.getElementById('end_date').value;

        const fest = getFestivalIconData(title);

        // Update title input group icon
        const titleInputIcon = document.getElementById('titleInputIcon');
        if (titleInputIcon) {
            titleInputIcon.className = fest.icon + ' text-primary';
        }

        // Update preview card
        document.getElementById('prevTitle').textContent = title;
        const prevEmoji = document.getElementById('prevEmoji');
        if (prevEmoji) prevEmoji.textContent = fest.emoji || '';

        const prevIconBox = document.getElementById('prevIconBox');
        const prevIcon = document.getElementById('prevIcon');
        if (prevIconBox && fest.bg) prevIconBox.style.background = fest.bg;
        if (prevIcon && fest.icon) prevIcon.className = fest.icon;

        const prevCat = document.getElementById('prevCategoryBadge');
        if (prevCat) {
            prevCat.textContent = fest.category;
            prevCat.style.background = fest.color ? `${fest.color}22` : 'rgba(16, 185, 129, 0.15)';
            prevCat.style.color = fest.color || '#065f46';
        }

        // Update preview chip
        document.getElementById('prevChipText').textContent = (fest.emoji ? fest.emoji + ' ' : '') + title;
        const prevChipIcon = document.getElementById('prevChipIcon');
        if (prevChipIcon && fest.icon) {
            prevChipIcon.className = fest.icon + ' me-1';
        }

        document.getElementById('prevDesc').textContent = desc;

        if (sDateStr) {
            const sDate = new Date(sDateStr + 'T00:00:00');
            const options = { year: 'numeric', month: 'short', day: 'numeric', weekday: 'short' };
            document.getElementById('prevStartDate').textContent = sDate.toLocaleDateString('en-US', options);

            let duration = 1;
            if (eDateStr) {
                const eDate = new Date(eDateStr + 'T00:00:00');
                document.getElementById('prevEndDate').textContent = eDate.toLocaleDateString('en-US', options);

                const diffTime = eDate - sDate;
                duration = Math.max(1, Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1);
            } else {
                document.getElementById('prevEndDate').textContent = sDate.toLocaleDateString('en-US', options);
            }

            document.getElementById('prevDurationBadge').textContent = `${duration} ${duration === 1 ? 'Day' : 'Days'} Holiday`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateLivePreview();
    });
</script>
@endsection