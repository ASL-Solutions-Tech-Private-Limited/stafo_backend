@extends('user.layouts.app')
@section('title', 'Expense Forms | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Expense Categories & Forms</h3>
                <p class="text-muted small mb-0">Define reimbursement types, custom submission fields, and attachment requirements</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('expenseformCreate') }}" class="btn btn-primary px-3 py-2">
                    <i class="fa-solid fa-plus me-1"></i> Create Expense Form
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 70px;" class="text-center">S.No</th>
                        <th style="width: 260px;">Expense Type</th>
                        <th>Description</th>
                        <th style="width: 140px;" class="text-center">Attachment</th>
                        <th style="width: 130px;" class="text-center">Status</th>
                        <th style="width: 150px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenseforms as $index => $form)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $expenseforms->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width: 38px; height: 38px; font-size: 0.9rem; flex-shrink: 0;">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('expenseformDetails', $form->id) }}" class="text-dark fw-bold text-decoration-none d-block">
                                            {{ $form->name }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted">{{ Str::limit($form->description, 80) ?: '—' }}</span>
                            </td>
                            <td class="text-center">
                                @if($form->is_document_req == 1)
                                    <span class="badge-stafo badge-stafo-info">
                                        <i class="fa-solid fa-paperclip me-1"></i> Required
                                    </span>
                                @else
                                    <span class="badge-stafo badge-stafo-secondary">
                                        <i class="fa-solid fa-minus me-1"></i> Optional
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(strtolower($form->status ?? '') == 'active' || $form->status == 1 || $form->status == '1')
                                    <span class="badge-stafo badge-stafo-success">
                                        <i class="fa-solid fa-circle-check"></i> Active
                                    </span>
                                @else
                                    <span class="badge-stafo badge-stafo-danger">
                                        <i class="fa-solid fa-circle-xmark"></i> {{ $form->status ?: 'Inactive' }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="{{ route('expenseformDetails', $form->id) }}" 
                                       class="btn btn-sm btn-outline-info p-0" 
                                       style="width: 32px; height: 32px; border-radius: 8px;"
                                       title="View Form Structure">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('expenseformEdit', $form->id) }}" 
                                       class="btn btn-sm btn-outline-warning p-0" 
                                       style="width: 32px; height: 32px; border-radius: 8px;"
                                       title="Edit Form">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger p-0" 
                                            style="width: 32px; height: 32px; border-radius: 8px;"
                                            title="Delete Form"
                                            onclick="confirmDelete(event, {{ $form->id }})">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                    <form action="{{ route('expenseformDelete', $form->id) }}" method="POST"
                                        style="display:none;" id="delete-form-{{ $form->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-file-invoice-dollar fs-2 mb-2 d-block opacity-50"></i>
                                No expense forms configured yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($expenseforms, 'links') && $expenseforms->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                <small class="text-muted">Showing {{ $expenseforms->firstItem() }} to {{ $expenseforms->lastItem() }} of {{ $expenseforms->total() }} forms</small>
                <div>{{ $expenseforms->links('pagination::bootstrap-4') }}</div>
            </div>
        @endif

    </div>
</div>
@endsection

@section('js')
<script>
    function confirmDelete(event, formId) {
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this expense form!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${formId}`).submit();
            }
        });
    }
</script>
@endsection