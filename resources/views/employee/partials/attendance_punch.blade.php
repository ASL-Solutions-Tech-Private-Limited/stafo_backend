{{-- Web Attendance Punch Card & Modals Component --}}
@php
    $currentEmp = $employee_info ?? auth()->guard('employee')->user();
    $rawAttType = strtolower(trim($currentEmp->attendance_type ?? 'geo'));
    if ($rawAttType === 'qr') {
        $rawAttType = 'qr code';
    }
    $empAttType = in_array($rawAttType, ['geo', 'selfie', 'qr code']) ? $rawAttType : 'geo';

    $modeConfig = [
        'geo' => [
            'name' => 'Geo Location',
            'short' => 'Geo',
            'icon' => 'fa-solid fa-location-dot',
            'badge' => 'bg-success text-success border-success',
            'punch_in_label' => 'Punch IN (Geo)',
            'punch_out_label' => 'Punch OUT (Geo)',
            'in_gradient' => 'background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;',
            'onclick_in' => "executeWebPunch('punch_in')",
            'onclick_out' => "executeWebPunch('punch_out')",
        ],
        'selfie' => [
            'name' => 'Selfie / Camera',
            'short' => 'Selfie',
            'icon' => 'fa-solid fa-camera',
            'badge' => 'bg-primary text-primary border-primary',
            'punch_in_label' => 'Selfie Punch IN',
            'punch_out_label' => 'Selfie Punch OUT',
            'in_gradient' => 'background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); border: none;',
            'onclick_in' => "openSelfieModal()",
            'onclick_out' => "openSelfieModal()",
        ],
        'qr code' => [
            'name' => 'Company QR Code',
            'short' => 'QR Code',
            'icon' => 'fa-solid fa-qrcode',
            'badge' => 'bg-warning text-dark border-warning',
            'punch_in_label' => 'Scan QR & Punch IN',
            'punch_out_label' => 'Scan QR & Punch OUT',
            'in_gradient' => 'background: linear-gradient(135deg, #1f2937 0%, #111827 100%); border: none;',
            'onclick_in' => "openQrModal()",
            'onclick_out' => "openQrModal()",
        ],
    ];
    $activeMode = $modeConfig[$empAttType];
@endphp

<div class="card shadow-sm mb-4 overflow-hidden emp-punch-terminal-card" style="border: 1px solid #e2e8f0; border-radius: 16px;">
    <div class="card-body p-3 p-md-4">
        <div class="row align-items-center g-4">
            <!-- Left: Real-time Live Clock & Date -->
            <div class="col-lg-5 border-lg-end">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="emp-terminal-badge">
                        <i class="fa-solid fa-satellite-dish me-1 fa-fade"></i> Web Attendance Terminal
                    </span>
                    <span class="badge {{ $activeMode['badge'] }} bg-opacity-10 border border-opacity-25 px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                        <i class="{{ $activeMode['icon'] }} me-1"></i> Authorized Mode: <strong>{{ $activeMode['name'] }}</strong>
                    </span>
                    <span class="text-muted small">• {{ date('l, d M Y') }}</span>
                </div>

                <!-- Digital Clock Display -->
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <h2 class="display-5 fw-bold text-dark font-monospace mb-0" id="livePunchClock" style="letter-spacing: -1px;">
                        --:--:--
                    </h2>
                    <span class="badge bg-light text-muted border text-uppercase" id="livePunchAmPm" style="font-size: 0.8rem;">AM</span>
                </div>

                <!-- Shift & Branch Info -->
                <div class="d-flex flex-wrap align-items-center gap-3 text-muted small mt-2">
                    @if(isset($employee_info->shift) && $employee_info->shift)
                        <div>
                            <i class="fa-solid fa-business-time text-success me-1"></i>
                            Shift: <strong class="text-dark">{{ $employee_info->shift->shift_name ?? 'Standard' }}</strong> 
                            ({{ $employee_info->shift->start_time ?? '10:00' }} - {{ $employee_info->shift->end_time ?? '19:00' }})
                        </div>
                    @endif
                    @if(isset($employee_info->branch) && $employee_info->branch)
                        <div>
                            <i class="fa-solid fa-location-dot text-danger me-1"></i>
                            Branch: <strong class="text-dark">{{ $employee_info->branch->branch_name }}</strong>
                            @if(!empty($employee_info->branch->radar))
                                <span class="badge bg-light text-muted border ms-1" style="font-size: 0.65rem;">Radius: {{ $employee_info->branch->radar }}m</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Middle: Current Punch Status Indicator -->
            <div class="col-lg-3 col-md-6 text-center text-lg-start">
                <span class="text-muted small text-uppercase fw-bold d-block mb-1" style="letter-spacing: 0.5px;">Today's Activity</span>
                @if(isset($isPunchedIn) && $isPunchedIn)
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 punch-pill-success">
                        <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
                        <div class="text-start lh-sm">
                            <strong class="d-block" style="font-size: 0.9rem;">Currently Punched IN</strong>
                            <small class="opacity-75" style="font-size: 0.75rem;">
                                In at {{ isset($activePunch) && $activePunch->punch_in ? Carbon\Carbon::parse($activePunch->punch_in)->format('h:i A') : 'Today' }}
                            </small>
                        </div>
                    </div>
                @else
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 emp-not-punched-box">
                        <i class="fa-solid fa-circle-pause fs-5 text-muted"></i>
                        <div class="text-start lh-sm">
                            <strong class="d-block text-dark" style="font-size: 0.9rem;">Not Punched In</strong>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                @if(isset($lastPunch) && $lastPunch && $lastPunch->punch_out)
                                    Last out at {{ Carbon\Carbon::parse($lastPunch->punch_out)->format('h:i A') }}
                                @else
                                    Ready to record attendance
                                @endif
                            </small>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right: Action Buttons (Location, Selfie, QR) -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex flex-column gap-2">
                    <!-- Main Smart Action Button dynamically mapped to authorized mode -->
                    @if(isset($isPunchedIn) && $isPunchedIn)
                        <button type="button" class="btn btn-danger btn-lg fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 py-2.5"
                                onclick="{{ $activeMode['onclick_out'] }}" id="btnMainPunch">
                            <i class="{{ $activeMode['icon'] }} fs-5"></i>
                            <span>{{ $activeMode['punch_out_label'] }}</span>
                        </button>
                    @else
                        <button type="button" class="btn btn-lg fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2 py-2.5 text-white"
                                style="{{ $activeMode['in_gradient'] }}"
                                onclick="{{ $activeMode['onclick_in'] }}" id="btnMainPunch">
                            <i class="{{ $activeMode['icon'] }} fs-4"></i>
                            <span>{{ $activeMode['punch_in_label'] }}</span>
                        </button>
                    @endif

                    <!-- Alternate Punch Modes Strip with Visual Lock/Check indicators -->
                    <div class="d-flex align-items-center gap-1.5 pt-1">
                        <!-- Geo Option -->
                        <button type="button" 
                                class="btn btn-sm rounded-3 flex-fill py-1.5 px-1 fw-semibold {{ $empAttType === 'geo' ? 'btn-success text-white' : 'btn-outline-secondary opacity-75' }}" 
                                onclick="executeWebPunch('{{ (isset($isPunchedIn) && $isPunchedIn) ? 'punch_out' : 'punch_in' }}')" 
                                title="{{ $empAttType === 'geo' ? 'Your assigned attendance mode' : 'Assigned attendance mode: ' . $activeMode['name'] }}"
                                style="font-size: 0.78rem;">
                            <i class="fa-solid fa-location-dot me-1"></i> Geo
                            @if($empAttType === 'geo')
                                <i class="fa-solid fa-check ms-0.5" style="font-size: 0.65rem;"></i>
                            @else
                                <i class="fa-solid fa-lock ms-0.5 text-muted" style="font-size: 0.65rem;"></i>
                            @endif
                        </button>

                        <!-- Selfie Option -->
                        <button type="button" 
                                class="btn btn-sm rounded-3 flex-fill py-1.5 px-1 fw-semibold {{ $empAttType === 'selfie' ? 'btn-primary text-white' : 'btn-outline-secondary opacity-75' }}" 
                                onclick="openSelfieModal()" 
                                title="{{ $empAttType === 'selfie' ? 'Your assigned attendance mode' : 'Assigned attendance mode: ' . $activeMode['name'] }}"
                                style="font-size: 0.78rem;">
                            <i class="fa-solid fa-camera me-1"></i> Selfie
                            @if($empAttType === 'selfie')
                                <i class="fa-solid fa-check ms-0.5" style="font-size: 0.65rem;"></i>
                            @else
                                <i class="fa-solid fa-lock ms-0.5 text-muted" style="font-size: 0.65rem;"></i>
                            @endif
                        </button>

                        <!-- QR Code Option -->
                        <button type="button" 
                                class="btn btn-sm rounded-3 flex-fill py-1.5 px-1 fw-semibold {{ $empAttType === 'qr code' ? 'btn-warning text-dark' : 'btn-outline-secondary opacity-75' }}" 
                                onclick="openQrModal()" 
                                title="{{ $empAttType === 'qr code' ? 'Your assigned attendance mode' : 'Assigned attendance mode: ' . $activeMode['name'] }}"
                                style="font-size: 0.78rem;">
                            <i class="fa-solid fa-qrcode me-1"></i> QR Code
                            @if($empAttType === 'qr code')
                                <i class="fa-solid fa-check ms-0.5" style="font-size: 0.65rem;"></i>
                            @else
                                <i class="fa-solid fa-lock ms-0.5 text-muted" style="font-size: 0.65rem;"></i>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Punch Timeline Strip -->
        @if(isset($todayPunches) && $todayPunches->count() > 0)
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        Today's Recorded Punches ({{ $todayPunches->count() }})
                    </small>
                    <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.7rem;">
                        Date: {{ date('d M Y') }}
                    </span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($todayPunches as $idx => $p)
                        <div class="px-3 py-1.5 rounded-3 border d-flex align-items-center gap-2 small bg-white shadow-xs" style="font-size: 0.8rem;">
                            <span class="badge bg-primary text-white rounded-circle" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.65rem;">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <span class="text-success fw-semibold">
                                    <i class="fa-solid fa-arrow-right-to-bracket me-0.5"></i> 
                                    {{ $p->punch_in ? Carbon\Carbon::parse($p->punch_in)->format('h:i:s A') : '--' }}
                                </span>
                                <span class="text-muted mx-1">→</span>
                                <span class="{{ $p->punch_out ? 'text-danger fw-semibold' : 'badge bg-warning text-warning' }}">
                                    @if($p->punch_out)
                                        <i class="fa-solid fa-arrow-right-from-bracket me-0.5"></i>
                                        {{ Carbon\Carbon::parse($p->punch_out)->format('h:i:s A') }}
                                    @else
                                        Active
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

