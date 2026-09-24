@extends('user.layouts.app')
@section('title', 'Employee Rankings & Scores | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Employee Rankings & Leaderboard</h3>
                <p class="text-muted small mb-0">Track monthly performance scores, KPI metrics, and top-ranking employees</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="p-3 bg-light rounded-4 border mb-4">
            <form action="{{ route('employeeRankList') }}" method="GET">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-calendar-days"></i></span>
                            <select name="month" class="form-select border-start-0">
                                <option value="1" @if($month == 1) selected @endif>January</option>
                                <option value="2" @if($month == 2) selected @endif>February</option>
                                <option value="3" @if($month == 3) selected @endif>March</option>
                                <option value="4" @if($month == 4) selected @endif>April</option>
                                <option value="5" @if($month == 5) selected @endif>May</option>
                                <option value="6" @if($month == 6) selected @endif>June</option>
                                <option value="7" @if($month == 7) selected @endif>July</option>
                                <option value="8" @if($month == 8) selected @endif>August</option>
                                <option value="9" @if($month == 9) selected @endif>September</option>
                                <option value="10" @if($month == 10) selected @endif>October</option>
                                <option value="11" @if($month == 11) selected @endif>November</option>
                                <option value="12" @if($month == 12) selected @endif>December</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-calendar"></i></span>
                            <select name="year" class="form-select border-start-0">
                                @for($yr = 2023; $yr <= date('Y') + 10; $yr++)
                                    <option value="{{ $yr }}" @if($year == $yr) selected @endif>{{ $yr }}</option>  
                                @endfor                          
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                            <select name="employee_id" class="form-select border-start-0">
                                <option value="">All Employees</option>
                                @if(!$employees->isEmpty())
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" @if($employee_id == $employee->id) selected @endif>{{ $employee->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 90px;" class="text-center">Rank</th>
                        <th style="width: 260px;">Employee</th>
                        <th style="width: 180px;">Contact Phone</th>
                        <th style="width: 160px;" class="text-center">Total Score</th>
                        <th style="width: 140px;" class="text-center">Breakdown</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employeeLists as $index => $employee)
                        <tr>
                            <td class="text-center">
                                @if($index == 0)
                                    <span class="badge rounded-circle p-2 bg-warning text-dark fw-bold fs-6 shadow-sm" style="width: 36px; height: 36px; line-height: 20px;">
                                        <i class="fa-solid fa-crown text-white"></i>
                                    </span>
                                @elseif($index == 1)
                                    <span class="badge rounded-circle p-2 bg-secondary text-white fw-bold fs-6 shadow-sm" style="width: 36px; height: 36px; line-height: 20px;">
                                        2
                                    </span>
                                @elseif($index == 2)
                                    <span class="badge rounded-circle p-2 bg-danger bg-opacity-75 text-white fw-bold fs-6 shadow-sm" style="width: 36px; height: 36px; line-height: 20px;">
                                        3
                                    </span>
                                @else
                                    <span class="text-muted fw-bold">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 38px; height: 38px; font-size: 0.9rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($employee->employee->name ?? 'E', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $employee->employee->name ?? 'N/A' }}</span>
                                        <small class="text-muted">{{ $employee->employee->emp_id ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted"><i class="fa-solid fa-phone me-1 small"></i>{{ $employee->employee->phone ?: '—' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge-stafo badge-stafo-success fs-6 px-3 py-1">
                                    <i class="fa-solid fa-star me-1"></i> {{ $employee->total_marks }} Pts
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info px-3 py-1" 
                                        style="border-radius: 8px;"
                                        onclick="showDetailsModal({{ $employee->employee_id }})">
                                    <i class="fa-solid fa-eye me-1"></i> View Points
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-award fs-2 mb-2 d-block opacity-50"></i>
                                No ranking data recorded for this month/year.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    function showDetailsModal(employeeId) {
        $.ajax({
            url: '{{ route("employeeRankDetails") }}',
            method: 'GET',
            data: { employee_id: employeeId, month: '{{$month}}', year: '{{$year}}' },
            success: function(response) {
                const modalHtml = `
                    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title fw-bold text-dark" id="detailsModalLabel">
                                        <i class="fa-solid fa-chart-pie text-primary me-2"></i> KPI Point Breakdown
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    ${response.html}
                                </div>
                                <div class="modal-footer border-top bg-light">
                                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('body').append(modalHtml);
                $('#detailsModal').modal('show');

                $('#detailsModal').on('hidden.bs.modal', function () {
                    $(this).remove();
                });
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch employee rank details.', 'error');
            }
        });
    }
</script>
@endsection