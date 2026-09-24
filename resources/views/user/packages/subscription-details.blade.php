@extends('user.layouts.app')
@section('title', 'Subscription Details | STAFO HRMS')

@section('content')
    <div class="container-fluid px-2 px-md-3 py-2">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                    <div class="p-4 text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 text-uppercase fw-semibold mb-2" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-gem me-1"></i> Active Membership
                                </span>
                                <h3 class="fw-bold text-white mb-1">{{ $package->package_name ?? 'Current Package' }}</h3>
                                <div class="text-white-50 small">
                                    {!! strip_tags($package->description ?? '') !!}
                                </div>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-white bg-opacity-20 text-white" style="width: 56px; height: 56px; font-size: 1.5rem;">
                                <i class="fa-solid fa-crown"></i>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        @php
                            $daysRemaining = round((strtotime($company->subscription_end) - time()) / 86400);
                            $isActive = ($daysRemaining >= 0);
                        @endphp

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <small class="text-muted text-uppercase fw-semibold d-block" style="font-size: 0.725rem;">Start Date</small>
                                    <h5 class="fw-bold text-dark mt-1 mb-0">
                                        <i class="fa-regular fa-calendar-check text-success me-1"></i>
                                        {{ !empty($company->subscription_start) ? date('dS F, Y', strtotime($company->subscription_start)) : 'N/A' }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <small class="text-muted text-uppercase fw-semibold d-block" style="font-size: 0.725rem;">Expiration Date</small>
                                    <h5 class="fw-bold text-dark mt-1 mb-0">
                                        <i class="fa-regular fa-calendar-xmark text-danger me-1"></i>
                                        {{ !empty($company->subscription_end) ? date('dS F, Y', strtotime($company->subscription_end)) : 'N/A' }}
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between p-3 rounded-3 border mb-4 {{ $isActive ? 'bg-success bg-opacity-10 border-success border-opacity-25' : 'bg-danger bg-opacity-10 border-danger border-opacity-25' }}">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fa-solid {{ $isActive ? 'fa-circle-check text-success' : 'fa-circle-exclamation text-danger' }} fs-3"></i>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">Subscription Status</h6>
                                    <small class="{{ $isActive ? 'text-success' : 'text-danger' }} fw-semibold">
                                        {{ $isActive ? 'Active - ' . $daysRemaining . ' days remaining' : 'Plan has expired' }}
                                    </small>
                                </div>
                            </div>
                            <div class="mt-2 mt-sm-0">
                                <a href="{{ route('company-packages.index') }}" class="btn btn-sm btn-primary px-3 py-2 fw-semibold">
                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View All Plans
                                </a>
                            </div>
                        </div>

                        <div class="text-center pt-2">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-light border px-4 py-2 text-muted fw-semibold">
                                <i class="fa-solid fa-house me-1"></i> Return to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection