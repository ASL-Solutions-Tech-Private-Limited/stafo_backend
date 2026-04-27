@extends('admin.layouts.layout')

@section('title', 'Comment Edit') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 ">
        <div class="container">
            <h2 class="fw-bold mb-3">Comment Edit</h2>
            <form action="{{ route('admin.commentUpdate',$comment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">              
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="comments">Comments</label>
                            <textarea name="comments" class="form-control" rows="4">{{ old('comments',$comment->comments) }}</textarea>
                            @error('comments')
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