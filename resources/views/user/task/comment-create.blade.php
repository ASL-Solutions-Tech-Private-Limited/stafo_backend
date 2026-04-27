@extends('user.layouts.app')

@section('title', 'Comment ') <!-- Set your custom title here -->

@section('content')
    <div class="card mt-4 p-3 ">
        <div class="container">
            <h2 class="fw-bold mb-3">Comment </h2>
            <form action="{{ route('commentStore',$task_id) }}" method="POST">
                @csrf

                <div class="row">                
                   

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="Comments">Comments</label>
                            <textarea name="comments" class="form-control" rows="4">{{ old('comments') }}</textarea>
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