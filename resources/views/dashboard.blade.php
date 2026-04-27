@extends('user.layouts.app')

@section('css')
    <style>
        /* Keyframes for confetti explosion */
        @keyframes confetti-explosion {
            0% {
                top: -50px;
                opacity: 1;
                transform: rotate(0deg);
            }

            100% {
                top: 100vh;
                /* Falls to bottom */
                opacity: 0;
                transform: rotate(360deg);
            }
        }

        /* Confetti elements */
        .confetti {
            position: absolute;
            top: -10px;
            /* Start from top of the container */
            width: 10px;
            height: 10px;
            background-color: #FF6347;
            /* Red Confetti */
            border-radius: 50%;
            animation: confetti-explosion 4s infinite ease-in-out;
            opacity: 0;
        }

        /* Variation in confetti for multiple colors */
        .confetti:nth-child(2) {
            background-color: #FFD700;
            /* Yellow Confetti */
            animation-delay: 0.5s;
        }

        .confetti:nth-child(3) {
            background-color: #32CD32;
            /* Green Confetti */
            animation-delay: 1s;
        }

        .confetti:nth-child(4) {
            background-color: #00BFFF;
            /* Blue Confetti */
            animation-delay: 1.5s;
        }

        .confetti:nth-child(5) {
            background-color: #FF1493;
            /* Pink Confetti */
            animation-delay: 2s;
        }

        .confetti:nth-child(6) {
            background-color: #FF4500;
            /* Orange Confetti */
            animation-delay: 2.5s;
        }

        /* Confetti spread across the screen */
        .birthday-card,
        .anniversary-card {
            position: relative;
            overflow: hidden;
        }

        /* Card hover effects */
        .carousel-card.birthday:hover,
        .carousel-card.anniversary:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease-in-out;
        }

        /* .carousel-card img {
                                                                                    width: 100%;
                                                                                    height: auto;
                                                                                    border-radius: 50%;
                                                                                    margin-bottom: 10px;
                                                                                } */

        .carousel-card h5 {
            font-size: 20px;
            color: #fff;
            font-weight: bold;
        }

        .carousel-card p {
            font-size: 16px;
            color: #fff;
        }

        @keyframes shiver {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-3px) rotate(-0.9deg);
            }

            50% {
                transform: translateX(3px) rotate(0.9deg);
            }

            75% {
                transform: translateX(-3px) rotate(-0.9deg);
            }

            100% {
                transform: translateX(0);
            }
        }

        .shiver {
            animation: shiver .8s infinite ease-in-out;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4">Dashboard Overview</h3>

        <!-- Cards Section -->
        <div class="row g-4">
            <!-- Total Employees Card -->
            <div class="col-xl-4 col-md-4">
                <div class="dashboard-card card h-100">
                    <div class="icon-box"><i class="fa-solid fa-users"></i></div>
                    <div class="card-text">
                        <h6>Total Employees</h6>
                        <h2>{{ $employeeCount }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-4">
                <div class="dashboard-card card h-100 present">
                    <div class="icon-box"><i class="fa-solid fa-user-check"></i></div>
                    <div class="card-text">
                        <h6>Today Present</h6>
                        <h2>{{ $presentCount }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-4">
                <div class="dashboard-card card h-100 absent">
                    <div class="icon-box"><i class="fa-solid fa-user-times"></i></div>
                    <div class="card-text">
                        <h6>Absent</h6>
                        <h2>{{ $absentCount }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sliders Section for Birthdays and Anniversaries -->
        <div class="row mt-4">
            <!-- Birthdays Slider -->
            <div class="col-md-12">
                <div id="birthdayCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($birthday->chunk(3) as $chunkIndex => $chunk)
                                        <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
                                            <div class="row justify-content-center">
                                                @foreach ($chunk as $emp)
                                                                        @php
                                                                            $isBirthdayMonth = \Carbon\Carbon::parse($emp->date_of_birth)->format('m') == \Carbon\Carbon::now()->format('m');
                                                                        @endphp
                                                                        <div class="col-md-4">
                                                                            <div
                                                                                class="carousel-card birthday birthday-card {{ $isBirthdayMonth ? 'shiver' : '' }}">
                                                                                <!-- Confetti explosion for Birthday -->
                                                                                <div class="confetti"></div>
                                                                                <div class="confetti" style="animation-delay: 0.5s;"></div>
                                                                                <div class="confetti" style="animation-delay: 1s;"></div>
                                                                                <div class="confetti" style="animation-delay: 1.5s;"></div>
                                                                                <div class="confetti" style="animation-delay: 2s;"></div>
                                                                                @if ($emp->image && file_exists(public_path('uploads/employees/' . $emp->image)))
                                                                                <img src="{{ asset('uploads/employees/' . $emp->image) }}"
                                                                                    alt="{{ $emp->name }}" class="img-fluid rounded-circle">

                                                                                @else
                                                                                    <img src="{{ asset('img/default-man.png') }}" alt="Current Logo"
                                                                                        class="img-thumbnail" width="30px" height="30px">
                                                                                @endif

                                                                                <h5>{{ $emp->name }} -
                                                                                    {{ \Carbon\Carbon::parse($emp->date_of_birth)->format('M d') }}
                                                                                </h5>
                                                                                <p>Happy Birthday! 🎉</p>
                                                                            </div>
                                                                        </div>
                                                @endforeach
                                            </div>
                                        </div>

                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#birthdayCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#birthdayCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>

            </div>
        </div>

        <!-- Anniversaries Slider -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div id="anniversaryCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach ($anniversary->chunk(3) as $chunkIndex => $chunk)
                            <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
                                <div class="row justify-content-center">
                                    @foreach ($chunk as $emp)
                                        <div class="col-md-4">
                                            <div class="carousel-card anniversary anniversary-card">
                                                <!-- Confetti explosion for Anniversary -->
                                                <div class="confetti"></div>
                                                <div class="confetti" style="animation-delay: 0.5s;"></div>
                                                <div class="confetti" style="animation-delay: 1s;"></div>
                                                <div class="confetti" style="animation-delay: 1.5s;"></div>
                                                <div class="confetti" style="animation-delay: 2s;"></div>

                                                @if ($emp->image && file_exists(public_path('uploads/employees/' . $emp->image)))
                                                <img src="{{ asset('uploads/employees/' . $emp->image) }}"
                                                    alt="{{ $emp->name }}" class="img-fluid rounded-circle">

                                                @else
                                                    <img src="{{ asset('img/default-man.png') }}" alt="Current Logo"
                                                        class="img-thumbnail" width="30px" height="30px">
                                                @endif
                                                <h5>{{ $emp->name }} -
                                                    {{ \Carbon\Carbon::parse($emp->date_of_joining)->format('M d') }}
                                                </h5>
                                                <p>Happy Work Anniversary! 🎊</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#anniversaryCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#anniversaryCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection