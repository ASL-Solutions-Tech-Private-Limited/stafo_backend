@extends('user.layouts.app')
@section('title', 'Refer & Earn | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <h3 class="fw-bold text-dark mb-1">Refer & Earn Program</h3>
                <p class="text-muted small mb-0">Share your exclusive referral code with other organizations and track signups</p>
            </div>
        </div>

        <!-- Referral Stats Card -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6">
                <div class="p-4 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25 h-100">
                    <span class="text-primary fw-bold small text-uppercase d-block mb-1">Your Unique Referral Code</span>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <h2 class="fw-bold text-primary mb-0 letter-spacing">{{ $company->referral_code ?? 'N/A' }}</h2>
                        <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyReferralCode('{{ $company->referral_code }}')">
                            <i class="fa-solid fa-copy me-1"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="p-4 bg-light rounded-4 border h-100">
                    <span class="text-muted fw-semibold small text-uppercase d-block mb-1">Total Organizations Referred</span>
                    <h2 class="fw-bold text-dark mb-0 mt-2">{{ $rcount }} <span class="fs-6 fw-normal text-muted">Companies</span></h2>
                </div>
            </div>
        </div>

        <!-- Referred Companies Table -->
        <div class="mt-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-building-user text-primary"></i> Referred Companies List
            </h5>

            @if($rlist->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 70px;" class="text-center">#</th>
                                <th>Company Name</th>
                                <th>Email Address</th>
                                <th>Contact Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rlist as $idx => $referrer)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px;">
                                                <i class="fa-solid fa-building"></i>
                                            </div>
                                            <span class="fw-bold text-dark">{{ $referrer->company_name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $referrer->email }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $referrer->mobile_no }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 bg-light rounded-4 border text-muted">
                    <i class="fa-solid fa-gift fs-2 mb-2 d-block opacity-50"></i>
                    No referred companies yet. Share your code to earn rewards!
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    function copyReferralCode(code) {
        if (!code) return;
        navigator.clipboard.writeText(code).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Referral code copied to clipboard.',
                timer: 1500,
                showConfirmButton: false
            });
        });
    }
</script>
@endsection
