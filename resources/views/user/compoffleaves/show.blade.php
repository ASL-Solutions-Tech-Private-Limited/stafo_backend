@extends('user.layouts.app')

@section('title', 'Comp-Off Leave Details | STAFO HRMS')

@section('content')
@include('user.layouts.alert')
<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">Comp-Off Leave Details</h3>
                    @if($compoffleave->status == 'Approved')
                        <span class="badge-stafo badge-stafo-success">
                            <i class="fa-solid fa-check-circle me-1"></i> Approved
                        </span>
                    @elseif($compoffleave->status == 'Pending')
                        <span class="badge-stafo badge-stafo-warning">
                            <i class="fa-solid fa-clock me-1"></i> Pending
                        </span>
                    @else
                        <span class="badge-stafo badge-stafo-danger">
                            <i class="fa-solid fa-circle-xmark me-1"></i> Rejected
                        </span>
                    @endif
                </div>
                <p class="text-muted small mb-0">Review compensatory off request and manage approval status</p>
            </div>
            <a href="{{ route('compoffleaves.index') }}" class="btn btn-outline-secondary px-3 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Comp-Off List
            </a>
        </div>

        <!-- Details Card -->
        <div class="p-4 bg-light rounded-4 border mb-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-info text-primary"></i> Request Summary
            </h5>

            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Employee</small>
                        <span class="fw-bold text-dark">{{ $compoffleave->employee->name ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Title</small>
                        <span class="fw-bold text-dark">{{ $compoffleave->title }}</span>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Comp-Off Date</small>
                        <span class="fw-bold text-dark">{{ $compoffleave->date }}</span>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Approval Status</small>
                        @if($compoffleave->status == 'Approved')
                            <span class="badge-stafo badge-stafo-success"><i class="fa-solid fa-check-circle me-1"></i> Approved</span>
                        @elseif($compoffleave->status == 'Pending')
                            <span class="badge-stafo badge-stafo-warning"><i class="fa-solid fa-clock me-1"></i> Pending</span>
                        @else
                            <span class="badge-stafo badge-stafo-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Rejected</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Description</small>
                        <p class="text-dark mb-0">{{ $compoffleave->description ?? 'No description provided.' }}</p>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="p-3 bg-white rounded-3 border">
                        <small class="text-muted d-block mb-1">Supporting Document</small>
                        @if($compoffleave->filename)
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-file-lines text-primary fs-5"></i>
                                <a href="{{ asset('uploads/compoff/' . $compoffleave->filename) }}" target="_blank" class="fw-semibold text-primary text-decoration-none">
                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Attached File
                                </a>
                            </div>
                        @else
                            <span class="text-muted">No document attached</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Action Bar (only for Pending) -->
        @if($compoffleave->status == 'Pending')
            <div class="p-4 bg-light rounded-4 border mb-4">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-gavel text-primary"></i> Approval Decision
                </h5>
                <p class="text-muted small mb-3">Take action on this compensatory off request</p>

                <div class="d-flex gap-2">
                    <form action="{{ route('compoffleaves.statuschange') }}" method="POST" style="display:inline;" id="approve-form-{{ $compoffleave->id }}">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="status" value="Approved">
                        <input type="hidden" name="id" value="{{ $compoffleave->id }}">
                        <button type="button" class="btn btn-success px-4 py-2 fw-bold shadow-sm" onclick="confirmAction('approve', {{ $compoffleave->id }})">
                            <i class="fa-solid fa-check-circle me-1"></i> Approve Request
                        </button>
                    </form>
                    <form action="{{ route('compoffleaves.statuschange') }}" method="POST" style="display:inline;" id="reject-form-{{ $compoffleave->id }}">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="status" value="Rejected">
                        <input type="hidden" name="id" value="{{ $compoffleave->id }}">
                        <button type="button" class="btn btn-outline-danger px-4 py-2 fw-bold" onclick="confirmAction('reject', {{ $compoffleave->id }})">
                            <i class="fa-solid fa-circle-xmark me-1"></i> Reject Request
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
            <a href="{{ route('compoffleaves.edit', $compoffleave->id) }}" class="btn btn-warning px-4 fw-bold">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
            </a>
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    function confirmAction(type, id) {
        const title = type === 'approve' ? 'Approve this request?' : 'Reject this request?';
        const text = type === 'approve' 
            ? 'The employee will be granted a compensatory off day.' 
            : 'This comp-off request will be marked as rejected.';
        const confirmBtn = type === 'approve' ? 'Yes, Approve!' : 'Yes, Reject!';
        const color = type === 'approve' ? '#28a745' : '#dc3545';

        Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmBtn
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`${type}-form-${id}`).submit();
            }
        });
    }
</script>
@endsection