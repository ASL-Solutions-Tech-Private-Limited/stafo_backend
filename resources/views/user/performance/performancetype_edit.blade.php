@extends('user.layouts.app')

@section('title', 'Edit Performance Type') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 edit-page">
        <h2 class="mb-3 fw-bold">Edit Performance Type</h2>

        <form action="{{ route('performancetypeUpdate', $Performancetype->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label for="name ">Performance Type</label>
                        <input type="text" name="name" class="form-control"
                            value="{{ old('name', $Performancetype->name) }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group mb-3">
                        <label for="description">Description</label>
                        <input type="text" name="description" class="form-control"
                            value="{{ old('description', $Performancetype->description) }}">
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                 
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary ">Update</button>
                    <a href="{{ route('salarytype.index') }}" class="btn btn-danger ">Cancel</a>
                </div>
            </div>







        </form>
    </div>
@endsection