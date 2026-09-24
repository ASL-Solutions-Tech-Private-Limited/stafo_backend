@extends('user.layouts.app')
@section('title', 'CRM Leads & Pipeline | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">CRM Leads & Sales Pipeline</h3>
                <p class="text-muted small mb-0">Track prospect conversations, follow-up reminders, and sales representative assignments</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('leadCreate') }}" class="btn btn-primary px-3 py-2">
                    <i class="fa-solid fa-plus me-1"></i> Add New Lead
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 70px;" class="text-center">S.No</th>                            
                        <th style="width: 220px;">Lead Contact</th>
                        <th style="width: 200px;">Company / Business</th>
                        <th>Email Address</th>
                        <th style="width: 150px;">Phone</th>
                        <th style="width: 180px;">Assigned Rep</th>
                        <th style="width: 130px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leads as $index => $lead)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($lead->name ?? 'L', 0, 1)) }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $lead->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $lead->company_name ?: '—' }}</span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $lead->email ?: '—' }}</span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $lead->phone ?: '—' }}</span>
                            </td>
                            <td>
                                @if(isset($lead->employee->name))
                                    <span class="badge-stafo badge-stafo-info">
                                        <i class="fa-solid fa-user-tie me-1"></i> {{ $lead->employee->name }}
                                    </span>
                                @else
                                    <span class="badge-stafo badge-stafo-secondary">Unassigned</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('leadFollowup', $lead->id) }}" 
                                       class="btn btn-sm btn-outline-success p-0" 
                                       style="width: 32px; height: 32px; border-radius: 8px;"
                                       title="Add Follow-up">
                                        <i class="fa-solid fa-calendar-plus"></i>
                                    </a>

                                    <a href="{{ route('leadEdit', $lead->id) }}" 
                                       class="btn btn-sm btn-outline-warning p-0" 
                                       style="width: 32px; height: 32px; border-radius: 8px;"
                                       title="Edit Lead">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form id="delete-form-{{ $lead->id }}"
                                        action="{{ route('leadDelete', $lead->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-bullhorn fs-2 mb-2 d-block opacity-50"></i>
                                No CRM leads found. Click "Add New Lead" to create your first prospect!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection