@extends('user.layouts.app')

@section('title', 'Compoff Leave Details')

@section('content')
    <div class="card mt-4 p-3 shadow-sm">
        <div class="container">

            <div class="row mb-3">
                <div class="col-md-6">
                    <h2 class="mb-3 fw-bold">Compoff Leave Details</h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('compoffleaves.index') }}" class="btn btn-primary float-right">
                        <i class="bi bi-building"></i> Compoff Leaves List
                    </a>
                </div>
            </div>

            <div class="mt-4">
                <div class="container px-0">
                    <div>
                        <div class="col-lg-12">
                         

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Title:</strong> {{ $compoffleave->title }}</p>
                                </div>
                                
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Date:</strong> {{ $compoffleave->date }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> {{ $compoffleave->status }}</p>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <p><strong>Description:</strong>
                                        {{ $compoffleave->description ?? 'No description available' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Receipt:</strong>
                                        @if($compoffleave->filename)
                                        <a href="{{ asset('uploads/compoff/' . $compoffleave->filename) }}" target="_blank">View File</a>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($compoffleave->status == 'Pending')
                            <hr class="my-4">

                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <form action="{{ route('compoffleaves.statuschange') }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="status" value="Approved">
                                        <input type="hidden" name="id" value="{{ $compoffleave->id }}">
                                        <button type="submit" class="btn btn-primary"
                                            onclick="return confirm('Are you sure you want to reject this compoffleave?')">
                                            <i class="bi bi-trash"></i> Approved
                                        </button>
                                    </form>
                                    <form action="{{ route('compoffleaves.statuschange') }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="status" value="Rejected">
                                        <input type="hidden" name="id" value="{{ $compoffleave->id }}">
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to reject this compoffleave?')">
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