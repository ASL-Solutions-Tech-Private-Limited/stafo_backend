@extends('admin.layouts.layout')
@section('title', 'Edit Banner')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            <div class="d-flex justify-content-between user-access">
                <div class="user-welcome">
                    <h3>Edit Banner</h3>
                </div>
                <!-- <button class="btn primary-bg"><i class="fa-solid fa-plus"></i> Add New User</button> -->
            </div>
            <form action="{{ route('homebanner.update', $appbanner->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT') 
               <div class="new-user-form">
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-6 mb-3">
                            <label for="title">Title<span class="red">*</span></label>
                            <input type="text" name="title" id="title" class="form-control"
                                value="{{ old('title', $appbanner->title) }}" placeholder="Enter Title">
                            <span class="error error-title"><span>
                        </div>

                        
                        <!-- Image -->
                        <div class="col-md-12 mb-3">
                            <label for="image">Image</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            @if($appbanner->image)
                                <div class="mt-2">
                                    <img src="{{ asset('uploads/homebanner/' . $appbanner->image) }}" alt="Current Image" width="100">
                                </div>
                            @endif
                            <span class="error error-image"><span>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('homebanner.index') }}">
                                <button type="button" class="btn bg-danger text-white">Cancel</button>
                            </a>
                            <button class="btn btn-primary text-white submit" type="submit">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


    </div>





@endsection