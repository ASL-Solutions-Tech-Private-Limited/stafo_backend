@extends('admin.layouts.layout')

@section('title', 'Create Referral Code')

@section('content')
    <div class="container">
        <h3 class="mb-3">Create Referral Code for {{ $company->company_name }}</h3>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('storeReferralCode') }}" method="POST">
            @csrf
            <input type="hidden" name="company_id" value="{{ $company->id }}">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-8">
                        <label class="form-label">Referral Code</label>
                        <input type="text" name="referralcode" class="form-control" placeholder="Enter Referral Code" required>
                    </div>
                </div>
                

                <div class="col-md-8">
                    <div class="mb-8">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-8">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>
                
            </div>
            <button type="submit" class="btn btn-primary mt-2">Submit</button>
            <a href="{{ route('referralCodeList',$company->id) }}" class="btn btn-secondary ">Back</a>
        </form>
    </div>
@endsection