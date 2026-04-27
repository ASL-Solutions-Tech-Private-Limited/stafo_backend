@extends('user.layouts.app')
@section('title', 'Subscription Details') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <div class="row">
            <b> {{$package->package_name}} </b>
            <i>{!! $package->description !!} </i>
            <br>

            Subscription Start: {{ date('dS F, Y', strtotime($company->subscription_start))}}
            <br>
            Subscription End: {{ date('dS F, Y', strtotime($company->subscription_end))}}
        </div>
    </div>

@endsection