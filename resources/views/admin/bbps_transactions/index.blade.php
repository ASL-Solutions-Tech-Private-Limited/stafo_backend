@extends('admin.layouts.layout')

@section('title', 'BBPS Transaction Report')

@section('css')
<style>
    /* Metric Cards */
    .kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.07);
    }
    .kpi-icon-wrap {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Filter Card */
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        padding: 16px 20px;
    }

    /* Table Card */
    .table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }
    .table thead th {
        background-color: #f8fafc !important;
        color: #475569;
        font-weight: 700;
        font-size: 0.76rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 14px;
        white-space: nowrap;
    }
    .table tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Status Badges */
    .badge-status {
        font-size: 0.74rem;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .badge-status-success {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .badge-status-pending {
        background-color: #fef9c3;
        color: #a16207;
        border: 1px solid #fef08a;
    }
    .badge-status-failed {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .badge-status-process {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    /* User Type Badge */
    .badge-user-type {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 4px;
        text-transform: uppercase;
    }
    .badge-type-employee {
        background-color: #e0e7ff;
        color: #4338ca;
    }
    .badge-type-company {
        background-color: #f3e8ff;
        color: #7e22ce;
    }

    /* Monospace Txn ID */
    .txn-code {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        font-size: 0.82rem;
        color: #1e293b;
        background: #f1f5f9;
        padding: 3px 6px;
        border-radius: 4px;
        font-weight: 600;
    }

    /* JSON Box in Modal */
    .json-code-box {
        background: #0f172a;
        color: #38bdf8;
        border-radius: 8px;
        padding: 14px;
        font-family: monospace;
        font-size: 0.82rem;
        max-height: 280px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-all;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-dark fw-bold">
                <i class="fa-solid fa-receipt text-primary me-2"></i>BBPS Transaction Report
            </h4>
           
        </div>
        <div class="mt-2 mt-sm-0 d-flex gap-2">
            <a href="{{ route('admin.bbps.commission.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-percent me-1"></i> Commission Setup
            </a>
            <a href="{{ route('admin.bbps.transactions.export', request()->all()) }}" class="btn btn-outline-success btn-sm">
                <i class="fa-solid fa-file-csv me-1"></i> Export to CSV
            </a>
            <a href="{{ route('admin.bbps.transactions.index') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-rotate me-1"></i> Refresh
            </a>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Volume -->
        <div class="col-xl-3 col-sm-6">
            <div class="kpi-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-semibold text-uppercase">Total Transactions</div>
                    <h3 class="fw-bold mb-0 text-dark mt-1">{{ number_format($totalCount) }}</h3>
                    <div class="text-muted small mt-1">Value: <span class="fw-bold text-dark">₹{{ number_format($totalAmount, 2) }}</span></div>
                </div>
                <div class="kpi-icon-wrap bg-primary-subtle text-primary">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
        </div>

        <!-- 2. Success -->
        <div class="col-xl-3 col-sm-6">
            <div class="kpi-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-success small fw-semibold text-uppercase">Successful</div>
                    <h3 class="fw-bold mb-0 text-success mt-1">{{ number_format($successCount) }}</h3>
                    <div class="text-muted small mt-1">Value: <span class="fw-bold text-success">₹{{ number_format($successAmount, 2) }}</span></div>
                </div>
                <div class="kpi-icon-wrap bg-success-subtle text-success">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        <!-- 3. Pending -->
        <div class="col-xl-3 col-sm-6">
            <div class="kpi-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-warning small fw-semibold text-uppercase">Pending</div>
                    <h3 class="fw-bold mb-0 text-warning mt-1">{{ number_format($pendingCount) }}</h3>
                    <div class="text-muted small mt-1">Value: <span class="fw-bold text-warning">₹{{ number_format($pendingAmount, 2) }}</span></div>
                </div>
                <div class="kpi-icon-wrap bg-warning-subtle text-warning">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
        </div>

        <!-- 4. Failed -->
        <div class="col-xl-3 col-sm-6">
            <div class="kpi-card d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-danger small fw-semibold text-uppercase">Failed / Error</div>
                    <h3 class="fw-bold mb-0 text-danger mt-1">{{ number_format($failedCount) }}</h3>
                    <div class="text-muted small mt-1">Value: <span class="fw-bold text-danger">₹{{ number_format($failedAmount, 2) }}</span></div>
                </div>
                <div class="kpi-icon-wrap bg-danger-subtle text-danger">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-card mb-4">
        <form method="GET" action="{{ route('admin.bbps.transactions.index') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <!-- Search Keyword -->
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Search Keyword</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="Txn ID, PayU ID, Bill info...">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="success" {{ $status === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="process" {{ $status === 'process' ? 'selected' : '' }}>Processing</option>
                    </select>
                </div>

                <!-- User Type Filter -->
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">User Type</label>
                    <select class="form-select form-select-sm" name="user_type">
                        <option value="">All Types</option>
                        <option value="employee" {{ $userType === 'employee' ? 'selected' : '' }}>Employee</option>
                        <option value="company" {{ $userType === 'company' ? 'selected' : '' }}>Company</option>
                    </select>
                </div>

                <!-- Company Filter -->
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Company</label>
                    <select class="form-select form-select-sm" name="company_id">
                        <option value="">All Companies</option>
                        @foreach($companies as $comp)
                            <option value="{{ $comp->id }}" {{ (string)$companyId === (string)$comp->id ? 'selected' : '' }}>
                                {{ $comp->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <label class="form-label small fw-semibold text-muted mb-1">From</label>
                            <input type="date" class="form-control form-control-sm" name="date_from" value="{{ $dateFrom }}">
                        </div>
                        <div class="flex-grow-1">
                            <label class="form-label small fw-semibold text-muted mb-1">To</label>
                            <input type="date" class="form-control form-control-sm" name="date_to" value="{{ $dateTo }}">
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                    <a href="{{ route('admin.bbps.transactions.index') }}" class="btn btn-light btn-sm px-3">
                        <i class="fa-solid fa-filter-circle-xmark me-1"></i> Clear
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fa-solid fa-filter me-1"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Transactions Data Table -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>Transaction ID</th>
                        <th>Payer / User</th>
                        <th>Company</th>
                        <th>Category / Bill Info</th>
                        <th class="text-end">Amount</th>
                        <th class="text-center">Status</th>
                        <th>Date &amp; Time</th>
                        <th class="text-center" style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                        @php
                            $st = strtolower($txn->status ?? 'pending');
                            $badgeClass = match($st) {
                                'success' => 'badge-status-success',
                                'failed', 'failure' => 'badge-status-failed',
                                'process' => 'badge-status-process',
                                default => 'badge-status-pending',
                            };
                            $iconClass = match($st) {
                                'success' => 'fa-solid fa-circle-check',
                                'failed', 'failure' => 'fa-solid fa-circle-xmark',
                                'process' => 'fa-solid fa-gear fa-spin',
                                default => 'fa-solid fa-clock',
                            };

                            // Resolve display name
                            $payerName = '-';
                            $companyName = '-';
                            if ($txn->user_type === 'employee' && $txn->employee) {
                                $payerName = $txn->employee->name ?? ('Employee #' . $txn->user_id);
                                $companyName = $txn->employee->company->company_name ?? '-';
                            } elseif ($txn->user_type === 'company' && $txn->company) {
                                $payerName = $txn->company->company_name ?? ('Company #' . $txn->user_id);
                                $companyName = $txn->company->company_name ?? '-';
                            } else {
                                $payerName = 'User #' . $txn->user_id;
                            }
                        @endphp
                        <tr>
                            <!-- # ID -->
                            <td class="text-center text-muted fw-bold">{{ $txn->id }}</td>

                            <!-- Txn ID with copy -->
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="txn-code">{{ $txn->txnid }}</span>
                                    <button type="button" class="btn btn-sm btn-link text-muted p-0" title="Copy Txn ID" onclick="navigator.clipboard.writeText('{{ $txn->txnid }}'); alert('Copied!');">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                                @if(!empty($txn->payu_payment_id))
                                    <div class="small text-muted mt-1">
                                        <span class="fw-semibold">PayU ID:</span> {{ $txn->payu_payment_id }}
                                    </div>
                                @endif
                            </td>

                            <!-- Payer -->
                            <td>
                                <div class="fw-bold text-dark">{{ $payerName }}</div>
                                <div class="mt-1">
                                    <span class="badge-user-type {{ $txn->user_type === 'employee' ? 'badge-type-employee' : 'badge-type-company' }}">
                                        {{ ucfirst($txn->user_type ?? 'N/A') }}
                                    </span>
                                    <span class="text-muted small ms-1">#{{ $txn->user_id }}</span>
                                </div>
                            </td>

                            <!-- Company -->
                            <td>
                                <span class="fw-medium text-secondary">
                                    <i class="fa-regular fa-building me-1 text-muted"></i>{{ $companyName }}
                                </span>
                            </td>

                            <!-- Product / Category Info -->
                            <td>
                                <div class="text-dark fw-medium">{{ $txn->product_info ?? 'BBPS Payment' }}</div>
                            </td>

                            <!-- Amount -->
                            <td class="text-end">
                                <span class="fw-bold fs-6 text-dark">₹{{ number_format((float)$txn->amount, 2) }}</span>
                            </td>

                            <!-- Status -->
                            <td class="text-center">
                                <span class="badge-status {{ $badgeClass }}">
                                    <i class="{{ $iconClass }}"></i> {{ ucfirst($st) }}
                                </span>
                            </td>

                            <!-- Date & Time -->
                            <td>
                                <div class="text-dark fw-medium">{{ $txn->created_at ? $txn->created_at->format('d M Y') : '-' }}</div>
                                <div class="text-muted small">{{ $txn->created_at ? $txn->created_at->format('h:i A') : '' }}</div>
                            </td>

                            <!-- Action -->
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1" title="View Full Details" onclick="viewTransactionDetails({{ $txn->id }})">
                                    <i class="fa-solid fa-eye me-1"></i> Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="py-3">
                                    <i class="fa-solid fa-receipt fa-3x text-muted mb-3 d-block opacity-50"></i>
                                    <h6 class="fw-bold text-muted">No BBPS Transactions Found</h6>
                                    <p class="text-muted small mb-0">Try clearing or adjusting your search filters above.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
            <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} records
                </div>
                <div>
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

<!-- Modal: Transaction Details -->
<div class="modal fade" id="txnDetailModal" tabindex="-1" aria-labelledby="txnDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark" id="txnDetailModalLabel">
                    <i class="fa-solid fa-circle-info text-primary me-2"></i>Transaction Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="txnModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.viewTransactionDetails = function(id) {
        const modalEl = document.getElementById('txnDetailModal');
        let modalInstance;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        }

        const modalBody = document.getElementById('txnModalBody');
        
        modalBody.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="text-muted small mt-2 fw-medium">Loading transaction details...</div>
            </div>
        `;

        if (modalInstance) {
            modalInstance.show();
        } else if (window.jQuery && typeof $(modalEl).modal === 'function') {
            $(modalEl).modal('show');
        }

        fetch(`{{ url('admin/bbps-transactions') }}/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(res => {
                if (res.status && res.data) {
                    const d = res.data;
                    let statusBadge = '';
                    if (d.status === 'success') {
                        statusBadge = '<span class="badge-status badge-status-success"><i class="fa-solid fa-circle-check"></i> SUCCESS</span>';
                    } else if (d.status === 'failed' || d.status === 'failure') {
                        statusBadge = '<span class="badge-status badge-status-failed"><i class="fa-solid fa-circle-xmark"></i> FAILED</span>';
                    } else if (d.status === 'process') {
                        statusBadge = '<span class="badge-status badge-status-process"><i class="fa-solid fa-gear fa-spin"></i> PROCESSING</span>';
                    } else {
                        statusBadge = '<span class="badge-status badge-status-pending"><i class="fa-solid fa-clock"></i> PENDING</span>';
                    }

                    modalBody.innerHTML = `
                        <!-- Top Highlights -->
                        <div class="p-3 bg-light rounded-3 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border">
                            <div>
                                <div class="text-muted small fw-semibold">TRANSACTION ID</div>
                                <div class="fs-5 fw-bold font-monospace text-dark">${d.txnid || '-'}</div>
                            </div>
                            <div class="text-end">
                                <div class="text-muted small fw-semibold">TRANSACTION AMOUNT</div>
                                <div class="fs-4 fw-bold text-success">₹${d.amount}</div>
                            </div>
                            <div>
                                ${statusBadge}
                            </div>
                        </div>

                        <!-- 2-Column Info Grid -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100 bg-white">
                                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                        <i class="fa-solid fa-user me-2 text-primary"></i>Payer Information
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><td class="text-muted" style="width: 100px;">User Type:</td><td class="fw-semibold text-dark">${d.user_type || '-'}</td></tr>
                                        <tr><td class="text-muted">User ID:</td><td class="fw-semibold text-dark">#${d.user_id || '-'}</td></tr>
                                        <tr><td class="text-muted">Name:</td><td class="fw-semibold text-dark">${d.user_name || '-'}</td></tr>
                                        <tr><td class="text-muted">Email:</td><td class="fw-semibold text-dark">${d.user_email || '-'}</td></tr>
                                        <tr><td class="text-muted">Phone:</td><td class="fw-semibold text-dark">${d.user_phone || '-'}</td></tr>
                                        <tr><td class="text-muted">Company:</td><td class="fw-semibold text-dark">${d.company_name || '-'}</td></tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 h-100 bg-white">
                                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                        <i class="fa-solid fa-credit-card me-2 text-primary"></i>Payment &amp; Gateway
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><td class="text-muted" style="width: 110px;">PayU Txn ID:</td><td class="fw-semibold font-monospace text-dark">${d.payu_payment_id || '-'}</td></tr>
                                        <tr><td class="text-muted">Product / Bill:</td><td class="fw-semibold text-dark">${d.product_info || '-'}</td></tr>
                                        <tr><td class="text-muted">Created At:</td><td class="fw-semibold text-dark">${d.created_at || '-'}</td></tr>
                                        <tr><td class="text-muted">Updated At:</td><td class="fw-semibold text-dark">${d.updated_at || '-'}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Hashes Section -->
                        <div class="mb-3">
                            <div class="card bg-light border">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                                        <i class="fa-solid fa-shield-halved me-1 text-primary"></i>Security &amp; PayU Hashes
                                    </h6>
                                    <div class="mb-2">
                                        <span class="fw-semibold small text-muted">Calculated PayU Hash:</span>
                                        <div class="font-monospace small text-break text-dark bg-white p-2 rounded border mt-1">${d.hash_calculated || 'N/A'}</div>
                                    </div>
                                    <div>
                                        <span class="fw-semibold small text-muted">Received PayU Hash:</span>
                                        <div class="font-monospace small text-break text-dark bg-white p-2 rounded border mt-1">${d.hash_received || 'N/A'}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Raw PayU Response JSON -->
                        ${d.payu_response ? `
                            <div>
                                <label class="form-label small fw-semibold text-muted text-uppercase">
                                    <i class="fa-solid fa-code me-1 text-primary"></i>PayU Gateway JSON Response:
                                </label>
                                <div class="json-code-box">${d.payu_response}</div>
                            </div>
                        ` : ''}
                    `;
                } else {
                    modalBody.innerHTML = `<div class="alert alert-danger mb-0">${res.message || 'Error loading transaction details.'}</div>`;
                }
            })
            .catch(err => {
                modalBody.innerHTML = `<div class="alert alert-danger mb-0">Failed to fetch transaction details: ${err.message}</div>`;
            });
    };
</script>
@endsection
