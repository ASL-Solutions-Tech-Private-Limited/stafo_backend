@extends('admin.layouts.layout')

@section('title', 'Contact Form Submissions')

@section('content')
    <div class="container-fluid p-4">
        
        <!-- Top Title & Stats Banner -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold mb-1" style="color: #1d274b;">Contact Form Submissions</h3>
                <p class="text-muted mb-0">Manage and follow up on customer inquiries, leads, and demo requests.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="fa-solid fa-print me-1"></i> Print
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="exportTableToCSV('contact_leads.csv')">
                    <i class="fa-solid fa-file-csv me-1"></i> Export CSV
                </button>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: #eef2ff; color: #4361ee; min-width: 50px; height: 50px;">
                        <i class="fa-solid fa-inbox fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Inquiries</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $submissions->total() }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: #ecfdf5; color: #10b981; min-width: 50px; height: 50px;">
                        <i class="fa-solid fa-user-check fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Showing Results</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $submissions->count() }} Leads</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: #fef2f2; color: #ef4444; min-width: 50px; height: 50px;">
                        <i class="fa-solid fa-clock-rotate-left fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Latest Lead</div>
                        <h6 class="fw-bold mb-0 text-dark text-truncate" style="max-width: 220px;">
                            {{ $submissions->first() ? $submissions->first()->created_at->diffForHumans() : 'No inquiries yet' }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar Card -->
        <div class="card shadow-sm border-0 rounded-3 mb-4 bg-white">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.contact_form_submissions') }}">
                    <div class="row g-2 align-items-center">
                        <!-- Name Search -->
                        <div class="col-12 col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                                <input type="text" name="name" class="form-control border-start-0" placeholder="Search by name..."
                                    value="{{ request('name') }}">
                            </div>
                        </div>

                        <!-- Email Search -->
                        <div class="col-12 col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0" placeholder="Search by email..."
                                    value="{{ request('email') }}">
                            </div>
                        </div>

                        <!-- Phone Search -->
                        <div class="col-12 col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-phone text-muted"></i></span>
                                <input type="text" name="phone" class="form-control border-start-0" placeholder="Search by phone..."
                                    value="{{ request('phone') }}">
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="col-12 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.contact_form_submissions') }}" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Submissions Table Card -->
        <div class="card shadow-sm border-0 rounded-3 bg-white">
            <div class="card-body p-0">
                @if ($submissions->isEmpty())
                    <div class="text-center py-5">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 70px; height: 70px;">
                            <i class="fa-solid fa-inbox text-muted fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-dark">No Submissions Found</h5>
                        <p class="text-muted small">No inquiries match your current filter criteria.</p>
                        <a href="{{ route('admin.contact_form_submissions') }}" class="btn btn-sm btn-outline-primary">View All Submissions</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="submissionsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Lead Info</th>
                                    <th>Contact Details</th>
                                    <th>Message / Inquiry</th>
                                    <th>Date & Time</th>
                                    <th style="width: 110px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($submissions as $index => $submission)
                                    @php
                                        $initials = collect(explode(' ', $submission->full_name))
                                            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                                            ->take(2)
                                            ->implode('');
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $submission->phone);
                                    @endphp
                                    <tr>
                                        <!-- Index -->
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $submissions->firstItem() + $index }}</span>
                                        </td>

                                        <!-- Lead Name with Avatar -->
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" 
                                                     style="background: #eef2ff; width: 36px; height: 36px; min-width: 36px; font-size: 13px;">
                                                    {{ $initials ?: 'U' }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">{{ $submission->full_name }}</div>
                                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">Website Lead</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Contact details -->
                                        <td>
                                            <div class="d-flex flex-column gap-1 small">
                                                <a href="mailto:{{ $submission->email }}" class="text-decoration-none text-dark fw-semibold">
                                                    <i class="fa-solid fa-envelope text-primary me-1"></i> {{ $submission->email }}
                                                </a>
                                                <div class="d-flex align-items-center gap-2">
                                                    <a href="tel:{{ $submission->phone }}" class="text-decoration-none text-muted">
                                                        <i class="fa-solid fa-phone text-success me-1"></i> {{ $submission->phone }}
                                                    </a>
                                                    @if($cleanPhone)
                                                        <a href="https://wa.me/91{{ $cleanPhone }}" target="_blank" class="text-success" title="Chat on WhatsApp">
                                                            <i class="fa-brands fa-whatsapp"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Message Preview -->
                                        <td>
                                            <div class="text-muted small" style="max-width: 380px;">
                                                <span class="d-inline-block text-truncate w-100" title="{{ $submission->message }}">
                                                    {{ $submission->message ?: 'No message provided' }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Date -->
                                        <td>
                                            <div class="small fw-semibold text-dark">{{ $submission->created_at->format('d M Y') }}</div>
                                            <div class="text-muted" style="font-size: 11px;">{{ $submission->created_at->format('h:i A') }} ({{ $submission->created_at->diffForHumans() }})</div>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <!-- View Modal Trigger -->
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewLeadModal{{ $submission->id }}"
                                                    title="View Full Inquiry">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDeleteLead(event, {{ $submission->id }})"
                                                    title="Delete Lead">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                                
                                                <form id="delete-lead-{{ $submission->id }}" 
                                                    action="{{ route('admin.contact_form_submissions.delete', $submission->id) }}"
                                                    method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- View Details Modal for this submission -->
                                    <div class="modal fade" id="viewLeadModal{{ $submission->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $submission->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow rounded-4">
                                                <div class="modal-header bg-light border-0 py-3">
                                                    <h5 class="modal-title fw-bold text-dark" id="modalLabel{{ $submission->id }}">
                                                        <i class="fa-solid fa-address-card text-primary me-2"></i> Inquiry Details
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3 bg-light">
                                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px;">
                                                            {{ $initials ?: 'U' }}
                                                        </div>
                                                        <div>
                                                            <h5 class="fw-bold mb-0 text-dark">{{ $submission->full_name }}</h5>
                                                            <span class="badge bg-primary bg-opacity-10 text-primary small">
                                                                Received {{ $submission->created_at->format('d M Y, h:i A') }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="text-muted small fw-bold text-uppercase">Email Address</label>
                                                        <div>
                                                            <a href="mailto:{{ $submission->email }}" class="text-primary fw-semibold text-decoration-none fs-6">
                                                                <i class="fa-solid fa-envelope me-1"></i> {{ $submission->email }}
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="text-muted small fw-bold text-uppercase">Phone Number</label>
                                                        <div>
                                                            <a href="tel:{{ $submission->phone }}" class="text-dark fw-semibold text-decoration-none fs-6">
                                                                <i class="fa-solid fa-phone me-1 text-success"></i> {{ $submission->phone }}
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="text-muted small fw-bold text-uppercase">Inquiry Message</label>
                                                        <div class="p-3 bg-light rounded-3 text-dark lh-base mt-1" style="white-space: pre-wrap;">
                                                            {{ $submission->message ?: 'No additional message provided.' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <a href="mailto:{{ $submission->email }}" class="btn btn-primary btn-sm px-3 text-white">
                                                        <i class="fa-solid fa-reply me-1"></i> Reply Email
                                                    </a>
                                                    <a href="tel:{{ $submission->phone }}" class="btn btn-outline-success btn-sm px-3">
                                                        <i class="fa-solid fa-phone me-1"></i> Call Lead
                                                    </a>
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if ($submissions->hasPages())
                <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                    {{ $submissions->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function confirmDeleteLead(event, id) {
            event.preventDefault();

            Swal.fire({
                title: 'Delete this submission?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-lead-${id}`).submit();
                }
            });
        }

        // Simple Client-side CSV Exporter
        function exportTableToCSV(filename) {
            let csv = [];
            let rows = document.querySelectorAll("#submissionsTable tr");

            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");
                // Skip the last actions column
                for (let j = 0; j < cols.length - 1; j++) {
                    let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").replace(/(\s\s+)/g, ' ').trim();
                    text = '"' + text.replace(/"/g, '""') + '"';
                    row.push(text);
                }
                if (row.length > 0) {
                    csv.push(row.join(","));
                }
            }

            if (csv.length <= 1) {
                Swal.fire('No data', 'There is no data to export.', 'info');
                return;
            }

            let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
            let downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
@endsection