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
    </style>
@endsection

@section('content')
    <div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-9 col-6">
            <h3 class="mb-4">{{ $employee->name}}'s Performance</h3>
        </div>
        <div class="col-md-3 col-6 text-end">
            <a href="{{ route('employeePerformanceAdd',$employee->id) }}" class="btn btn-success shadow-sm"><i class="fas fa-plus"></i>
                Add</a>
        </div>
    </div>
        <!-- Cards Section -->
        <div class="row g-4">
            <!-- Total Employees Card -->
            <div class="col-xl-4 col-md-4">
                <div class="dashboard-card card h-100"> 
                    @php
                        $month = '';
                        $monthValue = '';
                    @endphp
                    @foreach($employeePerformances as $performance)
                        @if($monthValue != '' && $monthValue != $performance->month)
                            </div> 
                            </div>
                            <div class="col-xl-4 col-md-4">  
                            <div class="dashboard-card card h-100">
                        @endif
                        <div class="card-text">
                            @php 
                                if($performance->month == 1) {
                                    $month = 'January';
                                } elseif ($performance->month == 2) {
                                    $month = 'February';
                                } elseif ($performance->month == 3) {
                                    $month = 'March';
                                } elseif ($performance->month == 4) {
                                    $month = 'April';
                                } elseif ($performance->month == 5) {
                                    $month = 'May';
                                } elseif ($performance->month == 6) {
                                    $month = 'June';
                                } elseif ($performance->month == 7) {
                                    $month = 'July';
                                } elseif ($performance->month == 8) {
                                    $month = 'August';
                                } elseif ($performance->month == 9) {
                                    $month = 'September';
                                } elseif ($performance->month == 10) {
                                    $month = 'October';
                                } elseif ($performance->month == 11) {
                                    $month = 'November';
                                } else {
                                    $month = 'December';
                                }
                                if($monthValue != $performance->month) {
                                    $monthValue = $performance->month; 
                                
                                    echo '<h6>'.$month.'</h6>';
                                }
                            @endphp
                            {{ $performance->performancetype->name }} - {{ $performance->marks}}
                        </div>
                        
                    @endforeach
                </div>
            </div>

        </div>


    </div>
@endsection