{{-- 1. Selfie Attendance Modal --}}
<div class="modal fade" id="selfieAttendanceModal" tabindex="-1" aria-labelledby="selfieModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title fw-bold d-flex align-items-center gap-2" id="selfieModalLabel">
                    <i class="fa-solid fa-camera text-info"></i> Web Selfie Attendance
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="position-relative mx-auto rounded-4 overflow-hidden shadow-sm bg-black mb-3" 
                     style="width: 100%; max-width: 360px; height: 270px; border: 2px solid #10b981;">
                    <video id="selfieVideo" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                    <canvas id="selfieCanvas" class="d-none"></canvas>
                    <img id="selfiePreview" class="d-none position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;" />
                    
                    <div id="cameraLoadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white bg-black bg-opacity-75">
                        <div class="spinner-border text-primary mb-2" role="status"></div>
                        <small>Initializing Camera...</small>
                    </div>
                </div>

                <p class="text-muted small mb-3">
                    Position your face clearly in the camera frame to record your attendance.
                </p>

                <div class="d-flex align-items-center justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-3 py-2" id="btnRetakeSelfie" style="display: none;" onclick="retakeSelfie()">
                        <i class="fa-solid fa-rotate-left me-1"></i> Retake
                    </button>
                    <button type="button" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold" id="btnCaptureSelfie" onclick="captureSelfieAndSubmit()">
                        <i class="fa-solid fa-camera me-1"></i> Capture & Punch
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. QR Code Attendance Modal --}}
<div class="modal fade" id="qrAttendanceModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title fw-bold d-flex align-items-center gap-2" id="qrModalLabel">
                    <i class="fa-solid fa-qrcode text-warning"></i> Company QR Code Attendance
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 d-inline-flex fs-2 mb-2">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Verify Company QR</h6>
                    <span class="text-muted small">Scan or enter the official company attendance QR payload to punch in/out.</span>
                </div>

                <form id="qrAttendanceForm" onsubmit="submitQrAttendance(event)">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Company QR Code / Token</label>
                        <input type="text" class="form-control form-control-lg rounded-3 text-center font-monospace" 
                               id="qrCodeInput" placeholder="Scan or paste QR payload here" required autofocus>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-3 py-2.5 fw-bold" id="btnSubmitQr">
                            <i class="fa-solid fa-circle-check me-1"></i> Validate & Punch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Attendance Scripts --}}
