@extends('admin.layouts.layout')

@section('title', 'Create Referral Code')

@section('content')
    <div class="container">
        <h3 class="mb-3">Edit Referral Code for {{ $company->company_name }}</h3>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('updateReferralCode',$referralcode->id) }}" method="POST">
            @csrf
            @method('PUT') 
            <input type="hidden" name="referralcode_id" value="{{ $referralcode->id }}">
            <input type="hidden" name="company_id" value="{{ $referralcode->company_id }}">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-8">
                        <label class="form-label">Referral Code</label>
                        <input type="text" name="referralcode" class="form-control" placeholder="Enter Referral Code" value="{{ $referralcode->referralcode }}" readonly>
                    </div>
                </div>
                

                <div class="col-md-8">
                    <div class="mb-8">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $referralcode->start_date }}" required>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-8">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $referralcode->end_date }}" required>
                    </div>
                </div>
                
            </div>
            <button type="submit" class="btn btn-primary mt-2">Submit</button>
        </form>
    </div>
@endsection