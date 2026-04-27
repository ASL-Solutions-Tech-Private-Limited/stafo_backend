@extends('admin.layouts.layout')
@section('title', 'User Add')
@section('content')
    <div class="app-main__outer">

        <div class="app-main__inner">
            <div class="d-flex justify-content-between user-access">
                <div class="user-welcome">
                    <h3 class="mb-3">Add New</h3>
                </div>
                <!-- <button class="btn primary-bg"><i class="fa-solid fa-plus"></i> Add New User</button> -->
            </div>
            <form action="{{ route('homebanner.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="new-user-form ">
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-12 mb-3">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Enter Title">
                            <span class="error error-title"><span>
                        </div>

                        <!-- Image -->
                        <div class="col-md-12 mb-3">
                            <label for="image">Image<span class="red">*</span></label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            <span class="error error-image"><span>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('homebanner.index') }}">
                                <button type="button" class="btn bg-danger text-white">Cancel</button>
                            </a>
                            <button class="btn btn-success text-white submit" type="submit">Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


    </div>



@endsection