<script>
    // 1. Live Digital Clock
    function updatePunchClock() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? String(hours).padStart(2, '0') : '12';

        const clockEl = document.getElementById('livePunchClock');
        const ampmEl = document.getElementById('livePunchAmPm');
        if (clockEl) clockEl.textContent = `${hours}:${minutes}:${seconds}`;
        if (ampmEl) ampmEl.textContent = ampm;
    }
    setInterval(updatePunchClock, 1000);
    updatePunchClock();

    const empAssignedType = "{{ $empAttType }}";
    const empAssignedName = "{{ $activeMode['name'] }}";

    // 2. Geolocation / Location Punch
    function executeWebPunch(actionType) {
        if (empAssignedType !== 'geo') {
            Swal.fire({
                icon: 'warning',
                title: 'Attendance Mode Restricted',
                text: `Your company administrator has configured your attendance mode as "${empAssignedName}". Please punch using ${empAssignedName}.`,
                showCancelButton: true,
                confirmButtonText: empAssignedType === 'selfie' ? '<i class="fa-solid fa-camera me-1"></i> Open Selfie Camera' : '<i class="fa-solid fa-qrcode me-1"></i> Open QR Scanner',
                cancelButtonText: 'Dismiss',
                confirmButtonColor: '#0d6efd'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (empAssignedType === 'selfie') openSelfieModal();
                    else if (empAssignedType === 'qr code') openQrModal();
                }
            });
            return;
        }

        Swal.fire({
            title: actionType === 'punch_in' ? 'Recording Punch IN...' : 'Recording Punch OUT...',
            text: 'Fetching location coordinates...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Request browser geolocation with high precision
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    // Format coordinates with 7 decimal precision matching database format (e.g. 22.4993589)
                    const lat = Number(position.coords.latitude).toFixed(7);
                    const lng = Number(position.coords.longitude).toFixed(7);
                    sendPunchRequest(lat, lng);
                },
                function (error) {
                    console.warn('Geolocation error:', error);
                    // Send request with null/empty location and let server check if geofence is mandatory
                    sendPunchRequest(null, null);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            sendPunchRequest(null, null);
        }
    }

    function sendPunchRequest(lat, lng) {
        $.ajax({
            url: "{{ route('employee.punch.submit') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                latitude: lat,
                longitude: lng
            },
            success: function (res) {
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: res.type === 'punch_in' ? 'Punched IN Successfully! 🎯' : 'Punched OUT Successfully! 🏁',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Punch Failed',
                        text: res.message || 'Unable to record attendance.'
                    });
                }
            },
            error: function (xhr) {
                let msg = 'An unexpected error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Punch Error',
                    text: msg
                });
            }
        });
    }

    // 3. Webcam Live Selfie Punch
    let selfieStream = null;

    function openSelfieModal() {
        if (empAssignedType !== 'selfie') {
            Swal.fire({
                icon: 'warning',
                title: 'Attendance Mode Restricted',
                text: `Your company administrator has configured your attendance mode as "${empAssignedName}". Selfie punch is not allowed for your profile.`,
                confirmButtonText: 'Understood',
                confirmButtonColor: '#6c757d'
            });
            return;
        }

        const modal = new bootstrap.Modal(document.getElementById('selfieAttendanceModal'));
        modal.show();

        const video = document.getElementById('selfieVideo');
        const overlay = document.getElementById('cameraLoadingOverlay');
        const preview = document.getElementById('selfiePreview');
        const btnRetake = document.getElementById('btnRetakeSelfie');
        const btnCapture = document.getElementById('btnCaptureSelfie');

        preview.classList.add('d-none');
        video.classList.remove('d-none');
        btnRetake.style.display = 'none';
        btnCapture.textContent = 'Capture & Punch';
        overlay.classList.remove('d-none');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(function (stream) {
                    selfieStream = stream;
                    video.srcObject = stream;
                    video.play();
                    overlay.classList.add('d-none');
                })
                .catch(function (err) {
                    console.error('Camera access denied:', err);
                    overlay.innerHTML = `<span class="text-danger p-2"><i class="fa-solid fa-triangle-exclamation"></i> Camera permission denied or not available.</span>`;
                });
        } else {
            overlay.innerHTML = `<span class="text-danger p-2">Webcam not supported in this browser.</span>`;
        }
    }

    document.getElementById('selfieAttendanceModal')?.addEventListener('hidden.bs.modal', function () {
        if (selfieStream) {
            selfieStream.getTracks().forEach(track => track.stop());
            selfieStream = null;
        }
    });

    function retakeSelfie() {
        document.getElementById('selfiePreview').classList.add('d-none');
        document.getElementById('selfieVideo').classList.remove('d-none');
        document.getElementById('btnRetakeSelfie').style.display = 'none';
        document.getElementById('btnCaptureSelfie').textContent = 'Capture & Punch';
    }

    function captureSelfieAndSubmit() {
        const video = document.getElementById('selfieVideo');
        const canvas = document.getElementById('selfieCanvas');
        const preview = document.getElementById('selfiePreview');

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const base64Data = canvas.toDataURL('image/jpeg', 0.85);
        preview.src = base64Data;
        preview.classList.remove('d-none');
        video.classList.add('d-none');

        document.getElementById('btnRetakeSelfie').style.display = 'inline-block';
        document.getElementById('btnCaptureSelfie').textContent = 'Submitting...';

        Swal.fire({
            title: 'Uploading Selfie Punch...',
            text: 'Please wait while we record your attendance',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('employee.punch.selfie') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                image_base64: base64Data
            },
            success: function (res) {
                if (res.status) {
                    bootstrap.Modal.getInstance(document.getElementById('selfieAttendanceModal'))?.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Selfie Punch Recorded! 📸',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: res.message
                    });
                    document.getElementById('btnCaptureSelfie').textContent = 'Capture & Punch';
                }
            },
            error: function (xhr) {
                let msg = 'An error occurred during selfie upload.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: msg
                });
                document.getElementById('btnCaptureSelfie').textContent = 'Capture & Punch';
            }
        });
    }

    // 4. QR Code Attendance
    function openQrModal() {
        if (empAssignedType !== 'qr code') {
            Swal.fire({
                icon: 'warning',
                title: 'Attendance Mode Restricted',
                text: `Your company administrator has configured your attendance mode as "${empAssignedName}". Company QR attendance is not allowed for your profile.`,
                confirmButtonText: 'Understood',
                confirmButtonColor: '#6c757d'
            });
            return;
        }

        const modal = new bootstrap.Modal(document.getElementById('qrAttendanceModal'));
        modal.show();
        document.getElementById('qrCodeInput').value = '';
    }

    function submitQrAttendance(e) {
        e.preventDefault();
        const qrValue = document.getElementById('qrCodeInput').value.trim();
        if (!qrValue) return;

        const btn = document.getElementById('btnSubmitQr');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Validating...';

        $.ajax({
            url: "{{ route('employee.punch.qr') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                qrcode: qrValue
            },
            success: function (res) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Validate & Punch';

                if (res.status) {
                    bootstrap.Modal.getInstance(document.getElementById('qrAttendanceModal'))?.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'QR Attendance Success! 📱',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Failed',
                        text: res.message
                    });
                }
            },
            error: function (xhr) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Validate & Punch';
                let msg = 'An error occurred during QR verification.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
            }
        });
    }
</script>
