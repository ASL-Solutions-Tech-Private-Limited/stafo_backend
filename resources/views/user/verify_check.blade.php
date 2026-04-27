@extends('user.layouts.app')
@section('title', '') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 shadow-sm border-0">
        <div>
            Please <a href="{{ route('companydocumentVerification') }}">Click Here</a> To Verify Your Aadhar Card To Proceed Further
        </div>
    </div>

@endsection