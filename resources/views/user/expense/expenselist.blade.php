@extends('user.layouts.app')
@section('title', 'Expense List | STAFO HRMS')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Expense Claims</h3>
                    <p class="text-muted small mb-0">Review employee reimbursement requests, invoices, and approval statuses</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('expenseCreate') }}" class="btn btn-primary px-3 py-2">
                        <i class="fa-solid fa-plus me-1"></i> Add Expense
                    </a>
                </div>
            </div>

            @include('user.layouts.alert')

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 70px;" class="text-center">S.No</th>
                            <th style="width: 240px;">Employee</th>
                            <th>Amount</th>
                            <th>Created On</th>
                            <th style="width: 200px;" class="text-center">Status</th>
                            <th style="width: 140px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($expensees as $index => $expense)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $expensees->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($expense->employee->name ?? 'E', 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $expense->employee->name ?? '-' }}</span>
                                            <small class="text-muted">ID: {{ $expense->employee->emp_id ?? $expense->employee_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">₹{{ number_format((float)$expense->amount, 2) }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary small">
                                        <i class="fa-regular fa-calendar me-1 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($expense->created_at)->format('M d, Y h:i A') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($expense->status == 'Approved')
                                        <span class="badge-stafo badge-stafo-success">
                                            <i class="fa-solid fa-circle-check"></i> Approved
                                        </span>
                                    @elseif($expense->status == 'Pending')
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <span class="badge-stafo badge-stafo-warning">
                                                <i class="fa-solid fa-clock"></i> Pending
                                            </span>
                                            <button type="button" class="btn btn-sm btn-success p-0 status_change" title="Approved" data-id="{{ $expense->id }}" style="width: 26px; height: 26px; border-radius: 6px;">
                                                <i class="fa-solid fa-check" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger p-0 status_change" title="Rejected" data-id="{{ $expense->id }}" style="width: 26px; height: 26px; border-radius: 6px;">
                                                <i class="fa-solid fa-xmark" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </div>
                                    @else
                                        <span class="badge-stafo badge-stafo-danger">
                                            <i class="fa-solid fa-circle-xmark"></i> Rejected
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="{{ route('expenseDetails', $expense->id) }}" class="btn btn-sm btn-outline-info p-0" title="View Details" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('expenseEdit', $expense->id) }}" class="btn btn-sm btn-outline-warning p-0" title="Edit Claim" style="width: 32px; height: 32px; border-radius: 8px;">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('expenseDelete', $expense->id) }}" method="POST"
                                            style="display:inline;" id="delete-form-{{ $expense->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger p-0" title="Delete Claim"
                                                onclick="confirmDelete(event, {{ $expense->id }})" style="width: 32px; height: 32px; border-radius: 8px;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-receipt fs-2 mb-2 d-block opacity-50"></i>
                                    No expense claims found. Click <strong>Add Expense</strong> to submit a request.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($expensees->hasPages())
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                    <small class="text-muted">Showing {{ $expensees->firstItem() }} to {{ $expensees->lastItem() }} of {{ $expensees->total() }} entries</small>
                    <div>{{ $expensees->links('pagination::bootstrap-4') }}</div>
                </div>
            @endif

        </div>
    </div>
@endsection

@section('js')    
    <script>
        $(document).ready(function() {
            $('.status_change').on('click', function() {
                var status = $(this).attr('title');
                var expenseId = $(this).attr('data-id');
                Swal.fire({
                    title: 'Change Status',
                    text: `Are you sure you want to change the status to ${status}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('expensestatuschange') }}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: status,
                                id: expenseId
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Changed!',
                                    `Status has been changed to ${status}.`,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'There was an error changing the status.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });

        function confirmDelete(event, reimbursementId) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${reimbursementId}`).submit();
                }
            });
        }
    </script>
@endsection