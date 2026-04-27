@extends('user.layouts.app')
@section('title', 'Branch List') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <div>
            {{ $message }} <br>
            Click <a href="{{ route('company-packages.index') }}">here</a> to upgrade your plan.
        </div>
    </div>

@endsection