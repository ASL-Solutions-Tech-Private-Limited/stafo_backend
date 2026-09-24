@extends('user.layouts.app')

@section('title', 'Employee Location Tracking')

@section('css')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map-wrapper {
            position: relative;
            transition: all 0.3s ease;
        }
        #map-wrapper.is-fullscreen {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 99999 !important;
            background: #ffffff !important;
            padding: 16px !important;
            border-radius: 0 !important;
            overflow-y: auto !important;
        }
        #map-wrapper.is-fullscreen #map {
            height: calc(100vh - 180px) !important;
        }
        #map {
            height: 600px;
            width: 100%;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.08);
            z-index: 1;
        }
        .stat-card {
            transition: all 0.2s ease;
            border-radius: 12px;
            background: #fff;
            border: 1px solid #e2e8f0;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        }
        .map-legend-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            font-size: 0.85rem;
            margin-bottom: 12px;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }
        .legend-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            display: inline-block;
        }
        .custom-map-popup .leaflet-popup-content-wrapper,
        .custom-map-popup .leaflet-popup-tip {
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        .custom-map-popup .leaflet-popup-content {
            margin: 10px 12px;
            line-height: 1.4;
        }
        .radar-pulse-container {
            position: relative;
            width: 38px;
            height: 48px;
        }
        .radar-pulse-ring {
            position: absolute;
            width: 48px;
            height: 48px;
            top: 0px;
            left: -5px;
            border: 2.5px solid #ef4444;
            border-radius: 50%;
            animation: radarPulse 1.8s infinite ease-out;
            pointer-events: none;
            opacity: 0;
        }
        @keyframes radarPulse {
            0% { transform: scale(0.3); opacity: 0.9; }
            100% { transform: scale(1.6); opacity: 0; }
        }
        .playback-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 18px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
            margin-top: 14px;
        }
        .playback-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }
        .timeline-slider {
            height: 7px;
            border-radius: 4px;
            background: #e2e8f0;
            accent-color: #2563eb;
            cursor: pointer;
        }
        .telemetry-chip {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 0.775rem;
            font-weight: 600;
        }
        .map-tool-btn {
            border-radius: 7px;
            font-size: 0.775rem;
            font-weight: 500;
            padding: 5px 10px;
            transition: all 0.15s ease;
        }
        .playback-avatar {
            position: relative;
            width: 36px;
            height: 36px;
            background: #2563eb;
            color: #fff;
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(37,99,235,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }
        .playback-avatar-ring {
            position: absolute;
            width: 52px;
            height: 52px;
            top: -8px;
            left: -8px;
            border: 2px solid #2563eb;
            border-radius: 50%;
            animation: radarPulse 1.5s infinite ease-out;
            pointer-events: none;
        }
        .live-indicator-badge {
            animation: liveGlow 2s infinite;
        }
        @keyframes liveGlow {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .speed-legend-pill {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .intel-chip {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .intel-chip:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            transform: translateY(-1px);
        }
        /* Print Styles */
        @media print {
            .navbar, .sidebar, .sidebar-wrapper, .map-tool-btn, .playback-panel, #filterBtn, .form-select, .form-control, #mapGeoSwitch, .btn, footer, #speedLegendBar, .intel-bar {
                display: none !important;
            }
            body, .container-fluid, .card {
                background: #fff !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
            #map {
                height: 480px !important;
                page-break-inside: avoid;
            }
            .stat-card {
                border: 1px solid #ccc !important;
            }
            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-3">

    <!-- Top Card: Title & Controls -->
    <div class="card p-3 p-md-4 shadow-sm border-0 rounded-4 mb-4">
        <div class="row align-items-end g-3">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    @if($date === date('Y-m-d') && $employee && (string)$employee->geo_status === '2')
                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill small fw-bold live-indicator-badge">
                            <i class="fa-solid fa-circle text-white fs-xs me-1 fa-beat" style="font-size: 8px;"></i> LIVE TRACKING (Auto-syncs 15s)
                        </span>
                    @elseif($date === date('Y-m-d') && $employee && (string)$employee->geo_status === '1')
                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill small fw-bold">
                            <i class="fa-solid fa-clock text-dark fs-xs me-1"></i> REQUEST PENDING (Awaiting Employee)
                        </span>
                    @else
                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill small">GPS Tracker</span>
                    @endif
                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill small">Route History</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill small">
                        <i class="fa-solid fa-user-check me-1"></i>Employee Field Tracking
                    </span>
                </div>
                <h3 class="fw-bold mb-1 text-dark">
                    <i class="fa-solid fa-location-crosshairs text-success me-2"></i>Location Tracking
                </h3>
                <div class="text-muted mb-0 small">
                    @if($employee)
                        Tracking path & pause history for <strong class="text-dark">{{ $employee->name }}</strong> on {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}
                        <div class="d-inline-flex align-items-center gap-2 mt-2 p-1.5 px-2.5 rounded-3 bg-light border flex-wrap">
                            <span class="small fw-semibold text-muted">Tracking Status:</span>
                            <div class="form-check form-switch mb-0 d-inline-flex align-items-center">
                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="mapGeoSwitch"
                                       {{ in_array((string)$employee->geo_status, ['1', '2']) ? 'checked' : '' }}
                                       onchange="toggleMapGeoStatus({{ $employee->id }}, this.checked, '{{ addslashes($employee->name) }}')">
                            </div>
                            <span id="mapGeoBadge" class="badge {{ (string)$employee->geo_status === '2' ? 'bg-success text-white' : ((string)$employee->geo_status === '1' ? 'bg-warning text-dark' : 'bg-secondary text-white') }} rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                <i class="fa-solid {{ (string)$employee->geo_status === '2' ? 'fa-circle-dot' : ((string)$employee->geo_status === '1' ? 'fa-clock' : 'fa-circle') }} me-1" id="mapGeoIcon"></i>
                                <span id="mapGeoText">
                                    @if((string)$employee->geo_status === '2')
                                        Active / Tracking ON
                                    @elseif((string)$employee->geo_status === '1')
                                        Request Sent / Pending
                                    @else
                                        Inactive / Tracking OFF
                                    @endif
                                </span>
                            </span>
                        </div>
                    @else
                        Track real-time field movement and pause durations of your employees.
                    @endif
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row g-2 justify-content-lg-end">
                    <div class="col-sm-5 col-md-5">
                        <label class="form-label small fw-semibold text-muted mb-1">Employee</label>
                        <select name="employee_id" id="employee_id" class="form-select form-select-sm shadow-none">
                            @if(isset($employees) && $employees->isNotEmpty())
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ $employee && $employee->id == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->name }} ({{ $emp->emp_id ?? 'ID: ' . $emp->id }})
                                    </option>
                                @endforeach
                            @elseif($employee)
                                <option value="{{ $employee->id }}" selected>{{ $employee->name }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-4 col-md-4">
                        <label class="form-label small fw-semibold text-muted mb-1">Date</label>
                        <input type="date" name="date" id="date" value="{{ $date }}" class="form-control form-control-sm shadow-none">
                    </div>
                    <div class="col-sm-3 col-md-3 d-flex align-items-end">
                        <button class="btn btn-sm btn-primary w-100 fw-semibold d-inline-flex align-items-center justify-content-center" id="filterBtn" style="height: 31px;">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Track
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($shiftDetails))
            <hr class="my-3 opacity-25">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-1">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 rounded-pill">
                        <i class="fa-regular fa-clock me-1"></i>Shift: <strong>{{ $shiftDetails['shift']->shift_name ?? 'Assigned Shift' }}</strong>
                    </span>
                    <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill">
                        <i class="fa-solid fa-business-time text-primary me-1"></i>Timing: <strong>{{ $shiftDetails['start']->format('h:i A') }}</strong> to <strong>{{ $shiftDetails['end']->format('h:i A') }}</strong>
                        @if(!empty($shiftDetails['is_overnight']))
                            <span class="text-muted small ms-1">(Next Day)</span>
                        @endif
                    </span>
                    @if($employee && ((string)$employee->geo_status === '0' || empty($employee->geo_status)))
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded-pill small">
                            <i class="fa-solid fa-location-slash me-1"></i>Tracking OFF by Company
                        </span>
                    @elseif($employee && (string)$employee->geo_status === '1')
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5 rounded-pill small">
                            <i class="fa-solid fa-clock me-1"></i>Request Pending Acceptance
                        </span>
                    @elseif(!empty($shiftDetails['is_off_day']))
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5 rounded-pill">
                            <i class="fa-solid fa-calendar-xmark me-1"></i>Scheduled Off-Day
                        </span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                            <i class="fa-solid fa-circle-check me-1"></i>Tracking Active In Shift Window
                        </span>
                    @endif
                </div>
                <div class="small text-muted d-flex align-items-center gap-1">
                    <i class="fa-solid fa-shield-halved text-success"></i>
                    <span>Tracking strictly limited to shift hours &bull; Privacy Protected</span>
                </div>
            </div>
        @endif
    </div>

    @if($employee && ((string)$employee->geo_status === '0' || empty($employee->geo_status)) && $date === date('Y-m-d'))
        <!-- Deactivated Tracking Notice (When Company turned tracking OFF) -->
        <div class="card p-5 shadow-sm border-0 rounded-4 text-center my-4 bg-light">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger" style="width: 76px; height: 76px;">
                    <i class="fa-solid fa-location-slash fs-2"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-2">Location Tracking is Turned OFF</h4>
            <p class="text-muted mx-auto mb-4" style="max-width: 580px;">
                Company ne <strong>{{ $employee->name }}</strong> ka location tracking off kar rakha hai (<code>geo_status: 0</code>). Isliye is employee ka live route map aur location movement yaha par show nahi hoga.
            </p>
            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                <button type="button" class="btn btn-success px-4 py-2 fw-semibold rounded-pill shadow-sm" 
                        onclick="toggleMapGeoStatus({{ $employee->id }}, true, '{{ addslashes($employee->name) }}')">
                    <i class="fa-solid fa-location-crosshairs me-2"></i> Request Location Tracking
                </button>
                <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Employee Directory
                </a>
            </div>
        </div>
    @elseif($employee && (string)$employee->geo_status === '1' && $date === date('Y-m-d'))
        <!-- Request Sent / Pending Employee Acceptance Notice -->
        <div class="card p-5 shadow-sm border-0 rounded-4 text-center my-4 bg-light">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning" style="width: 76px; height: 76px;">
                    <i class="fa-solid fa-clock fs-2"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-2">Tracking Request Sent (Pending Employee Acceptance)</h4>
            <p class="text-muted mx-auto mb-4" style="max-width: 580px;">
                Company has sent a real-time location tracking request to <strong>{{ $employee->name }}</strong>. 
                <br>
                <span class="text-secondary fw-semibold">Jab tak employee mobile app ya employee portal se request accept nahi karega (<code>Accept & Start Tracking</code>), tab tak location movement aur map yaha visible nahi hoga.</span>
            </p>
            <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-danger px-4 py-2 fw-semibold rounded-pill shadow-sm" 
                        onclick="toggleMapGeoStatus({{ $employee->id }}, false, '{{ addslashes($employee->name) }}')">
                    <i class="fa-solid fa-location-slash me-2"></i> Cancel / Turn OFF Request
                </button>
                <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Employee Directory
                </a>
            </div>
        </div>
    @else
        @if(!empty($journeyStats))
            <!-- Journey Summary Metric Cards (6-Card HRMS Grid) -->
            <div class="row g-3 mb-3">
                <!-- Start Point / First Ping -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card p-3 shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">First Ping (Start)</span>
                            <span class="badge bg-success text-white px-1.5 py-0.5 rounded-pill" style="font-size: 0.65rem;">START</span>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">{{ $journeyStats['start_time'] }}</h6>
                        <div class="small text-muted" style="font-size: 0.75rem;">
                            @if($journeyStats['start_battery'])
                                <i class="fa-solid fa-battery-three-quarters text-success me-1"></i>{{ $journeyStats['start_battery'] }}% Battery
                            @else
                                <i class="fa-regular fa-clock me-1 text-success"></i>First Ping
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Latest Point / Last Ping -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card p-3 shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Latest Ping</span>
                            <span class="badge bg-danger text-white px-1.5 py-0.5 rounded-pill" style="font-size: 0.65rem;">LATEST</span>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark" id="latestPingTimeCard">{{ $journeyStats['end_time'] }}</h6>
                        <div class="small text-muted" style="font-size: 0.75rem;">
                            @if($journeyStats['end_battery'])
                                <i class="fa-solid fa-battery-quarter text-warning me-1"></i><span id="latestPingBatteryCard">{{ $journeyStats['end_battery'] }}</span>% Battery
                            @else
                                <i class="fa-regular fa-clock me-1 text-danger"></i>Latest Ping
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Total Distance -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card p-3 shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Field Distance</span>
                            <i class="fa-solid fa-route text-primary fs-6"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-primary"><span id="totalDistCard">{{ $journeyStats['total_distance_km'] }}</span> <span class="fs-xs fw-normal text-muted">km</span></h6>
                        <div class="small text-muted" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-stopwatch me-1"></i>{{ $journeyStats['total_duration'] }}
                        </div>
                    </div>
                </div>

                <!-- Pauses / Stoppages -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card p-3 shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Stoppages</span>
                            <span class="badge bg-warning text-dark px-1.5 py-0.5 rounded-pill fw-bold" style="font-size: 0.65rem;">{{ $journeyStats['total_stops'] }} Stops</span>
                        </div>
                        <h6 class="fw-bold mb-1 text-warning text-dark">{{ $journeyStats['total_halt_duration'] }}</h6>
                        <div class="small text-muted" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-person-walking me-1 text-info"></i>Move: {{ $journeyStats['moving_duration'] }}
                        </div>
                    </div>
                </div>

                <!-- Speed Telemetry -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card p-3 shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Speed &amp; Pace</span>
                            <i class="fa-solid fa-gauge-high text-info fs-6"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-info">{{ $journeyStats['max_speed_kmh'] ?? 0 }} <span class="fs-xs fw-normal text-muted">max km/h</span></h6>
                        <div class="small text-muted" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-calculator me-1"></i>Avg: {{ $journeyStats['avg_speed_kmh'] ?? 0 }} km/h
                        </div>
                    </div>
                </div>

                <!-- Office Geofence Presence Card -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="stat-card p-3 shadow-sm h-100 cursor-pointer" onclick="openGeofenceModal()" title="View Geofence Entry/Exit Events">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Office vs Field</span>
                            <i class="fa-solid fa-door-open text-indigo fs-6" style="color: #6366f1;"></i>
                        </div>
                        @if(!empty($branchGeofence))
                            <h6 class="fw-bold mb-1 text-truncate text-dark" style="color: #4f46e5 !important;" id="officePresenceSummary">Calculating...</h6>
                            <div class="small text-muted" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-list-check me-1 text-primary"></i><span class="text-primary text-decoration-underline">View Details</span>
                            </div>
                        @else
                            <h6 class="fw-bold mb-1 text-muted">No Branch</h6>
                            <div class="small text-muted" style="font-size: 0.75rem;">Geofence inactive</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- HRMS Intelligence Bar (Speed Violations, TA/DA Claim, Battery Diagnostics, Route Efficiency) -->
            <div class="intel-bar d-flex align-items-center flex-wrap gap-2 mb-4">
                <!-- Speed Compliance Badge -->
                <div class="intel-chip" onclick="showOverspeedOnMap()" title="Click to highlight speed violations on map">
                    <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                    <span class="text-dark fw-semibold">Speed Compliance:</span>
                    <span id="overspeedBadge" class="badge bg-secondary rounded-pill px-2 py-1">Scanning...</span>
                </div>

                <!-- TA/DA Conveyance Claim Badge -->
                <div class="intel-chip" onclick="openTAModal()" title="Click to view & print Travel Allowance claim voucher">
                    <i class="fa-solid fa-receipt text-success"></i>
                    <span class="text-dark fw-semibold">TA/DA Claim (@ ₹8/km):</span>
                    <span id="taClaimBadge" class="badge bg-success text-white rounded-pill px-2 py-1">₹0</span>
                </div>

                <!-- Battery & GPS Diagnostics Badge -->
                <div class="intel-chip" onclick="openBatteryModal()" title="Inspect battery consumption & GPS signal continuity">
                    <i class="fa-solid fa-battery-half text-primary"></i>
                    <span class="text-dark fw-semibold">Battery &amp; GPS Health:</span>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1"><i class="fa-solid fa-chart-line me-1"></i>Diagnostics</span>
                </div>

                <!-- Route Efficiency Score -->
                <div class="intel-chip" title="Direct Displacement vs Actual Route Distance">
                    <i class="fa-solid fa-route text-info"></i>
                    <span class="text-dark fw-semibold">Route Efficiency:</span>
                    <span id="efficiencyBadge" class="badge bg-light text-dark border rounded-pill px-2 py-1">Analyzing...</span>
                </div>
            </div>
        @endif

        <!-- Map Container & Interactive Suite -->
        <div class="card p-3 shadow-sm border-0 rounded-4" id="map-wrapper">
            @if(!empty(json_decode($location_info)))
                <!-- Legend & Map Control Bar -->
                <div class="map-legend-bar">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <div class="legend-item">
                            <span class="legend-dot" style="background-color: #10b981;"></span>
                            <span>Start</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot" style="background-color: #ef4444;"></span>
                            <span>Latest</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot" style="background-color: #f59e0b;"></span>
                            <span>Pause</span>
                        </div>
                        @if(!empty($branchGeofence))
                            <div class="legend-item cursor-pointer" onclick="focusOnBranchOffice()" title="Click to view office geofence">
                                <span class="legend-dot" style="background-color: #6366f1;"></span>
                                <span class="text-primary fw-semibold"><i class="fa-solid fa-building me-1"></i>Office ({{ $branchGeofence['radius'] }}m)</span>
                            </div>
                        @endif
                        <div class="legend-item text-muted small ms-1">
                            <i class="fa-solid fa-arrows-to-dot me-1"></i> <span id="totalPointsCount">{{ $journeyStats['total_points'] ?? count(json_decode($location_info)) }}</span> Pts
                        </div>
                    </div>

                    <!-- Right Tool Buttons -->
                    <div class="d-flex align-items-center flex-wrap gap-1.5 ms-auto">
                        <!-- Map Layer Toggle (Satellite vs Street) -->
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle map-tool-btn active" id="currentLayerDisplayBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-layer-group me-1"></i> <span id="layerNameLabel">Satellite</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 small">
                                <li>
                                    <a class="dropdown-item active" href="javascript:void(0)" onclick="switchTileLayer('hybrid')" id="optLayerHybrid">
                                        <i class="fa-solid fa-satellite me-2 text-primary"></i>Satellite Hybrid
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="switchTileLayer('streets')" id="optLayerStreets">
                                        <i class="fa-solid fa-map me-2 text-success"></i>Street / Road Map
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary map-tool-btn" onclick="fitRouteBounds()" title="Fit Full Route on Screen">
                            <i class="fa-solid fa-expand me-1"></i> Fit
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-secondary map-tool-btn" id="btnToggleArrows" onclick="toggleDirectionArrows()" title="Toggle Movement Direction Arrows">
                            <i class="fa-solid fa-arrow-right-long me-1"></i> Arrows
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-secondary map-tool-btn" id="btnToggleStops" onclick="toggleStoppageMarkers()" title="Show or Hide Stoppage Markers">
                            <i class="fa-solid fa-circle-pause me-1"></i> Pauses
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-secondary map-tool-btn" id="btnToggleSpeedMap" onclick="toggleSpeedHeatmap()" title="Show Route Colored by Speed">
                            <i class="fa-solid fa-gauge-high me-1"></i> Speed Map
                        </button>

                        @if(!empty($branchGeofence))
                            <button type="button" class="btn btn-sm btn-outline-primary map-tool-btn" id="btnFocusOffice" onclick="focusOnBranchOffice()" title="Center on Company Branch Office">
                                <i class="fa-solid fa-building me-1"></i> Office
                            </button>
                        @endif

                        <button type="button" class="btn btn-sm btn-outline-dark map-tool-btn" id="btnFullscreen" onclick="toggleFullscreenMap()" title="Fullscreen View">
                            <i class="fa-solid fa-maximize me-1"></i> Fullscreen
                        </button>
                    </div>
                </div>

                <!-- Speed Legend (Shown when Speed Map is active) -->
                <div id="speedLegendBar" class="d-none bg-light border rounded-3 p-2 mb-2 d-flex align-items-center justify-content-between flex-wrap gap-2 small">
                    <span class="fw-bold text-dark"><i class="fa-solid fa-gauge-high text-info me-1"></i>Speed Heatmap:</span>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="speed-legend-pill" style="background:#3b82f6;">0-5 km/h (Halt/Walk)</span>
                        <span class="speed-legend-pill" style="background:#10b981;">5-25 km/h (Slow/Traffic)</span>
                        <span class="speed-legend-pill" style="background:#f59e0b;">25-50 km/h (Medium)</span>
                        <span class="speed-legend-pill" style="background:#ef4444;">&gt;50 km/h (High Speed)</span>
                    </div>
                </div>

                <!-- Leaflet Map Canvas -->
                <div id="map"></div>

                <!-- Route Playback / Trip Replay HUD Panel -->
                <div class="playback-panel">
                    <div class="row align-items-center g-3">
                        <div class="col-auto d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-primary playback-btn shadow-sm" id="btnPlayPause" onclick="togglePlayback()" title="Play / Pause Route Replay">
                                <i class="fa-solid fa-play" id="playPauseIcon"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary playback-btn" onclick="resetPlayback()" title="Restart from Beginning">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>

                        <div class="col">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="small text-muted fw-semibold" id="playbackTimeStart">{{ $journeyStats['start_time'] ?? 'Start' }}</span>
                                <span class="badge bg-primary text-white px-2.5 py-1 rounded-pill small fw-bold shadow-xs">
                                    <i class="fa-regular fa-clock me-1"></i><span id="playbackTimeDisplay">{{ $journeyStats['start_time'] ?? '--:--' }}</span>
                                </span>
                                <span class="small text-muted fw-semibold" id="playbackTimeEnd">{{ $journeyStats['end_time'] ?? 'End' }}</span>
                            </div>
                            <input type="range" class="form-range timeline-slider w-100" id="playbackSlider" min="0" max="100" value="0" step="0.5" oninput="onSliderScrub(this.value)">
                        </div>

                        <div class="col-auto d-flex align-items-center gap-2 flex-wrap">
                            <div class="d-flex align-items-center bg-light border rounded-pill px-2 py-1">
                                <span class="small text-muted me-1" style="font-size: 0.75rem;">Speed:</span>
                                <select class="form-select form-select-sm border-0 bg-transparent py-0 ps-1 pe-4 text-dark fw-bold cursor-pointer" id="playbackSpeed" onchange="setPlaybackSpeed(this.value)" style="width: 75px; font-size: 0.78rem;">
                                    <option value="1">1x</option>
                                    <option value="2" selected>2x</option>
                                    <option value="5">5x</option>
                                    <option value="10">10x</option>
                                </select>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill map-tool-btn" id="btnFollowCam" onclick="toggleFollowCam()" title="Keep Camera Locked on Moving Avatar">
                                <i class="fa-solid fa-video me-1"></i> <span id="followCamText">Follow Cam: OFF</span>
                            </button>

                            <div class="telemetry-chip text-muted" id="playbackSpeedChip">
                                <i class="fa-solid fa-gauge-high text-info me-1"></i><span id="playbackSpeedValue">0</span> km/h
                            </div>

                            <div class="telemetry-chip text-muted" id="playbackDistanceChip">
                                <i class="fa-solid fa-route text-primary me-1"></i><span id="playbackDistance">0.00</span> / {{ $journeyStats['total_distance_km'] ?? '0' }} km
                            </div>

                            <div class="telemetry-chip text-muted" id="playbackBatteryChip">
                                <i class="fa-solid fa-battery-half text-success me-1"></i><span id="playbackBattery">{{ $journeyStats['start_battery'] ?? '--' }}</span>%
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning my-4 p-4 rounded-4 border-0 text-center">
                    <div class="mb-2"><i class="fa-solid fa-location-dot fs-2 text-warning"></i></div>
                    <h5 class="fw-bold text-dark mb-1">No Location Data Found</h5>
                    <p class="text-muted mb-1">No GPS location pings were recorded for this employee on {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}.</p>
                    @if(!empty($shiftDetails))
                        <div class="mt-2">
                            @if(!empty($shiftDetails['is_off_day']))
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                    <i class="fa-solid fa-calendar-xmark me-1"></i>Today is a scheduled Off-Day for this shift. Tracking remains stopped until the next scheduled shift.
                                </span>
                            @else
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                    <i class="fa-regular fa-clock text-primary me-1"></i>Shift Window: {{ $shiftDetails['start']->format('h:i A') }} to {{ $shiftDetails['end']->format('h:i A') }} (Location tracking is bounded to this window only)
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Stoppages & Pauses Logs Table -->
        @if(!empty($halts))
            <div class="card mt-4 shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-circle-pause text-warning me-2"></i>Stoppage &amp; Pause Logs (कहाँ पर कितना time pause किया)
                        </h5>
                        <small class="text-muted">Detected halts where the employee was stationary within a small radius (&ge; 3 minutes).</small>
                    </div>
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                        {{ count($halts) }} {{ count($halts) == 1 ? 'Pause' : 'Pauses' }} Detected
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 70px;">Stop #</th>
                                <th>Pause Duration</th>
                                <th>Time Interval (From &rarr; To)</th>
                                <th>Location / Place Name</th>
                                <th>Coordinates</th>
                                <th>Battery</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($halts as $halt)
                                <tr>
                                    <td class="ps-3">
                                        <span class="badge bg-warning text-dark rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700;">
                                            {{ $halt['stop_number'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark px-2 py-1 fs-6 fw-bold">
                                            <i class="fa-solid fa-clock me-1"></i>{{ $halt['duration_text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $halt['start_time'] }} &rarr; {{ $halt['end_time'] }}</div>
                                        <small class="text-muted">{{ $halt['points_count'] }} stationary pings</small>
                                    </td>
                                    <td style="max-width: 260px;">
                                        <div id="table-address-{{ $halt['stop_number'] }}" class="small fw-semibold text-dark text-break">
                                            <span class="text-muted"><i class="fa-solid fa-spinner fa-spin me-1"></i> Resolving address...</span>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-muted small font-monospace">{{ $halt['lat'] }}, {{ $halt['lng'] }}</code>
                                    </td>
                                    <td>
                                        @if($halt['battery'])
                                            <span class="text-muted small"><i class="fa-solid fa-battery-half text-success me-1"></i>{{ $halt['battery'] }}%</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" onclick="focusOnStop({{ $halt['stop_number'] }})">
                                                <i class="fa-solid fa-crosshairs me-1"></i> View
                                            </button>
                                            <a href="https://www.google.com/maps/@?api=1&map_action=pano&viewpoint={{ $halt['lat'] }},{{ $halt['lng'] }}" target="_blank" class="btn btn-outline-danger" title="Open Street View 360">
                                                <i class="fa-solid fa-street-view"></i> 360°
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

</div>

<!-- 1. Office Geofence Entry/Exit Modal -->
<div class="modal fade" id="geofenceLogsModal" tabindex="-1" aria-labelledby="geofenceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark" id="geofenceModalLabel">
                    <i class="fa-solid fa-building-circle-check text-primary me-2"></i>Office Geofence Entry &amp; Exit Log
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-light border text-center">
                            <span class="text-muted small fw-semibold text-uppercase">Time Inside Office</span>
                            <h4 class="fw-bold text-primary mb-0 mt-1" id="modalInsideOfficePct">0%</h4>
                            <small class="text-muted" id="modalInsidePointsCount">0 recorded points</small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 bg-light border text-center">
                            <span class="text-muted small fw-semibold text-uppercase">Time In Field / Out</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1" id="modalOutsideOfficePct">0%</h4>
                            <small class="text-muted" id="modalOutsidePointsCount">0 recorded points</small>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-timeline me-2 text-primary"></i>Movement Events Timeline</h6>
                <div id="geofenceEventsTimeline" class="small">
                    <!-- Dynamic timeline injected here -->
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- 2. TA/DA Distance Reimbursement Modal -->
<div class="modal fade" id="taReimbursementModal" tabindex="-1" aria-labelledby="taModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark" id="taModalLabel">
                    <i class="fa-solid fa-receipt text-success me-2"></i>Travel Allowance (TA/DA) Claim Voucher
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 text-center mb-3">
                    <span class="text-success small fw-semibold text-uppercase">Total Approved Claim</span>
                    <h2 class="fw-bold text-success mb-0 mt-1" id="taModalAmount">₹0</h2>
                    <small class="text-muted">Calculated for {{ $employee->name ?? 'Employee' }} on {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">Rate per Kilometer (₹ / km):</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₹</span>
                        <input type="number" id="taRateInput" class="form-control" value="8" min="1" max="100" step="0.5" onchange="calculateReimbursement(this.value)">
                        <button class="btn btn-outline-secondary" type="button" onclick="calculateReimbursement(document.getElementById('taRateInput').value)">Recalculate</button>
                    </div>
                </div>

                <ul class="list-group list-group-flush border-top border-bottom small mb-3">
                    <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Total Distance Traveled:</span>
                        <strong class="text-dark" id="taModalKm">{{ $journeyStats['total_distance_km'] ?? '0' }} km</strong>
                    </li>
                    <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Total Active Time:</span>
                        <strong class="text-dark">{{ $journeyStats['total_duration'] ?? 'N/A' }}</strong>
                    </li>
                    <li class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Total Pauses / Stops:</span>
                        <strong class="text-dark">{{ $journeyStats['total_stops'] ?? 0 }} Stops</strong>
                    </li>
                </ul>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-success rounded-pill px-3" onclick="window.print()">
                    <i class="fa-solid fa-print me-1"></i> Print Voucher
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 3. Battery Health & Signal Loss Modal -->
<div class="modal fade" id="batteryHealthModal" tabindex="-1" aria-labelledby="batteryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark" id="batteryModalLabel">
                    <i class="fa-solid fa-battery-half text-primary me-2"></i>Phone Battery &amp; GPS Signal Diagnostics
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="p-3 rounded-3 bg-light border text-center">
                            <span class="text-muted small fw-semibold text-uppercase">Start Battery</span>
                            <h4 class="fw-bold text-success mb-0 mt-1">{{ $journeyStats['start_battery'] ?? '--' }}%</h4>
                            <small class="text-muted">First recorded ping</small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 rounded-3 bg-light border text-center">
                            <span class="text-muted small fw-semibold text-uppercase">Latest Battery</span>
                            <h4 class="fw-bold text-warning mb-0 mt-1">{{ $journeyStats['end_battery'] ?? '--' }}%</h4>
                            <small class="text-muted">Current status</small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 rounded-3 bg-light border text-center">
                            <span class="text-muted small fw-semibold text-uppercase">Battery Consumption</span>
                            @php
                                $startBatt = (int)($journeyStats['start_battery'] ?? 0);
                                $endBatt = (int)($journeyStats['end_battery'] ?? 0);
                                $drain = ($startBatt > 0 && $endBatt > 0) ? max(0, $startBatt - $endBatt) : 0;
                            @endphp
                            <h4 class="fw-bold text-danger mb-0 mt-1">-{{ $drain }}%</h4>
                            <small class="text-muted">Estimated drain</small>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-chart-line me-2 text-primary"></i>Battery Drain Timeline Chart</h6>
                <div style="height: 220px;" class="mb-4">
                    <canvas id="batteryChartCanvas"></canvas>
                </div>

                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-tower-broadcast me-2 text-warning"></i>GPS Signal Continuity &amp; Dropouts</h6>
                <div id="signalGapsList">
                    <!-- Dynamic signal gap alert -->
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Leaflet JS & Polyline Decorator -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-polylinedecorator@1.6.0/dist/leaflet.polylineDecorator.min.js"></script>
<!-- Chart.js for Battery Diagnostics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

@if(!empty(json_decode($location_info)) && $employee && ((string)$employee->geo_status === '2' || $date !== date('Y-m-d')))
<script>
    const routeCoordinates = {!! $location_info !!};
    const rawPointsData = {!! $raw_points_json ?? '[]' !!};
    const haltsData = {!! $halts_json ?? '[]' !!};
    const startPointData = {!! $start_point_json ?? 'null' !!};
    const endPointData = {!! $end_point_json ?? 'null' !!};
    const branchGeofenceData = {!! $branch_geofence_json ?? 'null' !!};
    let totalTripDistanceKm = {{ $journeyStats['total_distance_km'] ?? 0 }};
    const employeeId = {{ $employee->id }};
    const currentDate = "{{ $date }}";
    const isToday = (currentDate === "{{ date('Y-m-d') }}");

    let leafletMap = null;
    let mainPolyline = null;
    let glowPolyline = null;
    let arrowDecorator = null;
    let speedSegmentsGroup = null;
    let isSpeedHeatmapActive = false;
    let officeMarker = null;
    let officeGeofenceCircle = null;

    let showArrows = true;
    let showStops = true;
    let followCamEnabled = false;
    let stopMarkers = [];
    let stopMarkersMap = {};
    let endMarker = null;
    const addressCache = {};

    // Tile Layers
    let currentTileLayer = null;
    let tileLayers = {};

    // Overspeeding Markers
    let overspeedMarkers = [];
    let currentSpeedLimit = 50;

    // Battery Chart Instance
    let batteryChartInstance = null;

    // Playback Engine State
    let isPlaying = false;
    let playbackInterval = null;
    let currentPlaybackIndex = 0;
    let playbackSpeedMultiplier = 2;
    let playbackMarker = null;

    // Live Sync Polling
    let livePollingTimer = null;
    let lastKnownTimestamp = rawPointsData.length > 0 ? rawPointsData[rawPointsData.length - 1].timestamp : null;

    // Helper: Distance calculation (Haversine)
    function calcDistanceMeters(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    // Reverse Geocoding Helper with Cache
    function fetchAddress(lat, lng, callback) {
        const key = `${lat.toFixed(5)}_${lng.toFixed(5)}`;
        if (addressCache[key]) {
            callback(addressCache[key]);
            return;
        }

        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`, {
            headers: { 'Accept-Language': 'en' }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.display_name) {
                addressCache[key] = data.display_name;
                callback(data.display_name);
            } else {
                throw new Error('No display name');
            }
        })
        .catch(() => {
            fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`)
                .then(res => res.json())
                .then(data => {
                    let parts = [];
                    if (data.locality) parts.push(data.locality);
                    if (data.city && data.city !== data.locality) parts.push(data.city);
                    if (data.principalSubdivision) parts.push(data.principalSubdivision);
                    if (data.countryName) parts.push(data.countryName);
                    const addr = parts.length > 0 ? parts.join(', ') : `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                    addressCache[key] = addr;
                    callback(addr);
                })
                .catch(() => {
                    const fallback = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                    addressCache[key] = fallback;
                    callback(fallback);
                });
        });
    }

    // Resolve all halt addresses for table and popups
    function resolveAllHaltAddresses() {
        if (!haltsData || haltsData.length === 0) return;
        haltsData.forEach((halt, idx) => {
            setTimeout(() => {
                fetchAddress(halt.lat, halt.lng, function(addr) {
                    const tableEl = document.getElementById(`table-address-${halt.stop_number}`);
                    if (tableEl) {
                        tableEl.innerHTML = `<span class="fw-semibold text-dark"><i class="fa-solid fa-location-dot text-danger me-1"></i>${addr}</span>`;
                    }
                    const stopEl = document.getElementById(`stop-address-${halt.stop_number}`);
                    if (stopEl) {
                        stopEl.textContent = addr;
                    }
                });
            }, idx * 250);
        });
    }

    // SVG Pin Icons
    function getStartPinSvg() {
        return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="46" viewBox="0 0 36 46">
                <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#000" flood-opacity="0.35"/>
                </filter>
                <path fill="#10B981" stroke="#FFFFFF" stroke-width="2.5" filter="url(#shadow)" d="M18 1C9.16 1 2 8.16 2 17c0 12.5 16 27 16 27s16-14.5 16-27c0-8.84-7.16-16-16-16z"/>
                <circle cx="18" cy="17" r="8" fill="#FFFFFF"/>
                <polygon points="15.5,13 22.5,17 15.5,21" fill="#10B981"/>
            </svg>
        `);
    }

    function getEndPinSvg() {
        return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="46" viewBox="0 0 36 46">
                <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#000" flood-opacity="0.35"/>
                </filter>
                <path fill="#EF4444" stroke="#FFFFFF" stroke-width="2.5" filter="url(#shadow)" d="M18 1C9.16 1 2 8.16 2 17c0 12.5 16 27 16 27s16-14.5 16-27c0-8.84-7.16-16-16-16z"/>
                <circle cx="18" cy="17" r="8" fill="#FFFFFF"/>
                <rect x="14" y="13" width="8" height="8" rx="1.5" fill="#EF4444"/>
            </svg>
        `);
    }

    function getPausePinSvg(stopNumber) {
        return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="46" viewBox="0 0 36 46">
                <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#000" flood-opacity="0.35"/>
                </filter>
                <path fill="#F59E0B" stroke="#FFFFFF" stroke-width="2.5" filter="url(#shadow)" d="M18 1C9.16 1 2 8.16 2 17c0 12.5 16 27 16 27s16-14.5 16-27c0-8.84-7.16-16-16-16z"/>
                <circle cx="18" cy="17" r="9" fill="#FFFFFF"/>
                <text x="18" y="21" font-size="11" font-family="system-ui, -apple-system, sans-serif" font-weight="800" fill="#B45309" text-anchor="middle">${stopNumber}</text>
            </svg>
        `);
    }

    // Initialize Map with Leaflet
    function initLeafletMap() {
        if (!routeCoordinates || routeCoordinates.length === 0) return;

        const firstPt = routeCoordinates[0];
        leafletMap = L.map('map', {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView([firstPt.lat, firstPt.lng], 15);

        // Professional HRMS Map Tile Layers
        tileLayers = {
            hybrid: L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                maxZoom: 21,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '&copy; Google Satellite | STAFO HRMS'
            }),
            streets: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap | STAFO HRMS'
            })
        };

        // Default to Google Satellite Hybrid for verification
        currentTileLayer = tileLayers.hybrid.addTo(leafletMap);

        const latLngs = routeCoordinates.map(p => [p.lat, p.lng]);

        // Dual Polyline (Glow line behind + vibrant solid path on top)
        glowPolyline = L.polyline(latLngs, {
            color: '#1d4ed8',
            weight: 8,
            opacity: 0.35,
            lineJoin: 'round',
            lineCap: 'round'
        }).addTo(leafletMap);

        mainPolyline = L.polyline(latLngs, {
            color: '#3b82f6',
            weight: 4.5,
            opacity: 0.95,
            lineJoin: 'round',
            lineCap: 'round'
        }).addTo(leafletMap);

        // Directional Movement Arrows
        renderDirectionArrows();

        // START & END MARKERS WITH OVERLAP HANDLING
        let startLat = startPointData ? startPointData.lat : firstPt.lat;
        let startLng = startPointData ? startPointData.lng : firstPt.lng;
        let endLat = endPointData ? endPointData.lat : routeCoordinates[routeCoordinates.length - 1].lat;
        let endLng = endPointData ? endPointData.lng : routeCoordinates[routeCoordinates.length - 1].lng;

        const distStartEnd = calcDistanceMeters(startLat, startLng, endLat, endLng);
        let startOffsetLat = startLat;
        let startOffsetLng = startLng;
        let endOffsetLat = endLat;
        let endOffsetLng = endLng;

        if (distStartEnd < 25 && routeCoordinates.length > 1) {
            startOffsetLat -= 0.00012;
            startOffsetLng -= 0.00012;
            endOffsetLat += 0.00012;
            endOffsetLng += 0.00012;
        }

        // Start Marker
        if (startPointData) {
            const startIcon = L.icon({
                iconUrl: getStartPinSvg(),
                iconSize: [36, 46],
                iconAnchor: [18, 46],
                popupAnchor: [0, -44]
            });

            const startPopupHtml = `
                <div class="custom-map-popup" style="min-width: 240px; max-width: 300px;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success text-white px-2 py-1 rounded-pill small">START POINT</span>
                        ${distStartEnd < 25 ? '<span class="badge bg-info text-white px-2 py-1 rounded-pill small">Round Trip</span>' : ''}
                    </div>
                    <h6 class="fw-bold text-success mb-2"><i class="fa-solid fa-play me-1"></i>First Ping Recorded</h6>
                    <div class="small text-dark mb-1"><strong>Time:</strong> ${startPointData.time_full || startPointData.time}</div>
                    <div class="p-2 rounded bg-light border mb-2 mt-2">
                        <div class="small text-muted fw-bold mb-1" style="font-size: 10px;">
                            <i class="fa-solid fa-map-pin text-success me-1"></i>START LOCATION
                        </div>
                        <div class="small fw-semibold text-dark text-break" id="start-address">
                            <i class="fa-solid fa-spinner fa-spin text-muted me-1"></i> Resolving address...
                        </div>
                        <div class="small text-muted mt-1 font-monospace" style="font-size: 11px;">
                            <i class="fa-solid fa-compass me-1"></i>${startPointData.lat.toFixed(5)}, ${startPointData.lng.toFixed(5)}
                        </div>
                    </div>
                    ${startPointData.battery ? `<div class="small text-muted mb-2"><i class="fa-solid fa-battery-half text-success me-1"></i>Battery: ${startPointData.battery}%</div>` : ''}
                    <a href="https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${startPointData.lat},${startPointData.lng}" target="_blank" class="btn btn-xs btn-outline-danger w-100 py-1">
                        <i class="fa-solid fa-street-view me-1"></i> Street View 360° Ground
                    </a>
                </div>
            `;

            const startMarker = L.marker([startOffsetLat, startOffsetLng], { icon: startIcon })
                .addTo(leafletMap)
                .bindPopup(startPopupHtml);

            startMarker.on('popupopen', function() {
                fetchAddress(startPointData.lat, startPointData.lng, function(addr) {
                    const el = document.getElementById('start-address');
                    if (el) el.textContent = addr;
                });
            });
        }

        // End / Latest Marker (With pulsating radar ring)
        if (endPointData && routeCoordinates.length > 1) {
            const endIcon = L.divIcon({
                className: 'radar-pulse-container',
                html: `
                    <div class="radar-pulse-ring"></div>
                    <img src="${getEndPinSvg()}" width="36" height="46" style="position: absolute; top: 0; left: 0;">
                `,
                iconSize: [36, 46],
                iconAnchor: [18, 46],
                popupAnchor: [0, -44]
            });

            const endPopupHtml = `
                <div class="custom-map-popup" style="min-width: 240px; max-width: 300px;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-danger text-white px-2 py-1 rounded-pill small">LATEST LOCATION</span>
                        <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded-pill small"><i class="fa-solid fa-signal me-1"></i>Live Ping</span>
                    </div>
                    <h6 class="fw-bold text-danger mb-2"><i class="fa-solid fa-flag-checkered me-1"></i>Latest Ping</h6>
                    <div class="small text-dark mb-1"><strong>Time:</strong> <span id="popupLatestTime">${endPointData.time_full || endPointData.time}</span></div>
                    <div class="p-2 rounded bg-light border mb-2 mt-2">
                        <div class="small text-muted fw-bold mb-1" style="font-size: 10px;">
                            <i class="fa-solid fa-map-pin text-danger me-1"></i>LATEST LOCATION
                        </div>
                        <div class="small fw-semibold text-dark text-break" id="end-address">
                            <i class="fa-solid fa-spinner fa-spin text-muted me-1"></i> Resolving address...
                        </div>
                        <div class="small text-muted mt-1 font-monospace" style="font-size: 11px;">
                            <i class="fa-solid fa-compass me-1"></i><span id="popupLatestCoords">${endPointData.lat.toFixed(5)}, ${endPointData.lng.toFixed(5)}</span>
                        </div>
                    </div>
                    ${endPointData.battery ? `<div class="small text-muted mb-2"><i class="fa-solid fa-battery-quarter text-warning me-1"></i>Battery: <span id="popupLatestBattery">${endPointData.battery}</span>%</div>` : ''}
                    <a href="https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${endPointData.lat},${endPointData.lng}" target="_blank" class="btn btn-xs btn-outline-danger w-100 py-1">
                        <i class="fa-solid fa-street-view me-1"></i> Street View 360° Ground
                    </a>
                </div>
            `;

            endMarker = L.marker([endOffsetLat, endOffsetLng], { icon: endIcon })
                .addTo(leafletMap)
                .bindPopup(endPopupHtml);

            endMarker.on('popupopen', function() {
                const curEnd = rawPointsData[rawPointsData.length - 1] || endPointData;
                fetchAddress(curEnd.lat, curEnd.lng, function(addr) {
                    const el = document.getElementById('end-address');
                    if (el) el.textContent = addr;
                });
            });
        }

        // STOPPAGE / PAUSE MARKERS
        if (haltsData && haltsData.length > 0) {
            haltsData.forEach(halt => {
                const pauseIcon = L.icon({
                    iconUrl: getPausePinSvg(halt.stop_number),
                    iconSize: [36, 46],
                    iconAnchor: [18, 46],
                    popupAnchor: [0, -44]
                });

                const haltPopupHtml = `
                    <div class="custom-map-popup" style="min-width: 250px; max-width: 320px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded-pill">Stop #${halt.stop_number}</span>
                            <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1 fw-bold">Paused ${halt.duration_text}</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-circle-pause text-warning me-1"></i>Employee Paused Here</h6>
                        <div class="small text-dark mb-1"><strong>From:</strong> ${halt.start_time} &rarr; <strong>To:</strong> ${halt.end_time}</div>
                        <div class="small text-primary fw-semibold mb-2"><i class="fa-regular fa-clock me-1"></i>Duration: ${halt.duration_text}</div>

                        <div class="p-2 rounded bg-light border mb-2">
                            <div class="small text-muted fw-bold mb-1" style="font-size: 10px;">
                                <i class="fa-solid fa-map-pin text-danger me-1"></i>PLACE / LOCATION NAME
                            </div>
                            <div class="small fw-semibold text-dark text-break" id="stop-address-${halt.stop_number}">
                                <i class="fa-solid fa-spinner fa-spin text-muted me-1"></i> Resolving address...
                            </div>
                            <div class="small text-muted mt-1 font-monospace" style="font-size: 11px;">
                                <i class="fa-solid fa-compass me-1"></i>Coordinates: <strong class="text-dark">${halt.lat.toFixed(5)}, ${halt.lng.toFixed(5)}</strong>
                            </div>
                        </div>
                        ${halt.battery ? `<div class="small text-muted mb-2"><i class="fa-solid fa-battery-half text-success me-1"></i>Battery: ${halt.battery}%</div>` : ''}
                        <a href="https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${halt.lat},${halt.lng}" target="_blank" class="btn btn-xs btn-outline-danger w-100 py-1">
                            <i class="fa-solid fa-street-view me-1"></i> Street View 360° Ground
                        </a>
                    </div>
                `;

                const marker = L.marker([halt.lat, halt.lng], { icon: pauseIcon })
                    .addTo(leafletMap)
                    .bindPopup(haltPopupHtml);

                marker.on('popupopen', function() {
                    fetchAddress(halt.lat, halt.lng, function(addr) {
                        const el = document.getElementById(`stop-address-${halt.stop_number}`);
                        if (el) el.textContent = addr;
                        const tableEl = document.getElementById(`table-address-${halt.stop_number}`);
                        if (tableEl) tableEl.innerHTML = `<span class="fw-semibold text-dark"><i class="fa-solid fa-location-dot text-danger me-1"></i>${addr}</span>`;
                    });
                });

                stopMarkers.push(marker);
                stopMarkersMap[halt.stop_number] = marker;
            });
        }

        // BRANCH GEOFENCE PERIMETER & OFFICE PIN
        if (branchGeofenceData && branchGeofenceData.lat && branchGeofenceData.lng) {
            const officeIcon = L.divIcon({
                className: 'office-pin-marker',
                html: `
                    <div style="background:#4f46e5; color:#fff; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; border:2.5px solid #fff; box-shadow:0 3px 10px rgba(79,70,229,0.5);">
                        <i class="fa-solid fa-building" style="font-size: 15px;"></i>
                    </div>
                `,
                iconSize: [34, 34],
                iconAnchor: [17, 17],
                popupAnchor: [0, -17]
            });

            officeMarker = L.marker([branchGeofenceData.lat, branchGeofenceData.lng], { icon: officeIcon }).addTo(leafletMap);
            officeMarker.bindPopup(`
                <div class="custom-map-popup" style="min-width: 220px;">
                    <span class="badge text-white mb-2" style="background:#4f46e5;"><i class="fa-solid fa-building me-1"></i>Office Geofence</span>
                    <h6 class="fw-bold text-dark mb-1">${branchGeofenceData.name}</h6>
                    <div class="small text-muted mb-2">${branchGeofenceData.address || 'Designated Branch Office'}</div>
                    <div class="small text-primary fw-semibold"><i class="fa-solid fa-bullseye me-1"></i>Radar Radius: <strong>${branchGeofenceData.radius}m</strong></div>
                </div>
            `);

            officeGeofenceCircle = L.circle([branchGeofenceData.lat, branchGeofenceData.lng], {
                radius: branchGeofenceData.radius || 200,
                color: '#6366f1',
                weight: 2,
                fillColor: '#818cf8',
                fillOpacity: 0.15,
                dashArray: '5, 6'
            }).addTo(leafletMap);

            calculateOfficeGeofencePresence();
        }

        // PLAYBACK MARKER (Animated Avatar with radar ring)
        const playbackIcon = L.divIcon({
            className: 'playback-avatar-container',
            html: `
                <div class="playback-avatar">
                    <div class="playback-avatar-ring"></div>
                    <i class="fa-solid fa-person-walking"></i>
                </div>
            `,
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -18]
        });

        playbackMarker = L.marker([firstPt.lat, firstPt.lng], {
            icon: playbackIcon,
            zIndexOffset: 1000
        }).addTo(leafletMap);
        playbackMarker.setOpacity(0);

        // Fit map bounds to entire route
        fitRouteBounds();

        // Run HRMS Diagnostics (Speed violations, TA/DA, Efficiency)
        checkOverspeedViolations(50);
        calculateReimbursement(8);
        calculateRouteEfficiency();

        // If viewing today's tracking, start background live sync polling
        if (isToday) {
            startLivePolling();
        }
    }

    // Direction Arrows Renderer
    function renderDirectionArrows() {
        if (typeof L.polylineDecorator !== 'function' || !mainPolyline || !leafletMap) return;
        try {
            if (arrowDecorator) {
                leafletMap.removeLayer(arrowDecorator);
            }
            arrowDecorator = L.polylineDecorator(mainPolyline, {
                patterns: [
                    {
                        offset: 25,
                        repeat: 55,
                        symbol: L.Symbol.arrowHead({
                            pixelSize: 11,
                            polygon: false,
                            pathOptions: { stroke: true, color: '#ffffff', weight: 2.5, opacity: 0.95 }
                        })
                    }
                ]
            });
            if (showArrows) {
                arrowDecorator.addTo(leafletMap);
            }
        } catch (e) {
            console.warn('Polyline decorator error:', e);
        }
    }

    // Fit Route Bounds
    function fitRouteBounds() {
        if (mainPolyline && leafletMap) {
            leafletMap.fitBounds(mainPolyline.getBounds(), {
                padding: [50, 50],
                maxZoom: 18
            });
        }
    }

    // Focus on Branch Office
    window.focusOnBranchOffice = function() {
        if (branchGeofenceData && leafletMap) {
            leafletMap.setView([branchGeofenceData.lat, branchGeofenceData.lng], 16, { animate: true });
            if (officeMarker) {
                setTimeout(() => officeMarker.openPopup(), 300);
            }
        }
    };

    // MAP THEMES SWITCHER (Satellite vs Street Road Map)
    function switchTileLayer(type) {
        if (!leafletMap || !tileLayers[type]) return;

        if (currentTileLayer) {
            leafletMap.removeLayer(currentTileLayer);
        }
        currentTileLayer = tileLayers[type].addTo(leafletMap);

        const labels = {
            hybrid: 'Satellite',
            streets: 'Street / Road Map'
        };

        const lbl = document.getElementById('layerNameLabel');
        if (lbl) lbl.innerText = labels[type] || 'Map Layer';

        ['Hybrid', 'Streets'].forEach(k => {
            const opt = document.getElementById(`optLayer${k}`);
            if (opt) {
                if (k.toLowerCase() === type) opt.classList.add('active');
                else opt.classList.remove('active');
            }
        });
    }

    // SPEED COMPLIANCE & VIOLATION HIGHLIGHTER
    function checkOverspeedViolations(limit = 50) {
        currentSpeedLimit = limit;
        if (!rawPointsData || rawPointsData.length === 0) return [];

        let violations = [];
        rawPointsData.forEach((pt, idx) => {
            if ((pt.speed_kmh || 0) > currentSpeedLimit) {
                violations.push({
                    index: idx + 1,
                    time: pt.time_full || pt.time,
                    speed: pt.speed_kmh,
                    lat: pt.lat,
                    lng: pt.lng
                });
            }
        });

        const badge = document.getElementById('overspeedBadge');
        if (badge) {
            if (violations.length > 0) {
                badge.className = 'badge bg-danger text-white rounded-pill px-2 py-1';
                badge.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-1"></i>${violations.length} Alerts (&gt;${currentSpeedLimit} km/h)`;
            } else {
                badge.className = 'badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1';
                badge.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i>Within Limit (&le;${currentSpeedLimit} km/h)`;
            }
        }
        return violations;
    }

    window.showOverspeedOnMap = function() {
        const violations = checkOverspeedViolations(currentSpeedLimit);
        overspeedMarkers.forEach(m => leafletMap.removeLayer(m));
        overspeedMarkers = [];

        if (violations.length === 0) {
            alert(`No speed violations detected above ${currentSpeedLimit} km/h.`);
            return;
        }

        violations.forEach(v => {
            const icon = L.divIcon({
                className: 'overspeed-alert-pin',
                html: `<div style="background:#ef4444; color:#fff; border-radius:50%; width:30px; height:30px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:bold; box-shadow:0 0 0 4px rgba(239,68,68,0.4); border:2px solid #fff;"><i class="fa-solid fa-triangle-exclamation"></i></div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            const m = L.marker([v.lat, v.lng], { icon: icon }).addTo(leafletMap);
            m.bindPopup(`
                <div class="custom-map-popup" style="min-width: 220px;">
                    <span class="badge bg-danger text-white mb-1"><i class="fa-solid fa-gauge-high me-1"></i>Speed Warning</span>
                    <h6 class="fw-bold text-danger mb-1">${v.speed} km/h</h6>
                    <div class="small text-muted mb-2">Recorded at: <strong>${v.time}</strong></div>
                    <div class="small text-dark mb-2">Limit: ${currentSpeedLimit} km/h (Exceeded by +${Math.round(v.speed - currentSpeedLimit)} km/h)</div>
                    <a href="https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${v.lat},${v.lng}" target="_blank" class="btn btn-xs btn-outline-danger w-100 py-1">
                        <i class="fa-solid fa-street-view me-1"></i> Street View 360° Ground
                    </a>
                </div>
            `);
            overspeedMarkers.push(m);
        });

        leafletMap.setView([violations[0].lat, violations[0].lng], 17, { animate: true });
        setTimeout(() => overspeedMarkers[0].openPopup(), 400);
    };

    // TA / DA CONVEYANCE CLAIM CALCULATOR
    function calculateReimbursement(rate = 8) {
        const r = parseFloat(rate) || 8;
        const totalKm = totalTripDistanceKm || 0;
        const claimAmount = Math.round(totalKm * r);

        const badge = document.getElementById('taClaimBadge');
        if (badge) badge.innerText = `₹${claimAmount.toLocaleString('en-IN')}`;

        const modalAmount = document.getElementById('taModalAmount');
        if (modalAmount) modalAmount.innerText = `₹${claimAmount.toLocaleString('en-IN')}`;

        const modalKm = document.getElementById('taModalKm');
        if (modalKm) modalKm.innerText = `${totalKm.toFixed(2)} km`;
    }

    window.openTAModal = function() {
        calculateReimbursement(document.getElementById('taRateInput') ? document.getElementById('taRateInput').value : 8);
        const modal = new bootstrap.Modal(document.getElementById('taReimbursementModal'));
        modal.show();
    };

    // ROUTE EFFICIENCY & FIELD DETOUR CALCULATOR
    function calculateRouteEfficiency() {
        if (!startPointData || !endPointData || totalTripDistanceKm <= 0) return;
        const directMeters = calcDistanceMeters(
            startPointData.lat, startPointData.lng,
            endPointData.lat, endPointData.lng
        );
        const directKm = directMeters / 1000;
        const actualKm = totalTripDistanceKm;

        const detourKm = Math.max(0, actualKm - directKm);
        const efficiency = directKm > 0 ? Math.min(100, Math.round((directKm / actualKm) * 100)) : 100;

        const el = document.getElementById('efficiencyBadge');
        if (el) {
            if (directKm < 0.1) {
                el.innerText = 'Round Trip / Base Return';
            } else {
                el.innerText = `${efficiency}% Direct (${detourKm.toFixed(1)} km field detour)`;
            }
        }
    }

    // BATTERY DIAGNOSTICS & SIGNAL DROPOUTS
    window.openBatteryModal = function() {
        const modal = new bootstrap.Modal(document.getElementById('batteryHealthModal'));
        modal.show();
        setTimeout(renderBatteryChart, 250);
    };

    function renderBatteryChart() {
        if (!rawPointsData || rawPointsData.length === 0) return;
        const canvas = document.getElementById('batteryChartCanvas');
        if (!canvas) return;

        const labels = [];
        const batteryLevels = [];
        let signalGaps = [];

        rawPointsData.forEach((p, idx) => {
            if (p.battery !== null && p.battery !== undefined) {
                labels.push(p.time);
                batteryLevels.push(p.battery);
            }

            if (idx > 0) {
                const gapSec = p.timestamp - rawPointsData[idx - 1].timestamp;
                if (gapSec >= 900) {
                    signalGaps.push({
                        from: rawPointsData[idx - 1].time,
                        to: p.time,
                        durationMin: Math.round(gapSec / 60)
                    });
                }
            }
        });

        const gapList = document.getElementById('signalGapsList');
        if (gapList) {
            if (signalGaps.length === 0) {
                gapList.innerHTML = '<div class="alert alert-success py-2 mb-0 small"><i class="fa-solid fa-circle-check me-1"></i>No GPS signal blackouts detected. Continuous connection logged throughout the day.</div>';
            } else {
                let h = '<ul class="list-group list-group-flush small">';
                signalGaps.forEach(g => {
                    h += `<li class="list-group-item px-0 py-1 text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i>Signal Drop Window: <strong>${g.from} &rarr; ${g.to}</strong> (${g.durationMin} mins gap without pings)</li>`;
                });
                h += '</ul>';
                gapList.innerHTML = h;
            }
        }

        if (batteryChartInstance) batteryChartInstance.destroy();

        batteryChartInstance = new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Battery %',
                    data: batteryLevels,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.3,
                    pointRadius: labels.length > 50 ? 0 : 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { min: 0, max: 100, ticks: { callback: v => v + '%' } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // GEOFENCE ENTRY/EXIT PRESENCE ANALYZER
    let geofenceAnalysisData = null;

    function calculateOfficeGeofencePresence() {
        if (!branchGeofenceData || !branchGeofenceData.lat || !rawPointsData || rawPointsData.length === 0) return;
        const bLat = branchGeofenceData.lat;
        const bLng = branchGeofenceData.lng;
        const radius = branchGeofenceData.radius || 200;

        let insideCount = 0;
        let outsideCount = 0;
        let events = [];
        let currentlyInside = null;

        rawPointsData.forEach((pt, idx) => {
            const dist = calcDistanceMeters(pt.lat, pt.lng, bLat, bLng);
            const isInside = (dist <= radius);

            if (isInside) insideCount++;
            else outsideCount++;

            if (currentlyInside === null) {
                currentlyInside = isInside;
                events.push({
                    type: isInside ? 'initial_inside' : 'initial_outside',
                    time: pt.time_full || pt.time,
                    dist: Math.round(dist)
                });
            } else if (isInside !== currentlyInside) {
                currentlyInside = isInside;
                events.push({
                    type: isInside ? 'entry' : 'exit',
                    time: pt.time_full || pt.time,
                    dist: Math.round(dist)
                });
            }
        });

        const totalPts = rawPointsData.length;
        const insidePct = totalPts > 0 ? Math.round((insideCount / totalPts) * 100) : 0;
        const outsidePct = 100 - insidePct;

        geofenceAnalysisData = { events, insideCount, outsideCount, insidePct, outsidePct };

        const summaryEl = document.getElementById('officePresenceSummary');
        if (summaryEl) {
            summaryEl.innerText = `${insidePct}% Office / ${outsidePct}% Field`;
        }
    }

    window.openGeofenceModal = function() {
        if (!geofenceAnalysisData) {
            alert('Geofence data not available for this employee.');
            return;
        }

        document.getElementById('modalInsideOfficePct').innerText = `${geofenceAnalysisData.insidePct}%`;
        document.getElementById('modalInsidePointsCount').innerText = `${geofenceAnalysisData.insideCount} pings inside office radar`;
        document.getElementById('modalOutsideOfficePct').innerText = `${geofenceAnalysisData.outsidePct}%`;
        document.getElementById('modalOutsidePointsCount').innerText = `${geofenceAnalysisData.outsideCount} pings in field / outside`;

        const timelineContainer = document.getElementById('geofenceEventsTimeline');
        if (timelineContainer) {
            let html = '<div class="list-group list-group-flush">';
            geofenceAnalysisData.events.forEach((ev, i) => {
                let badge = '';
                let title = '';
                if (ev.type === 'initial_inside') {
                    badge = '<span class="badge bg-success-subtle text-success"><i class="fa-solid fa-building me-1"></i>Start Inside</span>';
                    title = `Started day inside office perimeter (${ev.dist}m from office center)`;
                } else if (ev.type === 'initial_outside') {
                    badge = '<span class="badge bg-secondary-subtle text-secondary"><i class="fa-solid fa-person-walking me-1"></i>Start Outside</span>';
                    title = `Started day in the field (${ev.dist}m away from office)`;
                } else if (ev.type === 'entry') {
                    badge = '<span class="badge bg-success text-white"><i class="fa-solid fa-arrow-right-to-bracket me-1"></i>ENTERED</span>';
                    title = `Entered office geofence perimeter (${ev.dist}m from office)`;
                } else {
                    badge = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-arrow-right-from-bracket me-1"></i>EXITED</span>';
                    title = `Exited office geofence & entered field (${ev.dist}m from office)`;
                }

                html += `
                    <div class="list-group-item px-0 py-2 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-semibold text-dark">${title}</div>
                            <div class="text-muted fs-xs"><i class="fa-regular fa-clock me-1"></i>${ev.time}</div>
                        </div>
                        <div>${badge}</div>
                    </div>
                `;
            });
            html += '</div>';
            timelineContainer.innerHTML = html;
        }

        const modal = new bootstrap.Modal(document.getElementById('geofenceLogsModal'));
        modal.show();
    };

    // EXPORT TO EXCEL / CSV
    function downloadFile(content, fileName, mimeType) {
        const blob = new Blob([content], { type: mimeType });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        setTimeout(() => {
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 200);
    }

    window.exportRouteCSV = function() {
        if (!rawPointsData || rawPointsData.length === 0) {
            alert('No route points to export.');
            return;
        }
        const empName = "{{ addslashes($employee->name ?? 'Employee') }}";
        const trackDate = "{{ $date }}";
        let csv = "Index,Timestamp,DateTime,Latitude,Longitude,Speed_kmh,Battery_pct\n";
        rawPointsData.forEach((p, idx) => {
            csv += `${idx + 1},${p.timestamp},"${p.time_full || p.time}",${p.lat},${p.lng},${p.speed_kmh || 0},${p.battery || ''}\n`;
        });
        downloadFile(csv, `STAFO_${empName.replace(/\s+/g, '_')}_${trackDate}.csv`, 'text/csv');
    };

    // Toggle Direction Arrows
    function toggleDirectionArrows() {
        if (!arrowDecorator || !leafletMap) return;
        const btn = document.getElementById('btnToggleArrows');

        if (showArrows) {
            leafletMap.removeLayer(arrowDecorator);
            showArrows = false;
            if (btn) btn.classList.remove('active');
        } else {
            leafletMap.addLayer(arrowDecorator);
            showArrows = true;
            if (btn) btn.classList.add('active');
        }
    }

    // Toggle Stoppage Markers
    function toggleStoppageMarkers() {
        if (!leafletMap || stopMarkers.length === 0) return;
        const btn = document.getElementById('btnToggleStops');

        showStops = !showStops;
        stopMarkers.forEach(m => {
            if (showStops) {
                leafletMap.addLayer(m);
            } else {
                leafletMap.removeLayer(m);
            }
        });

        if (btn) {
            if (showStops) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        }
    }

    // Toggle Speed Heatmap (Multi-color polyline)
    function toggleSpeedHeatmap() {
        if (!leafletMap || !rawPointsData || rawPointsData.length < 2) return;
        const btn = document.getElementById('btnToggleSpeedMap');
        const legend = document.getElementById('speedLegendBar');

        isSpeedHeatmapActive = !isSpeedHeatmapActive;

        if (isSpeedHeatmapActive) {
            if (mainPolyline) leafletMap.removeLayer(mainPolyline);
            if (glowPolyline) leafletMap.removeLayer(glowPolyline);
            if (arrowDecorator) leafletMap.removeLayer(arrowDecorator);

            if (!speedSegmentsGroup) {
                speedSegmentsGroup = L.featureGroup();
                for (let i = 1; i < rawPointsData.length; i++) {
                    const p1 = rawPointsData[i - 1];
                    const p2 = rawPointsData[i];
                    const spd = p2.speed_kmh || 0;

                    let color = '#3b82f6';
                    if (spd >= 5 && spd < 25) color = '#10b981';
                    else if (spd >= 25 && spd < 50) color = '#f59e0b';
                    else if (spd >= 50) color = '#ef4444';

                    const seg = L.polyline([[p1.lat, p1.lng], [p2.lat, p2.lng]], {
                        color: color,
                        weight: 5,
                        opacity: 0.95,
                        lineCap: 'round'
                    });
                    seg.bindTooltip(`${spd} km/h (${p2.time})`, { sticky: true });
                    speedSegmentsGroup.addLayer(seg);
                }
            }
            speedSegmentsGroup.addTo(leafletMap);
            if (btn) btn.classList.add('active', 'btn-primary');
            if (legend) legend.classList.remove('d-none');
        } else {
            if (speedSegmentsGroup) leafletMap.removeLayer(speedSegmentsGroup);
            if (glowPolyline) glowPolyline.addTo(leafletMap);
            if (mainPolyline) mainPolyline.addTo(leafletMap);
            if (arrowDecorator && showArrows) arrowDecorator.addTo(leafletMap);
            if (btn) btn.classList.remove('active', 'btn-primary');
            if (legend) legend.classList.add('d-none');
        }
    }

    // Toggle Camera Auto-Follow during playback
    function toggleFollowCam() {
        followCamEnabled = !followCamEnabled;
        const btn = document.getElementById('btnFollowCam');
        const text = document.getElementById('followCamText');
        if (followCamEnabled) {
            if (btn) btn.className = 'btn btn-sm btn-primary rounded-pill map-tool-btn active';
            if (text) text.innerText = 'Follow Cam: ON';
        } else {
            if (btn) btn.className = 'btn btn-sm btn-outline-secondary rounded-pill map-tool-btn';
            if (text) text.innerText = 'Follow Cam: OFF';
        }
    }

    // Toggle Fullscreen Map
    function toggleFullscreenMap() {
        const wrapper = document.getElementById('map-wrapper');
        const btn = document.getElementById('btnFullscreen');
        if (!wrapper) return;

        wrapper.classList.toggle('is-fullscreen');
        const isFull = wrapper.classList.contains('is-fullscreen');

        if (btn) {
            btn.innerHTML = isFull
                ? '<i class="fa-solid fa-compress me-1"></i> Exit'
                : '<i class="fa-solid fa-maximize me-1"></i> Fullscreen';
            btn.className = isFull
                ? 'btn btn-sm btn-dark map-tool-btn active'
                : 'btn btn-sm btn-outline-dark map-tool-btn';
        }

        setTimeout(() => {
            if (leafletMap) {
                leafletMap.invalidateSize();
                fitRouteBounds();
            }
        }, 300);
    }

    // ROUTE PLAYBACK ENGINE
    function togglePlayback() {
        if (isPlaying) {
            pausePlayback();
        } else {
            startPlayback();
        }
    }

    function startPlayback() {
        if (!rawPointsData || rawPointsData.length === 0) return;

        if (currentPlaybackIndex >= rawPointsData.length - 1) {
            currentPlaybackIndex = 0;
        }

        isPlaying = true;
        updatePlayPauseButton();

        if (playbackMarker) {
            playbackMarker.setOpacity(1);
        }

        const intervalMs = Math.max(25, Math.round(300 / playbackSpeedMultiplier));
        clearInterval(playbackInterval);

        playbackInterval = setInterval(() => {
            if (currentPlaybackIndex < rawPointsData.length - 1) {
                currentPlaybackIndex++;
                updatePlaybackDisplay();
            } else {
                pausePlayback();
            }
        }, intervalMs);
    }

    function pausePlayback() {
        isPlaying = false;
        clearInterval(playbackInterval);
        updatePlayPauseButton();
    }

    function resetPlayback() {
        pausePlayback();
        currentPlaybackIndex = 0;
        updatePlaybackDisplay();
        if (playbackMarker) {
            playbackMarker.setOpacity(0);
        }
        fitRouteBounds();
    }

    function setPlaybackSpeed(spd) {
        playbackSpeedMultiplier = parseFloat(spd) || 2;
        if (isPlaying) {
            startPlayback();
        }
    }

    function onSliderScrub(percent) {
        if (!rawPointsData || rawPointsData.length === 0) return;
        const total = rawPointsData.length - 1;
        currentPlaybackIndex = Math.min(total, Math.max(0, Math.round((percent / 100) * total)));

        if (playbackMarker) {
            playbackMarker.setOpacity(1);
        }
        updatePlaybackDisplay();
    }

    function updatePlayPauseButton() {
        const btn = document.getElementById('btnPlayPause');
        const icon = document.getElementById('playPauseIcon');
        if (!icon) return;

        if (isPlaying) {
            icon.className = 'fa-solid fa-pause';
            if (btn) btn.className = 'btn btn-warning playback-btn shadow-sm text-white';
        } else {
            icon.className = 'fa-solid fa-play';
            if (btn) btn.className = 'btn btn-primary playback-btn shadow-sm';
        }
    }

    function updatePlaybackDisplay() {
        if (!rawPointsData || rawPointsData.length === 0) return;
        const pt = rawPointsData[currentPlaybackIndex];
        const total = rawPointsData.length - 1;

        if (playbackMarker) {
            playbackMarker.setLatLng([pt.lat, pt.lng]);
            if (followCamEnabled && leafletMap) {
                leafletMap.panTo([pt.lat, pt.lng], { animate: false });
            }
        }

        const slider = document.getElementById('playbackSlider');
        if (slider) {
            slider.value = total > 0 ? (currentPlaybackIndex / total) * 100 : 0;
        }

        const timeDisplay = document.getElementById('playbackTimeDisplay');
        if (timeDisplay) {
            timeDisplay.innerText = pt.time || pt.time_full || '--:--';
        }

        const speedDisplay = document.getElementById('playbackSpeedValue');
        if (speedDisplay) {
            speedDisplay.innerText = pt.speed_kmh || 0;
        }

        const batteryDisplay = document.getElementById('playbackBattery');
        if (batteryDisplay && pt.battery) {
            batteryDisplay.innerText = pt.battery;
        }

        const distanceDisplay = document.getElementById('playbackDistance');
        if (distanceDisplay && totalTripDistanceKm > 0) {
            const currentDist = (totalTripDistanceKm * (currentPlaybackIndex / total)).toFixed(2);
            distanceDisplay.innerText = currentDist;
        }
    }

    // REAL-TIME AUTO-POLLING ENGINE (15s Sync)
    function startLivePolling() {
        if (livePollingTimer) clearInterval(livePollingTimer);
        livePollingTimer = setInterval(pollLiveLocation, 15000);
    }

    function pollLiveLocation() {
        if (!isToday || !employeeId) return;

        const url = `{{ url('company/employee/location-live') }}/${employeeId}?date=${currentDate}&since_timestamp=${lastKnownTimestamp || ''}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.status && data.new_points && data.new_points.length > 0) {
                    handleNewLivePoints(data.new_points);
                }
            })
            .catch(err => console.error('Live polling error:', err));
    }

    function handleNewLivePoints(newPoints) {
        newPoints.forEach(pt => {
            const prevPt = rawPointsData[rawPointsData.length - 1];
            if (prevPt) {
                const dist = calcDistanceMeters(prevPt.lat, prevPt.lng, pt.lat, pt.lng);
                const dt = Math.max(1, (pt.timestamp - prevPt.timestamp));
                pt.speed_kmh = Math.min(120, Math.round((dist / dt) * 3.6));
                totalTripDistanceKm += (dist / 1000);
            } else {
                pt.speed_kmh = 0;
            }

            rawPointsData.push(pt);
            routeCoordinates.push({ lat: pt.lat, lng: pt.lng });
            lastKnownTimestamp = pt.timestamp;
        });

        const updatedLatLngs = routeCoordinates.map(p => [p.lat, p.lng]);
        if (mainPolyline) mainPolyline.setLatLngs(updatedLatLngs);
        if (glowPolyline) glowPolyline.setLatLngs(updatedLatLngs);
        renderDirectionArrows();

        const latestPt = rawPointsData[rawPointsData.length - 1];
        if (endMarker && latestPt) {
            endMarker.setLatLng([latestPt.lat, latestPt.lng]);
            const timeEl = document.getElementById('popupLatestTime');
            const coordsEl = document.getElementById('popupLatestCoords');
            const battEl = document.getElementById('popupLatestBattery');
            if (timeEl) timeEl.innerText = latestPt.time_full || latestPt.time;
            if (coordsEl) coordsEl.innerText = `${latestPt.lat.toFixed(5)}, ${latestPt.lng.toFixed(5)}`;
            if (battEl && latestPt.battery) battEl.innerText = latestPt.battery;
        }

        const totalPointsEl = document.getElementById('totalPointsCount');
        if (totalPointsEl) totalPointsEl.innerText = rawPointsData.length;

        const latestCard = document.getElementById('latestPingTimeCard');
        if (latestCard && latestPt) latestCard.innerText = latestPt.time;

        const distCard = document.getElementById('totalDistCard');
        if (distCard) distCard.innerText = totalTripDistanceKm.toFixed(2);

        const battCard = document.getElementById('latestPingBatteryCard');
        if (battCard && latestPt && latestPt.battery) battCard.innerText = latestPt.battery;

        calculateOfficeGeofencePresence();
        checkOverspeedViolations(currentSpeedLimit);
        const currentRate = document.getElementById('taRateInput') ? parseFloat(document.getElementById('taRateInput').value) || 8 : 8;
        calculateReimbursement(currentRate);
        calculateRouteEfficiency();
    }

    // Pan and focus on a specific stoppage from the table
    window.focusOnStop = function(stopNumber) {
        const marker = stopMarkersMap[stopNumber];
        if (marker && leafletMap) {
            document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
            leafletMap.setView(marker.getLatLng(), 18, { animate: true });
            setTimeout(() => {
                marker.openPopup();
            }, 300);
        }
    };

    // Initialize on DOM ready
    $(document).ready(function() {
        initLeafletMap();
        resolveAllHaltAddresses();
    });
</script>
@endif

<script>
    function filterEmployeeLocation() {
        var employeeId = $('#employee_id').val();
        var date = $('#date').val();
        if (!employeeId) {
            alert('Please select an employee.');
            return;
        }
        var baseUrl = "{{ url('company/employee/location') }}";
        window.location.href = baseUrl + '/' + employeeId + (date ? '?date=' + date : '');
    }

    $('#filterBtn').on('click', function() {
        filterEmployeeLocation();
    });

    $('#employee_id').on('change', function() {
        filterEmployeeLocation();
    });

    $('#date').on('change', function() {
        filterEmployeeLocation();
    });

    function toggleMapGeoStatus(employeeId, isChecked, employeeName) {
        const newStatus = isChecked ? '1' : '0';
        const switchEl = document.getElementById('mapGeoSwitch');
        const badgeEl = document.getElementById('mapGeoBadge');
        const textEl = document.getElementById('mapGeoText');
        const iconEl = document.getElementById('mapGeoIcon');

        if (switchEl) switchEl.disabled = true;
        if (badgeEl) {
            badgeEl.className = 'badge bg-warning text-dark rounded-pill px-2 py-0.5';
            if (textEl) textEl.innerText = 'Updating...';
            if (iconEl) iconEl.className = 'fa-solid fa-spinner fa-spin me-1';
        }

        fetch("{{ route('employee.updateGeoStatus') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                employee_id: employeeId,
                geo_status: newStatus
            })
        })
        .then(res => res.json())
        .then(data => {
            if (switchEl) switchEl.disabled = false;
            if (data.status) {
                const statusStr = String(data.geo_status || '0');
                const isNowChecked = (statusStr === '1' || statusStr === '2');
                if (switchEl) switchEl.checked = isNowChecked;
                if (badgeEl) {
                    badgeEl.className = (statusStr === '2')
                        ? 'badge bg-success text-white rounded-pill px-2 py-0.5' 
                        : ((statusStr === '1') ? 'badge bg-warning text-dark rounded-pill px-2 py-0.5' : 'badge bg-secondary text-white rounded-pill px-2 py-0.5');
                }
                if (textEl) {
                    textEl.innerText = (statusStr === '2')
                        ? 'Active / Tracking ON'
                        : ((statusStr === '1') ? 'Request Sent / Pending' : 'Inactive / Tracking OFF');
                }
                if (iconEl) {
                    iconEl.className = (statusStr === '2')
                        ? 'fa-solid fa-circle-dot me-1'
                        : ((statusStr === '1') ? 'fa-solid fa-clock me-1' : 'fa-solid fa-circle me-1');
                }

                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                if (switchEl) switchEl.checked = !isChecked;
                if (badgeEl) {
                    badgeEl.className = isChecked ? 'badge bg-secondary text-white rounded-pill px-2 py-0.5' : 'badge bg-success text-white rounded-pill px-2 py-0.5';
                }
                if (textEl) {
                    textEl.innerText = isChecked ? 'Inactive / Tracking OFF' : 'Active / Tracking ON';
                }
                alert(data.message || 'Failed to update tracking status.');
            }
        })
        .catch(err => {
            console.error(err);
            if (switchEl) {
                switchEl.disabled = false;
                switchEl.checked = !isChecked;
            }
            alert('An error occurred while updating tracking status.');
        });
    }
</script>
@endsection
