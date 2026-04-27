@extends('user.layouts.app')

@section('title', 'Salary Type add') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 ">
        <div class="container">
            <h2 class="fw-bold mb-3">Performance Type</h2>
            <form action="{{ route('performancetypeStore') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="name">Type Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                required>
                            @error('salary_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>                 
                   

                    <div class="col-md-6 text-end">
                        <button type="submit" class="btn btn-primary ">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection