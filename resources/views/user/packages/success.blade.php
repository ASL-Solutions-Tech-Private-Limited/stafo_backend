@extends('user.layouts.app')
@section('title', 'Payment success') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <div>
            <h4 class="text-success">Payment Successful</h4>
            <p>Thank you for your payment! Your subscrtiption was successful.</p>
            <p>Your <b>{{ $package->package_name }}</b> package will expire on <b>{{ $subscription }}</b></p>
            
        </div>
    </div>

@endsection