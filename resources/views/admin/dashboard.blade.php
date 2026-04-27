@extends('admin.layouts.layout')

@section('title', 'Home')

@section('content')
    <!-- Sale & Revenue Start -->
    <div class="container-fluid pt-4 px-4 dash-main">
        <div class="row g-4">
            <div class="col-sm-6 col-xl-3">
                <div class="bg-dark rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa fa-building fa-3x text-white"></i> <!-- Icon for Company -->
                    <div class="ms-4">
                        <p class="mb-2 text-white">Company</p>
                        <!-- Check if company count is not empty -->
                        <h6 class="mb-0 text-white">{{ $companyCount > 0 ? $companyCount : 'No data available' }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="bg-primary rounded d-flex align-items-center justify-content-between p-4">
                    <i class="fa fa-users fa-3x text-white"></i> <!-- Icon for Employee -->
                    <div class="ms-4">
                        <p class="mb-2 text-white">Employee</p>
                        <!-- Check if employee count is not empty -->
                        <h6 class="mb-0 text-white">{{ $employeeCount > 0 ? $employeeCount : 'No data available' }}</h6>
                    </div>
                </div>
            </div>
            <!-- <div class="col-sm-6 col-xl-3">
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                                                        <i class="fa fa-chart-area fa-3x text-primary"></i>
                                                        <div class="ms-3">
                                                            <p class="mb-2">Today Revenue</p>
                                                            <h6 class="mb-0">$1234</h6>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <div class="col-sm-6 col-xl-3">
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                                                        <i class="fa fa-dollar-sign fa-3x text-primary"></i>
                                                        <div class="ms-3">
                                                            <p class="mb-2">Total Revenue</p>
                                                            <h6 class="mb-0">$1234</h6>
                                                        </div>
                                                    </div>
                                                </div>-->
        </div>
    </div>
    <!-- Sale & Revenue End -->
@endsection