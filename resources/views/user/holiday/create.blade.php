@extends('user.layouts.app')

@section('title', 'Holiday Add') <!-- Set your custom title here -->

@section('content')
@include('user.layouts.alert')
    <div class="card mt-4 p-3 ">
        <div class="container">
            <h2 class="fw-bold mb-3">Create New</h2>
            <form action="{{ route('holiday.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                                required>
                            @error('title')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control" rows="3">
                                {{ old('description') }}
                            </textarea>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}"
                                required>
                            @error('start_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}"
                                required>
                            @error('end_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary ">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection