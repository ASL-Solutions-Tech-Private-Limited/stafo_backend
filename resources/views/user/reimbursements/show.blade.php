@extends('user.layouts.app')

@section('title', 'Reimbursement Details')

@section('content')
    <div class="card mt-4 p-3 shadow-sm">
        <div class="container">

            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="mb-3 fw-bold">Reimbursement Details</h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('reimbursements.index') }}" class="btn btn-primary float-right">
                        <i class="bi bi-building"></i> Reimbursements List
                    </a>
                </div>
            </div>

            <div class="mt-4">
                <div class="container px-0">
                    <div>
                        <div class="col-lg-12">
                         

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Reimbursement Name:</strong> {{ $reimbursement->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Amount:</strong> {{ $reimbursement->amount }}</p>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Date:</strong> {{ $reimbursement->date }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> {{ $reimbursement->status }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Description:</strong>
                                        {{ $reimbursement->description ?? 'No description available' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Receipt:</strong>
                                        @if($reimbursement->receipt_file)
                                        <a href="{{ asset('uploads/receipt/' . $reimbursement->receipt_file) }}" target="_blank">View File</a>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($reimbursement->status == 'Pending')
                            <hr class="my-4">

                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <form action="{{ route('reimbursements.statuschange') }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="status" value="Approved">
                                        <input type="hidden" name="id" value="{{ $reimbursement->id }}">
                                        <button type="submit" class="btn btn-primary"
                                            onclick="return confirm('Are you sure you want to reject this reimbursement?')">
                                            <i class="bi bi-trash"></i> Approved
                                        </button>
                                    </form>
                                    <form action="{{ route('reimbursements.statuschange') }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="status" value="Rejected">
                                        <input type="hidden" name="id" value="{{ $reimbursement->id }}">
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to reject this reimbursement?')">
                                            <i class="bi bi-trash"></i> Rejected
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection