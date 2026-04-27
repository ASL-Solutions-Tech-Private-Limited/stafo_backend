@extends('admin.layouts.layout')

@section('title', 'Document Edit')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('company.update') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $companytype->id}}">
        <div class="col-sm-12 col-xl-12">
            <div class="bg-light rounded h-100 p-4">
                <h3 class="mb-3">Company Edit</h3>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" value="{{$companytype->company_name}}" id="company_name"
                        name="company_name" placeholder="Company Name" required>
                    <label for="company_name">Company Name</label>
                </div>

                <div class="col-sm-4 col-xl-4">
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
            </div>
        </div>
        <form>

@endsection